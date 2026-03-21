# TASK-091

Durum: `DOING`  
Ajan: `codex`  
Branch: `agent/codex/task-091`  
Baslangic: `2026-03-21 06:32`

## Gorev Ozeti
- Merge treni: TASK-085, TASK-086, TASK-087, TASK-088 ve TASK-090 zincirini kontrollu sekilde ana hatta tasiyip runtime dogrulamasini tamamla

## Task Karari
- [ ] mevcut task devam
- [ ] task genisletme
- [ ] yeni task

## In Scope
- [ ] Degisiklik 1
- [ ] Degisiklik 2

## Out of Scope
- [ ] Konu disi 1
- [ ] Konu disi 2

## Lock Dosyalari
- `docs/tasks/TASK-091.md`
- `docs/TASK_LOCKS.md`
- `docs/NEXT_TASK.md`
- `docs/WORKLOG.md`
- `docs/SESSION_HANDOFF_TR.md`
- `scripts/smoke-test.ps1`
- `scripts/validate.ps1`
- `docs/category-catalog/ready/locations_v1/manifest_v1.json`
- `docs/DEMO_FIXTURE_STANDARD_TR.md`
- `docs/demo-media/bando-review/**`
- `local-rebuild/apps/orkestram/routes/console.php`
- `local-rebuild/apps/izmirorkestra/routes/console.php`
- `local-rebuild/apps/orkestram/database/seeders/**`
- `local-rebuild/apps/izmirorkestra/database/seeders/**`
- `local-rebuild/apps/orkestram/tests/Feature/**`
- `local-rebuild/apps/izmirorkestra/tests/Feature/**`
- `local-rebuild/apps/orkestram/app/Models/Listing.php`
- `local-rebuild/apps/izmirorkestra/app/Models/Listing.php`
- `local-rebuild/apps/orkestram/app/Http/Controllers/PublicController.php`
- `local-rebuild/apps/izmirorkestra/app/Http/Controllers/PublicController.php`
- `local-rebuild/apps/orkestram/resources/views/frontend/listing.blade.php`
- `local-rebuild/apps/izmirorkestra/resources/views/frontend/listing.blade.php`
- `local-rebuild/apps/orkestram/resources/views/frontend/partials/listing-card.blade.php`
- `local-rebuild/apps/izmirorkestra/resources/views/frontend/partials/listing-card.blade.php`

## Preview Kontrati
- Lane: `main | design-preview | n/a`
- Preview URL: `n/a`
- Mount Source: `n/a`
- Edit Source: `n/a`
- UI review gerekir mi?: `yes | no`
- UI Review Durumu: `pending | revize | approved | n/a`
- Revize Notu: `n/a`

## Uygulama Adimlari
- [ ] Zorunlu dokumanlar okundu: `AGENTS.md`, `docs/REPO_DISCIPLINE_TR.md`, `docs/MULTI_AGENT_RULES_TR.md`
- [ ] Branch dogrulandi: `agent/<ajan>/<task-id>`
- [ ] Lock kapsam disina cikilmadi
- [ ] Gorev kapsamindaki degisiklikler tamamlandi
- [ ] Goreve ozel test/dogrulama calistirildi

## Kabul Kriterleri
- [ ] Beklenen davranis 1
- [ ] Beklenen davranis 2
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
powershell -ExecutionPolicy Bypass -File scripts/close-task.ps1 -TaskId TASK-091 -Agent agent-name -ClosureNote "kisa kapanis ozeti" -WorklogTitle "baslik" -WorklogSummary "madde-1" -Files "dosya-1" -Commands "komut-1" -Result PASS
```

## Risk / Not
- Riskler ve geri donus notu

