param(
    [ValidateSet("orkestram", "izmirorkestra", "both")]
    [string]$App = "both",
    [string]$AdminUser = "",
    [string]$AdminPass = ""
)

$ErrorActionPreference = "Stop"
$failures = New-Object System.Collections.Generic.List[string]
$SMOKE_RANGE_CATEGORY_SLUG = "smoke-range-kategori"
$SMOKE_RANGE_ATTR_KEY = "attr_smoke_ekip_sayisi"
$SMOKE_RANGE_LISTING_A = "Smoke Range Ilan A"
$SMOKE_RANGE_LISTING_B = "Smoke Range Ilan B"
$SMOKE_BANDO_CATEGORY_SLUG = "bando-takimi"
$SMOKE_BANDO_LISTING_A = "TEST Bando Senaryo A"
$SMOKE_BANDO_LISTING_B = "TEST Bando Senaryo B"
$global:FixtureWarningsPrinted = $false

function Invoke-DockerFixtureCommand {
    param(
        [string]$ContainerName,
        [string]$CommandName,
        [string]$SiteArg
    )

    $output = & docker exec $ContainerName php artisan $CommandName "--site=$SiteArg" 2>&1
    $exitCode = $LASTEXITCODE
    $text = [string]::Join("`n", ($output | ForEach-Object { [string]$_ }))

    $permissionDenied = $text -match 'permission denied while trying to connect to the docker API'
    return @{
        ExitCode = $exitCode
        PermissionDenied = $permissionDenied
    }
}

function Resolve-ContainerName {
    param([string]$TargetName)

    if ($TargetName -eq "orkestram") { return "orkestram-local-web" }
    if ($TargetName -eq "izmirorkestra") { return "izmirorkestra-local-web" }
    return ""
}

function Resolve-AdminCred {
    param([string]$User, [string]$Pass)

    $resolvedUser = $User
    $resolvedPass = $Pass

    if ([string]::IsNullOrWhiteSpace($resolvedUser)) { $resolvedUser = [Environment]::GetEnvironmentVariable("ADMIN_BASIC_USER") }
    if ([string]::IsNullOrWhiteSpace($resolvedPass)) { $resolvedPass = [Environment]::GetEnvironmentVariable("ADMIN_BASIC_PASS") }
    if ([string]::IsNullOrWhiteSpace($resolvedUser)) { $resolvedUser = "admin" }
    if ([string]::IsNullOrWhiteSpace($resolvedPass)) { $resolvedPass = "change-me" }

    return @{ User = $resolvedUser; Pass = $resolvedPass }
}

function Get-AuthHeader {
    param([string]$User, [string]$Pass)

    $pair = "{0}:{1}" -f $User, $Pass
    $bytes = [System.Text.Encoding]::ASCII.GetBytes($pair)
    $token = [Convert]::ToBase64String($bytes)
    return @{ Authorization = "Basic $token" }
}

function Get-HttpResult {
    param(
        [string]$Url,
        [hashtable]$Headers = @{}
    )

    try {
        $res = Invoke-WebRequest -Uri $Url -UseBasicParsing -TimeoutSec 12 -Headers $Headers
        return @{
            Code = [int]$res.StatusCode
            Body = [string]$res.Content
        }
    }
    catch {
        if ($_.Exception.Response -and $_.Exception.Response.StatusCode) {
            $body = ""
            $stream = $_.Exception.Response.GetResponseStream()
            if ($stream) {
                $reader = New-Object System.IO.StreamReader($stream)
                $body = $reader.ReadToEnd()
                $reader.Dispose()
            }

            return @{
                Code = [int]$_.Exception.Response.StatusCode
                Body = [string]$body
            }
        }

        return @{
            Code = -1
            Body = ""
        }
    }
}

function Get-ResultWithRetry {
    param(
        [string]$Url,
        [hashtable]$Headers = @{},
        [int]$Retries = 3,
        [int]$DelayMs = 1200
    )

    for ($i = 1; $i -le $Retries; $i++) {
        $result = Get-HttpResult -Url $Url -Headers $Headers
        if ($result.Code -ne -1) {
            return $result
        }

        Start-Sleep -Milliseconds $DelayMs
    }

    return @{
        Code = -1
        Body = ""
    }
}

