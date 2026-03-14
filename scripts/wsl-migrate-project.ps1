Param(
    [string]$Distro = "Ubuntu",
    [string]$LinuxUser = "bekir",
    [string]$LinuxProjectRoot = "",
    [switch]$SetupAgentWorkspaces
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

function Invoke-WslBash {
    param(
        [string]$Command,
        [string]$User = "root"
    )
    # Prevent CRLF from breaking bash parsing inside WSL.
    $safe = $Command -replace "`r", ""
    wsl -d $Distro -u $User -- bash -lc $safe
    if ($LASTEXITCODE -ne 0) { throw "WSL komutu basarisiz: $safe" }
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
$syncCommand = @(
    "set -e",
    "mkdir -p '$LinuxProjectRoot'",
    "rsync -a --delete --exclude '.git/' --exclude 'local-rebuild/apps/*/vendor/' --exclude 'local-rebuild/apps/*/storage/' --exclude 'local-rebuild/apps/*/public/uploads/' '$wslSourcePath/' '$LinuxProjectRoot/'",
    "chown -R $LinuxUser:$LinuxUser '$LinuxProjectRoot'"
) -join "; "

Invoke-WslBash -Command $syncCommand -User "root"

if ($SetupAgentWorkspaces) {
    Write-Host "Ajan calisma klasorleri kuruluyor..."
    $setupCommand = @(
        "set -e",
        "for d in a b c; do rm -rf '/home/$LinuxUser/orkestram-'`$d; done",
        "for d in a b c; do git clone '$LinuxProjectRoot' '/home/$LinuxUser/orkestram-'`$d; done",
        "chown -R $LinuxUser:$LinuxUser '/home/$LinuxUser/orkestram-a' '/home/$LinuxUser/orkestram-b' '/home/$LinuxUser/orkestram-c'"
    ) -join "; "
    Invoke-WslBash -Command $setupCommand -User "root"
}

Write-Host "Tamamlandi."
Write-Host "Linux proje yolu: $LinuxProjectRoot"
if ($SetupAgentWorkspaces) {
    Write-Host "Ajan klasorleri:"
    Write-Host "  /home/$LinuxUser/orkestram-a"
    Write-Host "  /home/$LinuxUser/orkestram-b"
    Write-Host "  /home/$LinuxUser/orkestram-c"
}
Write-Host "Calistirma adimi:"
Write-Host "  powershell -ExecutionPolicy Bypass -File scripts/dev-up.ps1 -App both"
