param(
    [Parameter(Mandatory = $true)]
    [string]$Domain,

    [Parameter(Mandatory = $true)]
    [ValidateSet("local-check", "predeploy-check")]
    [string]$Profile = "predeploy-check",

    [Parameter(Mandatory = $true)]
    [string]$EnvFilePath,

    [string]$DeployPackPath = "D:\orkestram\deploy-pack",
    [string]$PolicyPath = "D:\orkestram\scripts\deploy-guard.policy.json"
)

$ErrorActionPreference = "Stop"
$Failures = New-Object System.Collections.Generic.List[string]
$Warnings = New-Object System.Collections.Generic.List[string]

function Add-Fail { param([string]$m) $script:Failures.Add($m) | Out-Null }
function Add-Warn { param([string]$m) $script:Warnings.Add($m) | Out-Null }

function Parse-EnvFile {
    param([string]$Path)
    $envMap = @{}
    if (-not (Test-Path -LiteralPath $Path -PathType Leaf)) { return $envMap }

    foreach ($line in (Get-Content -LiteralPath $Path)) {
        $raw = $line.Trim()
        if ([string]::IsNullOrWhiteSpace($raw)) { continue }
        if ($raw.StartsWith("#")) { continue }
        if (-not $raw.Contains("=")) { continue }

        $parts = $raw.Split("=", 2)
        $k = $parts[0].Trim()
        $v = $parts[1].Trim()
        $v = [regex]::Replace($v, '^("|\'')(.*)\1$', '$2')
        if (-not [string]::IsNullOrWhiteSpace($k)) { $envMap[$k] = $v }
    }
    return $envMap
}

function Test-WriteableDir {
    param([string]$FullPath)
    if (-not (Test-Path -LiteralPath $FullPath -PathType Container)) { return $false }
    $probe = Join-Path $FullPath (".guard_write_test_" + [guid]::NewGuid().ToString("N") + ".tmp")
    try {
        Set-Content -LiteralPath $probe -Value "ok" -Encoding ASCII
        Remove-Item -LiteralPath $probe -Force
        return $true
    } catch {
        return $false
    }
}

if (-not (Test-Path -LiteralPath $PolicyPath -PathType Leaf)) { throw "Policy yok: $PolicyPath" }
if (-not (Test-Path -LiteralPath $DeployPackPath -PathType Container)) { throw "Deploy path yok: $DeployPackPath" }
if (-not (Test-Path -LiteralPath $EnvFilePath -PathType Leaf)) { throw ".env yok: $EnvFilePath" }

$policy = Get-Content -LiteralPath $PolicyPath -Raw | ConvertFrom-Json
$profilePolicy = $policy.profiles.$Profile
if ($null -eq $profilePolicy) { throw "Profile policy yok: $Profile" }

$domainKey = $Domain.ToLowerInvariant()
$domainPolicy = $policy.domains.$domainKey
if ($null -eq $domainPolicy) { Add-Fail "Domain policy disi: $domainKey" }

$observedVersion = [string]$policy.php.observed_version
$minId = [int]$policy.php.minimum_id
$verParts = $observedVersion.Split('.')
$obsMajor = 0; $obsMinor = 0; $obsPatch = 0
if ($verParts.Count -ge 1) { [void][int]::TryParse($verParts[0], [ref]$obsMajor) }
if ($verParts.Count -ge 2) { [void][int]::TryParse($verParts[1], [ref]$obsMinor) }
if ($verParts.Count -ge 3) { [void][int]::TryParse($verParts[2], [ref]$obsPatch) }
$obsId = ($obsMajor * 10000) + ($obsMinor * 100) + $obsPatch
if ($obsId -lt $minId) { Add-Fail "PHP surumu yetersiz. Gozlenen: $observedVersion | Minimum: $($policy.php.minimum)" }

$requiredExt = @($policy.php.required_extensions)
$warnOnlyExt = @($profilePolicy.warn_only_extensions)
foreach ($ext in $requiredExt) {
    $extState = $policy.php.observed_extensions.$ext
    if ($extState -ne $true) {
        if ($warnOnlyExt -contains $ext) { Add-Warn "PHP extension eksik (WARN): $ext" }
        else { Add-Fail "PHP extension eksik: $ext" }
    }
}

foreach ($rel in @($policy.deploy.required_paths)) {
    $full = Join-Path $DeployPackPath $rel
    if (-not (Test-Path -LiteralPath $full)) { Add-Fail "Deploy pakette eksik yol: $rel" }
}

foreach ($rel in @($policy.deploy.writable_dirs)) {
    $full = Join-Path $DeployPackPath $rel
    if (-not (Test-WriteableDir -FullPath $full)) { Add-Fail "Yazilabilir degil: $rel" }
}

