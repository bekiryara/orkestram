param(
    [string]$SourceCsv = "D:\stack-data\catalog-dataset\csv\_imports\armut\categories_candidates_wave1_mapped.csv",
    [string]$WorkspaceRoot = "D:\orkestram\docs\category-workspace"
)

$ErrorActionPreference = "Stop"

$exp = Join-Path $WorkspaceRoot "exports"
$analysis = Join-Path $WorkspaceRoot "03-analysis"
$treeDir = Join-Path $WorkspaceRoot "04-approved-tree"
$rulesDir = Join-Path $WorkspaceRoot "02-rules"
New-Item -ItemType Directory -Force -Path $exp, $analysis, $treeDir, $rulesDir | Out-Null

$rows = Import-Csv $SourceCsv

$topPools = @(
    "service-events-organization",
    "service-photo-video-media",
    "service-rental",
    "service-beauty-fashion",
    "service-print-sign-ad"
)

$includeRegex = "dugun|düğün|nisan|nişan|kina|kına|soz|söz|nikah|nikâh|davet|etkinlik|organizasyon|orkestra|muzik|müzik|dj|bando|fasil|fasıl|muzisyen|müzisyen|davul|zurna|keman|saksafon|gelin|damat|fotograf|fotoğraf|video|cekim|çekim|drone|klip|mekan|mekân|salon|kır bahcesi|kır bahçesi|otel|bahce|bahçe|catering|ikram|kokteyl|pasta|tatli|tatlı|ses sistemi|isik|ışık|sahne|led|hostes|animasyon|sunucu|konsept|susleme|süsleme|gelinlik|makyaj|kuafor|kuaför|davetiye|nikah sekeri|nikah şekeri|hediyelik|arac kiralama|araç kiralama|transfer|limuzin|vale"
$excludeRegex = "montaj|tamir|bakim|onarim|tesisat|dogalgaz|ariza|temizlik|nakliyat|kurye|ozel ders|özel ders|matematik|fizik|kimya|ingilizce|lgs|yks|ilkokul|ortaokul|biyometrik|pasaport|hastane|doktor|dis hekimi|diş hekimi|oto tamir|servis bakim|servis bakım|cam balkon tamiri|makine montaji|makine montajı"

$mainRules = @(
    @{ Main = "muzik-gruplari"; Regex = "orkestra|muzik|müzik|dj|bando|fasil|fasıl|muzisyen|müzisyen|davul|zurna|keman|saksafon|trio|canli muzik|canlı müzik" },
    @{ Main = "organizasyon-hizmetleri"; Regex = "organizasyon|etkinlik|dugun|düğün|nisan|nişan|kina|kına|soz|söz|nikah|nikâh|konsept|susleme|süsleme|hostes|sunucu|planlama" },
    @{ Main = "mekanlar"; Regex = "mekan|mekân|salon|kır bahcesi|kır bahçesi|otel|bahce|bahçe|davet alani|davet alanı" },
    @{ Main = "ikram-catering"; Regex = "catering|ikram|kokteyl|pasta|tatli|tatlı|yemek|menu|menü" },
    @{ Main = "foto-video"; Regex = "fotograf|fotoğraf|video|cekim|çekim|drone|klip|kameraman" },
    @{ Main = "teknik-produksiyon"; Regex = "ses sistemi|isik|ışık|sahne|led|mikser|hoparlor|hoparlör|jenerator|jeneratör" },
    @{ Main = "eglence-sov-sanatci"; Regex = "animasyon|palyaco|palyaço|sov|şov|dansci|dansçı|illuzyon|illüzyon|sanatci|sanatçı" },
    @{ Main = "davet-destek-hizmetleri"; Regex = "davetiye|nikah sekeri|nikah şekeri|hediyelik|vale" },
    @{ Main = "guzellik-hazirlik"; Regex = "gelinlik|makyaj|kuafor|kuaför|sac tasarim|saç tasarım|manikur|manikür|prova" },
    @{ Main = "ulasim-kiralama"; Regex = "arac kiralama|araç kiralama|transfer|limuzin|vip arac|vip araç" }
)

function Get-FallbackMain {
    param([string]$TopCategory)
    switch ($TopCategory) {
        "service-events-organization" { return "organizasyon-hizmetleri" }
        "service-photo-video-media" { return "foto-video" }
        "service-rental" { return "ulasim-kiralama" }
        "service-beauty-fashion" { return "guzellik-hazirlik" }
        "service-print-sign-ad" { return "davet-destek-hizmetleri" }
        default { return "manual-review" }
    }
}

