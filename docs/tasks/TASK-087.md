# TASK-087

Durum: `DOING`  
Ajan: `codex`  
Branch: `agent/codex/task-087`  
Baslangic: `2026-03-20 09:31`

## Gorev Ozeti
- Masaustu bando fotograflarini repo ici canonical review-demo medya setine cevir ve rebuild sonrasi eksiksiz geri gelen deterministic medya hattini tamamla

## Task Karari
- [ ] mevcut task devam
- [ ] task genisletme
- [x] yeni task

## In Scope
- [x] Masaustu bando foto klasorunden review demo icin secili medya setini repo icine almak
- [x] Provenance manifesti ile desktop -> repo eslesmesini kaydetmek
- [x] `demo:prepare-bando-review-fixture` komutunu fiziksel medya sync yapacak sekilde genisletmek
- [x] Iki appte parity fixture testine fiziksel dosya kaniti eklemek
- [x] Rebuild sonrasi review demo medyanin desktop klasoru olmadan geri gelmesini saglamak

## Out of Scope
- [ ] Yeni UI tasarimi veya preview review acmak
- [ ] Smoke fixture medya setini genisletmek
- [ ] Review demo yorum/like datasetini buyutmek
- [ ] `agent-status` false-positive driftini bu taskta cozmeye calismak

## Lock Dosyalari
- `docs/tasks/TASK-087.md`
- `docs/TASK_LOCKS.md`
- `docs/NEXT_TASK.md`
- `docs/WORKLOG.md`
- `docs/SESSION_HANDOFF_TR.md`
- `docs/DEMO_FIXTURE_STANDARD_TR.md`
- `docs/demo-media/bando-review/**`
- `local-rebuild/apps/orkestram/routes/console.php`
- `local-rebuild/apps/izmirorkestra/routes/console.php`
- `local-rebuild/apps/orkestram/tests/Feature/FixtureCommandTest.php`
- `local-rebuild/apps/izmirorkestra/tests/Feature/FixtureCommandTest.php`
- `local-rebuild/apps/orkestram/database/seeders/data/review_demo_media/**`
- `local-rebuild/apps/izmirorkestra/database/seeders/data/review_demo_media/**`

## Preview Kontrati
- Lane: `n/a`
- Preview URL: `n/a`
- Mount Source: `n/a`
- Edit Source: `n/a`
- UI review gerekir mi?: `no`
- UI Review Durumu: `n/a`
- Revize Notu: `n/a`

## Runtime Kontrati
- Runtime Source: `/home/bekir/orkestram-k`
- Preview Source: `n/a`
- Git Katmani: `WSL`
- Script Katmani: `PowerShell`
- App/Test Katmani: `container`
- Runtime Readiness: `ready`
- Upstream Durumu: `yok (ilk push -u bekleniyor)`
- Not: `Desktop kaynak klasoru yalniz ilk alma icin kullanildi; runtime deterministic kaynak artik repo icindeki tracked media setidir.`

## Uygulama Adimlari
- [x] Zorunlu dokumanlar okundu: `AGENTS.md`, `docs/REPO_DISCIPLINE_TR.md`, `docs/MULTI_AGENT_RULES_TR.md`
- [x] Branch dogrulandi: `agent/<ajan>/<task-id>`
- [x] Lock kapsam disina cikilmadi
- [x] Gorev kapsamindaki degisiklikler tamamlandi
- [x] Goreve ozel test/dogrulama calistirildi

## Kabul Kriterleri
- [x] Review demo medya kaynagi masaustu klasorune bagimli olmadan repo icinde track edilir
- [x] `demo:prepare-bando-review-fixture` komutu canonical medya setini storage altina fiziksel olarak senkronlar
- [x] Komut tekrar calistiginda listing metadata ve fiziksel medya deterministik kalir
- [x] Iki appte parity fixture testleri fiziksel dosya varligini kanitlar
- [ ] `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick` PASS

## Teslimde Zorunlu Kanit
- [ ] `git branch --show-current`
- [ ] `git branch -vv`
- [ ] `git status --short`
- [ ] `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick`
- [x] Goreve ozel test/komut sonucu
- [x] `Edit Source == Mount Source` kaniti `n/a (UI gorevi degil)`
- [ ] Commit hash

## Kapanis Adimlari
- [ ] Task kartindaki checklistler gercek sonuca gore guncellendi
- [ ] `docs/WORKLOG.md` guncellendi
- [ ] `docs/TASK_LOCKS.md` kaydi `closed` yapildi
- [ ] `docs/NEXT_TASK.md` panosu guncellendi
- [ ] Branch pushlandi

## Komutlar
```powershell
docker exec orkestram-local-web php artisan test --filter=FixtureCommandTest
docker exec izmirorkestra-local-web php artisan test --filter=FixtureCommandTest
docker exec orkestram-local-web php artisan demo:prepare-bando-review-fixture --site=orkestram.net
docker exec izmirorkestra-local-web php artisan demo:prepare-bando-review-fixture --site=izmirorkestra.net
powershell -ExecutionPolicy Bypass -File scripts/validate.ps1 -App both
powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick
```

## Risk / Not
- `docs/demo-media/bando-review/manifest.json` kaynak kaniti icindir; runtime kaynagi app icindeki tracked `review_demo_media` klasorleridir.
- Bu task smoke medya setini degistirmedi; yalniz review demo rebuild kaliciligini kapatti.
- `agent-status` false-positive kirli status riski ayrik operasyon konusu olarak acik kalir.
