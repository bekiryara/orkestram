param(
    [Parameter(Mandatory = $true)]
    [string]$PriorityCsvPath,
    [Parameter(Mandatory = $true)]
    [string]$OutMappingCsvPath
)

Set-StrictMode -Version Latest
$ErrorActionPreference = 'Stop'

if (-not (Test-Path -LiteralPath $PriorityCsvPath)) {
    throw "Dosya bulunamadi: $PriorityCsvPath"
}

$rows = Import-Csv -LiteralPath $PriorityCsvPath
$result = New-Object System.Collections.Generic.List[object]

function Get-MapDecision {
    param(
        [string]$Url,
        [string]$Domain,
        [string]$Priority
    )

    $u = [Uri]$Url
    $path = $u.AbsolutePath.ToLowerInvariant()

    # Sensitive/private routes: keep out of index and remap to service/contact later.
    if ($path.StartsWith('/rezervasyon/') -or $path.StartsWith('/fiyat-teklifi/')) {
        return [PSCustomObject]@{
            new_url = "https://$Domain/iletisim"
            mapping_type = '301'
            status = 'draft'
            note = 'private_or_form_url'
        }
    }

    # Listing/profile style pages can be preserved in v1.
    if ($path.StartsWith('/ilan/') -or $path.StartsWith('/muzik-grubu/')) {
        return [PSCustomObject]@{
            new_url = $Url
            mapping_type = 'keep'
            status = 'draft'
            note = 'listing_like_keep_same_slug'
        }
    }

    # Category-like technical paths: redirect to related hub pages.
    if ($path.StartsWith('/kategori/') -or $path.StartsWith('/turu/') -or $path.StartsWith('/muzik-grubu-kategorileri/')) {
        return [PSCustomObject]@{
            new_url = "https://$Domain/hizmetler"
            mapping_type = '301'
            status = 'draft'
            note = 'category_to_hub'
        }
    }

    # City/service slug style pages should stay if possible.
    if ($path -match '(izmir|istanbul|ankara|bursa|antalya|dugun|orkestra|kina|bando|muzik)') {
        return [PSCustomObject]@{
            new_url = $Url
            mapping_type = 'keep'
            status = 'draft'
            note = 'seo_slug_keep_same'
        }
    }

    # Low-priority unknown pages go to closest generic blog/hub.
    if ($Priority -eq 'low') {
        return [PSCustomObject]@{
            new_url = "https://$Domain/blog"
            mapping_type = '301'
            status = 'draft'
            note = 'low_priority_to_blog_hub'
        }
    }

    # Default: preserve.
    return [PSCustomObject]@{
        new_url = $Url
        mapping_type = 'keep'
        status = 'draft'
        note = 'default_keep_until_manual_review'
    }
}

foreach ($r in $rows) {
    if ([string]::IsNullOrWhiteSpace($r.url)) { continue }
    if ([string]::IsNullOrWhiteSpace($r.domain)) { continue }

    $d = Get-MapDecision -Url $r.url -Domain $r.domain -Priority $r.priority
    $result.Add([PSCustomObject]@{
        old_url = $r.url
        new_url = $d.new_url
        mapping_type = $d.mapping_type
        priority = $r.priority
        status = $d.status
        note = $d.note
    })
}

$outDir = Split-Path -Parent $OutMappingCsvPath
if ($outDir -and -not (Test-Path -LiteralPath $outDir)) {
    New-Item -ItemType Directory -Path $outDir | Out-Null
}

$result | Sort-Object old_url -Unique | Export-Csv -Path $OutMappingCsvPath -NoTypeInformation -Encoding UTF8
Write-Output "OK: $OutMappingCsvPath ($($result.Count) rows)"
