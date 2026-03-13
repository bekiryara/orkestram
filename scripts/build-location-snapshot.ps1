param(
    [string]$DistrictsPath = "D:\stack-data\catalog-dataset\out\manifests\options\districts.tr.json",
    [string]$NeighborhoodsPath = "D:\stack-data\catalog-dataset\out\manifests\options\neighborhoods.tr.json",
    [string]$WorkspaceRoot = "D:\orkestram\docs\category-workspace\08-location-workspace",
    [string]$ReadyRoot = "D:\orkestram\docs\category-catalog\ready\locations_v1"
)

$ErrorActionPreference = "Stop"

function Assert-PathFile {
    param([string]$Path)
    if (-not (Test-Path -LiteralPath $Path -PathType Leaf)) {
        throw "Dosya bulunamadi: $Path"
    }
}

function Ensure-Dir {
    param([string]$Path)
    if (-not (Test-Path -LiteralPath $Path -PathType Container)) {
        New-Item -ItemType Directory -Path $Path -Force | Out-Null
    }
}

function To-AsciiSlug {
    param([string]$Value)

    if ([string]::IsNullOrWhiteSpace($Value)) {
        $v = ""
    }
    else {
        $v = $Value.Trim()
    }
    if ($v -eq "") { return "" }

    $map = @{
        "ç" = "c"; "Ç" = "c";
        "ğ" = "g"; "Ğ" = "g";
        "ı" = "i"; "İ" = "i";
        "ö" = "o"; "Ö" = "o";
        "ş" = "s"; "Ş" = "s";
        "ü" = "u"; "Ü" = "u";
    }
    foreach ($k in $map.Keys) {
        $v = $v.Replace($k, $map[$k])
    }

    $v = $v.ToLowerInvariant()
    $v = [regex]::Replace($v, "[^a-z0-9]+", "-")
    $v = [regex]::Replace($v, "^-+|-+$", "")
    $v = [regex]::Replace($v, "-{2,}", "-")
    return $v
}

function Get-Sha256 {
    param([string]$Path)
    return (Get-FileHash -LiteralPath $Path -Algorithm SHA256).Hash.ToLowerInvariant()
}

function Write-CsvUtf8NoBom {
    param(
        [Parameter(Mandatory = $true)][string]$Path,
        [Parameter(Mandatory = $true)][array]$Rows
    )

    if ($Rows.Count -eq 0) {
        [System.IO.File]::WriteAllText($Path, "", (New-Object System.Text.UTF8Encoding($false)))
        return
    }

    $csv = $Rows | ConvertTo-Csv -NoTypeInformation
    [System.IO.File]::WriteAllLines($Path, $csv, (New-Object System.Text.UTF8Encoding($false)))
}

Assert-PathFile -Path $DistrictsPath
Assert-PathFile -Path $NeighborhoodsPath

$sourceDir = Join-Path $WorkspaceRoot "01-source"
$dbReadyDir = Join-Path $WorkspaceRoot "05-db-ready"
$analysisDir = Join-Path $WorkspaceRoot "03-analysis"
$exportsDir = Join-Path $WorkspaceRoot "exports"

Ensure-Dir -Path $WorkspaceRoot
Ensure-Dir -Path $sourceDir
Ensure-Dir -Path $dbReadyDir
Ensure-Dir -Path $analysisDir
Ensure-Dir -Path $exportsDir
Ensure-Dir -Path $ReadyRoot

Copy-Item -LiteralPath $DistrictsPath -Destination (Join-Path $sourceDir "districts.tr.json") -Force
Copy-Item -LiteralPath $NeighborhoodsPath -Destination (Join-Path $sourceDir "neighborhoods.tr.json") -Force

$districtsRaw = Get-Content -LiteralPath $DistrictsPath -Raw | ConvertFrom-Json
$neighborhoodsRaw = Get-Content -LiteralPath $NeighborhoodsPath -Raw | ConvertFrom-Json

