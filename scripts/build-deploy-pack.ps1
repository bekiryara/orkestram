param(
    [ValidateSet("orkestram", "izmirorkestra", "both")]
    [string]$App = "both",
    [string]$OutRoot = "D:\orkestram\deploy",
    [string]$PolicyPath = "D:\orkestram\scripts\deploy-guard.policy.json",
    [ValidateSet("auto", "7z", "compressarchive")]
    [string]$Compressor = "auto",
    [switch]$SkipVendor,
    [int]$CopyThreads = 32,
    [string]$ReleaseContextToken = ""
)

$ErrorActionPreference = "Stop"
$validate = "D:\orkestram\scripts\validate.ps1"

function Ensure-Dir {
    param([string]$Path)
    if (-not (Test-Path -LiteralPath $Path)) {
        New-Item -ItemType Directory -Path $Path | Out-Null
    }
}

function Write-EnvExample {
    param(
        [string]$Path,
        [string]$Domain,
        [string]$DbName
    )

@"
APP_NAME=OrkestramV1
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://$Domain

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=$DbName
DB_USERNAME=
DB_PASSWORD=

SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=database
"@ | Set-Content -Path $Path -Encoding UTF8
}

function Resolve-Compressor {
    param([string]$Requested)
    if ($Requested -eq "7z") { return "7z" }
    if ($Requested -eq "compressarchive") { return "compressarchive" }

    $sevenZip = Get-Command 7z -ErrorAction SilentlyContinue
    if ($sevenZip) { return "7z" }
    return "compressarchive"
}

function Compress-Stage {
    param(
        [string]$StageDir,
        [string]$ZipPath,
        [string]$Mode
    )

    if (Test-Path -LiteralPath $ZipPath) {
        Remove-Item -LiteralPath $ZipPath -Force
    }

    if ($Mode -eq "7z") {
        $old = Get-Location
        try {
            Set-Location $StageDir
            & 7z a -tzip -mx=3 $ZipPath * | Out-Null
            if ($LASTEXITCODE -ne 0) {
                throw "7z sikistirma hatasi: $LASTEXITCODE"
            }
        }
        finally {
            Set-Location $old
        }
        return
    }

    Compress-Archive -Path (Join-Path $StageDir "*") -DestinationPath $ZipPath -CompressionLevel Fastest
}

if (-not (Test-Path -LiteralPath $PolicyPath -PathType Leaf)) {
    throw "Policy dosyasi bulunamadi: $PolicyPath"
}
$policy = Get-Content -LiteralPath $PolicyPath -Raw | ConvertFrom-Json

$releaseTrustedCaller = (
    $env:ORKESTRAM_VALIDATE_GATE_PASSED -eq "1" -and
    $env:ORKESTRAM_RELEASE_CONTEXT -eq "release.ps1" -and
    $env:ORKESTRAM_RELEASE_APPROVED -eq "HAZIR-YAYIN" -and
    -not [string]::IsNullOrWhiteSpace($ReleaseContextToken) -and
    $env:ORKESTRAM_RELEASE_CONTEXT_TOKEN -eq $ReleaseContextToken
)

if (-not $releaseTrustedCaller) {
    Write-Host "[build-deploy] step=validate (gate)"
    & $validate -App $App -Mode quick
    if ($LASTEXITCODE -ne 0) {
        throw "validate gate FAIL"
    }
} else {
    Write-Host "[build-deploy] gate=trusted-caller (release context verified)"
}

$targets = @()
if ($App -eq "both") {
    $targets = @(
        @{ Name = "orkestram"; Domain = "orkestram.net"; Source = "D:\orkestram\local-rebuild\apps\orkestram" },
        @{ Name = "izmirorkestra"; Domain = "izmirorkestra.net"; Source = "D:\orkestram\local-rebuild\apps\izmirorkestra" }
    )
} elseif ($App -eq "orkestram") {
    $targets = @(@{ Name = "orkestram"; Domain = "orkestram.net"; Source = "D:\orkestram\local-rebuild\apps\orkestram" })
} else {
    $targets = @(@{ Name = "izmirorkestra"; Domain = "izmirorkestra.net"; Source = "D:\orkestram\local-rebuild\apps\izmirorkestra" })
}

Ensure-Dir -Path $OutRoot
$stamp = Get-Date -Format "yyyyMMdd_HHmmss"
$compressorMode = Resolve-Compressor -Requested $Compressor

Write-Host "[build-deploy] app=$App compressor=$compressorMode skipVendor=$SkipVendor copyThreads=$CopyThreads"

foreach ($t in $targets) {
    if (-not (Test-Path -LiteralPath $t.Source -PathType Container)) {
        throw "Kaynak app bulunamadi: $($t.Source)"
    }

    $releaseDir = Join-Path $OutRoot "$($t.Name)_$stamp"
    $stageDir = Join-Path $releaseDir "app"
    Ensure-Dir -Path $releaseDir
    Ensure-Dir -Path $stageDir

    Get-ChildItem -LiteralPath $stageDir -Force | Remove-Item -Recurse -Force -ErrorAction SilentlyContinue

    $robocopyArgs = @(
        $t.Source,
        $stageDir,
        '/E','/R:1','/W:1',"/MT:$CopyThreads",
        '/NFL','/NDL','/NJH','/NJS','/NC','/NS','/NP',
        '/XD','node_modules','tests','.git','.github',
        '/XF','.env','phpunit.xml','*.log'
    )

    if ($SkipVendor) {
        $robocopyArgs += @('/XD','vendor')
    }

    & robocopy @robocopyArgs | Out-Null
    if ($LASTEXITCODE -gt 7) {
        throw "robocopy hata verdi: $LASTEXITCODE ($($t.Name))"
    }

    $junk = @(
        'storage\\logs\\laravel.log',
        'storage\\framework\\views\\*.php',
        'storage\\framework\\cache\\data\\*',
        'storage\\framework\\sessions\\*'
    )
    foreach ($j in $junk) {
        Get-ChildItem -Path (Join-Path $stageDir $j) -ErrorAction SilentlyContinue |
            Remove-Item -Recurse -Force -ErrorAction SilentlyContinue
    }

    Ensure-Dir -Path (Join-Path $stageDir 'storage')
    Ensure-Dir -Path (Join-Path $stageDir 'bootstrap\\cache')

    $dbName = [string]$policy.domains.($t.Domain).db_name
    Write-EnvExample -Path (Join-Path $releaseDir '.env.production.example') -Domain $t.Domain -DbName $dbName

@"
release=$stamp
app=$($t.Name)
domain=$($t.Domain)
source=$($t.Source)
created=$(Get-Date -Format "yyyy-MM-dd HH:mm:ss")
compressor=$compressorMode
skip_vendor=$SkipVendor
notes=Upload app.zip to domain docroot, extract, then create .env from .env.production.example
"@ | Set-Content -Path (Join-Path $releaseDir 'DEPLOY_INFO.txt') -Encoding UTF8

    $zipPath = Join-Path $releaseDir 'app.zip'
    Compress-Stage -StageDir $stageDir -ZipPath $zipPath -Mode $compressorMode

    Write-Host "[build-deploy] PASS -> $zipPath"
}

exit 0
