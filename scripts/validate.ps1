param(
    [ValidateSet("orkestram", "izmirorkestra", "both")]
    [string]$App = "both",
    [ValidateSet("quick", "full")]
    [string]$Mode = "full"
)

$ErrorActionPreference = "Stop"

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
            Write-Host "[validate] FAIL -> $Name"
            Write-Host "[validate] neden: Docker erisim izni yok. Cozum: komutu Docker erisimi olan yetkiyle tekrar calistir."
            exit 1
        }
        if ($raw -match "storage\/framework\/views" -or $raw -match "bootstrap\/cache") {
            Write-Host "[validate] FAIL -> $Name"
            Write-Host "[validate] neden: Runtime izin sorunu (storage/bootstrap-cache yazma izni). Cozum: dev-up preflightini tekrar calistir."
            exit 1
        }
        throw
    }

    if ($LASTEXITCODE -ne 0) {
        Write-Host "[validate] FAIL -> $Name"
        Write-Host "[validate] neden: Komut exit code=$LASTEXITCODE"
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
        powershell -ExecutionPolicy Bypass -File D:\orkestram\scripts\dev-up.ps1 -App $App
    }
}

if ($App -in @("orkestram", "both")) {
    Run-FeatureTestsFor -Container "orkestram-local-web"
}

if ($App -in @("izmirorkestra", "both")) {
    Run-FeatureTestsFor -Container "izmirorkestra-local-web"
}

Run-Step -Name "smoke-test ($App)" -Action {
    powershell -ExecutionPolicy Bypass -File D:\orkestram\scripts\smoke-test.ps1 -App $App
}

Write-Host "[validate] PASS"
exit 0
