param(
    [ValidateSet("orkestram", "izmirorkestra", "both")]
    [string]$App = "both",
    [ValidateSet("main", "design", "all")]
    [string]$Lane = "main",
    [switch]$Build,
    [switch]$SkipSync,
    [switch]$SyncFromWindows,
    [string]$Distro = "Ubuntu",
    [string]$LinuxUser = "bekir",
    [string]$LinuxDockerUser = "root",
    [string]$LinuxProjectRoot = "",
    [string]$DesignLinuxProjectRoot = ""
)

$ErrorActionPreference = "Stop"

$repoRoot = (Resolve-Path (Join-Path $PSScriptRoot "..")).Path
$guardScript = Join-Path $repoRoot "scripts\deploy-guard.ps1"

if ([string]::IsNullOrWhiteSpace($LinuxProjectRoot)) {
    $LinuxProjectRoot = "/home/$LinuxUser/orkestram-k"
}
if ([string]::IsNullOrWhiteSpace($DesignLinuxProjectRoot)) {
    $DesignLinuxProjectRoot = "/home/$LinuxUser/orkestram-b"
}

$linuxComposeDir = "$LinuxProjectRoot/local-rebuild"

function Convert-WindowsPathToWsl {
    param([string]$Path)
    $resolved = (Resolve-Path $Path).Path
    $drive = $resolved.Substring(0, 1).ToLowerInvariant()
    $rest = $resolved.Substring(2).Replace('\', '/')
    return "/mnt/$drive$rest"
}

function Convert-LinuxPathToWindowsUnc {
    param([string]$Path)

    $trimmed = $Path.Trim().TrimEnd('/')
    if (-not $trimmed.StartsWith('/')) {
        throw "Linux path bekleniyordu: $Path"
    }

    return "\\wsl$\$Distro" + ($trimmed -replace '/', '\\')
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
        [string]$ExpectedSource
    )

    $sources = docker inspect $Container --format "{{range .Mounts}}{{.Source}}{{println}}{{end}}" | ForEach-Object { ([string]$_).Trim() } | Where-Object { -not [string]::IsNullOrWhiteSpace($_) }
    if ($sources.Count -eq 0) {
        throw "Mount kaynagi okunamadi: $Container"
    }

    if ($sources -notcontains $ExpectedSource) {
        $joined = [string]::Join(', ', $sources)
        throw "Yanlis mount kaynagi: $Container -> $joined | Beklenen: $ExpectedSource"
    }

    Write-Host "[dev-up] mount OK $Container -> $ExpectedSource"
}

function Ensure-RuntimePermissions {
    param([string]$Container)

    $permissionCommand = @"
set -e
mkdir -p /var/www/html/storage/framework/views
mkdir -p /var/www/html/storage/framework/cache
mkdir -p /var/www/html/storage/framework/sessions
mkdir -p /var/www/html/storage/logs
mkdir -p /var/www/html/bootstrap/cache
mkdir -p /var/www/html/storage/app/public
mkdir -p /var/www/html/public/uploads
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R ug+rwX /var/www/html/storage /var/www/html/bootstrap/cache
if [ ! -L /var/www/html/public/storage ]; then
  rm -rf /var/www/html/public/storage
  php artisan storage:link >/dev/null 2>&1 || ln -s /var/www/html/storage/app/public /var/www/html/public/storage
fi
test -L /var/www/html/public/storage
test -w /var/www/html/storage/framework/views
test -w /var/www/html/bootstrap/cache
"@
    $permissionCommand = $permissionCommand -replace "`r", ""

    & docker exec $Container sh -lc $permissionCommand
    if ($LASTEXITCODE -ne 0) {
        throw "Runtime izin preflight FAIL: $Container (storage/bootstrap-cache yazma izni saglanamadi)"
    }

    Write-Host "[dev-up] runtime-permissions OK $Container"
}

function New-Target {
    param(
        [string]$Name,
        [string]$Domain,
        [string]$Container,
        [string]$LinuxRoot,
        [string]$SharedLinuxRoot,
        [string]$LaneName,
        [string]$PreviewUrl
    )

    $linuxPath = "$LinuxRoot/local-rebuild/apps/$Name"
    $sharedLinuxPath = "$SharedLinuxRoot/local-rebuild/apps/$Name"
    return @{
        Name = $Name
        Domain = $Domain
        Container = $Container
        Lane = $LaneName
        LinuxPath = $linuxPath
        WindowsPath = Convert-LinuxPathToWindowsUnc -Path $linuxPath
        SharedLinuxPath = $sharedLinuxPath
        SharedWindowsPath = Convert-LinuxPathToWindowsUnc -Path $sharedLinuxPath
        ExpectedSource = $linuxPath
        PreviewUrl = $PreviewUrl
    }
}

