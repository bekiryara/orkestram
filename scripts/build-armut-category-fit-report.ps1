param(
  [string]$InputCsv = 'D:\stack-data\catalog-dataset\csv\_imports\armut\categories_approved_wave1_top500_v2.csv',
  [string]$OutDir = 'D:\orkestram\docs\exports\armut-category-fit'
)

$ErrorActionPreference='Stop'
New-Item -ItemType Directory -Force -Path $OutDir | Out-Null

$rows = Import-Csv $InputCsv

# Deterministic exclusion rules: outside wedding/invitation marketplace scope
$excludeRegex = '(montaj|tamir|bakim|onarim|tesisat|temizlik|nakliyat|kurye|ozel ders|matematik|fizik|kimya|dogalgaz|klima bakim|perde tamiri|ariza)'

$rules = @(
  @{ Main='Muzik Gruplari'; Regex='(orkestra|bando|dj|fasil|fasýl|muzik|müzik|muzisyen|müzisyen|keman|saksafon|davul|zurna|canli muzik|canlý müzik)' },
  @{ Main='Organizasyon Hizmetleri'; Regex='(organizasyon|etkinlik|dugun|düðün|nisan|niþan|kina|kýna|davet|acilis|açýlýþ|hostes|sunucu|animasyon)' },
  @{ Main='Mekanlar'; Regex='(mekan|mekân|salon|kýr bahcesi|kýr bahçesi|otel|restaurant|restoran|kafe|cafe|tekne|yat|villa)' },
  @{ Main='Fotograf Video'; Regex='(fotograf|fotoðraf|video|klip|drone|kameraman|cekim|çekim)' },
  @{ Main='Teknik Produksiyon'; Regex='(ses sistemi|isik|ýþýk|sahne|podyum|led|jenerator|jeneratör|mikser|hoparlor|hoparlör)' }
)

$result = foreach($r in $rows){
  $title = [string]$r.title_tr
  $slug = [string]$r.slug
  $txt = ($title + ' ' + $slug).ToLowerInvariant()

  if ($txt -match $excludeRegex) {
    [pscustomobject]@{
      slug=$slug; title_tr=$title; top_category_slug=$r.top_category_slug; mapping_confidence=$r.top_mapping_confidence;
      decision='rejected_scope'; main_category=''; reason='exclude_rule'
    }
    continue
  }

  $hits = @()
  foreach($rule in $rules){
    if ($txt -match $rule.Regex) { $hits += $rule.Main }
  }

  if ($hits.Count -eq 1) {
    [pscustomobject]@{
      slug=$slug; title_tr=$title; top_category_slug=$r.top_category_slug; mapping_confidence=$r.top_mapping_confidence;
      decision='approved'; main_category=$hits[0]; reason='single_match'
    }
  } elseif ($hits.Count -gt 1) {
    [pscustomobject]@{
      slug=$slug; title_tr=$title; top_category_slug=$r.top_category_slug; mapping_confidence=$r.top_mapping_confidence;
      decision='manual_review'; main_category=($hits -join '|'); reason='multi_match'
    }
  } else {
    [pscustomobject]@{
      slug=$slug; title_tr=$title; top_category_slug=$r.top_category_slug; mapping_confidence=$r.top_mapping_confidence;
      decision='manual_review'; main_category=''; reason='no_match'
    }
  }
}

$approved = $result | Where-Object decision -eq 'approved'
$manual = $result | Where-Object decision -eq 'manual_review'
$rejected = $result | Where-Object decision -eq 'rejected_scope'

$approved | Export-Csv -NoTypeInformation -Encoding UTF8 (Join-Path $OutDir 'approved_for_site.csv')
$manual | Export-Csv -NoTypeInformation -Encoding UTF8 (Join-Path $OutDir 'manual_review.csv')
$rejected | Export-Csv -NoTypeInformation -Encoding UTF8 (Join-Path $OutDir 'rejected_scope.csv')

$summaryMain = $approved | Group-Object main_category | Sort-Object Count -Descending | Select-Object @{n='main_category';e={$_.Name}}, Count
$summaryDecision = $result | Group-Object decision | Sort-Object Count -Descending | Select-Object @{n='decision';e={$_.Name}}, Count
$summaryMain | Export-Csv -NoTypeInformation -Encoding UTF8 (Join-Path $OutDir 'summary_main_categories.csv')
$summaryDecision | Export-Csv -NoTypeInformation -Encoding UTF8 (Join-Path $OutDir 'summary_decision.csv')

"Input rows: $($rows.Count)"
"Approved: $($approved.Count)"
"Manual review: $($manual.Count)"
"Rejected scope: $($rejected.Count)"
"\nMain category counts:"
$summaryMain | Format-Table -AutoSize
"\nOutput dir: $OutDir"
