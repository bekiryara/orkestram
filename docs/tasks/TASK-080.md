# TASK-080

Durum: `DONE`  
Ajan: `codex`  
Branch: `agent/codex/task-080`  
Baslangic: `2026-03-19 23:42`

## Gorev Ozeti
- TASK-079 owner coverage write-path parity tamamlama ve merge karari icin iki app owner write-path hizasi

## Task Karari
- [ ] mevcut task devam
- [x] task genisletme
- [ ] yeni task

## In Scope
- [x] `TASK-079` owner coverage write-path degisikligi `izmirorkestra` owner flowuna tasinacak
- [x] `orkestram` ve `izmirorkestra` owner coverage write-pathi ayni branchte hizalanacak
- [x] Parity sonucu merge karari icin dogrulama ve merkezi kapanis kaydi uretilecek

## Out of Scope
- [ ] Yeni coverage modeli icat etmek
- [ ] Public coverage algoritmasini bastan yazmak
- [ ] UI preview/review lane gorevi acmak

## Lock Dosyalari
- `docs/tasks/TASK-080.md`
- `docs/TASK_LOCKS.md`
- `docs/NEXT_TASK.md`
- `local-rebuild/apps/izmirorkestra/app/Http/Controllers/Owner/OwnerDashboardController.php`
- `local-rebuild/apps/izmirorkestra/resources/views/portal/owner/listings-create.blade.php`
- `local-rebuild/apps/izmirorkestra/resources/views/portal/owner/listings-edit.blade.php`
- `local-rebuild/apps/izmirorkestra/tests/Feature/OwnerPanelActionsTest.php`
- `local-rebuild/apps/orkestram/app/Http/Controllers/Owner/OwnerDashboardController.php`
- `local-rebuild/apps/orkestram/resources/views/portal/owner/listings-create.blade.php`
- `local-rebuild/apps/orkestram/resources/views/portal/owner/listings-edit.blade.php`
- `local-rebuild/apps/orkestram/tests/Feature/OwnerPanelActionsTest.php`
- `docs/SESSION_HANDOFF_TR.md`
- `docs/WORKLOG.md`

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
- [x] `izmirorkestra` owner create/edit akisi `coverage_mode` ve `service_areas_text` write-pathini tasir
- [x] `orkestram` ve `izmirorkestra` owner coverage write-pathi ayni branchte birlikte bulunur
- [x] Owner coverage parity sonucu merge karari netlesir
- [x] `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick` PASS

## Teslimde Zorunlu Kanit
- [x] `git branch --show-current`
- [x] `git branch -vv`
- [x] `git status --short`
- [x] `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick`
- [x] Goreve ozel test/komut sonucu
- [ ] `Edit Source == Mount Source` kaniti
- [x] Commit hash

## Kapanis Adimlari
- [x] Task kartindaki checklistler gercek sonuca gore guncellendi
- [x] `docs/WORKLOG.md` guncellendi
- [x] `docs/TASK_LOCKS.md` kaydi `closed` yapildi
- [x] `docs/NEXT_TASK.md` panosu guncellendi
- [x] Branch pushlandi

## Komutlar
```powershell
powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick
docker exec orkestram-local-web php artisan test --filter=OwnerPanelActionsTest
docker exec izmirorkestra-local-web php artisan test --filter=OwnerPanelActionsTest
```

## Risk / Not
- Risk, parity icin ayni owner write-path degisiklikleri iki appte ayni branchte toplanirken merkezi koordinasyon kayitlari ile cakisma uretmekti; dar lock ile yalniz owner coverage zinciri tasindi.
- `TASK-079` owner icerigi parity branch'ine tasindigi icin merge karari artik `agent/codex/task-080` uzerinden verilecektir.
