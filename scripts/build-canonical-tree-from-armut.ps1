param(
  [string]$ArmutCsv = 'D:\stack-data\catalog-dataset\csv\_imports\armut\categories_candidates_wave1_mapped.csv',
  [string]$OutTree = 'D:\orkestram\docs\category-workspace\04-approved-tree\FINAL_TREE_CANONICAL_v1.txt',
  [string]$OutCsv = 'D:\orkestram\docs\category-workspace\exports\FINAL_TREE_CANONICAL_v1.csv',
  [string]$OutReport = 'D:\orkestram\docs\category-workspace\03-analysis\FINAL_TREE_CANONICAL_REPORT_v1.txt'
)

$ErrorActionPreference='Stop'
$rows = Import-Csv $ArmutCsv

# DUGUN.COM main categories as canonical roots
$canonical = @(
  @{main='dugun-mekanlari'; regex='mekan|mekân|salon|kir dugunu|kýr düðünü|balo|davet salonu|otel dugunu|tekne dugunu|nikah salonu|after party mekani|after party mekaný'; priority=1},
  @{main='gelinlik'; regex='gelinlik|gelin elbise|gelin ta[cç]|duvak'; priority=2},
  @{main='dugun-organizasyon'; regex='dugun organizasyon|düðün organizasyon|organizasyon|etkinlik planlama|hostes|karsilama|karþýlama|konsept|susleme|süsleme'; priority=3},
  @{main='dugun-fotografcilari-video'; regex='fotograf|fotoðraf|video|klip|drone cekimi|drone çekimi|kameraman|tanitim filmi|tanýtým filmi|dis cekim|dýþ çekim'; priority=4},
  @{main='gelin-saci-ve-makyaji'; regex='gelin sac|gelin saç|makyaj|kuafor|kuaför|protez tirnak|týrnak'; priority=5},
  @{main='muzik'; regex='orkestra|muzik|müzik|dj|bando|fasil|fasýl|muzisyen|müzisyen|davul|zurna|trio|canli muzik|canlý müzik'; priority=6},
  @{main='kina-ve-bekarliga-veda'; regex='kina|kýna|bekarliga veda|bekarlýða veda|kýna gecesi'; priority=7},
  @{main='soz-ve-nisan'; regex='soz|söz|nisan|niþan|isteme|söz niþan'; priority=8},
  @{main='catering-hizmetleri'; regex='catering|ikram|kokteyl|menu|menü|yemek servisi|dugun pastasi|düðün pastasý|pasta'; priority=9},
  @{main='gelin-arabasi'; regex='gelin arabasi|gelin arabasý|limuzin|vip arac|vip araç|arac kiralama|araç kiralama|transfer'; priority=10},
  @{main='dugun-davetiyesi'; regex='davetiye'; priority=11},
  @{main='nikah-sekeri-ve-hediyelik'; regex='nikah sekeri|nikah þekeri|hediyelik|magnet|anah[t]?arlik'; priority=12},
  @{main='cicekciler'; regex='cicek|çiçek|gelin buketi|buket'; priority=13}
)

# Out-of-scope for this marketplace
$exclude='tuvalet|tesisat|dogalgaz|tamir|onarim|montaj|ariza|temizlik|nakliyat|kurye|ozel ders|özel ders|matematik|fizik|kimya|lgs|yks|hastane|doktor|dis hekimi|diþ hekimi|oto tamir|pcb|eczane tabela|dukkan tabelasi|dükkan tabelasý|billboard|matbaa|ozalit|vinil germe|metal lazer kesim|cnc lazer kesim|lazer kesim balkon'

# Candidate gate: keep likely event/wedding ecosystem rows first
$gate='dugun|düðün|nikah|nikâh|nisan|niþan|soz|söz|kina|kýna|evlilik teklifi|gelin|damat|orkestra|muzik|müzik|dj|bando|fasil|fasýl|muzisyen|müzisyen|mekan|mekân|salon|fotograf|fotoðraf|video|drone|kamera|kuafor|kuaför|makyaj|gelinlik|davetiye|nikah sekeri|nikah þekeri|hediyelik|catering|ikram|pasta|cicek|çiçek|gelin arabasi|gelin arabasý|limuzin|transfer|hostes|konsept|susleme|süsleme|bekarliga veda|bekarlýða veda'

$selected = foreach($r in $rows){
  $title=[string]$r.title_tr; $slug=[string]$r.slug; $txt=($title+' '+$slug).ToLowerInvariant();
  if($txt -match $exclude){ continue }
  if($txt -notmatch $gate){ continue }

  $matched=$null
  foreach($c in ($canonical | Sort-Object priority)){
    if($txt -match $c.regex){ $matched=$c.main; break }
  }
  if([string]::IsNullOrWhiteSpace($matched)){ continue }

  [pscustomobject]@{
    main_category=$matched
    title_tr=$title
    slug=$slug
    top_category_slug=$r.top_category_slug
    confidence=$r.top_mapping_confidence
  }
}

$final = $selected | Sort-Object main_category,title_tr,slug -Unique
$final | Export-Csv -NoTypeInformation -Encoding UTF8 $OutCsv

$lines=@()
$lines += 'FINAL TREE CANONICAL v1 (DUGUN.COM + ARMUT)'
$lines += 'Tarih: ' + (Get-Date -Format 'yyyy-MM-dd HH:mm:ss')
$lines += 'Kaynak Armut: ' + $ArmutCsv
$lines += 'Model: DUGUN.COM ana omurga + ARMUT alt kategori esleme'
$lines += 'Toplam Alt Kategori: ' + $final.Count
$lines += ''

$groups=$final | Group-Object main_category | Sort-Object Name
$idx=1
foreach($g in $groups){
  $lines += ("{0}. {1} ({2})" -f $idx,$g.Name,$g.Count)
  foreach($row in ($g.Group | Sort-Object title_tr)){
    $lines += ("  - {0} [slug={1}; top={2}; conf={3}]" -f $row.title_tr,$row.slug,$row.top_category_slug,$row.confidence)
  }
  $lines += ''
  $idx++
}
Set-Content -Path $OutTree -Value $lines -Encoding UTF8

$rep=@()
$rep += 'FINAL TREE CANONICAL REPORT v1'
$rep += 'Toplam: ' + $final.Count
$rep += ''
$rep += 'Ana kategori dagilimi:'
foreach($g in ($final | Group-Object main_category | Sort-Object Count -Descending)){
  $rep += ('- ' + $g.Name + ': ' + $g.Count)
}
$rep += ''
$rep += 'Dosyalar:'
$rep += '- ' + $OutTree
$rep += '- ' + $OutCsv
Set-Content -Path $OutReport -Value $rep -Encoding UTF8

Write-Host "tree=$OutTree"
Get-Content $OutReport
