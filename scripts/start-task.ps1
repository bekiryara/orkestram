param(
    [Parameter(Mandatory = $true)]
    [string]$TaskId,

    [string]$Agent = "codex",

    [string]$Branch = "",

    [string]$Files = "",

    [string]$Note = ""
)

$ErrorActionPreference = "Stop"

if ([string]::IsNullOrWhiteSpace($Branch)) {
    $taskSlug = $TaskId.ToLower()
    $Branch = "agent/$Agent/$taskSlug"
}

$now = Get-Date -Format "yyyy-MM-dd HH:mm:ss"

Write-Host "[start-task] task: $TaskId"
Write-Host "[start-task] agent: $Agent"
Write-Host "[start-task] branch: $Branch"
Write-Host "[start-task] started_at: $now"

if (-not [string]::IsNullOrWhiteSpace($Files)) {
    Write-Host "[start-task] files: $Files"
}
if (-not [string]::IsNullOrWhiteSpace($Note)) {
    Write-Host "[start-task] note: $Note"
}

exit 0
