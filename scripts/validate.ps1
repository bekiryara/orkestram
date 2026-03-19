param(
    [ValidateSet("orkestram", "izmirorkestra", "both")]
    [string]$App = "both",
    [ValidateSet("quick", "full")]
    [string]$Mode = "full"
)

$ErrorActionPreference = "Stop"

$scriptDir = Convert-Path $PSScriptRoot
$repoRoot = Convert-Path (Join-Path $scriptDir '..')
Set-Location $repoRoot
$devUpScript = Convert-Path (Join-Path $scriptDir 'dev-up.ps1')
$smokeTestScript = Convert-Path (Join-Path $scriptDir 'smoke-test.ps1')

function Fail-Step {
    param(
        [string]$Name,
        [string]$Reason,
        [string]$Class = "",
        [string]$Solution = ""
    )

    Write-Host "[validate] FAIL -> $Name"
    if (-not [string]::IsNullOrWhiteSpace($Class)) {
        Write-Host "[validate] sinif: $Class"
    }
    if (-not [string]::IsNullOrWhiteSpace($Reason)) {
        Write-Host "[validate] neden: $Reason"
    }
    if (-not [string]::IsNullOrWhiteSpace($Solution)) {
        Write-Host "[validate] cozum: $Solution"
    }
}

function Run-Step {
    param(
        [string]$Name,
        [scriptblock]$Action
    )

    Write-Host "[validate] $Name"
    try {
        & $Action
    } catch {
        $raw = $_.Exception.Message
        if ($raw -match "permission denied while trying to connect to the docker API") {
            Fail-Step -Name $Name -Class "ENV_BLOCKED" -Reason "Docker erisim izni yok" -Solution "komutu Docker erisimi olan yetkiyle tekrar calistir"
            exit 1
        }
        if ($raw -match "storage/framework/views" -or $raw -match "bootstrap/cache") {
            Fail-Step -Name $Name -Class "RUNTIME_BLOCKED" -Reason "Runtime izin sorunu (storage/bootstrap-cache yazma izni)" -Solution "dev-up preflightini tekrar calistir"
            exit 1
        }
        if ($raw -match "No such container" -or $raw -match "is not running") {
            Fail-Step -Name $Name -Class "RUNTIME_BLOCKED" -Reason "Container ayakta degil" -Solution "dev-up ve runtime readiness kanitini tekrar al"
            exit 1
        }
        if ($raw -match "vendor/autoload.php" -or $raw -match "Failed opening required") {
            Fail-Step -Name $Name -Class "RUNTIME_BLOCKED" -Reason "Uygulama dependency/runtime hazir degil" -Solution "vendor/runtime hazirligini dogrula; bu durum kod fail degil ortam blokajidir"
            exit 1
        }
        throw
    }

    if ($LASTEXITCODE -ne 0) {
        Fail-Step -Name $Name -Class "CODE_FAIL" -Reason "Komut exit code=$LASTEXITCODE"
        exit $LASTEXITCODE
    }
    Write-Host "[validate] OK -> $Name"
}

function Run-FeatureTestsFor {
    param([string]$Container)

    Run-Step -Name "$Container FeedbackModerationAccessTest" -Action {
        docker exec $Container php artisan test --filter=FeedbackModerationAccessTest
    }

    Run-Step -Name "$Container ListingFeedbackFlowTest" -Action {
        docker exec $Container php artisan test --filter=ListingFeedbackFlowTest
    }
}

if ($Mode -eq "full") {
    Run-Step -Name "dev-up ($App)" -Action {
        powershell -ExecutionPolicy Bypass -File $devUpScript -App $App
    }
}

if ($App -in @("orkestram", "both")) {
    Run-FeatureTestsFor -Container "orkestram-local-web"
}

if ($App -in @("izmirorkestra", "both")) {
    Run-FeatureTestsFor -Container "izmirorkestra-local-web"
}

Run-Step -Name "smoke-test ($App)" -Action {
    powershell -ExecutionPolicy Bypass -File $smokeTestScript -App $App
}

Write-Host "[validate] PASS"
exit 0


