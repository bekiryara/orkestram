Param(
    [string]$Distro = "Ubuntu",
    [string]$LinuxUser = "bekir",
    [string]$LinuxProjectRoot = ""
)

$ErrorActionPreference = "Stop"

if ([string]::IsNullOrWhiteSpace($LinuxProjectRoot)) {
    $LinuxProjectRoot = "/home/$LinuxUser/orkestram"
}

function Test-DistroExists {
    param([string]$Name)
    $list = (wsl -l -q) -replace "`0", "" | ForEach-Object { $_.Trim() } | Where-Object { $_ }
    return $list -contains $Name
}

function Convert-WindowsPathToWsl {
    param([string]$Path)
    $resolved = (Resolve-Path $Path).Path
    $drive = $resolved.Substring(0, 1).ToLowerInvariant()
    $rest = $resolved.Substring(2).Replace('\', '/')
    return "/mnt/$drive$rest"
}

if (-not (Test-DistroExists -Name $Distro)) {
    Write-Host "WSL distro '$Distro' bulunamadi."
    Write-Host "Kurulum icin once su komutu calistir:"
    Write-Host "  wsl --install -d $Distro"
    Write-Host "Kurulumdan sonra bu scripti tekrar calistir."
    exit 1
}

$repoRoot = (Resolve-Path (Join-Path $PSScriptRoot "..")).Path
$wslSourcePath = Convert-WindowsPathToWsl -Path $repoRoot

Write-Host "WSL kopya/senkron basliyor..."
$syncCommand = @"
mkdir -p '$LinuxProjectRoot' && rsync -a --delete \
  --exclude '.git/' \
  --exclude 'local-rebuild/apps/*/vendor/' \
  --exclude 'local-rebuild/apps/*/storage/' \
  --exclude 'local-rebuild/apps/*/public/uploads/' \
  '$wslSourcePath/' '$LinuxProjectRoot/'
"@

wsl -d $Distro -u root -- bash -lc $syncCommand
if ($LASTEXITCODE -ne 0) { throw "WSL sync basarisiz" }

Write-Host "Tamamlandi."
Write-Host "Linux proje yolu: $LinuxProjectRoot"
Write-Host "Calistirma adimi:"
Write-Host "  powershell -ExecutionPolicy Bypass -File scripts/dev-up.ps1 -App both"
