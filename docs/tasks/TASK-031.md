# TASK-031

Durum: `DONE`  
Ajan: `codex`  
Branch: `agent/codex/task-031`  
Baslangic: `2026-03-15 02:53`  
Bitis: `2026-03-15 06:35`

## Ozet
- Admin ve owner tarafinda listing gorsel yukleme/silme/gosterim hattini iki projede (hos-stack/orkestram) bozulma yaratmadan calisir hale getirmek.
- Admin edit ekranindaki 500 hatasini model tarafinda deterministic sekilde giderip iki app parity korumak.

## In Scope
- [x] Mevcut listing gorsel akisini (upload/delete/display) admin ve owner formlarinda iki app parity ile analiz etmek.
- [x] Hata noktalarini minimal etkili kod degisimiyle duzeltmek (varolan davranisi koruyarak).
- [x] Iki appte smoke + ilgili hedef testlerle akisin calistigini dogrulamak.
- [x] Task/lock/pano kayitlarini resmi disipline gore guncel tutmak.

## Out of Scope
- [ ] UI redesign veya kapsam disi stil degisikligi
- [ ] Listing gorsel disi modullerde refactor
- [ ] Container/topoloji degisimi

## Lock Dosyalari
- `docs/tasks/TASK-031.md`
- `docs/TASK_LOCKS.md`
- `docs/NEXT_TASK.md`
- `docs/WORKLOG.md`
- `local-rebuild/apps/orkestram/app/Http/Controllers/**`
- `local-rebuild/apps/izmirorkestra/app/Http/Controllers/**`
- `local-rebuild/apps/orkestram/resources/views/admin/listings/**`
- `local-rebuild/apps/izmirorkestra/resources/views/admin/listings/**`
- `local-rebuild/apps/orkestram/resources/views/portal/owner/**`
- `local-rebuild/apps/izmirorkestra/resources/views/portal/owner/**`
- `local-rebuild/apps/orkestram/tests/Feature/**`
- `local-rebuild/apps/izmirorkestra/tests/Feature/**`
- `local-rebuild/apps/orkestram/app/Models/Listing.php`
- `local-rebuild/apps/izmirorkestra/app/Models/Listing.php`

## Kabul Kriteri
- [x] Admin listing formunda gorsel yukleme ve silme akisi calisiyor.
- [x] Owner listing create/edit ekranlarinda gorsel yukleme ve silme akisi calisiyor.
- [x] Public listing detayinda gorseller dogru gosteriliyor.
- [x] Admin listing edit 500 hatasi (temp upload + request has/hasAny etkisi) deterministic model duzeltmesi ile giderildi.
- [x] `powershell -ExecutionPolicy Bypass -File D:\orkestram\scripts\pre-pr.ps1 -Mode quick` => PASS

## Komutlar
```powershell
powershell -ExecutionPolicy Bypass -File D:\orkestram\scripts\pre-pr.ps1 -Mode quick
```

## Notlar
- Temel ilke: "varolan calisan davranisi bozma", minimal ve hedef odakli duzeltme.
- `git fetch --all --prune` bu turda ag erisimi nedeniyle tamamlanamadi.

## Kapanis Kaniti
1. `git branch --show-current` => `agent/codex/task-031`
2. `git status --short` => temiz (commit sonrasi)
3. `powershell -ExecutionPolicy Bypass -File D:\orkestram\scripts\pre-pr.ps1 -Mode quick` => `PASS`
