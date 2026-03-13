param(
  [string]$SourceCsv = 'D:\stack-data\catalog-dataset\csv\_imports\armut\categories_candidates_wave1_mapped.csv',
  [string]$Workspace = 'D:\orkestram\docs\category-workspace'
)

$ErrorActionPreference='Stop'
$rows = Import-Csv $SourceCsv

$outTree = Join-Path $Workspace 'FINAL_TREE_OMURGA_V3.txt'
$outReport = Join-Path $Workspace 'FINAL_TREE_OMURGA_V3_REPORT.txt'
$outKeepCsv = Join-Path $Workspace 'exports\FINAL_TREE_OMURGA_V3_KEEP.csv'
$outDropCsv = Join-Path $Workspace 'exports\FINAL_TREE_OMURGA_V3_DROP.csv'
New-Item -ItemType Directory -Force -Path (Join-Path $Workspace 'exports') | Out-Null

# Senin ana omurgan (zorunlu)
$roots = @(
  @{name='muzik-gruplari'; regex='orkestra|muzik|müzik|dj|bando|fasil|fasýl|muzisyen|müzisyen|davul|zurna|keman|saksafon|trio|canli muzik|canlý müzik|klarnet'},
  @{name='organizasyon-hizmetleri'; regex='organizasyon|etkinlik|dugun|düðün|nisan|niþan|kina|kýna|soz|söz|nikah|nikâh|evlilik teklifi|hostes|karsilama|karþýlama|konsept|susleme|süsleme|acilis|açýlýþ|kurumsal etkinlik|dogum gunu|doðum günü|baby shower'},
  @{name='mekanlar'; regex='mekan|mekân|salon|kir dugunu|kýr düðünü|otel dugunu|otel düðünü|tekne dugunu|tekne düðünü|nikah salonu|davet salonu|balo salonu|bahce|bahçe|after party mekani|after party mekaný'},
  @{name='ikram-catering'; regex='catering|ikram|kokteyl|menu|menü|yemek servisi|dugun pastasi|düðün pastasý|nisan pastasi|niþan pastasý|soz pastasi|söz pastasý|butik pasta|ikramlik|ikramlýk'},
  @{name='foto-video'; regex='fotograf|fotoðraf|video|drone cekimi|drone çekimi|kameraman|klip cekimi|klip çekimi|dis cekim|dýþ çekim|tanitim filmi|tanýtým filmi|etkinlik fotograf|etkinlik fotoðraf'},
  @{name='teknik-produksiyon'; regex='ses sistemi|isik|ýþýk|sahne|led|mikser|hoparlor|hoparlör|jenerator|jeneratör|ses teknisyeni|sahne kurulumu'},
  @{name='eglence-sov-sanatci'; regex='animasyon|palyaco|palyaço|sov|þov|dansci|dansçý|illuzyon|illüzyon|sanatci|sanatçý|canli performans|canlý performans'},
  @{name='davet-destek-hizmetleri'; regex='davetiye|nikah sekeri|nikah þekeri|hediyelik|gelin arabasi|gelin arabasý|limuzin|transfer|vale|cicekci|çiçekçi|gelin buketi|gelin çiçeði'}
)

# Kesin dýþlanacaklar (bizim pazar dýþý)
$hardDrop = 'tuvalet|tesisat|dogalgaz|tamir|onarim|montaj|ariza|temizlik|nakliyat|kurye|ozel ders|özel ders|matematik|fizik|kimya|lgs|yks|ilkokul|ortaokul|emlak|airbnb|pasta cila|oto tamir|hastane|doktor|dis hekimi|diþ hekimi|eczane tabela|pcb|matbaa|ozalit|vinil germe|lazer kesim balkon|kedi|kopek|köpek'

# kapsam kapýsý
$scope = 'dugun|düðün|nisan|niþan|kina|kýna|soz|söz|nikah|nikâh|davet|organizasyon|etkinlik|muzik|müzik|orkestra|dj|bando|fasil|fasýl|mekan|mekân|salon|fotograf|fotoðraf|video|drone|kameraman|gelin|damat|catering|ikram|pasta|davetiye|nikah sekeri|nikah þekeri|hediyelik|ses sistemi|ýþýk|isik|sahne|animasyon|palyaço|palyaco|limuzin|transfer|çiçek|cicek|hostes|konsept|susleme|süsleme'

$keep = New-Object System.Collections.Generic.List[object]
$drop = New-Object System.Collections.Generic.List[object]

foreach($r in $rows){
  $title=[string]$r.title_tr
  $slug=[string]$r.slug
  $txt=($title+' '+$slug).ToLowerInvariant()

  if($txt -notmatch $scope){ continue }
  if($txt -match $hardDrop){
    $drop.Add([pscustomobject]@{title_tr=$title;slug=$slug;reason='hard_drop'}) | Out-Null
    continue
  }

  $assigned=$null
  foreach($root in $roots){
    if($txt -match $root.regex){ $assigned=$root.name; break }
  }

  if([string]::IsNullOrWhiteSpace($assigned)){
    # top category fallback for missing keyword hit
    switch([string]$r.top_category_slug){
      'service-events-organization' { $assigned='organizasyon-hizmetleri' }
      'service-photo-video-media' { $assigned='foto-video' }
      'service-rental' { $assigned='davet-destek-hizmetleri' }
      'service-beauty-fashion' { $assigned='davet-destek-hizmetleri' }
      default { $drop.Add([pscustomobject]@{title_tr=$title;slug=$slug;reason='no_match'}) | Out-Null; continue }
    }
  }

  $keep.Add([pscustomobject]@{
    main_category=$assigned
    title_tr=$title
    slug=$slug
    top_category_slug=$r.top_category_slug
    confidence=$r.top_mapping_confidence
  }) | Out-Null
}

$final = $keep | Sort-Object main_category,title_tr,slug -Unique
$drops = $drop | Sort-Object reason,title_tr,slug -Unique

$final | Export-Csv -NoTypeInformation -Encoding UTF8 $outKeepCsv
$drops | Export-Csv -NoTypeInformation -Encoding UTF8 $outDropCsv

$lines=@()
$lines += 'FINAL TREE OMURGA V3 (SENIN ANA OMURGA)'
$lines += 'Tarih: ' + (Get-Date -Format 'yyyy-MM-dd HH:mm:ss')
$lines += 'Toplam Alt Kategori: ' + $final.Count
$lines += ''
$groups = $final | Group-Object main_category | Sort-Object Name
$i=1
foreach($g in $groups){
  $lines += ("{0}. {1} ({2})" -f $i,$g.Name,$g.Count)
  foreach($row in ($g.Group | Sort-Object title_tr)){
    $lines += ("  - {0} [slug={1}; top={2}; conf={3}]" -f $row.title_tr,$row.slug,$row.top_category_slug,$row.confidence)
  }
  $lines += ''
  $i++
}
Set-Content -Path $outTree -Value $lines -Encoding UTF8

$rep=@()
$rep += 'FINAL TREE OMURGA V3 REPORT'
$rep += 'Keep: ' + $final.Count
$rep += 'Drop: ' + $drops.Count
$rep += ''
$rep += 'Ana kategori dagilimi:'
foreach($g in ($final | Group-Object main_category | Sort-Object Count -Descending)){
  $rep += ('- ' + $g.Name + ': ' + $g.Count)
}
$rep += ''
$rep += 'Dosyalar:'
$rep += '- ' + $outTree
$rep += '- ' + $outKeepCsv
$rep += '- ' + $outDropCsv
Set-Content -Path $outReport -Value $rep -Encoding UTF8

Get-Content $outReport
