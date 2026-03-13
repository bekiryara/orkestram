param(
  [string]$InputCsv = 'D:\orkestram\docs\category-workspace\exports\FINAL_TREE_CANONICAL_v1.csv',
  [string]$Workspace = 'D:\orkestram\docs\category-workspace'
)

$ErrorActionPreference = 'Stop'

$treeDir = Join-Path $Workspace '04-approved-tree'
$analysisDir = Join-Path $Workspace '03-analysis'
$expDir = Join-Path $Workspace 'exports'
New-Item -ItemType Directory -Force -Path $treeDir,$analysisDir,$expDir | Out-Null

$rows = Import-Csv $InputCsv

# Strict wedding/invitation scope filters
$dropRegex = 'biyometrik|emlak|airbnb|e\s?ticaret|sosyal medya|youtube|reklam|kurs|egitim|ders|dogum fotograf|bebek|cocuk|çocuk|sunnet|mezuniyet|aile fotograf|mezar|cicek bakimi|çiçek bakýmý|pasta cila|oto |fotokopi|fiber kablo|tv ses var|ürun |urun |facebook|instagram|google reklam|meta reklam'

# Main buckets with deterministic rule order
$bucketRules = @(
  @{ Main='dugun-mekanlari'; Regex='mekan|mekân|salon|kir dugunu|kýr düðünü|otel dugunu|tekne dugunu|nikah salonu|after party mekani|after party mekaný|soz nisan mekani|söz niþan mekaný' },
  @{ Main='dugun-organizasyon'; Regex='dugun organizasyon|düðün organizasyon|organizasyon|etkinlik|hostes|karsilama|karþýlama|konsept|susleme|süsleme|evlilik teklifi organizasyonu|kýna organizasyonu|kina organizasyonu|nisan organizasyonu|niþan organizasyonu' },
  @{ Main='muzik'; Regex='orkestra|muzik|müzik|dj|bando|fasil|fasýl|muzisyen|müzisyen|davul|zurna|trio|canli muzik|canlý müzik' },
  @{ Main='dugun-fotografcilari-video'; Regex='dugun fotograf|düðün fotoðraf|dugun video|düðün video|dis cekim|dýþ çekim|drone cekimi|drone çekimi|kameraman|klip cekimi|klip çekimi|evlilik teklifi fotograf|evlilik teklifi fotoðraf|etkinlik fotograf' },
  @{ Main='gelin-saci-ve-makyaji'; Regex='gelin sac|gelin saç|makyaj|kuafor|kuaför|protez tirnak|týrnak' },
  @{ Main='gelinlik'; Regex='gelinlik|duvak|gelin ta[cç]' },
  @{ Main='catering-hizmetleri'; Regex='catering|ikram|kokteyl|dugun pastasi|düðün pastasý|nisan pastasi|niþan pastasý|soz pastasi|söz pastasý|davet catering|dugun catering|düðün catering' },
  @{ Main='gelin-arabasi'; Regex='gelin arabasi|gelin arabasý|limuzin|vip arac|vip araç|transfer' },
  @{ Main='dugun-davetiyesi'; Regex='davetiye' },
  @{ Main='nikah-sekeri-ve-hediyelik'; Regex='nikah sekeri|nikah þekeri|hediyelik|mevlut hediyelik|mevlüt hediyelik|magnet' },
  @{ Main='cicekciler'; Regex='cicekci|çiçekçi|gelin buketi|gelin çiçeði|cicek gonderme|çiçek gönderme|cicek tasarimi|çiçek tasarýmý' },
  @{ Main='kina-ve-bekarliga-veda'; Regex='kina|kýna|bekarliga veda|bekarlýða veda' },
  @{ Main='soz-ve-nisan'; Regex='soz|söz|nisan|niþan' }
)

$keep = New-Object System.Collections.Generic.List[object]
$drop = New-Object System.Collections.Generic.List[object]

foreach($r in $rows){
  $title = [string]$r.title_tr
  $slug = [string]$r.slug
  $txt = ($title + ' ' + $slug).ToLowerInvariant()

  if($txt -match $dropRegex){
    $drop.Add([pscustomobject]@{title_tr=$title;slug=$slug;from_main=$r.main_category;reason='strict_drop_regex'}) | Out-Null
    continue
  }

  $assigned = $null
  foreach($br in $bucketRules){
    if($txt -match $br.Regex){ $assigned = $br.Main; break }
  }

  if([string]::IsNullOrWhiteSpace($assigned)){
    $drop.Add([pscustomobject]@{title_tr=$title;slug=$slug;from_main=$r.main_category;reason='no_strict_bucket_match'}) | Out-Null
    continue
  }

  $keep.Add([pscustomobject]@{main_category=$assigned;title_tr=$title;slug=$slug;top_category_slug=$r.top_category_slug;confidence=$r.confidence}) | Out-Null
}