$cityRows = New-Object System.Collections.Generic.List[object]
$districtRows = New-Object System.Collections.Generic.List[object]
$neighborhoodRows = New-Object System.Collections.Generic.List[object]
$anomalies = New-Object System.Collections.Generic.List[string]

$cityBySlug = @{}
$districtByKey = @{}
$districtSetFromDistricts = @{}
$neighborhoodBuckets = @{}

$cityNames = @($districtsRaw.PSObject.Properties.Name) | Sort-Object `
    @{ Expression = { To-AsciiSlug $_ } }, `
    @{ Expression = { $_ } }
$cityId = 1
$districtId = 1
$neighborhoodId = 1

foreach ($cityName in $cityNames) {
    $citySlug = To-AsciiSlug $cityName
    if ($citySlug -eq "") { continue }
    if ($cityBySlug.ContainsKey($citySlug)) {
        $anomalies.Add("Duplicate city slug: $citySlug -> $cityName") | Out-Null
        continue
    }

    $cityRow = [pscustomobject]@{
        city_id = $cityId
        city_name = $cityName
        city_slug = $citySlug
        sort_order = $cityId * 10
    }
    $cityRows.Add($cityRow) | Out-Null
    $cityBySlug[$citySlug] = $cityRow
    $cityId++

    $districtNames = @($districtsRaw.PSObject.Properties[$cityName].Value) | Sort-Object `
        @{ Expression = { To-AsciiSlug $_ } }, `
        @{ Expression = { $_ } }
    $districtSort = 10
    foreach ($districtName in $districtNames) {
        $districtSlug = To-AsciiSlug $districtName
        if ($districtSlug -eq "") { continue }

        $districtKey = "$citySlug|$districtSlug"
        if ($districtByKey.ContainsKey($districtKey)) {
            $anomalies.Add("Duplicate district key: $districtKey -> $cityName / $districtName") | Out-Null
            continue
        }

        $districtRow = [pscustomobject]@{
            district_id = $districtId
            city_id = $cityBySlug[$citySlug].city_id
            city_name = $cityName
            city_slug = $citySlug
            district_name = $districtName
            district_slug = $districtSlug
            sort_order = $districtSort
        }
        $districtRows.Add($districtRow) | Out-Null
        $districtByKey[$districtKey] = $districtRow
        $districtSetFromDistricts[$districtKey] = $true
        $districtSort += 10
        $districtId++
    }
}

foreach ($p in $neighborhoodsRaw.PSObject.Properties) {
    $parts = $p.Name -split "\|", 2
    if ($parts.Count -ne 2) {
        $anomalies.Add("Invalid neighborhood key format: $($p.Name)") | Out-Null
        continue
    }

    $cityName = $parts[0].Trim()
    $districtName = $parts[1].Trim()
    $citySlug = To-AsciiSlug $cityName
    $districtSlug = To-AsciiSlug $districtName
    $districtKey = "$citySlug|$districtSlug"

    if (-not $districtByKey.ContainsKey($districtKey)) {
        $anomalies.Add("Neighborhood source key does not exist in districts: $($p.Name)") | Out-Null
        continue
    }

    $bucket = @($p.Value) | Where-Object { -not [string]::IsNullOrWhiteSpace([string]$_) } | Sort-Object `
        @{ Expression = { To-AsciiSlug $_ } }, `
        @{ Expression = { $_ } }
    $neighborhoodBuckets[$districtKey] = $bucket
}