function Assert-Status {
    param(
        [string]$Name,
        [string]$Url,
        [int]$Expect,
        [hashtable]$Headers = @{}
    )

    $result = Get-ResultWithRetry -Url $Url -Headers $Headers
    if ($result.Code -ne $Expect) {
        $failures.Add("$Name $Url beklenen $Expect, gelen $($result.Code)") | Out-Null
        return $false
    }

    Write-Host "[smoke] OK $url -> $($result.Code)"
    return $true
}

function Assert-BodyContains {
    param(
        [string]$Name,
        [string]$Url,
        [string]$Needle,
        [hashtable]$Headers = @{}
    )

    $result = Get-ResultWithRetry -Url $Url -Headers $Headers
    if ($result.Code -lt 200 -or $result.Code -ge 300) {
        $failures.Add("$Name $Url icerik kontrolu icin 2xx donmedi: $($result.Code)") | Out-Null
        return $false
    }

    if ([string]::IsNullOrWhiteSpace($result.Body) -or -not $result.Body.Contains($Needle)) {
        $failures.Add("$Name $Url beklenen metin bulunamadi: '$Needle'") | Out-Null
        return $false
    }

    Write-Host "[smoke] OK body $url -> '$Needle'"
    return $true
}

function Get-SelectOptionValues {
    param(
        [string]$Html,
        [string]$SelectName
    )

    if ([string]::IsNullOrWhiteSpace($Html) -or [string]::IsNullOrWhiteSpace($SelectName)) {
        return @()
    }

    $safeName = [regex]::Escape($SelectName)
    $selectRegex = [regex]::new("(?is)<select[^>]*name=['""]$safeName['""][^>]*>(.*?)</select>")
    $selectMatch = $selectRegex.Match($Html)
    if (-not $selectMatch.Success) {
        return @()
    }

    $inner = $selectMatch.Groups[1].Value
    $optionRegex = [regex]::new("(?is)<option[^>]*value=['""]([^'""]*)['""][^>]*>")
    $optionMatches = $optionRegex.Matches($inner)

    $values = New-Object System.Collections.Generic.List[string]
    foreach ($m in $optionMatches) {
        $raw = ""
        if ($m.Groups.Count -gt 1 -and $null -ne $m.Groups[1].Value) {
            $raw = [string]$m.Groups[1].Value
        }
        $value = [System.Net.WebUtility]::HtmlDecode($raw.Trim())
        if (-not [string]::IsNullOrWhiteSpace($value)) {
            $values.Add($value) | Out-Null
        }
    }

    return $values.ToArray()
}

function Assert-CityOptionsCaseInsensitiveUnique {
    param(
        [string]$Name,
        [string]$BaseUrl
    )

    $result = Get-ResultWithRetry -Url ($BaseUrl + "/ilanlar")
    if ($result.Code -ne 200 -or [string]::IsNullOrWhiteSpace($result.Body)) {
        $failures.Add("$Name /ilanlar city option tekillestirme kontrolu icin 200 donmedi: $($result.Code)") | Out-Null
        return $false
    }

    $cityValues = Get-SelectOptionValues -Html $result.Body -SelectName "city"
    if ($cityValues.Count -eq 0) {
        $failures.Add("$Name /ilanlar city select option listesi okunamadi") | Out-Null
        return $false
    }

    $seen = @{}
    $dupes = New-Object System.Collections.Generic.List[string]
    foreach ($city in $cityValues) {
        $norm = $city.Trim().ToLowerInvariant()
        if ($seen.ContainsKey($norm)) {
            $dupes.Add($city) | Out-Null
        }
        else {
            $seen[$norm] = $true
        }
    }

    if ($dupes.Count -gt 0) {
        $failures.Add("$Name /ilanlar city option listesinde case-insensitive tekrar var: $([string]::Join(', ', $dupes))") | Out-Null
        return $false
    }

    Write-Host "[smoke] OK city-options unique ($Name)"
    return $true
}

