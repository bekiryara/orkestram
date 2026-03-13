param(
  [string]$MergedCsv = 'D:\orkestram\docs\category-workspace\exports\FINAL_TREE_OMURGA_V4_MERGED.csv',
  [string]$MustCsv = 'D:\orkestram\docs\category-workspace\exports\must_have_categories_v1.csv',
  [string]$Workspace = 'D:\orkestram\docs\category-workspace'
)

$ErrorActionPreference = 'Stop'

$outCsv = Join-Path $Workspace 'exports\FINAL_TREE_V5_CLEAN.csv'
$outTxt = Join-Path $Workspace 'FINAL_TREE_V5_CLEAN.txt'
$outTree = Join-Path $Workspace 'FINAL_TREE_V5_TREEVIEW.txt'
$outRpt = Join-Path $Workspace 'FINAL_TREE_V5_REPORT.txt'

$rows = Import-Csv $MergedCsv
$must = Import-Csv $MustCsv

function Map-Main([string]$main){
  switch($main){
    'dugun-organizasyon' { 'Etkinlik Organizasyon' }
    'organizasyon-hizmetleri' { 'Etkinlik Organizasyon' }
    'soz-ve-nisan' { 'Etkinlik Organizasyon' }
    'kina-ve-bekarliga-veda' { 'Etkinlik Organizasyon' }

    'muzik' { 'Muzik Gruplari' }
    'muzik-gruplari' { 'Muzik Gruplari' }

    'dugun-mekanlari' { 'Mekanlar' }
    'mekanlar' { 'Mekanlar' }

    'dugun-fotografcilari-video' { 'Fotograf ve Video' }
    'foto-video' { 'Fotograf ve Video' }

    'catering-hizmetleri' { 'Ikram ve Catering' }
    'ikram-catering' { 'Ikram ve Catering' }

    'gelin-saci-ve-makyaji' { 'Guzellik ve Hazirlik' }
    'gelinlik' { 'Guzellik ve Hazirlik' }

    'dugun-davetiyesi' { 'Davetiye Hediyelik ve Cicek' }
    'nikah-sekeri-ve-hediyelik' { 'Davetiye Hediyelik ve Cicek' }
    'davet-destek-hizmetleri' { 'Davetiye Hediyelik ve Cicek' }
    'cicekciler' { 'Davetiye Hediyelik ve Cicek' }

    'gelin-arabasi' { 'Gelin Arabasi ve Transfer' }
    'ulasim-kiralama' { 'Gelin Arabasi ve Transfer' }

    'teknik-produksiyon' { 'Sahne ve Eglence' }
    'eglence-sov-sanatci' { 'Sahne ve Eglence' }
    default { '' }
  }
}

# Strong cleanup filters for irrelevant/noisy categories
$dropRegex = 'camekan|mekanik|makina|pet|kedi|kopek|köpek|fuar|iftar|doner|motosiklet|karavan|tekne transfer|havaalani|havaalaný|sehirler arasi|þehirler arasý|erkek kuafor|erkek kuaför|dogum odasi|doðum odasý|dogum gunu|doðum günü|plastik makyaj|yuz tasi|yüz taþý|porselen makyaj|susleme organizasyon|zincir balon|ucan balon|balon susleme|balon süsleme|konsept fotograf|konsept fotoðraf'

$clean = New-Object System.Collections.Generic.List[object]
$dropped = New-Object System.Collections.Generic.List[object]

foreach($r in $rows){
  $name = [string]$r.name
  $slug = [string]$r.slug
  $rawMain = [string]$r.main_category
  $txt = ($name + ' ' + $slug).ToLowerInvariant()

  $mapped = Map-Main $rawMain
  if([string]::IsNullOrWhiteSpace($mapped)){
    $dropped.Add([pscustomobject]@{name=$name;slug=$slug;reason='unmapped_main';raw_main=$rawMain}) | Out-Null
    continue
  }

  if($txt -match $dropRegex){
    $dropped.Add([pscustomobject]@{name=$name;slug=$slug;reason='clean_drop_regex';raw_main=$rawMain}) | Out-Null
    continue
  }

  $clean.Add([pscustomobject]@{
    main_category=$mapped
    name=$name
    slug=$slug
    source='merged'
  }) | Out-Null
}

