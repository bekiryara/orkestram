param(
    [Parameter(Mandatory = $true)]
    [string]$InputPath,
    [Parameter(Mandatory = $true)]
    [string]$Domain,
    [Parameter(Mandatory = $true)]
    [string]$OutPath,
    [int]$Limit = 200
)

Set-StrictMode -Version Latest
$ErrorActionPreference = 'Stop'

if (-not (Test-Path -LiteralPath $InputPath)) {
    throw "Girdi dosyasi yok: $InputPath"
}

$blockedStarts = @(
    '/wp-admin', '/wp-content', '/wp-includes', '/xmlrpc.php', '/wp-login.php', '/feed',
    '/author', '/tag', '/category', '/comments', '/ads.txt', '/sitemap', '/robots.txt',
    '/as.php', '/bk.php', '/style.php', '/zip.php', '/ws53.php', '/ws55.php', '/public/'
)

$blockedContains = @(
    '/jet-popup/', '/jsf/', '/vendor/phpunit/', '/media/system/', '/jet-engine:', '/_wfsf=',
    '/?elementor_library=', '/?p='
)

$priorityTokens = @(
    'izmir','istanbul','ankara','bursa','antalya','dugun','düğün','orkestra','muzik','müzik',
    'fiyat','teklif','rezervasyon','bando','fasil','fasıl','nikah','kina','kına',
    'kurumsal','etkinlik','grup','canli-muzik','canli','organizasyon','orchestra'
)

function Get-Score {
    param([string]$Path)
    $score = 0
    $segCount = @($Path.Trim('/') -split '/').Count
    if ($segCount -ge 1) { $score += 1 }
    if ($segCount -ge 2) { $score += 1 }
    foreach ($t in $priorityTokens) {
        if ($Path -match [Regex]::Escape($t)) { $score += 2 }
    }
    if ($Path -match '\d{4,}') { $score -= 3 }
    if ($Path.Length -gt 80) { $score -= 1 }
    return $score
}

$rows = @()
Get-Content -LiteralPath $InputPath | ForEach-Object {
    $u = $_.Trim()
    if ([string]::IsNullOrWhiteSpace($u)) { return }

    try {
        $uri = [Uri]$u
    }
    catch {
        return
    }

    if ($uri.Host -notmatch ("(^|\.)" + [Regex]::Escape($Domain) + "$")) { return }
    if (-not [string]::IsNullOrEmpty($uri.Query)) { return }

    $path = $uri.AbsolutePath
    if ([string]::IsNullOrWhiteSpace($path)) { return }
    if ($path -eq '/') { return }

    foreach ($b in $blockedStarts) {
        if ($path.StartsWith($b, [System.StringComparison]::OrdinalIgnoreCase)) { return }
    }

    foreach ($c in $blockedContains) {
        if ($u.ToLowerInvariant().Contains($c)) { return }
    }

    # canonicalize domain + scheme
    $canonical = "https://$Domain$path".ToLowerInvariant().TrimEnd('/')
    if ($canonical -eq "https://$Domain".ToLowerInvariant()) { return }

    $rows += [PSCustomObject]@{
        url = $canonical
        score = Get-Score -Path $path.ToLowerInvariant()
    }
}

$top = $rows |
    Sort-Object url -Unique |
    Sort-Object @{Expression='score';Descending=$true}, @{Expression='url';Descending=$false} |
    Select-Object -First $Limit

$csvRows = $top | ForEach-Object {
    [PSCustomObject]@{
        url = $_.url
        domain = $Domain
        page_type = 'unknown'
        priority = if ($_.score -ge 6) { 'high' } elseif ($_.score -ge 3) { 'medium' } else { 'low' }
        reason = 'auto_candidate'
        action = 'review_keep_or_map'
    }
}

$outDir = Split-Path -Parent $OutPath
if ($outDir -and -not (Test-Path $outDir)) {
    New-Item -ItemType Directory -Path $outDir | Out-Null
}

$csvRows | Export-Csv -Path $OutPath -NoTypeInformation -Encoding UTF8
Write-Output "OK: $OutPath ($($csvRows.Count) rows)"
