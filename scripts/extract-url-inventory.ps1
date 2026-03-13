param(
    [Parameter(Mandatory = $true)]
    [string]$DumpPath,
    [Parameter(Mandatory = $true)]
    [string]$Domain,
    [Parameter(Mandatory = $true)]
    [string]$OutDir
)

Set-StrictMode -Version Latest
$ErrorActionPreference = 'Stop'

function New-DirIfMissing {
    param([string]$Path)
    if (-not (Test-Path -LiteralPath $Path)) {
        New-Item -ItemType Directory -Path $Path | Out-Null
    }
}

function Open-GzipReader {
    param([string]$Path)
    $fs = [System.IO.File]::OpenRead($Path)
    $gz = New-Object System.IO.Compression.GzipStream($fs, [System.IO.Compression.CompressionMode]::Decompress)
    $sr = New-Object System.IO.StreamReader($gz)
    return [PSCustomObject]@{
        FileStream = $fs
        GzipStream = $gz
        Reader = $sr
    }
}

function Close-GzipReader {
    param($Handle)
    if ($null -ne $Handle.Reader) { $Handle.Reader.Dispose() }
    if ($null -ne $Handle.GzipStream) { $Handle.GzipStream.Dispose() }
    if ($null -ne $Handle.FileStream) { $Handle.FileStream.Dispose() }
}

if (-not (Test-Path -LiteralPath $DumpPath)) {
    throw "Dump dosyasi bulunamadi: $DumpPath"
}

New-DirIfMissing -Path $OutDir

$allUrls = New-Object 'System.Collections.Generic.HashSet[string]'
$mediaUrls = New-Object 'System.Collections.Generic.HashSet[string]'
$pageLikeUrls = New-Object 'System.Collections.Generic.HashSet[string]'

$domainEscaped = [Regex]::Escape($Domain)
$urlPattern = New-Object System.Text.RegularExpressions.Regex("https?://(?:www\.)?$domainEscaped[^\s'""<>\\)]+", [System.Text.RegularExpressions.RegexOptions]::IgnoreCase)
$mediaPattern = New-Object System.Text.RegularExpressions.Regex("\.(jpg|jpeg|png|webp|gif|svg|avif|mp4|mov|webm|m4v|avi|pdf)(\?|$)", [System.Text.RegularExpressions.RegexOptions]::IgnoreCase)
$pageLikePattern = New-Object System.Text.RegularExpressions.Regex("/(izmir|istanbul|ankara|bursa|antalya|dugun|orkestra|muzik|kurumsal|etkinlik|fiyat|teklif|rezervasyon|iletisim|hakkimizda|blog)(/|$)", [System.Text.RegularExpressions.RegexOptions]::IgnoreCase)

$h = Open-GzipReader -Path $DumpPath
try {
    while (-not $h.Reader.EndOfStream) {
        $line = $h.Reader.ReadLine()
        if ([string]::IsNullOrWhiteSpace($line)) { continue }

        $matches = $urlPattern.Matches($line)
        foreach ($m in $matches) {
            $u = $m.Value.TrimEnd('.', ',', ';')
            [void]$allUrls.Add($u)
            if ($mediaPattern.IsMatch($u)) {
                [void]$mediaUrls.Add($u)
            }
            else {
                [void]$pageLikeUrls.Add($u)
            }
        }
    }
}
finally {
    Close-GzipReader -Handle $h
}

$allSorted = @($allUrls) | Sort-Object
$mediaSorted = @($mediaUrls) | Sort-Object
$pageSorted = @($pageLikeUrls) | Sort-Object

$allPath = Join-Path $OutDir 'all-urls.txt'
$mediaPath = Join-Path $OutDir 'media-urls.txt'
$pagePath = Join-Path $OutDir 'page-like-urls.txt'
$summaryPath = Join-Path $OutDir 'summary.txt'

$allSorted | Set-Content -Path $allPath -Encoding UTF8
$mediaSorted | Set-Content -Path $mediaPath -Encoding UTF8
$pageSorted | Set-Content -Path $pagePath -Encoding UTF8

@(
    "domain=$Domain"
    "dump=$DumpPath"
    "all_urls=$($allSorted.Count)"
    "media_urls=$($mediaSorted.Count)"
    "page_like_urls=$($pageSorted.Count)"
) | Set-Content -Path $summaryPath -Encoding UTF8

Write-Output "OK: $summaryPath"