$classified = foreach ($r in $rows) {
    $title = [string]$r.title_tr
    $slug = [string]$r.slug
    $top = [string]$r.top_category_slug
    $conf = [string]$r.top_mapping_confidence
    $txt = ($title + " " + $slug).ToLowerInvariant()

    $inScope = ($topPools -contains $top) -or ($txt -match $includeRegex)
    if (-not $inScope) { continue }

    if ($txt -match $excludeRegex) {
        [pscustomobject]@{
            slug = $slug; title_tr = $title; top_category_slug = $top; confidence = $conf;
            decision = "out"; main_category = ""; reason = "exclude_rule"
        }
        continue
    }

    $assigned = $null
    foreach ($rule in $mainRules) {
        if ($txt -match $rule.Regex) {
            $assigned = $rule.Main
            break
        }
    }

    if ([string]::IsNullOrWhiteSpace($assigned)) {
        $assigned = Get-FallbackMain -TopCategory $top
        $reason = if ($assigned -eq "manual-review") { "fallback_manual" } else { "fallback_top_category" }
    } else {
        $reason = "keyword_rule"
    }

    [pscustomobject]@{
        slug = $slug; title_tr = $title; top_category_slug = $top; confidence = $conf;
        decision = "candidate"; main_category = $assigned; reason = $reason
    }
}

$candidates = $classified | Where-Object { $_.decision -eq "candidate" }
$outRows = $classified | Where-Object { $_.decision -eq "out" }
$manual = $candidates | Where-Object { $_.main_category -eq "manual-review" }
$final = $candidates | Where-Object { $_.main_category -ne "manual-review" }

$finalSorted = $final | Sort-Object main_category, title_tr, slug -Unique
$manualSorted = $manual | Sort-Object top_category_slug, title_tr
$outSorted = $outRows | Sort-Object top_category_slug, title_tr

$finalCsv = Join-Path $exp "final_tree_candidates_v1.csv"
$manualCsv = Join-Path $exp "final_tree_manual_review_v1.csv"
$outCsv = Join-Path $exp "final_tree_out_scope_v1.csv"

$finalSorted | Export-Csv -NoTypeInformation -Encoding UTF8 $finalCsv
$manualSorted | Export-Csv -NoTypeInformation -Encoding UTF8 $manualCsv
$outSorted | Export-Csv -NoTypeInformation -Encoding UTF8 $outCsv

$treeTxt = Join-Path $treeDir "ARMUT_FINAL_TREE_v1.txt"
$reportTxt = Join-Path $analysis "ARMUT_FINAL_TREE_REPORT_v1.txt"

$treeLines = @()
$treeLines += "ARMUT -> SITEM NIHAYI AGAC (v1)"
$treeLines += "Kaynak: $SourceCsv"
$treeLines += "Tarih: $(Get-Date -Format 'yyyy-MM-dd HH:mm:ss')"
$treeLines += ""

$groups = $finalSorted | Group-Object main_category | Sort-Object Name
foreach ($g in $groups) {
    $treeLines += "# $($g.Name) ($($g.Count))"
    foreach ($row in ($g.Group | Sort-Object title_tr)) {
        $treeLines += "- $($row.title_tr) [$($row.slug)]"
    }
    $treeLines += ""
}
Set-Content -Path $treeTxt -Value $treeLines -Encoding UTF8

$mainCounts = $finalSorted | Group-Object main_category | Sort-Object Count -Descending
$topCounts = $finalSorted | Group-Object top_category_slug | Sort-Object Count -Descending

$rep = @()
$rep += "ARMUT FINAL TREE RAPORU (v1)"
$rep += "Tarih: $(Get-Date -Format 'yyyy-MM-dd HH:mm:ss')"
$rep += ""
$rep += "Toplam kaynak satir: $($rows.Count)"
$rep += "Aday (candidate): $($candidates.Count)"
$rep += "Final agaca giren: $($finalSorted.Count)"
$rep += "Manual review: $($manualSorted.Count)"
$rep += "Out-of-scope: $($outSorted.Count)"
$rep += ""
$rep += "Ana kategori dagilimi:"
foreach ($m in $mainCounts) { $rep += "- $($m.Name): $($m.Count)" }
$rep += ""
$rep += "Armut top_category dagilimi (final):"
foreach ($t in $topCounts) { $rep += "- $($t.Name): $($t.Count)" }
$rep += ""
$rep += "Dosyalar:"
$rep += "- $treeTxt"
$rep += "- $finalCsv"
$rep += "- $manualCsv"
$rep += "- $outCsv"
Set-Content -Path $reportTxt -Value $rep -Encoding UTF8

$rulesTxt = Join-Path $rulesDir "FINAL_TREE_RULES_v1.txt"
$ruleLines = @()
$ruleLines += "SOURCE=$SourceCsv"
$ruleLines += "TOP_POOLS=$($topPools -join ',')"
$ruleLines += "INCLUDE_REGEX=$includeRegex"
$ruleLines += "EXCLUDE_REGEX=$excludeRegex"
$ruleLines += ""
$ruleLines += "MAIN_RULE_ORDER:"
foreach ($mr in $mainRules) {
    $ruleLines += "- $($mr.Main) => $($mr.Regex)"
}
Set-Content -Path $rulesTxt -Value $ruleLines -Encoding UTF8

Write-Host "report=$reportTxt"
Get-Content $reportTxt
