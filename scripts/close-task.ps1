param(
    [Parameter(Mandatory = $true)]
    [string]$TaskId,

    [string]$Agent = "codex",

    [Parameter(Mandatory = $true)]
    [string]$ClosureNote,

    [Parameter(Mandatory = $true)]
    [string]$WorklogTitle,

    [Parameter(Mandatory = $true)]
    [string[]]$WorklogSummary,

    [Parameter(Mandatory = $true)]
    [string[]]$Files,

    [Parameter(Mandatory = $true)]
    [string[]]$Commands,

    [ValidateSet("PASS", "FAIL")]
    [string]$Result = "PASS",

    [string[]]$WorklogNote = @(),

    [switch]$BranchPushed
)

$ErrorActionPreference = "Stop"

function Fail([string]$Message) {
    Write-Host "[close-task] FAIL: $Message"
    exit 1
}

function Normalize-Line([string]$Line) {
    return ($Line -replace '\|', '/').Trim()
}

function Build-NumberedList([string[]]$Items) {
    $list = [System.Collections.Generic.List[string]]::new()
    for ($i = 0; $i -lt $Items.Count; $i++) {
        [void]$list.Add("$($i + 1). $($Items[$i])")
    }
    return $list
}

function Replace-SectionLines {
    param(
        [System.Collections.Generic.List[string]]$Lines,
        [string]$StartHeader,
        [string]$EndHeader,
        [string[]]$Items
    )

    $start = $Lines.IndexOf($StartHeader)
    $end = $Lines.IndexOf($EndHeader)
    if ($start -lt 0 -or $end -lt 0 -or $end -le $start) {
        Fail "NEXT_TASK bolum basliklari beklenen formatta degil: $StartHeader -> $EndHeader"
    }

    $Lines.RemoveRange($start + 1, $end - $start - 1)
    $replacement = @(Build-NumberedList $Items)
    for ($idx = 0; $idx -lt $replacement.Count; $idx++) {
        $Lines.Insert($start + 1 + $idx, [string]$replacement[$idx])
    }
}

function Get-ActiveTaskEntries([System.Collections.Generic.List[string]]$Lines) {
    $entries = @()
    $activeStart = $Lines.IndexOf('## Aktif Gorevler (Merkezi Koordinasyon)')
    $coordinatorStart = $Lines.IndexOf('## Son Koordinator Kapanisi')
    if ($activeStart -lt 0 -or $coordinatorStart -le $activeStart) {
        Fail 'NEXT_TASK aktif gorevler bolumu beklenen formatta degil.'
    }

    for ($i = $activeStart + 1; $i -lt $coordinatorStart; $i++) {
        $line = $Lines[$i].Trim()
        if ([string]::IsNullOrWhiteSpace($line)) {
            continue
        }
        if ($line -match '^\d+\.\s+`YOK` - ') {
            continue
        }
        $entries += ($line -replace '^\d+\.\s+', '')
    }

    return $entries
}

if ($TaskId -notmatch '^TASK-[0-9]{3}$') {
    Fail "TaskId formati gecersiz. Beklenen: TASK-XXX"
}

$taskFile = "docs/tasks/$TaskId.md"
$locksPath = "docs/TASK_LOCKS.md"
$nextTaskPath = "docs/NEXT_TASK.md"
$worklogPath = "docs/WORKLOG.md"

foreach ($path in @($taskFile, $locksPath, $nextTaskPath, $worklogPath)) {
    if (-not (Test-Path -LiteralPath $path)) {
        Fail "Gerekli dosya bulunamadi: $path"
    }
}

$nowStamp = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
$nowShort = Get-Date -Format "yyyy-MM-dd HH:mm"
$taskSlug = $TaskId.ToLower()
$branch = "agent/$Agent/$taskSlug"
$cleanNote = Normalize-Line $ClosureNote

$taskRaw = Get-Content -Path $taskFile -Raw
$taskRaw = [regex]::Replace($taskRaw, 'Durum:\s*`[^`]+`', 'Durum: `DONE`', 1)
$taskRaw = $taskRaw.Replace('- [ ] Task kartindaki checklistler gercek sonuca gore guncellendi', '- [x] Task kartindaki checklistler gercek sonuca gore guncellendi')
$taskRaw = $taskRaw.Replace('- [ ] `docs/WORKLOG.md` guncellendi', '- [x] `docs/WORKLOG.md` guncellendi')
$taskRaw = $taskRaw.Replace('- [ ] `docs/TASK_LOCKS.md` kaydi `closed` yapildi', '- [x] `docs/TASK_LOCKS.md` kaydi `closed` yapildi')
$taskRaw = $taskRaw.Replace('- [ ] `docs/NEXT_TASK.md` panosu guncellendi', '- [x] `docs/NEXT_TASK.md` panosu guncellendi')
if ($BranchPushed) {
    $taskRaw = $taskRaw.Replace('- [ ] Branch pushlandi', '- [x] Branch pushlandi')
}
Set-Content -Encoding utf8 -Path $taskFile -Value $taskRaw
Write-Host "[close-task] step-1 task file -> $taskFile"

