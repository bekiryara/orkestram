param(
    [ValidateSet("quick", "full")]
    [string]$Mode = "quick"
)

$ErrorActionPreference = "Stop"

function Run-Step {
    param(
        [string]$Name,
        [scriptblock]$Action
    )

    Write-Host "[pre-pr] $Name"
    & $Action
    if ($LASTEXITCODE -ne 0) {
        Write-Host "[pre-pr] FAIL -> $Name"
        exit $LASTEXITCODE
    }
    Write-Host "[pre-pr] OK -> $Name"
}

Run-Step -Name "ci-gate local" -Action {
    powershell -ExecutionPolicy Bypass -File D:\orkestram\scripts\ci-gate.ps1
}

if ($Mode -eq "full") {
    Run-Step -Name "validate full both" -Action {
        powershell -ExecutionPolicy Bypass -File D:\orkestram\scripts\validate.ps1 -App both
    }
} else {
    Run-Step -Name "validate quick both" -Action {
        powershell -ExecutionPolicy Bypass -File D:\orkestram\scripts\validate.ps1 -App both -Mode quick
    }
}

Write-Host "[pre-pr] PASS"
exit 0