function Assert-RangeFilterPairsAreComplete {
    param(
        [string]$Name,
        [string]$BaseUrl
    )

    $result = Get-ResultWithRetry -Url ($BaseUrl + "/ilanlar?category=$SMOKE_RANGE_CATEGORY_SLUG")
    if ($result.Code -ne 200 -or [string]::IsNullOrWhiteSpace($result.Body)) {
        $failures.Add("$Name /ilanlar?category=$SMOKE_RANGE_CATEGORY_SLUG range pair kontrolu icin 200 donmedi: $($result.Code)") | Out-Null
        return $false
    }

    $minRegex = [regex]::new('name=["''](attr_[a-z0-9_]+)_min["'']', [System.Text.RegularExpressions.RegexOptions]::IgnoreCase)
    $maxRegex = [regex]::new('name=["''](attr_[a-z0-9_]+)_max["'']', [System.Text.RegularExpressions.RegexOptions]::IgnoreCase)

    $mins = @{}
    foreach ($m in $minRegex.Matches($result.Body)) {
        $mins[$m.Groups[1].Value.ToLowerInvariant()] = $true
    }

    $maxs = @{}
    foreach ($m in $maxRegex.Matches($result.Body)) {
        $maxs[$m.Groups[1].Value.ToLowerInvariant()] = $true
    }

    if ($mins.Count -eq 0 -and $maxs.Count -eq 0) {
        $failures.Add("$Name /ilanlar?category=$SMOKE_RANGE_CATEGORY_SLUG range filtre inputlari bulunamadi") | Out-Null
        return $false
    }

    foreach ($k in $mins.Keys) {
        if (-not $maxs.ContainsKey($k)) {
            $failures.Add("$Name /ilanlar range input eksik: ${k}_min var ama ${k}_max yok") | Out-Null
            return $false
        }
    }

    foreach ($k in $maxs.Keys) {
        if (-not $mins.ContainsKey($k)) {
            $failures.Add("$Name /ilanlar range input eksik: ${k}_max var ama ${k}_min yok") | Out-Null
            return $false
        }
    }

    Write-Host "[smoke] OK range-pairs complete ($Name)"
    return $true
}

function Assert-RangeFilterIsFunctional {
    param(
        [string]$Name,
        [string]$BaseUrl
    )

    $minUrl = "$BaseUrl/ilanlar?category=$SMOKE_RANGE_CATEGORY_SLUG&${SMOKE_RANGE_ATTR_KEY}_min=6"
    $minResult = Get-ResultWithRetry -Url $minUrl
    if ($minResult.Code -ne 200 -or [string]::IsNullOrWhiteSpace($minResult.Body)) {
        $failures.Add("$Name range min fonksiyonel kontrolu icin 200 donmedi: $($minResult.Code)") | Out-Null
        return $false
    }
    if (-not $minResult.Body.Contains($SMOKE_RANGE_LISTING_B) -or $minResult.Body.Contains($SMOKE_RANGE_LISTING_A)) {
        $failures.Add("$Name range min kontrolu hatali sonuc dondu (beklenen: sadece '$SMOKE_RANGE_LISTING_B')") | Out-Null
        return $false
    }

    $maxUrl = "$BaseUrl/ilanlar?category=$SMOKE_RANGE_CATEGORY_SLUG&${SMOKE_RANGE_ATTR_KEY}_max=6"
    $maxResult = Get-ResultWithRetry -Url $maxUrl
    if ($maxResult.Code -ne 200 -or [string]::IsNullOrWhiteSpace($maxResult.Body)) {
        $failures.Add("$Name range max fonksiyonel kontrolu icin 200 donmedi: $($maxResult.Code)") | Out-Null
        return $false
    }
    if (-not $maxResult.Body.Contains($SMOKE_RANGE_LISTING_A) -or $maxResult.Body.Contains($SMOKE_RANGE_LISTING_B)) {
        $failures.Add("$Name range max kontrolu hatali sonuc dondu (beklenen: sadece '$SMOKE_RANGE_LISTING_A')") | Out-Null
        return $false
    }

    Write-Host "[smoke] OK range-functional ($Name)"
    return $true
}