$locksLines = [System.Collections.Generic.List[string]](Get-Content -Path $locksPath)
$lockIndex = -1
$lockParts = $null
for ($i = 0; $i -lt $locksLines.Count; $i++) {
    $line = $locksLines[$i]
    if ($line -match "^\|\s*$TaskId\s*\|\s*$Agent\s*\|") {
        $parts = $line.Split('|') | ForEach-Object { $_.Trim() }
        if ($parts.Count -ge 9) {
            $lockIndex = $i
            $lockParts = $parts
            break
        }
    }
}
if ($lockIndex -lt 0) {
    Fail "TASK_LOCKS icinde $TaskId / $Agent satiri bulunamadi."
}
if ($lockParts[4] -ne 'active') {
    Fail "$TaskId aktif degil. Mevcut durum: $($lockParts[4])"
}
$filesValue = $lockParts[5]
$startedAt = $lockParts[6]
$locksLines[$lockIndex] = "| $TaskId | $Agent | $branch | closed | $filesValue | $startedAt | $nowStamp | $cleanNote |"
Set-Content -Encoding utf8 -Path $locksPath -Value $locksLines
Write-Host "[close-task] step-2 lock row -> $locksPath"

$lineList = [System.Collections.Generic.List[string]]::new()
foreach ($entry in (Get-Content -Path $nextTaskPath)) {
    [void]$lineList.Add([string]$entry)
}

$activeStart = $lineList.IndexOf('## Aktif Gorevler (Merkezi Koordinasyon)')
$coordinatorStart = $lineList.IndexOf('## Son Koordinator Kapanisi')
if ($activeStart -lt 0 -or $coordinatorStart -le $activeStart) {
    Fail 'NEXT_TASK aktif gorevler bolumu beklenen formatta degil.'
}

$remainingActiveItems = @(Get-ActiveTaskEntries -Lines $lineList | Where-Object { $_ -notmatch "^`$TaskId` - " })
for ($i = $coordinatorStart - 1; $i -gt $activeStart; $i--) {
    [void]$lineList.RemoveAt($i)
}
if ($remainingActiveItems.Count -eq 0) {
    $lineList.Insert($activeStart + 1, '1. `YOK` - aktif koordinasyon gorevi bulunmuyor')
}
else {
    $replacement = Build-NumberedList $remainingActiveItems
    for ($idx = 0; $idx -lt $replacement.Count; $idx++) {
        $lineList.Insert($activeStart + 1 + $idx, [string]$replacement[$idx])
    }
}

for ($i = 0; $i -lt $lineList.Count; $i++) {
    if ($lineList[$i] -match '^Durum:\s+`') {
        if ($remainingActiveItems.Count -eq 0) {
            $lineList[$i] = 'Durum: `READY`  '
        }
        else {
            $lineList[$i] = 'Durum: `ACTIVE`  '
        }
        break
    }
}

$existingCoordinator = @()
$existingClosing = @()
$coordStart = $lineList.IndexOf('## Son Koordinator Kapanisi')
$closeStart = $lineList.IndexOf('## Son Kapanis')
$ruleStart = $lineList.IndexOf('## Kapanis Kurali (Zorunlu)')
if ($coordStart -lt 0 -or $closeStart -lt 0 -or $ruleStart -lt 0) {
    Fail 'NEXT_TASK kapanis bolumleri beklenen formatta degil.'
}
for ($i = $coordStart + 1; $i -lt $closeStart; $i++) {
    if (-not [string]::IsNullOrWhiteSpace($lineList[$i])) {
        $existingCoordinator += $lineList[$i] -replace '^\d+\.\s+', ''
    }
}
for ($i = $closeStart + 1; $i -lt $ruleStart; $i++) {
    if (-not [string]::IsNullOrWhiteSpace($lineList[$i])) {
        $existingClosing += $lineList[$i] -replace '^\d+\.\s+', ''
    }
}
$existingCoordinator = $existingCoordinator | Where-Object { $_ -notmatch "^`$TaskId` - " }
$existingClosing = $existingClosing | Where-Object { $_ -notmatch "^`$TaskId` - " }
$coordinatorItems = @("`$TaskId` - $cleanNote") + ($existingCoordinator | Select-Object -First 2)
$closingItems = @("`$TaskId` - $cleanNote") + ($existingClosing | Select-Object -First 2)
Replace-SectionLines -Lines $lineList -StartHeader '## Son Koordinator Kapanisi' -EndHeader '## Son Kapanis' -Items $coordinatorItems
Replace-SectionLines -Lines $lineList -StartHeader '## Son Kapanis' -EndHeader '## Kapanis Kurali (Zorunlu)' -Items $closingItems
Set-Content -Encoding utf8 -Path $nextTaskPath -Value $lineList
Write-Host "[close-task] step-3 next task -> $nextTaskPath"

$summaryLines = $WorklogSummary | ForEach-Object { "  - ``$([string](Normalize-Line $_))``" }
$fileLines = $Files | ForEach-Object { "  - ``$([string](Normalize-Line $_))``" }
$commandLines = $Commands | ForEach-Object { "  - ``$([string](Normalize-Line $_))``" }
$noteLines = if ($WorklogNote.Count -gt 0) { $WorklogNote | ForEach-Object { "  - ``$([string](Normalize-Line $_))``" } } else { @('  - `n/a`') }
$entryLines = @(
    '',
    '---',
    '',
    "### [$nowShort] $WorklogTitle",
    "- Sorumlu: ``$Agent``",
    '- Is Ozeti:'
) + $summaryLines + @(
    '- Degisen Dosyalar:'
) + $fileLines + @(
    '- Calistirilan Komutlar:'
) + $commandLines + @(
    '- Sonuc:',
    "  - ``$Result``",
    '- Not:'
) + $noteLines
Add-Content -Path $worklogPath -Value ($entryLines -join "`r`n")
Write-Host "[close-task] step-4 worklog -> $worklogPath"

Write-Host "[close-task] OK"
exit 0
