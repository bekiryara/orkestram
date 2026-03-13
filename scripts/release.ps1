param(
    [ValidateSet("orkestram", "izmirorkestra", "both")]
    [string]$App = "both",
    [switch]$BuildImages,
    [switch]$OnayliYayin,
    [string]$OnayKodu
)

$ErrorActionPreference = "Stop"

$devUp = "D:\orkestram\scripts\dev-up.ps1"
$validate = "D:\orkestram\scripts\validate.ps1"
$smoke = "D:\orkestram\scripts\smoke-test.ps1"
$pack = "D:\orkestram\scripts\build-deploy-pack.ps1"

if (-not $OnayliYayin) {
    throw "Release kilidi aktif. Hazir degilsen calismaz. Hazir oldugunda: powershell -ExecutionPolicy Bypass -File D:\orkestram\scripts\release.ps1 -App both -OnayliYayin -OnayKodu HAZIR-YAYIN"
}

if ($OnayKodu -ne "HAZIR-YAYIN") {
    throw "Onay kodu eksik/yanlis. Release icin: -OnayKodu HAZIR-YAYIN"
}

$releaseContextToken = [Guid]::NewGuid().ToString("N")

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
    $env:ORKESTRAM_RELEASE_CONTEXT = "release.ps1"
    $env:ORKESTRAM_RELEASE_APPROVED = $OnayKodu
    $env:ORKESTRAM_RELEASE_CONTEXT_TOKEN = $releaseContextToken
    & $pack -App $App -ReleaseContextToken $releaseContextToken
} finally {
    Remove-Item Env:ORKESTRAM_VALIDATE_GATE_PASSED -ErrorAction SilentlyContinue
    Remove-Item Env:ORKESTRAM_RELEASE_CONTEXT -ErrorAction SilentlyContinue
    Remove-Item Env:ORKESTRAM_RELEASE_APPROVED -ErrorAction SilentlyContinue
    Remove-Item Env:ORKESTRAM_RELEASE_CONTEXT_TOKEN -ErrorAction SilentlyContinue
}
if ($LASTEXITCODE -ne 0) { throw "build-deploy-pack FAIL" }

Write-Host "[release] PASS"
exit 0
