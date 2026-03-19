# TASK-079

Durum: `DONE`  
Ajan: `codex-a`  
Branch: `agent/codex-a/task-079`  
Baslangic: `2026-03-19 19:11`
Kapanis: `2026-03-19 21:32`

## Gorev Ozeti
- Paket 01 kapsaminda owner service area / coverage write-path baglanti eksigi tamamlanacak

## Task Karari
- [ ] mevcut task devam
- [ ] task genisletme
- [x] yeni task

## In Scope
- [x] Owner create/edit contractinde `coverage_mode` ve service area payload'i request/store/update akisina baglandi
- [x] Owner edit ekranina mevcut coverage/service area verisi geri dolduruldu
- [x] Owner coverage write-path icin feature test eklendi ve mevcut owner panel testi genisletildi

## Out of Scope
- [ ] Yeni coverage modeli icat etmek
- [ ] Admin coverage veya public filtre mantigini bastan yazmak
- [ ] Fiyat, attribute veya fixture paketlerine gecmek

## Lock Dosyalari
- `docs/tasks/TASK-079.md`
- `docs/TASK_LOCKS.md`
- `docs/NEXT_TASK.md`
- `local-rebuild/apps/orkestram/app/Http/Controllers/Owner/OwnerDashboardController.php`
- `local-rebuild/apps/orkestram/resources/views/portal/owner/listings-create.blade.php`
- `local-rebuild/apps/orkestram/resources/views/portal/owner/listings-edit.blade.php`
- `local-rebuild/apps/orkestram/tests/Feature/OwnerPanelActionsTest.php`

## Preview Kontrati
- Lane: `n/a`
- Preview URL: `n/a`
- Mount Source: `n/a`
- Edit Source: `n/a`
- UI review gerekir mi?: `no`
- UI Review Durumu: `n/a`
- Revize Notu: `n/a`

## Uygulama Adimlari
- [x] Zorunlu dokumanlar okundu: `AGENTS.md`, `docs/REPO_DISCIPLINE_TR.md`, `docs/MULTI_AGENT_RULES_TR.md`
- [x] Branch dogrulandi: `agent/<ajan>/<task-id>`
- [x] Lock kapsam disina cikilmadi
- [x] Gorev kapsamindaki degisiklikler tamamlandi
- [x] Goreve ozel test/dogrulama calistirildi

## Kabul Kriterleri
- [x] Owner create ekranindan `coverage_mode` kaydedilir
- [x] Owner create ekranindan service area secimi kaydedilir
- [x] Owner edit ekraninda mevcut coverage/service area verisi geri dolar
- [x] Public city/district coverage sonucu owner kaydiyla uyumlu davranir
- [x] `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick` PASS

## Teslimde Zorunlu Kanit
- [x] `git branch --show-current`
- [x] `git branch -vv`
- [x] `git status --short`
- [x] `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick`
- [x] Goreve ozel test/komut sonucu
- [x] `Edit Source == Mount Source` kaniti
- [x] Commit hash

## Kapanis Adimlari
- [x] Task kartindaki checklistler gercek sonuca gore guncellendi
- [ ] `docs/WORKLOG.md` guncellendi
- [ ] `docs/TASK_LOCKS.md` kaydi `closed` yapildi
- [ ] `docs/NEXT_TASK.md` panosu guncellendi
- [x] Branch pushlandi

## Komutlar
```powershell
powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick
powershell -ExecutionPolicy Bypass -File scripts/close-task.ps1 -TaskId TASK-079 -Agent codex-a -ClosureNote "kisa kapanis ozeti" -WorklogTitle "baslik" -WorklogSummary "madde-1" -Files "dosya-1" -Commands "komut-1" -Result PASS
```

## Risk / Not
- Risk, owner payload ve view alan adlarinin mevcut coverage akisi ile birebir eslesmemesi; minimum degisiklikle sadece owner write-path baglanti eksigi kapatilacak.

## Kapanis Notu
- Owner create/update write-path'ine `coverage_mode` ve `service_areas_text` baglandi; kayit sonrasi `ListingCoverageService::syncFromRawText(...)` ile service area yazimi aktiflestirildi.
- Owner edit ekraninda mevcut service area secimleri relation uzerinden geri dolduruldu ve owner panel feature testi coverage yazim senaryosu ile genisletildi.
- Teslim kaniti: `git branch --show-current` => `agent/codex-a/task-079`, `git branch -vv` => `origin/agent/codex-a/task-079: ahead 1`, `git status --short` => temiz, `pre-pr -Mode quick` => PASS, commit => `8d06a46`.
- Merkezi kapanis dosyalari (`docs/WORKLOG.md`, `docs/TASK_LOCKS.md`, `docs/NEXT_TASK.md`) koordinator alaninda kaldigi icin owner tesliminde degistirilmedi.