$keepFinal = $keep | Sort-Object main_category,title_tr,slug -Unique
$dropFinal = $drop | Sort-Object reason,title_tr,slug -Unique

$keepCsv = Join-Path $expDir 'FINAL_TREE_STRICT_v2_KEEP.csv'
$dropCsv = Join-Path $expDir 'FINAL_TREE_STRICT_v2_DROP.csv'
$treeTxt = Join-Path $treeDir 'FINAL_TREE_STRICT_v2.txt'
$keepTxt = Join-Path $treeDir 'CLEAN_KEEP_v2.txt'
$dropTxt = Join-Path $treeDir 'CLEAN_DROP_v2.txt'
$reportTxt = Join-Path $analysisDir 'FINAL_TREE_STRICT_v2_REPORT.txt'

$keepFinal | Export-Csv -NoTypeInformation -Encoding UTF8 $keepCsv
$dropFinal | Export-Csv -NoTypeInformation -Encoding UTF8 $dropCsv

$tree = @()
$tree += 'FINAL TREE STRICT v2'
$tree += 'Tarih: ' + (Get-Date -Format 'yyyy-MM-dd HH:mm:ss')
$tree += 'Toplam Keep: ' + $keepFinal.Count
$tree += ''
$groups = $keepFinal | Group-Object main_category | Sort-Object Name
$ix = 1
foreach($g in $groups){
  $tree += ("{0}. {1} ({2})" -f $ix,$g.Name,$g.Count)
  foreach($row in ($g.Group | Sort-Object title_tr)){
    $tree += ("  - {0} [slug={1}; top={2}; conf={3}]" -f $row.title_tr,$row.slug,$row.top_category_slug,$row.confidence)
  }
  $tree += ''
  $ix++
}
Set-Content -Path $treeTxt -Value $tree -Encoding UTF8

$keepLines = @('CLEAN KEEP v2','Toplam: ' + $keepFinal.Count,'')
foreach($g in ($keepFinal | Group-Object main_category | Sort-Object Name)){
  $keepLines += ('# ' + $g.Name + ' (' + $g.Count + ')')
  foreach($row in ($g.Group | Sort-Object title_tr)){$keepLines += ('- ' + $row.title_tr)}
  $keepLines += ''
}
Set-Content -Path $keepTxt -Value $keepLines -Encoding UTF8

$dropLines = @('CLEAN DROP v2','Toplam: ' + $dropFinal.Count,'')
foreach($g in ($dropFinal | Group-Object reason | Sort-Object Name)){
  $dropLines += ('# reason=' + $g.Name + ' (' + $g.Count + ')')
  foreach($row in ($g.Group | Sort-Object title_tr)){$dropLines += ('- ' + $row.title_tr + ' [' + $row.slug + ']')}
  $dropLines += ''
}
Set-Content -Path $dropTxt -Value $dropLines -Encoding UTF8

$rep = @()
$rep += 'FINAL TREE STRICT v2 REPORT'
$rep += 'Toplam Keep: ' + $keepFinal.Count
$rep += 'Toplam Drop: ' + $dropFinal.Count
$rep += ''
$rep += 'Ana kategori dagilimi:'
foreach($g in ($keepFinal | Group-Object main_category | Sort-Object Count -Descending)){$rep += ('- ' + $g.Name + ': ' + $g.Count)}
$rep += ''
$rep += 'Dosyalar:'
$rep += '- ' + $treeTxt
$rep += '- ' + $keepTxt
$rep += '- ' + $dropTxt
$rep += '- ' + $keepCsv
$rep += '- ' + $dropCsv
Set-Content -Path $reportTxt -Value $rep -Encoding UTF8

# Easy-open copies at workspace root
Copy-Item $treeTxt (Join-Path $Workspace 'FINAL_TREE_STRICT_v2.txt') -Force
Copy-Item $keepTxt (Join-Path $Workspace 'CLEAN_KEEP_v2.txt') -Force
Copy-Item $dropTxt (Join-Path $Workspace 'CLEAN_DROP_v2.txt') -Force
Copy-Item $reportTxt (Join-Path $Workspace 'FINAL_TREE_STRICT_v2_REPORT.txt') -Force

Get-Content $reportTxt
