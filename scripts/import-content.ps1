param(
    [ValidateSet("orkestram", "izmirorkestra")]
    [string]$App,

    [ValidateSet("pages", "listings", "city-pages")]
    [string]$Type,

    [Parameter(Mandatory = $true)]
    [string]$CsvPath,

    [switch]$Published
)

$ErrorActionPreference = "Stop"

if (-not (Test-Path -LiteralPath $CsvPath -PathType Leaf)) {
    throw "CSV bulunamadi: $CsvPath"
}

$container = if ($App -eq "orkestram") { "orkestram-local-web" } else { "izmirorkestra-local-web" }
$site = if ($App -eq "orkestram") { "orkestram.net" } else { "izmirorkestra.net" }
$tmp = "/tmp/import.csv"

# Copy CSV into container and run import command.
docker cp $CsvPath "${container}:$tmp" | Out-Null

$publishedArg = if ($Published) { " --published" } else { "" }
$cmd = "cd /var/www/html && php artisan content:import $Type $tmp --site=$site$publishedArg"
docker exec $container sh -lc $cmd

Write-Host "[import-content] PASS app=$App type=$Type site=$site csv=$CsvPath"
