# TASK-023

Durum: `DONE`  
Ajan: `codex-a`  
Branch: `agent/codex-a/task-023`  
Baslangic: `2026-03-14`  
Bitis: `2026-03-14`

## Ozet
- Servis bolgesi alani admin + owner formlarinda il/ilce bazli coklu secim olarak yenilendi.
- Kayit formati geriye uyumlu tutuldu (`service_areas_text` satir formati devam ediyor).
- `ListingCoverageService` yeni JSON tabanli payloadi da okuyacak sekilde uyumlulukla genisletildi.

## Lock Dosyalari
- `docs/tasks/TASK-023.md`
- `local-rebuild/apps/orkestram/resources/views/admin/listings/form.blade.php`
- `local-rebuild/apps/izmirorkestra/resources/views/admin/listings/form.blade.php`
- `local-rebuild/apps/orkestram/resources/views/portal/owner/listings-create.blade.php`
- `local-rebuild/apps/izmirorkestra/resources/views/portal/owner/listings-create.blade.php`
- `local-rebuild/apps/orkestram/resources/views/portal/owner/listings-edit.blade.php`
- `local-rebuild/apps/izmirorkestra/resources/views/portal/owner/listings-edit.blade.php`
- `local-rebuild/apps/orkestram/app/Services/Listings/ListingCoverageService.php`
- `local-rebuild/apps/izmirorkestra/app/Services/Listings/ListingCoverageService.php`

## Kabul Kriteri
- [x] Servis bolgesi il/ilce coklu secim davranisi admin + owner formlarinda ayni.
- [x] Eski satir bazli format bozulmadan calisiyor.
- [x] `pre-pr -Mode quick` PASS.
