param()

$ErrorActionPreference = "Stop"

function Fail([string]$Message) {
    Write-Host "[security-gate] FAIL: $Message"
    exit 1
}

$tracked = git ls-files
if ($LASTEXITCODE -ne 0) {
    Fail "git ls-files calistirilamadi"
}

$patterns = @(
    @{ Name = "github_pat"; Regex = 'github_pat_[A-Za-z0-9_]+' },
    @{ Name = "ghp_token"; Regex = 'ghp_[A-Za-z0-9]{20,}' },
    @{ Name = "aws_access_key"; Regex = 'AKIA[0-9A-Z]{16}' },
    @{ Name = "private_key_block"; Regex = '-----BEGIN (RSA |EC |OPENSSH |DSA )?PRIVATE KEY-----' },
    @{ Name = "slack_token"; Regex = 'xox[baprs]-[0-9A-Za-z-]{10,}' }
)

$excludeExt = @(".png", ".jpg", ".jpeg", ".gif", ".webp", ".svg", ".ico", ".zip", ".gz", ".7z", ".pdf")

foreach ($file in $tracked) {
    $path = [string]$file
    if ([string]::IsNullOrWhiteSpace($path)) { continue }
    if (-not (Test-Path -LiteralPath $path -PathType Leaf)) { continue }

    $ext = [System.IO.Path]::GetExtension($path).ToLowerInvariant()
    if ($excludeExt -contains $ext) { continue }

    $content = ""
    try {
        $content = Get-Content -LiteralPath $path -Raw -ErrorAction Stop
    } catch {
        continue
    }

    foreach ($p in $patterns) {
        if ($content -match $p.Regex) {
            Fail "Potansiyel secret bulundu ($($p.Name)) -> $path"
        }
    }
}

Write-Host "[security-gate] PASS"
exit 0