if ([bool]$profilePolicy.enforce_runtime_writable_paths) {
    foreach ($rel in @($policy.deploy.runtime_writable_paths)) {
        $full = Join-Path $DeployPackPath $rel
        if (-not (Test-Path -LiteralPath $full -PathType Container)) {
            Add-Fail "Runtime path eksik: $rel (profile=$Profile)"
            continue
        }
        if (-not (Test-WriteableDir -FullPath $full)) {
            Add-Fail "Runtime path yazilabilir degil: $rel (profile=$Profile)"
        }
    }
}

$ignorePatterns = @($profilePolicy.ignore_forbidden_patterns)
$forbiddenPatterns = @($policy.deploy.forbidden_path_patterns)
$allItems = Get-ChildItem -LiteralPath $DeployPackPath -Recurse -Force

foreach ($item in $allItems) {
    $relative = $item.FullName.Substring($DeployPackPath.Length).TrimStart('\\').Replace('\\', '/')

    $ignored = $false
    foreach ($ig in $ignorePatterns) {
        if ($relative -match $ig) { $ignored = $true; break }
    }
    if ($ignored) { continue }

    foreach ($pattern in $forbiddenPatterns) {
        if ($relative -match $pattern) { Add-Fail "Yasak dosya/yol bulundu: $relative"; break }
    }
}

$envMap = Parse-EnvFile -Path $EnvFilePath
foreach ($k in @($policy.env.required_keys)) {
    if (-not $envMap.ContainsKey($k) -or [string]::IsNullOrWhiteSpace([string]$envMap[$k])) {
        Add-Fail ".env eksik veya bos: $k"
    }
}

if ([bool]$profilePolicy.enforce_exact_env_values) {
    foreach ($p in $policy.env.exact_values.PSObject.Properties) {
        $k = [string]$p.Name
        $expected = [string]$p.Value
        $actual = [string]($envMap[$k])
        if ($actual.ToLowerInvariant() -ne $expected.ToLowerInvariant()) {
            Add-Fail "$k beklenen degil. Beklenen: $expected | Gelen: $actual"
        }
    }
}

if ([bool]$profilePolicy.enforce_https_app_url -and $envMap.ContainsKey("APP_URL")) {
    $appUrl = [string]$envMap["APP_URL"]
    if (-not $appUrl.ToLowerInvariant().StartsWith("https://")) { Add-Fail "APP_URL https ile baslamali." }
}

if ([bool]$profilePolicy.enforce_domain_in_app_url -and $envMap.ContainsKey("APP_URL") -and $domainPolicy -ne $null) {
    $appUrl = [string]$envMap["APP_URL"]
    if (-not $appUrl.ToLowerInvariant().Contains($domainKey)) {
        Add-Fail "APP_URL domain ile uyusmuyor. Domain: $domainKey | APP_URL: $appUrl"
    }
}

if ([bool]$profilePolicy.enforce_domain_db_match -and $envMap.ContainsKey("DB_DATABASE") -and $domainPolicy -ne $null) {
    $dbName = [string]$envMap["DB_DATABASE"]
    if ($dbName -ne [string]$domainPolicy.db_name) {
        Add-Fail "DB_DATABASE domain ile uyusmuyor. Beklenen: $($domainPolicy.db_name) | Gelen: $dbName"
    }
}

if ($envMap.ContainsKey("DB_HOST")) {
    $dbHost = [string]$envMap["DB_HOST"]
    $allowedDbHosts = @($profilePolicy.allowed_db_hosts)
    if ($allowedDbHosts -notcontains $dbHost) { Add-Fail "DB_HOST policy disi: $dbHost" }
}

if ([bool]$profilePolicy.enforce_disallowed_values_scan) {
    $badValues = @($policy.deploy.disallowed_values)
    foreach ($k in $envMap.Keys) {
        $val = [string]$envMap[$k]
        if ([string]::IsNullOrWhiteSpace($val)) { continue }
        if ($badValues -contains $val.ToLowerInvariant()) { Add-Fail "Riskli placeholder deger bulundu: $k=$val" }
    }
}

Write-Host "STATUS: " -NoNewline
if ($Failures.Count -eq 0) { Write-Host "PASS" } else { Write-Host "FAIL" }
Write-Host "PROFILE: $Profile"
Write-Host "DOMAIN: $domainKey"
if ($domainPolicy -ne $null) {
    Write-Host "DOCROOT POLICY: $($domainPolicy.docroot)"
    Write-Host "DB POLICY: $($domainPolicy.db_name)"
}
Write-Host "PHP OBSERVED: $observedVersion"
Write-Host ("-" * 60)

if ($Failures.Count -gt 0) {
    Write-Host "FAIL ITEMS:"
    for ($i = 0; $i -lt $Failures.Count; $i++) { Write-Host ("{0}. {1}" -f ($i + 1), $Failures[$i]) }
}

if ($Warnings.Count -gt 0) {
    Write-Host "WARNINGS:"
    for ($i = 0; $i -lt $Warnings.Count; $i++) { Write-Host ("{0}. {1}" -f ($i + 1), $Warnings[$i]) }
}

if ($Failures.Count -gt 0) { exit 1 }
exit 0
