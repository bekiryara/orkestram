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

Run-Step -Name "git remote/upstream discipline" -Action {
    $origin = (git remote get-url origin).Trim()
    if ($LASTEXITCODE -ne 0 -or [string]::IsNullOrWhiteSpace($origin)) {
        throw "origin remote okunamadi"
    }

    $expectedOrigin = "https://github.com/bekiryara/orkestram.git"
    if ($origin -ne $expectedOrigin) {
        throw "origin beklenen GitHub remote degil: $origin"
    }

    $currentBranch = (git rev-parse --abbrev-ref HEAD).Trim()
    if ($LASTEXITCODE -ne 0 -or [string]::IsNullOrWhiteSpace($currentBranch)) {
        throw "aktif branch okunamadi"
    }

    if ($currentBranch -notmatch '^agent\/[a-z0-9-]+\/task-[0-9]+$' -and $currentBranch -ne 'main') {
        throw "branch disiplini disi branch: $currentBranch"
    }

    $upstream = (git rev-parse --abbrev-ref --symbolic-full-name "@{u}" 2>$null).Trim()
    if ($LASTEXITCODE -ne 0 -or [string]::IsNullOrWhiteSpace($upstream)) {
        throw "upstream tanimi yok; once git push -u origin $currentBranch"
    }

    $expectedUpstream = "origin/$currentBranch"
    if ($upstream -ne $expectedUpstream) {
        throw "upstream uyumsuz: $upstream (beklenen: $expectedUpstream)"
    }
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
