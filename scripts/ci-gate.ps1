param()

$ErrorActionPreference = "Stop"

function Fail([string]$Message) {
    Write-Host "[ci-gate] FAIL: $Message"
    exit 1
}

function Assert-FileExists([string]$Path) {
    if (-not (Test-Path -LiteralPath $Path -PathType Leaf)) {
        Fail "Eksik dosya: $Path"
    }
    Write-Host "[ci-gate] OK file: $Path"
}

Assert-FileExists ".gitignore"
Assert-FileExists "scripts/validate.ps1"
Assert-FileExists "docs/NEXT_TASK.md"
Assert-FileExists "docs/WORKLOG.md"

$tracked = git ls-files
if ($LASTEXITCODE -ne 0) {
    Fail "git ls-files calistirilamadi"
}

$forbiddenPatterns = @(
    '(^|/).*\.sql\.gz$',
    '(^|/).*\.zip$',
    '(^|/)deploy/',
    '(^|/)deploy-pack/',
    '(^|/)local-rebuild/apps/[^/]+/\.env$',
    '(^|/)local-rebuild/apps/[^/]+/vendor/',
    '(^|/)local-rebuild/apps/[^/]+/storage/'
)

foreach ($line in $tracked) {
    $path = [string]$line
    foreach ($pattern in $forbiddenPatterns) {
        if ($path -match $pattern) {
            Fail "Yasakli/agir dosya track ediliyor: $path"
        }
    }
}

Write-Host "[ci-gate] PASS"
exit 0
