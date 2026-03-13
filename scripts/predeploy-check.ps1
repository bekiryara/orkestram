param(
    [Parameter(Mandatory = $true)]
    [ValidateSet("orkestram.net", "izmirorkestra.net", "www.orkestram.net", "www.izmirorkestra.net")]
    [string]$Domain,

    [Parameter(Mandatory = $true)]
    [string]$AppPath,

    [Parameter(Mandatory = $true)]
    [string]$EnvFilePath,

    [switch]$RunSmoke,
    [ValidateSet("orkestram", "izmirorkestra")]
    [string]$SmokeApp,
    [switch]$RunCategoryFlow,
    [ValidateSet("orkestram", "izmirorkestra")]
    [string]$CategoryApp
)

$ErrorActionPreference = "Stop"
$guardScript = "D:\orkestram\scripts\deploy-guard.ps1"
$smokeScript = "D:\orkestram\scripts\smoke-test.ps1"

function Invoke-CategoryFlowTest {
    param([string]$Target)

    $container = if ($Target -eq "orkestram") {
        "orkestram-local-web"
    } elseif ($Target -eq "izmirorkestra") {
        "izmirorkestra-local-web"
    } else {
        throw "Gecersiz app hedefi: $Target"
    }

    Write-Host "[predeploy] category-flow start app=$Target"
    & docker exec $container php artisan test --filter=CategorySystemFlowTest
    if ($LASTEXITCODE -ne 0) {
        throw "CategorySystemFlowTest FAIL: $Target"
    }
}

Write-Host "[predeploy] guard start"
& $guardScript -Profile "predeploy-check" -Domain $Domain -EnvFilePath $EnvFilePath -DeployPackPath $AppPath
if ($LASTEXITCODE -ne 0) {
    throw "Predeploy guard FAIL"
}

if ($RunSmoke) {
    if ([string]::IsNullOrWhiteSpace($SmokeApp)) {
        throw "-RunSmoke icin -SmokeApp zorunlu"
    }
    Write-Host "[predeploy] smoke start"
    & $smokeScript -App $SmokeApp
    if ($LASTEXITCODE -ne 0) {
        throw "Smoke test FAIL"
    }
}

if ($RunCategoryFlow) {
    if ([string]::IsNullOrWhiteSpace($CategoryApp)) {
        throw "-RunCategoryFlow icin -CategoryApp zorunlu"
    }
    Invoke-CategoryFlowTest -Target $CategoryApp
}

Write-Host "[predeploy] PASS"
exit 0
