param(
    [switch]$Detailed
)

$ErrorActionPreference = "Stop"

$slots = @(
    @{ Agent = 'codex';   RepoPath = '/home/bekir/orkestram-k'; LockAgent = 'codex' },
    @{ Agent = 'codex-a'; RepoPath = '/home/bekir/orkestram-a'; LockAgent = 'codex-a' },
    @{ Agent = 'codex-b'; RepoPath = '/home/bekir/orkestram-b'; LockAgent = 'codex-b' },
    @{ Agent = 'codex-c'; RepoPath = '/home/bekir/orkestram-c'; LockAgent = 'codex-c' }
)

$locksRaw = Get-Content -Raw 'docs/TASK_LOCKS.md'

function Get-ActiveTask([string]$agentName) {
    $pattern = "(?m)^\|\s*(TASK-\d{3})\s*\|\s*$([regex]::Escape($agentName))\s*\|\s*([^|]+)\|\s*active\s*\|"
    $match = [regex]::Match($locksRaw, $pattern)
    if ($match.Success) {
        return $match.Groups[1].Value
    }
    return 'yok'
}

function Get-RepoInfo([string]$repoPath) {
    $commandLines = @(
        "cd '$repoPath' || exit 1",
        'branch=$(git branch --show-current 2>/dev/null)',
        'upstream=$(git rev-parse --abbrev-ref --symbolic-full-name @{u} 2>/dev/null || true)',
        'count=$(git status --short | wc -l)',
        'printf ''BRANCH=%s\n'' "$branch"',
        'printf ''UPSTREAM=%s\n'' "$upstream"',
        'printf ''STATUS_COUNT=%s\n'' "$count"',
        'printf ''STATUS_HEAD<<EOF\n''',
        'git status --short | sed -n ''1,8p''',
        'printf ''EOF\n''' 
    )
    $command = [string]::Join("`n", $commandLines)
    return wsl -e bash -lc $command
}

$rows = foreach ($slot in $slots) {
    $raw = Get-RepoInfo $slot.RepoPath
    $branch = (($raw | Select-String '^BRANCH=').ToString() -replace '^BRANCH=', '').Trim()
    $upstream = (($raw | Select-String '^UPSTREAM=').ToString() -replace '^UPSTREAM=', '').Trim()
    $statusCount = [int]((($raw | Select-String '^STATUS_COUNT=').ToString() -replace '^STATUS_COUNT=', '').Trim())
    $preview = @()
    $capture = $false
    foreach ($line in $raw) {
        if ($line -eq 'STATUS_HEAD<<EOF') { $capture = $true; continue }
        if ($line -eq 'EOF') { $capture = $false; continue }
        if ($capture) { $preview += $line }
    }

    $activeTask = Get-ActiveTask $slot.LockAgent
    $stale = if (($activeTask -eq 'yok' -and $statusCount -gt 0) -or ($branch -eq 'main' -and $statusCount -gt 0)) { 'yes' } else { 'no' }

    [pscustomobject]@{
        Agent = $slot.Agent
        RepoPath = $slot.RepoPath
        Branch = $branch
        ActiveTask = $activeTask
        Upstream = if ([string]::IsNullOrWhiteSpace($upstream)) { 'yok' } else { $upstream }
        StatusCount = $statusCount
        StaleCandidate = $stale
        StatusHead = ($preview -join '; ')
    }
}

if ($Detailed) {
    $rows | Format-List
} else {
    $rows | Select-Object Agent, Branch, ActiveTask, Upstream, StatusCount, StaleCandidate | Format-Table -AutoSize
}
