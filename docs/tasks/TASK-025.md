# TASK-025

Durum: `DONE`  
Ajan: `codex-a`  
Branch: `agent/codex-a/task-025`  
Baslangic: `2026-03-14 00:00`

## Ozet
- Fiyat alanini serbest metinden yapisal modele tasi (geri uyumlu): `price_label` korunurken `price_min`, `price_max`, `currency`, `price_type` eklensin.

## In Scope
- [x] Iki appte DB/model katmanina yapisal fiyat alanlari eklendi.
- [x] Admin ve owner form validasyonlari parity olacak sekilde guncellendi.
- [x] Kayit/guncelleme akisinda `price_label` + yapisal alanlar birlikte korundu.
- [x] Temel feature test ile fiyat alanlarinin kaydi dogrulandi.
- [x] `pre-pr -Mode quick` PASS.

## Out of Scope
- [ ] Public filtre/siralama davranisi (TASK-026).
- [ ] SEO structured data (TASK-027).
- [ ] Tarihsel toplu backfill komutu.

## Lock Dosyalari
- `docs/tasks/TASK-025.md`
- `local-rebuild/apps/orkestram/database/migrations/**`
- `local-rebuild/apps/izmirorkestra/database/migrations/**`
- `local-rebuild/apps/orkestram/app/Models/Listing.php`
- `local-rebuild/apps/izmirorkestra/app/Models/Listing.php`
- `local-rebuild/apps/orkestram/resources/views/admin/listings/form.blade.php`
- `local-rebuild/apps/izmirorkestra/resources/views/admin/listings/form.blade.php`
- `local-rebuild/apps/orkestram/resources/views/portal/owner/listings-create.blade.php`
- `local-rebuild/apps/izmirorkestra/resources/views/portal/owner/listings-create.blade.php`
- `local-rebuild/apps/orkestram/resources/views/portal/owner/listings-edit.blade.php`
- `local-rebuild/apps/izmirorkestra/resources/views/portal/owner/listings-edit.blade.php`
- `local-rebuild/apps/orkestram/tests/Feature/**`
- `local-rebuild/apps/izmirorkestra/tests/Feature/**`

## Kabul Kriteri
- [x] Admin/owner panelinde fiyat alanlari ayni kuralla calisir.
- [x] `price_label` geriye uyumlu kalir.
- [x] Yeni yapisal alanlar DB'de kalici saklanir.
- [x] `pre-pr` PASS.

## Komutlar
```powershell
powershell -ExecutionPolicy Bypass -File D:\orkestram\scripts\pre-pr.ps1 -Mode quick
```

## Notlar
- Risk: mevcut veriyi bozmamak icin migration nullable + geriye uyumlu olmalidir.
- Kapanis kaniti:
  - Branch: `agent/codex-a/task-025`
  - Commit: `3ff02ff`
  - PR: `https://github.com/bekiryara/orkestram/pull/new/agent/codex-a/task-025`
  - `pre-pr -Mode quick` -> PASS
