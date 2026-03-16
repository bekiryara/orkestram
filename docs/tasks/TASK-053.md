# TASK-053

Durum: `DONE`  
Ajan: `codex`  
Branch: `agent/codex/task-053`  
Baslangic: `2026-03-16 22:25`
Bitis: `2026-03-16 23:05`

## Ozet
- `TASK-052` altinda sabitlenen medya hardening planini kod tarafinda uygulamak.
- Canonical repo `/home/bekir/orkestram` icinde baglanti kopmasi nedeniyle commitlenmeden kalan media WIP'yi resmi worktree'ye tasimak.

## In Scope
- [x] Canonical repodaki media WIP kapsam dosyalarini tespit etmek.
- [x] Listing/profil/kart/galeri medya path, render ve delete davranisini iki appte standartlastirmak.
- [x] `MediaPath` helper, media servisleri ve ilgili blade/test dosyalarini parity ile tamamlamak.
- [x] Runtime'i `orkestram-k` worktree'ye hizalayip CI + media testlerini dogrulamak.
- [x] Task/lock/pano kayitlarini resmi disipline gore guncel tutmak.

## Out of Scope
- [ ] CTA/layout polish
- [ ] Medya disi kapsamli portal refactoru
- [ ] Canonical repo icindeki alakasiz kirli degisiklikleri temizlemek

## Lock Dosyalari
- `docs/tasks/TASK-053.md`
- `docs/TASK_LOCKS.md`
- `docs/NEXT_TASK.md`
- `docs/WORKLOG.md`
- `local-rebuild/apps/orkestram/app/Http/Controllers/Auth/PortalAuthController.php`
- `local-rebuild/apps/orkestram/app/Services/Listings/ListingMediaService.php`
- `local-rebuild/apps/orkestram/app/Support/MediaPath.php`
- `local-rebuild/apps/orkestram/resources/views/admin/listings/form.blade.php`
- `local-rebuild/apps/orkestram/resources/views/admin/listings/index.blade.php`
- `local-rebuild/apps/orkestram/resources/views/frontend/city-page.blade.php`
- `local-rebuild/apps/orkestram/resources/views/frontend/listing.blade.php`
- `local-rebuild/apps/orkestram/resources/views/frontend/partials/listing-card.blade.php`
- `local-rebuild/apps/orkestram/resources/views/portal/messages/center-content.blade.php`
- `local-rebuild/apps/orkestram/tests/Feature/AdminListingMediaFlowTest.php`
- `local-rebuild/apps/orkestram/tests/Feature/ListingMediaRenderingTest.php`
- `local-rebuild/apps/orkestram/tests/Feature/PortalProfileMediaTest.php`
- `local-rebuild/apps/izmirorkestra/app/Http/Controllers/Auth/PortalAuthController.php`
- `local-rebuild/apps/izmirorkestra/app/Services/Listings/ListingMediaService.php`
- `local-rebuild/apps/izmirorkestra/app/Support/MediaPath.php`
- `local-rebuild/apps/izmirorkestra/resources/views/admin/listings/form.blade.php`
- `local-rebuild/apps/izmirorkestra/resources/views/admin/listings/index.blade.php`
- `local-rebuild/apps/izmirorkestra/resources/views/frontend/city-page.blade.php`
- `local-rebuild/apps/izmirorkestra/resources/views/frontend/listing.blade.php`
- `local-rebuild/apps/izmirorkestra/resources/views/frontend/partials/listing-card.blade.php`
- `local-rebuild/apps/izmirorkestra/resources/views/portal/messages/center-content.blade.php`
- `local-rebuild/apps/izmirorkestra/tests/Feature/AdminListingMediaFlowTest.php`
- `local-rebuild/apps/izmirorkestra/tests/Feature/ListingMediaRenderingTest.php`
- `local-rebuild/apps/izmirorkestra/tests/Feature/PortalProfileMediaTest.php`

## Kabul Kriteri
- [x] Iki appte DB media path formati `storage/uploads/...` standardinda sabitlendi.
- [x] Legacy `uploads/...` kayitlari render ve update akisinda deterministic normalize edildi.
- [x] Listing media update/delete/reorder testleri PASS verdi.
- [x] Profil ve listing render fallback davranislari yeni testlerle dogrulandi.
- [x] Runtime `orkestram-k` mount'u ile calisti ve `pre-pr -Mode quick` PASS verdi.

## Komutlar
```powershell
git fetch --all --prune
git checkout -b agent/codex/task-053 origin/main
```

## Notlar
- Kaynak WIP kaniti: canonical repo `/home/bekir/orkestram` icinde commitlenmemis media refactoru mevcut ve runtime su anda onu mount ediyor.
- Bu gorevin ilk hedefi canonical WIP'yi resmi worktree'ye tasiyip sahipsiz durumu bitirmektir.


## Kapanis Kaniti
1. `git branch --show-current` => `agent/codex/task-053`
2. `git branch -vv` => `origin/agent/codex/task-053` upstream aktif
3. `git status --short` => yalniz task kapsamindaki degisiklikler
4. `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick` => `PASS`
