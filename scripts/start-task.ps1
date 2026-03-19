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

function Normalize-LockTarget([string]$Target) {
    if ([string]::IsNullOrWhiteSpace($Target)) {
        return ""
    }
    return ($Target.Trim() -replace '\\', '/')
}

function Has-Wildcard([string]$Target) {
    return (Normalize-LockTarget $Target) -match '[*?]'
}

function Get-LockBase([string]$Target) {
    $normalized = Normalize-LockTarget $Target
    if ([string]::IsNullOrWhiteSpace($normalized)) {
        return ""
    }
    $match = [regex]::Match($normalized, '[*?]')
    if (-not $match.Success) {
        return $normalized
    }
    return $normalized.Substring(0, $match.Index)
}

function Is-CoordinationTarget([string]$Target) {
    $normalized = Normalize-LockTarget $Target
    if ([string]::IsNullOrWhiteSpace($normalized)) {
        return $true
    }
    if ($normalized -in @('docs/TASK_LOCKS.md', 'docs/NEXT_TASK.md', 'docs/WORKLOG.md')) {
        return $true
    }
    if ($normalized -match '^docs/tasks/TASK-[0-9]{3}\.md$') {
        return $true
    }
    return $false
}

function Test-LockOverlap([string]$Left, [string]$Right) {
    $leftNorm = Normalize-LockTarget $Left
    $rightNorm = Normalize-LockTarget $Right
    if ([string]::IsNullOrWhiteSpace($leftNorm) -or [string]::IsNullOrWhiteSpace($rightNorm)) {
        return $false
    }
    if ($leftNorm -eq $rightNorm) {
        return $true
    }

    $leftWild = Has-Wildcard $leftNorm
    $rightWild = Has-Wildcard $rightNorm
    if (-not $leftWild -and -not $rightWild) {
        return $false
    }

    $leftBase = Get-LockBase $leftNorm
    $rightBase = Get-LockBase $rightNorm
    if ([string]::IsNullOrWhiteSpace($leftBase) -or [string]::IsNullOrWhiteSpace($rightBase)) {
        return $true
    }

    if ($leftWild -and $rightNorm.StartsWith($leftBase, [System.StringComparison]::OrdinalIgnoreCase)) {
        return $true
    }
    if ($rightWild -and $leftNorm.StartsWith($rightBase, [System.StringComparison]::OrdinalIgnoreCase)) {
        return $true
    }
    if ($leftWild -and $rightWild) {
        if ($leftBase.StartsWith($rightBase, [System.StringComparison]::OrdinalIgnoreCase) -or $rightBase.StartsWith($leftBase, [System.StringComparison]::OrdinalIgnoreCase)) {
            return $true
        }
    }

    return $false
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
    $filesList = $Files.Split(',') | ForEach-Object { Normalize-LockTarget $_ } | Where-Object { -not [string]::IsNullOrWhiteSpace($_) }
}

$requestedTargets = $filesList | Where-Object { -not (Is-CoordinationTarget $_) }
if ($requestedTargets.Count -gt 0) {
    $lockLines = Get-Content -Path $locksPath | Where-Object { $_ -match '^\|\s*TASK-[0-9]{3}\s*\|.*\|\s*active\s*\|' }
    foreach ($line in $lockLines) {
        $parts = $line.Split('|') | ForEach-Object { $_.Trim() }
        if ($parts.Count -lt 9) {
            continue
        }
        $existingTask = $parts[1]
        $existingAgent = $parts[2]
        $existingFilesRaw = $parts[5]
        if ([string]::IsNullOrWhiteSpace($existingFilesRaw)) {
            continue
        }
        $existingTargets = $existingFilesRaw.Split(',') | ForEach-Object { Normalize-LockTarget $_ } | Where-Object { -not (Is-CoordinationTarget $_) }
        foreach ($requestedTarget in $requestedTargets) {
            foreach ($existingTarget in $existingTargets) {
                if (Test-LockOverlap $requestedTarget $existingTarget) {
                    Fail "Lock overlap: '$requestedTarget' aktif gorev $existingTask ($existingAgent) icindeki '$existingTarget' ile cakisiyor."
                }
            }
        }
    }
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

$branchOutput = cmd /c "git checkout --quiet -b $Branch" 2>&1
if ($LASTEXITCODE -ne 0) {
    Fail "Branch acilamadi ($Branch): $($branchOutput -join ' ')"
}
Write-Host "[start-task] step-4 branch -> $Branch"
Write-Host "[start-task] started_at: $nowStamp"
Write-Host "[start-task] OK"
exit 0