# Must-have guarantee
foreach($m in $must){
  $mapped = Map-Main ([string]$m.main_category)
  if([string]::IsNullOrWhiteSpace($mapped)){
    # fallback map for must entries already in target Turkish structure
    switch([string]$m.main_category){
      'muzik-gruplari' { $mapped='Muzik Gruplari' }
      'organizasyon-hizmetleri' { $mapped='Etkinlik Organizasyon' }
      'mekanlar' { $mapped='Mekanlar' }
      'foto-video' { $mapped='Fotograf ve Video' }
      'ikram-catering' { $mapped='Ikram ve Catering' }
      'davet-destek-hizmetleri' { $mapped='Davetiye Hediyelik ve Cicek' }
      default { $mapped='Etkinlik Organizasyon' }
    }
  }

  $exists = $clean | Where-Object { $_.slug -eq [string]$m.slug } | Select-Object -First 1
  if(-not $exists){
    $clean.Add([pscustomobject]@{
      main_category=$mapped
      name=[string]$m.name
      slug=[string]$m.slug
      source='must_have'
    }) | Out-Null
  }
}

$final = $clean | Sort-Object main_category,name,slug -Unique
$final | Export-Csv -NoTypeInformation -Encoding UTF8 $outCsv

# List format
$list=@()
$list += 'FINAL TREE V5 CLEAN'
$list += 'Tarih: ' + (Get-Date -Format 'yyyy-MM-dd HH:mm:ss')
$list += 'Toplam Alt Kategori: ' + $final.Count
$list += ''
$mainGroups=$final | Group-Object main_category | Sort-Object Name
$i=1
foreach($g in $mainGroups){
  $list += ("{0}. {1} ({2})" -f $i,$g.Name,$g.Count)
  foreach($row in ($g.Group | Sort-Object name)){
    $list += ("  - {0} [{1}]" -f $row.name,$row.slug)
  }
  $list += ''
  $i++
}
Set-Content -Path $outTxt -Value $list -Encoding UTF8

# Tree view format
$tree=@()
$tree += 'FINAL TREE V5 CLEAN - TREE VIEW'
$tree += 'Toplam: ' + $final.Count
$tree += ''
for($a=0;$a -lt $mainGroups.Count;$a++){
  $g=$mainGroups[$a]
  $isLastMain=($a -eq $mainGroups.Count-1)
  $mainPrefix = if($isLastMain){'L¦¦ '} else {'+¦¦ '}
  $tree += ($mainPrefix + $g.Name + ' (' + $g.Count + ')')
  $subs = $g.Group | Sort-Object name
  for($b=0;$b -lt $subs.Count;$b++){
    $s=$subs[$b]
    $isLastSub=($b -eq $subs.Count-1)
    $indent = if($isLastMain){'    '} else {'-   '}
    $subPrefix = if($isLastSub){'L¦¦ '} else {'+¦¦ '}
    $tree += ($indent + $subPrefix + $s.name + ' [' + $s.slug + ']')
  }
}
Set-Content -Path $outTree -Value $tree -Encoding UTF8

$rep=@()
$rep += 'FINAL TREE V5 CLEAN REPORT'
$rep += 'Keep: ' + $final.Count
$rep += 'Drop: ' + ($dropped.Count)
$rep += ''
$rep += 'Ana kategori dagilimi:'
foreach($g in ($final | Group-Object main_category | Sort-Object Count -Descending)){
  $rep += ('- ' + $g.Name + ': ' + $g.Count)
}
$rep += ''
$rep += 'Kaynak dagilimi:'
foreach($g in ($final | Group-Object source | Sort-Object Name)){
  $rep += ('- ' + $g.Name + ': ' + $g.Count)
}
$rep += ''
$rep += 'Dosyalar:'
$rep += '- ' + $outTxt
$rep += '- ' + $outTree
$rep += '- ' + $outCsv
Set-Content -Path $outRpt -Value $rep -Encoding UTF8

Get-Content $outRpt
