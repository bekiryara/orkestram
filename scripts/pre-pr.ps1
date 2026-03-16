param(
    [ValidateSet("quick", "full")]
    [string]$Mode = "quick"
)

$ErrorActionPreference = "Stop"

$scriptDir = Convert-Path $PSScriptRoot
$repoRoot = Convert-Path (Join-Path $scriptDir '..')
Set-Location $repoRoot
$ciGateScript = Convert-Path (Join-Path $scriptDir 'ci-gate.ps1')
$securityGateScript = Convert-Path (Join-Path $scriptDir 'security-gate.ps1')
$validateScript = Convert-Path (Join-Path $scriptDir 'validate.ps1')

function Run-Step {
    param(
        [string]$Name,
        [scriptblock]$Action
    )

    Write-Host "[pre-pr] $Name"
    try {
        & $Action
    } catch {
        $raw = $_.Exception.Message
        Write-Host "[pre-pr] FAIL -> $Name"
        if (-not [string]::IsNullOrWhiteSpace($raw)) {
            Write-Host "[pre-pr] neden: $raw"
        }
        exit 1
    }

    if ($LASTEXITCODE -ne 0) {
        Write-Host "[pre-pr] FAIL -> $Name"
        Write-Host "[pre-pr] neden: Komut exit code=$LASTEXITCODE"
        exit $LASTEXITCODE
    }
    Write-Host "[pre-pr] OK -> $Name"
}

Run-Step -Name "ci-gate local" -Action {
    powershell -ExecutionPolicy Bypass -File $ciGateScript
}

Run-Step -Name "security-gate local" -Action {
    powershell -ExecutionPolicy Bypass -File $securityGateScript
}

if ($Mode -eq "full") {
    Run-Step -Name "validate full both" -Action {
        powershell -ExecutionPolicy Bypass -File $validateScript -App both
    }
} else {
    Run-Step -Name "validate quick both" -Action {
        powershell -ExecutionPolicy Bypass -File $validateScript -App both -Mode quick
    }
}

Write-Host "[pre-pr] PASS (mode=$Mode)"
exit 0
