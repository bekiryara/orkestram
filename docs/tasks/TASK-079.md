# TASK-079

Durum: `DOING`  
Ajan: `codex-a`  
Branch: `agent/codex-a/task-079`  
Baslangic: `2026-03-19 19:11`

## Gorev Ozeti
- Paket 01 kapsaminda owner service area / coverage write-path baglanti eksigi tamamlanacak

## Task Karari
- [ ] mevcut task devam
- [ ] task genisletme
- [x] yeni task

## In Scope
- [ ] Owner create/edit contractinde `coverage_mode` ve service area payload'i request/store/update akisina baglanacak
- [ ] Owner edit ekranina mevcut coverage/service area verisi geri doldurulacak
- [ ] Owner coverage write-path icin feature test eklenecek veya mevcut test genisletilecek

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
- [ ] Zorunlu dokumanlar okundu: `AGENTS.md`, `docs/REPO_DISCIPLINE_TR.md`, `docs/MULTI_AGENT_RULES_TR.md`
- [ ] Branch dogrulandi: `agent/<ajan>/<task-id>`
- [ ] Lock kapsam disina cikilmadi
- [ ] Gorev kapsamindaki degisiklikler tamamlandi
- [ ] Goreve ozel test/dogrulama calistirildi

## Kabul Kriterleri
- [ ] Owner create ekranindan `coverage_mode` kaydedilir
- [ ] Owner create ekranindan service area secimi kaydedilir
- [ ] Owner edit ekraninda mevcut coverage/service area verisi geri dolar
- [ ] Public city/district coverage sonucu owner kaydiyla uyumlu davranir
- [ ] `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick` PASS

## Teslimde Zorunlu Kanit
- [ ] `git branch --show-current`
- [ ] `git branch -vv`
- [ ] `git status --short`
- [ ] `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick`
- [ ] Goreve ozel test/komut sonucu
- [ ] `Edit Source == Mount Source` kaniti
- [ ] Commit hash

## Kapanis Adimlari
- [ ] Task kartindaki checklistler gercek sonuca gore guncellendi
- [ ] `docs/WORKLOG.md` guncellendi
- [ ] `docs/TASK_LOCKS.md` kaydi `closed` yapildi
- [ ] `docs/NEXT_TASK.md` panosu guncellendi
- [ ] Branch pushlandi

## Komutlar
```powershell
powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick
powershell -ExecutionPolicy Bypass -File scripts/close-task.ps1 -TaskId TASK-079 -Agent codex-a -ClosureNote "kisa kapanis ozeti" -WorklogTitle "baslik" -WorklogSummary "madde-1" -Files "dosya-1" -Commands "komut-1" -Result PASS
```

## Risk / Not
- Risk, owner payload ve view alan adlarinin mevcut coverage akisi ile birebir eslesmemesi; minimum degisiklikle sadece owner write-path baglanti eksigi kapatilacak.
