# TASK-026

Durum: `DONE`  
Ajan: `codex-b`  
Branch: `agent/codex-b/task-026`  
Baslangic: `2026-03-14 00:00`

## Ozet
- Public listing akisi icin fiyat sort/filtreyi deterministik hale getir.

## In Scope
- [x] `/ilanlar` sayfasina fiyat siralama secenekleri eklenir (artan/azalan).
- [x] `/ilanlar` ve hizmet kategori sayfalarina fiyat filtreleme eklenir.
- [x] Filtre URL query param ile calisir; sayfa yenilenince secim korunur.
- [x] Iki app parity saglanir.
- [x] Fiyat yok kayitlar fallback kuraliyla yonetilir (liste sonu vb. sabit kural).
- [x] `pre-pr -Mode quick` PASS.

## Out of Scope
- [ ] Admin/owner fiyat veri modeli degisikligi (TASK-025).
- [ ] SEO structured data (TASK-027).

## Lock Dosyalari
- `docs/tasks/TASK-026.md`
- `local-rebuild/apps/orkestram/app/Http/Controllers/PublicController.php`
- `local-rebuild/apps/izmirorkestra/app/Http/Controllers/PublicController.php`
- `local-rebuild/apps/orkestram/resources/views/frontend/listings.blade.php`
- `local-rebuild/apps/izmirorkestra/resources/views/frontend/listings.blade.php`
- `local-rebuild/apps/orkestram/resources/views/frontend/service-category.blade.php`
- `local-rebuild/apps/izmirorkestra/resources/views/frontend/service-category.blade.php`
- `local-rebuild/apps/orkestram/tests/Feature/**`
- `local-rebuild/apps/izmirorkestra/tests/Feature/**`

## Kabul Kriteri
- [x] Fiyat sort/filtre iki appte ayni davranir.
- [x] URL parametreleri kalicidir.
- [x] Smoke ve quick testler PASS.

## Komutlar
```powershell
powershell -ExecutionPolicy Bypass -File D:\orkestram\scripts\pre-pr.ps1 -Mode quick
```

## Notlar
- Geriye uyum icin mevcut `price_label` destegi bozulmamalidir.