$targets = @()
if ($Lane -eq "main" -or $Lane -eq "all") {
    if ($App -eq "both" -or $App -eq "orkestram") {
        $targets += New-Target -Name "orkestram" -Domain "orkestram.net" -Container "orkestram-local-web" -LinuxRoot $LinuxProjectRoot -SharedLinuxRoot $LinuxProjectRoot -LaneName "main" -PreviewUrl "http://127.0.0.1:8180"
    }
    if ($App -eq "both" -or $App -eq "izmirorkestra") {
        $targets += New-Target -Name "izmirorkestra" -Domain "izmirorkestra.net" -Container "izmirorkestra-local-web" -LinuxRoot $LinuxProjectRoot -SharedLinuxRoot $LinuxProjectRoot -LaneName "main" -PreviewUrl "http://127.0.0.1:8181"
    }
}
if ($Lane -eq "design" -or $Lane -eq "all") {
    if ($App -eq "both" -or $App -eq "orkestram") {
        $targets += New-Target -Name "orkestram" -Domain "orkestram.net" -Container "orkestram-design-web" -LinuxRoot $DesignLinuxProjectRoot -SharedLinuxRoot $LinuxProjectRoot -LaneName "design" -PreviewUrl "http://127.0.0.1:8280"
    }
    if ($App -eq "both" -or $App -eq "izmirorkestra") {
        $targets += New-Target -Name "izmirorkestra" -Domain "izmirorkestra.net" -Container "izmirorkestra-design-web" -LinuxRoot $DesignLinuxProjectRoot -SharedLinuxRoot $LinuxProjectRoot -LaneName "design" -PreviewUrl "http://127.0.0.1:8281"
    }
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
$composeEnvironment = @(
    "DESIGN_ORKESTRAM_SOURCE=$DesignLinuxProjectRoot/local-rebuild/apps/orkestram",
    "DESIGN_IZMIRORKESTRA_SOURCE=$DesignLinuxProjectRoot/local-rebuild/apps/izmirorkestra",
    "DESIGN_SHARED_ORKESTRAM_VENDOR=$LinuxProjectRoot/local-rebuild/apps/orkestram/vendor",
    "DESIGN_SHARED_IZMIRORKESTRA_VENDOR=$LinuxProjectRoot/local-rebuild/apps/izmirorkestra/vendor",
    "DESIGN_SHARED_ORKESTRAM_STORAGE=$LinuxProjectRoot/local-rebuild/apps/orkestram/storage",
    "DESIGN_SHARED_IZMIRORKESTRA_STORAGE=$LinuxProjectRoot/local-rebuild/apps/izmirorkestra/storage",
    "DESIGN_SHARED_ORKESTRAM_UPLOADS=$LinuxProjectRoot/local-rebuild/apps/orkestram/public/uploads",
    "DESIGN_SHARED_IZMIRORKESTRA_UPLOADS=$LinuxProjectRoot/local-rebuild/apps/izmirorkestra/public/uploads",
    "DESIGN_SHARED_ORKESTRAM_ENV=$LinuxProjectRoot/local-rebuild/apps/orkestram/.env",
    "DESIGN_SHARED_IZMIRORKESTRA_ENV=$LinuxProjectRoot/local-rebuild/apps/izmirorkestra/.env",
    "DESIGN_ORKESTRAM_PORT=8280",
    "DESIGN_IZMIRORKESTRA_PORT=8281"
) -join ' '
$serviceList = @('mysql', 'adminer')
if ($Lane -eq 'main' -or $Lane -eq 'all') {
    if ($App -eq 'both' -or $App -eq 'orkestram') { $serviceList += 'orkestram-web' }
    if ($App -eq 'both' -or $App -eq 'izmirorkestra') { $serviceList += 'izmirorkestra-web' }
}
if ($Lane -eq 'design' -or $Lane -eq 'all') {
    if ($App -eq 'both' -or $App -eq 'orkestram') { $serviceList += 'orkestram-design-web' }
    if ($App -eq 'both' -or $App -eq 'izmirorkestra') { $serviceList += 'izmirorkestra-design-web' }
}
$serviceArgs = [string]::Join(' ', $serviceList)
$composeVerb = if ($Build) { 'docker compose up -d --build' } else { 'docker compose up -d' }
$composeCommand = "cd '$linuxComposeDir' && $composeEnvironment $composeVerb $serviceArgs"
Invoke-WslBash -User $LinuxDockerUser -Command $composeCommand

foreach ($t in $targets) {
    Assert-MountSource -Container $t.Container -ExpectedSource $t.ExpectedSource
    Ensure-RuntimePermissions -Container $t.Container
}

foreach ($t in $targets) {
    $envPath = if ($t.Lane -eq 'design') { Join-Path $t.SharedWindowsPath '.env' } else { Join-Path $t.WindowsPath '.env' }
    if (-not (Test-Path -LiteralPath $envPath -PathType Leaf)) {
        throw ".env bulunamadi: $envPath"
    }

    if ($t.Lane -eq 'design') {
        Write-Host "[dev-up] env shared -> $envPath"
    }

    $guardDeployPath = if ($t.Lane -eq 'design') { $t.SharedWindowsPath } else { $t.WindowsPath }
    Write-Host "[dev-up] local-check -> $($t.Domain) ($($t.Lane))"
    & $guardScript -Profile "local-check" -Domain $t.Domain -EnvFilePath $envPath -DeployPackPath $guardDeployPath
    if ($LASTEXITCODE -ne 0) {
        throw "Guard local-check FAIL: $($t.Domain) [$($t.Lane)]"
    }

    Write-Host "[dev-up] preview $($t.Lane)/$($t.Name) -> $($t.PreviewUrl) | source=$($t.ExpectedSource)"
}

Write-Host "[dev-up] PASS"
exit 0


