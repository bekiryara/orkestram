param(
    [ValidateSet("orkestram", "izmirorkestra", "both")]
    [string]$App = "both",
    [switch]$BuildImages
)

$ErrorActionPreference = "Stop"

$devUp = "D:\orkestram\scripts\dev-up.ps1"
$validate = "D:\orkestram\scripts\validate.ps1"
$smoke = "D:\orkestram\scripts\smoke-test.ps1"
$pack = "D:\orkestram\scripts\build-deploy-pack.ps1"

function Invoke-CategoryFlowTest {
    param([string]$Target)

    $container = if ($Target -eq "orkestram") {
        "orkestram-local-web"
    } elseif ($Target -eq "izmirorkestra") {
        "izmirorkestra-local-web"
    } else {
        throw "Gecersiz app hedefi: $Target"
    }

    Write-Host "[release] step=category-flow-test app=$Target"
    & docker exec $container php artisan test --filter=CategorySystemFlowTest
    if ($LASTEXITCODE -ne 0) {
        throw "CategorySystemFlowTest FAIL: $Target"
    }
}

Write-Host "[release] step=dev-up"
if ($BuildImages) {
    & $devUp -App $App -Build
} else {
    & $devUp -App $App
}
if ($LASTEXITCODE -ne 0) { throw "dev-up FAIL" }

Write-Host "[release] step=validate (gate)"
& $validate -App $App -Mode quick
if ($LASTEXITCODE -ne 0) { throw "validate gate FAIL" }

if ($App -eq "both") {
    Invoke-CategoryFlowTest -Target "orkestram"
    Invoke-CategoryFlowTest -Target "izmirorkestra"
} else {
    Invoke-CategoryFlowTest -Target $App
}

Write-Host "[release] step=smoke"
& $smoke -App $App
if ($LASTEXITCODE -ne 0) { throw "smoke FAIL" }

Write-Host "[release] step=build-deploy-pack"
try {
    $env:ORKESTRAM_VALIDATE_GATE_PASSED = "1"
    & $pack -App $App
} finally {
    Remove-Item Env:ORKESTRAM_VALIDATE_GATE_PASSED -ErrorAction SilentlyContinue
}
if ($LASTEXITCODE -ne 0) { throw "build-deploy-pack FAIL" }

Write-Host "[release] PASS"
exit 0