foreach ($districtKey in ($districtByKey.Keys | Sort-Object)) {
    $districtRow = $districtByKey[$districtKey]
    $bucket = @()
    if ($neighborhoodBuckets.ContainsKey($districtKey)) {
        $bucket = @($neighborhoodBuckets[$districtKey])
    }

    $seenN = @{}
    $sort = 10
    foreach ($name in $bucket) {
        $slug = To-AsciiSlug $name
        if ($slug -eq "") { continue }
        if ($seenN.ContainsKey($slug)) {
            $anomalies.Add("Duplicate neighborhood slug in district ${districtKey}: $slug") | Out-Null
            continue
        }
        $seenN[$slug] = $true

        $neighborhoodRows.Add([pscustomobject]@{
            neighborhood_id = $neighborhoodId
            district_id = $districtRow.district_id
            city_id = $districtRow.city_id
            city_name = $districtRow.city_name
            city_slug = $districtRow.city_slug
            district_name = $districtRow.district_name
            district_slug = $districtRow.district_slug
            neighborhood_name = $name
            neighborhood_slug = $slug
            sort_order = $sort
        }) | Out-Null
        $sort += 10
        $neighborhoodId++
    }
}

$citiesCsv = Join-Path $dbReadyDir "cities_v1.csv"
$districtsCsv = Join-Path $dbReadyDir "districts_v1.csv"
$neighborhoodsCsv = Join-Path $dbReadyDir "neighborhoods_v1.csv"
$manifestJson = Join-Path $dbReadyDir "manifest_v1.json"
$anomalyTxt = Join-Path $analysisDir "location_anomalies_v1.txt"

Write-CsvUtf8NoBom -Path $citiesCsv -Rows $cityRows
Write-CsvUtf8NoBom -Path $districtsCsv -Rows $districtRows
Write-CsvUtf8NoBom -Path $neighborhoodsCsv -Rows $neighborhoodRows

if ($anomalies.Count -eq 0) {
    [System.IO.File]::WriteAllText($anomalyTxt, "No anomalies detected.", (New-Object System.Text.UTF8Encoding($false)))
}
else {
    [System.IO.File]::WriteAllLines($anomalyTxt, @($anomalies), (New-Object System.Text.UTF8Encoding($false)))
}

$manifest = [pscustomobject]@{
    version = "locations_v1"
    source = @{
        districts_path = $DistrictsPath
        neighborhoods_path = $NeighborhoodsPath
        districts_sha256 = Get-Sha256 -Path $DistrictsPath
        neighborhoods_sha256 = Get-Sha256 -Path $NeighborhoodsPath
    }
    outputs = @{
        cities_csv = "cities_v1.csv"
        districts_csv = "districts_v1.csv"
        neighborhoods_csv = "neighborhoods_v1.csv"
        anomalies_report = "location_anomalies_v1.txt"
        cities_sha256 = Get-Sha256 -Path $citiesCsv
        districts_sha256 = Get-Sha256 -Path $districtsCsv
        neighborhoods_sha256 = Get-Sha256 -Path $neighborhoodsCsv
    }
    stats = @{
        city_count = $cityRows.Count
        district_count = $districtRows.Count
        neighborhood_count = $neighborhoodRows.Count
        anomaly_count = $anomalies.Count
    }
    generated_at_utc = (Get-Date).ToUniversalTime().ToString("yyyy-MM-ddTHH:mm:ssZ")
}

$manifestContent = $manifest | ConvertTo-Json -Depth 8
[System.IO.File]::WriteAllText($manifestJson, $manifestContent, (New-Object System.Text.UTF8Encoding($false)))

Copy-Item -LiteralPath $citiesCsv -Destination (Join-Path $ReadyRoot "cities_v1.csv") -Force
Copy-Item -LiteralPath $districtsCsv -Destination (Join-Path $ReadyRoot "districts_v1.csv") -Force
Copy-Item -LiteralPath $neighborhoodsCsv -Destination (Join-Path $ReadyRoot "neighborhoods_v1.csv") -Force
Copy-Item -LiteralPath $manifestJson -Destination (Join-Path $ReadyRoot "manifest_v1.json") -Force
Copy-Item -LiteralPath $anomalyTxt -Destination (Join-Path $ReadyRoot "location_anomalies_v1.txt") -Force

Write-Host "[location-snapshot] PASS"
Write-Host "cities=$($cityRows.Count) districts=$($districtRows.Count) neighborhoods=$($neighborhoodRows.Count) anomalies=$($anomalies.Count)"
