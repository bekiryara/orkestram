param(
    [Parameter(Mandatory = $true)]
    [string]$TaskId,

    [string]$Agent = "codex",

    [string]$Branch = "",

    [string]$Files = "",

    [string]$Note = ""
)

$ErrorActionPreference = "Stop"

if ($TaskId -notmatch '^TASK-[0-9]{3}$') {
    Write-Host "[start-task] FAIL: TaskId formati gecersiz. Beklenen: TASK-XXX"
    exit 1
}

if ([string]::IsNullOrWhiteSpace($Branch)) {
    $taskSlug = $TaskId.ToLower()
    $Branch = "agent/$Agent/$taskSlug"
}

$taskFile = "docs/tasks/$TaskId.md"
if (Test-Path -LiteralPath $taskFile) {
    Write-Host "[start-task] FAIL: Task dosyasi zaten var ($taskFile). Task ID tekrar kullanilamaz."
    exit 1
}

$locksPath = "docs/TASK_LOCKS.md"
if (Test-Path -LiteralPath $locksPath) {
    $locks = Get-Content -Path $locksPath -Raw
    if ($locks -match [regex]::Escape("| $TaskId |")) {
        Write-Host "[start-task] FAIL: TASK_LOCKS icinde ayni TaskId mevcut. Task ID tekrar kullanilamaz."
        exit 1
    }
}

$localBranchHit = git branch --list $Branch
$remoteBranchHit = git branch -r --list "origin/$Branch"
if (-not [string]::IsNullOrWhiteSpace($localBranchHit) -or -not [string]::IsNullOrWhiteSpace($remoteBranchHit)) {
    Write-Host "[start-task] FAIL: Branch zaten mevcut ($Branch). Yeni task icin yeni branch gerekir."
    exit 1
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

Write-Host "[start-task] OK"
exit 0
