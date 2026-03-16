# TASK-051

Durum: `DONE`  
Ajan: `codex-c`  
Branch: `agent/codex-c/task-051`  
Baslangic: `2026-03-16 17:45`
Kapanis: `2026-03-16 18:30`

## Ozet
- Listing gorsellerinin kaybolmasina neden olan runtime dosya hattini kanitlamak ve onarmak.

## In Scope
- [ ] `ListingMediaService` iki appte path mantigini incelemek.
- [ ] `public/uploads`, `storage/app/public`, `public/storage` ve container icindeki fiziksel dosya hattini kanitlamak.
- [ ] Gerekirse admin upload akislarinda minimal teknik fix onermek/uygulamak.

## Out of Scope
- [ ] Listing detail bilgi hiyerarsisi degisikligi
- [ ] Yeni gorsel feature'i eklemek

## Lock Dosyalari
- `local-rebuild/apps/orkestram/app/Services/Listings/ListingMediaService.php`
- `local-rebuild/apps/izmirorkestra/app/Services/Listings/ListingMediaService.php`
- `local-rebuild/apps/orkestram/public/uploads/**`
- `local-rebuild/apps/izmirorkestra/public/uploads/**`
- `local-rebuild/apps/orkestram/storage/app/public/**`
- `local-rebuild/apps/izmirorkestra/storage/app/public/**`
- `local-rebuild/apps/orkestram/public/storage`
- `local-rebuild/apps/izmirorkestra/public/storage`
- `docs/tasks/TASK-051.md`

## Kabul Kriteri
- [x] `cover_image_path` gercek dosyaya karsilik gelir.
- [x] Runtime/container icinde gorseller fiziksel olarak mevcuttur.
- [x] Fallback disinda gercek gorseller tekrar gorunur.

## Komutlar
```powershell
powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick
```

## Kapanis Notu
- Ajan teslimi `87b3834` (`fix(task-051): move listing media uploads to public storage`) commit'i ile upstream'e pushlandi.
- `ListingMediaService` iki appte `Storage::disk('public')` hattina tasindi; DB `cover_image_path` degeri `storage/uploads/listings/...` formatina alindi.
- `AdminListingMediaFlowTest` iki appte PASS; koordinator entegrasyonu `eebb322` merge commit'i ile `agent/codex/task-048` branch'ine alindi.
