param(
    [Parameter(Mandatory = $true)]
    [string]$TaskId,

    [string]$Agent = "codex",

    [string]$Branch = "",

    [string]$Files = "",

    [string]$Note = ""
)

$ErrorActionPreference = "Stop"

function Fail([string]$Message) {
    Write-Host "[start-task] FAIL: $Message"
    exit 1
}

if ($TaskId -notmatch '^TASK-[0-9]{3}$') {
    Fail "TaskId formati gecersiz. Beklenen: TASK-XXX"
}

if ([string]::IsNullOrWhiteSpace($Branch)) {
    $taskSlug = $TaskId.ToLower()
    $Branch = "agent/$Agent/$taskSlug"
}

$taskFile = "docs/tasks/$TaskId.md"
$templatePath = "docs/tasks/_TEMPLATE.md"
$locksPath = "docs/TASK_LOCKS.md"
$nextTaskPath = "docs/NEXT_TASK.md"
$maxActiveTasks = 3

if (-not (Test-Path -LiteralPath $templatePath)) {
    Fail "Task template bulunamadi ($templatePath)."
}
if (-not (Test-Path -LiteralPath $locksPath)) {
    Fail "TASK_LOCKS bulunamadi ($locksPath)."
}
if (-not (Test-Path -LiteralPath $nextTaskPath)) {
    Fail "NEXT_TASK bulunamadi ($nextTaskPath)."
}
if (Test-Path -LiteralPath $taskFile) {
    Fail "Task dosyasi zaten var ($taskFile). Task ID tekrar kullanilamaz."
}

$locksRaw = Get-Content -Path $locksPath -Raw
if ($locksRaw -match [regex]::Escape("| $TaskId |")) {
    Fail "TASK_LOCKS icinde ayni TaskId mevcut. Task ID tekrar kullanilamaz."
}
$activeTaskCount = ([regex]::Matches($locksRaw, '\|[^\r\n]*\|\s*active\s*\|')).Count
if ($activeTaskCount -ge $maxActiveTasks) {
    Fail "TASK_LOCKS icinde $activeTaskCount aktif task var. En fazla $maxActiveTasks aktif task acilabilir."
}
if ($locksRaw -match "\|[^\r\n]*\|\s*$Agent\s*\|[^\r\n]*\|\s*active\s*\|") {
    Fail "$Agent icin zaten aktif task var. Her ajan ayni anda yalniz 1 aktif task tasiyabilir."
}

$localBranchHit = git branch --list $Branch
$remoteBranchHit = git branch -r --list "origin/$Branch"
if (-not [string]::IsNullOrWhiteSpace($localBranchHit) -or -not [string]::IsNullOrWhiteSpace($remoteBranchHit)) {
    Fail "Branch zaten mevcut ($Branch). Yeni task icin yeni branch gerekir."
}

$nowStamp = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
$nowShort = Get-Date -Format "yyyy-MM-dd HH:mm"
$filesList = @()
if (-not [string]::IsNullOrWhiteSpace($Files)) {
    $filesList = $Files.Split(',') | ForEach-Object { $_.Trim() } | Where-Object { -not [string]::IsNullOrWhiteSpace($_) }
}

$lockFiles = [System.Collections.Generic.List[string]]::new()
$lockFiles.Add($taskFile)
$lockFiles.Add('docs/TASK_LOCKS.md')
$lockFiles.Add('docs/NEXT_TASK.md')
foreach ($file in $filesList) {
    if (-not $lockFiles.Contains($file)) {
        $lockFiles.Add($file)
    }
}

$taskSummary = if ([string]::IsNullOrWhiteSpace($Note)) { "Bu gorevin amaci" } else { $Note }
$taskTemplate = Get-Content -Path $templatePath -Raw
$lockSection = ($lockFiles | ForEach-Object { '- `' + $_ + '`' }) -join [Environment]::NewLine
$taskContent = $taskTemplate.Replace('TASK-XXX', $TaskId)
$taskContent = $taskContent.Replace('`TODO | DOING | DONE`', '`DOING`')
$taskContent = $taskContent.Replace('`agent-name`', ('`' + $Agent + '`'))
$taskContent = $taskContent.Replace('`agent/agent-name/task-xxx`', ('`' + $Branch + '`'))
$taskContent = $taskContent.Replace('`YYYY-MM-DD HH:mm`', ('`' + $nowShort + '`'))
$taskContent = $taskContent.Replace('- Bu gorevin amaci', ('- ' + $taskSummary))
$taskContent = [regex]::Replace($taskContent, '(?s)## Lock Dosyalari\r?\n- `path/one`\r?\n- `path/two`', "## Lock Dosyalari`r`n$lockSection")
Set-Content -Encoding utf8 -Path $taskFile -Value $taskContent
Write-Host "[start-task] step-1 task file -> $taskFile"
Write-Host "[start-task] note: task owner checklistleri gercek sonuca gore doldurmak, WORKLOG/TASK_LOCKS/NEXT_TASK kapanisini yapmak ve teslim kanitini sunmak zorundadir"

$lockFilesValue = ($lockFiles -join ',')
$lockLine = "| $TaskId | $Agent | $Branch | active | $lockFilesValue | $nowStamp | $nowStamp | $taskSummary |"
Add-Content -Path $locksPath -Value $lockLine
Write-Host "[start-task] step-2 lock row -> $locksPath"

$nextRaw = Get-Content -Path $nextTaskPath -Raw
$nextRaw = $nextRaw -replace 'Durum: `READY`', 'Durum: `ACTIVE`'
$nextRaw = $nextRaw -replace 'Durum: `IDLE`', 'Durum: `ACTIVE`'
$activeLine = '- `' + $TaskId + '` - ' + $taskSummary
if ($nextRaw -match '## Aktif Gorevler \(Tek Kaynak\)') {
    $nextRaw = $nextRaw -replace '## Aktif Gorevler \(Tek Kaynak\)', '## Aktif Gorevler (Merkezi Koordinasyon)'
}
if ($nextRaw -match '1\. `YOK`.*') {
    $nextRaw = $nextRaw -replace '1\. `YOK`.*', ('1. `' + $TaskId + '` - ' + $taskSummary)
} else {
    $activeMatches = [regex]::Matches($nextRaw, '(?m)^\d+\. `TASK-\d{3}` - .+$')
    $nextIndex = $activeMatches.Count + 1
    if ($nextIndex -gt $maxActiveTasks) {
        Fail "NEXT_TASK icinde zaten $($activeMatches.Count) aktif gorev listelenmis. En fazla $maxActiveTasks aktif gorev desteklenir."
    }
    $nextRaw = [regex]::Replace($nextRaw, '(## Aktif Gorevler \(Merkezi Koordinasyon\)\r?\n)', "$1$nextIndex. `$TaskId` - $taskSummary`r`n", 1)
}
Set-Content -Encoding utf8 -Path $nextTaskPath -Value $nextRaw
Write-Host "[start-task] step-3 next task -> $nextTaskPath"

git checkout -b $Branch | Out-Null
Write-Host "[start-task] step-4 branch -> $Branch"
Write-Host "[start-task] started_at: $nowStamp"
Write-Host "[start-task] OK"
exit 0