function Assert-BandoFilterMatrix {
    param(
        [string]$Name,
        [string]$BaseUrl
    )

    $scenarios = @(
        @{
            Label = "bando-sure-60"
            Url = "$BaseUrl/ilanlar?category=$SMOKE_BANDO_CATEGORY_SLUG&attr_sure_dk=60"
            MustSee = $SMOKE_BANDO_LISTING_A
            MustNotSee = $SMOKE_BANDO_LISTING_B
        },
        @{
            Label = "bando-enstruman-sayisi-min-8"
            Url = "$BaseUrl/ilanlar?category=$SMOKE_BANDO_CATEGORY_SLUG&attr_enstruman_sayisi_min=8"
            MustSee = $SMOKE_BANDO_LISTING_B
            MustNotSee = $SMOKE_BANDO_LISTING_A
        },
        @{
            Label = "bando-solist-cinsiyeti-kadin"
            Url = "$BaseUrl/ilanlar?category=$SMOKE_BANDO_CATEGORY_SLUG&attr_solist_cinsiyeti=Kadin"
            MustSee = $SMOKE_BANDO_LISTING_A
            MustNotSee = $SMOKE_BANDO_LISTING_B
        },
        @{
            Label = "bando-enstrumanlar-trompet"
            Url = "$BaseUrl/ilanlar?category=$SMOKE_BANDO_CATEGORY_SLUG&attr_enstrumanlar%5B%5D=Trompet"
            MustSee = $SMOKE_BANDO_LISTING_A
            MustNotSee = $SMOKE_BANDO_LISTING_B
        },
        @{
            Label = "bando-kostum-papyon"
            Url = "$BaseUrl/ilanlar?category=$SMOKE_BANDO_CATEGORY_SLUG&attr_kostum%5B%5D=Papyon"
            MustSee = $SMOKE_BANDO_LISTING_B
            MustNotSee = $SMOKE_BANDO_LISTING_A
        }
    )

    foreach ($s in $scenarios) {
        $result = Get-ResultWithRetry -Url $s.Url
        if ($result.Code -ne 200 -or [string]::IsNullOrWhiteSpace($result.Body)) {
            $failures.Add("$Name $($s.Label) icin 200 donmedi: $($result.Code)") | Out-Null
            return $false
        }

        if (-not $result.Body.Contains($s.MustSee) -or $result.Body.Contains($s.MustNotSee)) {
            $failures.Add("$Name $($s.Label) beklenen ayrisimi saglamadi (must-see: '$($s.MustSee)', must-not-see: '$($s.MustNotSee)')") | Out-Null
            return $false
        }
    }

    Write-Host "[smoke] OK bando-filter-matrix ($Name)"
    return $true
}

function Resolve-ListingPath {
    param([string]$BaseUrl)

    $candidates = @(
        "/ilan/grup-moda",
        "/ilan/izmir-bandosu",
        "/ilan/gelin-alma-bandosu"
    )

    foreach ($path in $candidates) {
        $result = Get-ResultWithRetry -Url ($BaseUrl + $path)
        if ($result.Code -eq 200) {
            return $path
        }
    }

    $listingResult = Get-ResultWithRetry -Url ($BaseUrl + "/ilanlar")
    if ($listingResult.Code -ne 200 -or [string]::IsNullOrWhiteSpace($listingResult.Body)) {
        return $null
    }

    $regex = [regex]'href=["'']([^"''>]*?/ilan/[^"''>]+)["'']'
    $match = $regex.Match($listingResult.Body)
    if ($match.Success) {
        $href = $match.Groups[1].Value
        if ([string]::IsNullOrWhiteSpace($href)) {
            return $null
        }

        if ($href.StartsWith('/')) {
            return $href
        }

        try {
            $uri = [Uri]$href
            return $uri.PathAndQuery
        }
        catch {
            return $null
        }
    }

    return $null
}

function Resolve-CategoryPath {
    param([string]$BaseUrl)

    $candidates = @(
        "/hizmet/dugun-orkestrasi",
        "/hizmet/canli-dugun-muzigi",
        "/hizmet/bando-takimi"
    )

    foreach ($path in $candidates) {
        $result = Get-ResultWithRetry -Url ($BaseUrl + $path)
        if ($result.Code -eq 200) {
            return $path
        }
    }

    $listingResult = Get-ResultWithRetry -Url ($BaseUrl + "/ilanlar")
    if ($listingResult.Code -ne 200 -or [string]::IsNullOrWhiteSpace($listingResult.Body)) {
        return $null
    }

    $regex = [regex]'name=["'']category["''][^>]*>[\s\S]*?<option value=["'']([^"''<>]+)["'']'
    $match = $regex.Match($listingResult.Body)
    if ($match.Success) {
        $slug = $match.Groups[1].Value
        if (-not [string]::IsNullOrWhiteSpace($slug)) {
            return "/hizmet/$slug"
        }
    }

    return $null
}

function Resolve-CategoryCityPath {
    param([string]$BaseUrl)

    $candidates = @(
        "/hizmet/dugun-orkestrasi/izmir",
        "/hizmet/canli-dugun-muzigi/izmir",
        "/hizmet/bando-takimi/izmir"
    )

    foreach ($path in $candidates) {
        $result = Get-ResultWithRetry -Url ($BaseUrl + $path)
        if ($result.Code -eq 200) {
            return $path
        }
    }

    return $null
}

