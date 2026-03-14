param(
    [ValidateSet("orkestram", "izmirorkestra", "both")]
    [string]$App = "both",
    [switch]$Build,
    [switch]$SkipSync,
    [switch]$SyncFromWindows,
    [string]$Distro = "Ubuntu",
    [string]$LinuxUser = "bekir",
    [string]$LinuxDockerUser = "root",
    [string]$LinuxProjectRoot = ""
)

$ErrorActionPreference = "Stop"

$repoRoot = (Resolve-Path (Join-Path $PSScriptRoot "..")).Path
$guardScript = Join-Path $repoRoot "scripts\deploy-guard.ps1"

if ([string]::IsNullOrWhiteSpace($LinuxProjectRoot)) {
    $LinuxProjectRoot = "/home/$LinuxUser/orkestram"
}

$linuxComposeDir = "$LinuxProjectRoot/local-rebuild"

function Convert-WindowsPathToWsl {
    param([string]$Path)
    $resolved = (Resolve-Path $Path).Path
    $drive = $resolved.Substring(0, 1).ToLowerInvariant()
    $rest = $resolved.Substring(2).Replace('\', '/')
    return "/mnt/$drive$rest"
}

function Invoke-WslBash {
    param(
        [string]$User,
        [string]$Command
    )

    & wsl -d $Distro -u $User -- bash -lc $Command
    if ($LASTEXITCODE -ne 0) {
        throw "WSL komutu basarisiz: $Command"
    }
}

function Assert-MountSource {
    param(
        [string]$Container,
        [string]$ExpectedPrefix
    )

    $source = (docker inspect $Container --format "{{range .Mounts}}{{.Source}}{{println}}{{end}}" | Select-Object -First 1).Trim()
    if ([string]::IsNullOrWhiteSpace($source)) {
        throw "Mount kaynagi okunamadi: $Container"
    }

    if (-not $source.StartsWith($ExpectedPrefix)) {
        throw "Yanlis mount kaynagi: $Container -> $source | Beklenen prefix: $ExpectedPrefix"
    }

    Write-Host "[dev-up] mount OK $Container -> $source"
}

function Ensure-RuntimePermissions {
    param([string]$Container)

    $permissionCommand = @'
set -e
mkdir -p /var/www/html/storage/framework/views
mkdir -p /var/www/html/storage/framework/cache
mkdir -p /var/www/html/storage/logs
mkdir -p /var/www/html/bootstrap/cache
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R ug+rwX /var/www/html/storage /var/www/html/bootstrap/cache
test -w /var/www/html/storage/framework/views
test -w /var/www/html/bootstrap/cache
'@
    $permissionCommand = $permissionCommand -replace "`r", ""

    & docker exec $Container sh -lc $permissionCommand
    if ($LASTEXITCODE -ne 0) {
        throw "Runtime izin preflight FAIL: $Container (storage/bootstrap-cache yazma izni saglanamadi)"
    }

    Write-Host "[dev-up] runtime-permissions OK $Container"
}

$targets = @()
if ($App -eq "both") {
    $targets = @(
        @{ Name = "orkestram"; Domain = "orkestram.net"; Path = "$repoRoot\local-rebuild\apps\orkestram"; Container = "orkestram-local-web" },
        @{ Name = "izmirorkestra"; Domain = "izmirorkestra.net"; Path = "$repoRoot\local-rebuild\apps\izmirorkestra"; Container = "izmirorkestra-local-web" }
    )
} elseif ($App -eq "orkestram") {
    $targets = @(@{ Name = "orkestram"; Domain = "orkestram.net"; Path = "$repoRoot\local-rebuild\apps\orkestram"; Container = "orkestram-local-web" })
} else {
    $targets = @(@{ Name = "izmirorkestra"; Domain = "izmirorkestra.net"; Path = "$repoRoot\local-rebuild\apps\izmirorkestra"; Container = "izmirorkestra-local-web" })
}

if ($SyncFromWindows) {
    $wslSourcePath = Convert-WindowsPathToWsl -Path $repoRoot
    Write-Host "[dev-up] sync windows -> wsl"
    $syncCommand = @"
set -e
mkdir -p '$LinuxProjectRoot'
rsync -a --delete \
  --exclude '.git/' \
  --exclude 'local-rebuild/apps/*/vendor/' \
  --exclude 'local-rebuild/apps/*/storage/' \
  --exclude 'local-rebuild/apps/*/public/uploads/' \
  '$wslSourcePath/' '$LinuxProjectRoot/'
"@
    Invoke-WslBash -User $LinuxDockerUser -Command $syncCommand
} else {
    Write-Host "[dev-up] sync skipped (WSL source of truth). Use -SyncFromWindows to force Windows -> WSL sync."
}

Write-Host "[dev-up] docker compose up from WSL path: $linuxComposeDir"
$composeCommand = if ($Build) {
    "cd '$linuxComposeDir' && docker compose up -d --build"
} else {
    "cd '$linuxComposeDir' && docker compose up -d"
}
Invoke-WslBash -User $LinuxDockerUser -Command $composeCommand

$expectedPrefix = "/home/$LinuxUser/orkestram/local-rebuild/apps/"
foreach ($t in $targets) {
    Assert-MountSource -Container $t.Container -ExpectedPrefix $expectedPrefix
    Ensure-RuntimePermissions -Container $t.Container
}

foreach ($t in $targets) {
    $envPath = Join-Path $t.Path ".env"
    if (-not (Test-Path -LiteralPath $envPath -PathType Leaf)) {
        throw ".env bulunamadi: $envPath"
    }

    Write-Host "[dev-up] local-check -> $($t.Domain)"
    & $guardScript -Profile "local-check" -Domain $t.Domain -EnvFilePath $envPath -DeployPackPath $t.Path
    if ($LASTEXITCODE -ne 0) {
        throw "Guard local-check FAIL: $($t.Domain)"
    }
}

Write-Host "[dev-up] PASS"
exit 0

