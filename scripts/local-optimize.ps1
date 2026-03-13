Param(
    [switch]$IncludeIzmir
)

$ErrorActionPreference = "Stop"

Write-Host "Orkestram cache temizleme + optimize basliyor..."
docker exec orkestram-local-web php artisan optimize:clear
docker exec orkestram-local-web php artisan config:cache
docker exec orkestram-local-web php artisan route:cache

if ($IncludeIzmir) {
    Write-Host "Izmir cache temizleme + optimize basliyor..."
    docker exec izmirorkestra-local-web php artisan optimize:clear
    docker exec izmirorkestra-local-web php artisan config:cache
    docker exec izmirorkestra-local-web php artisan route:cache
}

Write-Host "Bitti."