function Resolve-CategoryCityDistrictPath {
    param([string]$BaseUrl)

    $candidates = @(
        "/hizmet/dugun-orkestrasi/izmir/konak",
        "/hizmet/canli-dugun-muzigi/izmir/karsiyaka",
        "/hizmet/bando-takimi/izmir/bornova"
    )

    foreach ($path in $candidates) {
        $result = Get-ResultWithRetry -Url ($BaseUrl + $path)
        if ($result.Code -eq 200) {
            return $path
        }
    }

    $sitemapResult = Get-ResultWithRetry -Url ($BaseUrl + "/sitemap.xml")
    if ($sitemapResult.Code -eq 200 -and -not [string]::IsNullOrWhiteSpace($sitemapResult.Body)) {
        $regex = [regex]'<loc>(https?://[^<]+/hizmet/[^<]+/[^<]+/[^<]+)</loc>'
        $match = $regex.Match($sitemapResult.Body)
        if ($match.Success) {
            try {
                $uri = [Uri]$match.Groups[1].Value
                return $uri.PathAndQuery
            }
            catch {
                return $null
            }
        }
    }

    return $null
}

$targets = @()
if ($App -eq "both") {
    $targets = @(
        @{ Name = "orkestram"; Base = "http://127.0.0.1:8180" },
        @{ Name = "izmirorkestra"; Base = "http://127.0.0.1:8181" }
    )
}
elseif ($App -eq "orkestram") {
    $targets = @(@{ Name = "orkestram"; Base = "http://127.0.0.1:8180" })
}
else {
    $targets = @(@{ Name = "izmirorkestra"; Base = "http://127.0.0.1:8181" })
}

$creds = Resolve-AdminCred -User $AdminUser -Pass $AdminPass
$authHeaders = Get-AuthHeader -User $creds.User -Pass $creds.Pass

foreach ($t in $targets) {
    Write-Host "[smoke] target=$($t.Name)"
    $fixturesReady = $true

    $containerName = Resolve-ContainerName -TargetName $t.Name
    if ([string]::IsNullOrWhiteSpace($containerName)) {
        $failures.Add("$($t.Name) icin container adi cozulemedi") | Out-Null
        $fixturesReady = $false
    }
    else {
        $siteArg = if ($t.Name -eq "izmirorkestra") { "izmirorkestra.net" } else { "orkestram.net" }
        $rangeResult = Invoke-DockerFixtureCommand -ContainerName $containerName -CommandName "smoke:prepare-range-fixture" -SiteArg $siteArg
        if ($rangeResult.ExitCode -ne 0) {
            if ($rangeResult.PermissionDenied) {
                $fixturesReady = $false
                if (-not $global:FixtureWarningsPrinted) {
                    Write-Host "[smoke] WARN docker fixture adimi izin nedeniyle atlandi; fixture-bagimli kontroller skip edilecek."
                    $global:FixtureWarningsPrinted = $true
                }
            }
            else {
                $failures.Add("$($t.Name) smoke:prepare-range-fixture komutu basarisiz") | Out-Null
            }
        }
        else {
            Write-Host "[smoke] OK range-fixture prepared ($($t.Name))"
        }

        if ($fixturesReady) {
            $bandoResult = Invoke-DockerFixtureCommand -ContainerName $containerName -CommandName "smoke:prepare-bando-fixture" -SiteArg $siteArg
            if ($bandoResult.ExitCode -ne 0) {
                if ($bandoResult.PermissionDenied) {
                    $fixturesReady = $false
                    if (-not $global:FixtureWarningsPrinted) {
                        Write-Host "[smoke] WARN docker fixture adimi izin nedeniyle atlandi; fixture-bagimli kontroller skip edilecek."
                        $global:FixtureWarningsPrinted = $true
                    }
                }
                else {
                    $failures.Add("$($t.Name) smoke:prepare-bando-fixture komutu basarisiz") | Out-Null
                }
            }
            else {
                Write-Host "[smoke] OK bando-fixture prepared ($($t.Name))"
            }
        }
        else {
            Write-Host "[smoke] SKIP bando-fixture prepared ($($t.Name))"
        }

        if (-not $fixturesReady) {
            Write-Host "[smoke] SKIP fixture-dependent checks ($($t.Name))"
        }
    }

    $publicChecks = @(
        @{ Path = "/"; Expect = 200 },
        @{ Path = "/ilanlar"; Expect = 200 },
        @{ Path = "/robots.txt"; Expect = 200 },
        @{ Path = "/sitemap.xml"; Expect = 200 },
        @{ Path = "/this-should-404"; Expect = 404 }
    )

    foreach ($c in $publicChecks) {
        Assert-Status -Name $t.Name -Url ($t.Base + $c.Path) -Expect $c.Expect | Out-Null
    }
    Assert-CityOptionsCaseInsensitiveUnique -Name $t.Name -BaseUrl $t.Base | Out-Null
    if ($fixturesReady) {
        Assert-RangeFilterPairsAreComplete -Name $t.Name -BaseUrl $t.Base | Out-Null
        Assert-RangeFilterIsFunctional -Name $t.Name -BaseUrl $t.Base | Out-Null
        Assert-BandoFilterMatrix -Name $t.Name -BaseUrl $t.Base | Out-Null
    }

    $listingPath = Resolve-ListingPath -BaseUrl $t.Base
    if ([string]::IsNullOrWhiteSpace($listingPath)) {
        $failures.Add("$($t.Name) listing detay bulunamadi (/ilanlar uzerinden 200 bir detay URL'i yakalanamadi)") | Out-Null
    }
    else {
        Assert-Status -Name $t.Name -Url ($t.Base + $listingPath) -Expect 200 | Out-Null
        Assert-BodyContains -Name $t.Name -Url ($t.Base + $listingPath) -Needle "Yorumlar" | Out-Null
    }

    $categoryPath = Resolve-CategoryPath -BaseUrl $t.Base
    if ([string]::IsNullOrWhiteSpace($categoryPath)) {
        $failures.Add("$($t.Name) kategori landing bulunamadi (/ilanlar uzerinden kategori slug yakalanamadi)") | Out-Null
    }
    else {
        Assert-Status -Name $t.Name -Url ($t.Base + $categoryPath) -Expect 200 | Out-Null
    }

    $categoryCityPath = Resolve-CategoryCityPath -BaseUrl $t.Base
    if ([string]::IsNullOrWhiteSpace($categoryCityPath)) {
        $failures.Add("$($t.Name) kategori-sehir landing bulunamadi (/hizmet/{kategori}/{sehir} 200 bir URL yakalanamadi)") | Out-Null
    }
    else {
        Assert-Status -Name $t.Name -Url ($t.Base + $categoryCityPath) -Expect 200 | Out-Null
    }

    $categoryCityDistrictPath = Resolve-CategoryCityDistrictPath -BaseUrl $t.Base
    if ([string]::IsNullOrWhiteSpace($categoryCityDistrictPath)) {
        $failures.Add("$($t.Name) kategori-sehir-ilce landing bulunamadi (/hizmet/{kategori}/{sehir}/{ilce} 200 bir URL yakalanamadi)") | Out-Null
    }
    else {
        Assert-Status -Name $t.Name -Url ($t.Base + $categoryCityDistrictPath) -Expect 200 | Out-Null
    }

    Assert-Status -Name $t.Name -Url ($t.Base + "/admin/pages") -Expect 401 | Out-Null
    Assert-Status -Name $t.Name -Url ($t.Base + "/admin/pages") -Expect 200 -Headers $authHeaders | Out-Null
    Assert-BodyContains -Name $t.Name -Url ($t.Base + "/admin/pages") -Needle "Geri Bildirimler" -Headers $authHeaders | Out-Null
    Assert-Status -Name $t.Name -Url ($t.Base + "/admin/categories") -Expect 200 -Headers $authHeaders | Out-Null
    Assert-Status -Name $t.Name -Url ($t.Base + "/admin/listings") -Expect 200 -Headers $authHeaders | Out-Null
    Assert-Status -Name $t.Name -Url ($t.Base + "/admin/city-pages") -Expect 200 -Headers $authHeaders | Out-Null
}

if ($failures.Count -gt 0) {
    Write-Host "[smoke] FAIL"
    for ($i = 0; $i -lt $failures.Count; $i++) {
        Write-Host ("{0}. {1}" -f ($i + 1), $failures[$i])
    }
    exit 1
}

Write-Host "[smoke] PASS"
exit 0
