# TASK-088

Durum: `DONE`
Ajan: `codex`
Branch: `agent/codex/task-088`
Baslangic: `2026-03-20 18:34`

## Gorev Ozeti
- Deterministic account fixture ve DB reset recovery akisini base seed, smoke fixture ve review demo fixture katmanlarindan guvenli sekilde ayristir.

## Task Karari
- [x] mevcut task devam
- [ ] task genisletme
- [ ] yeni task

## In Scope
- [x] Iki app icin ayri `LocalAccountFixtureSeeder` katmani eklemek
- [x] `local:prepare-account-fixture` ve `local:prepare-reset-recovery` artisan komutlarini tanimlamak
- [x] Smoke scriptini local account fixture katmanini resmi siraya alacak sekilde guncellemek
- [x] Fixture standardi dokumanini yeni katmanlarla hizalamak
- [x] Yeni command davranisini hedefli test ile kanitlamak

## Out of Scope
- [x] Ortak DB icinde `migrate:fresh` veya reset operasyonu tetiklemek
- [x] Baseline `DatabaseSeeder` kapsamindaki cekirdek seed akisini genisletmek
- [x] Admin/musteri paneline yeni UI akis eklemek

## Lock Dosyalari
- `docs/tasks/TASK-088.md`
- `docs/TASK_LOCKS.md`
- `docs/NEXT_TASK.md`
- `docs/WORKLOG.md`
- `docs/SESSION_HANDOFF_TR.md`
- `local-rebuild/apps/orkestram/database/seeders/DatabaseSeeder.php`
- `local-rebuild/apps/izmirorkestra/database/seeders/DatabaseSeeder.php`
- `local-rebuild/apps/orkestram/database/seeders/**`
- `local-rebuild/apps/izmirorkestra/database/seeders/**`
- `local-rebuild/apps/orkestram/routes/console.php`
- `local-rebuild/apps/izmirorkestra/routes/console.php`
- `local-rebuild/apps/orkestram/tests/Feature/**`
- `local-rebuild/apps/izmirorkestra/tests/Feature/**`
- `scripts/validate.ps1`
- `scripts/smoke-test.ps1`
- `docs/DEMO_FIXTURE_STANDARD_TR.md`

## Preview Kontrati
- Lane: `n/a`
- Preview URL: `n/a`
- Mount Source: `n/a`
- Edit Source: `n/a`
- UI review gerekir mi?: `no`
- UI Review Durumu: `n/a`
- Revize Notu: `n/a`

## Runtime Kontrati
- Runtime Source: `shared local DB + app containers`
- Preview Source: `n/a`
- Git Katmani: `WSL + Windows`
- Script Katmani: `PowerShell`
- App/Test Katmani: `container`
- Runtime Readiness: `ready`
- Upstream Durumu: `origin/agent/codex/task-088`
- Not: `Recovery komutu destructive reset yapmaz; sadece seed/fixture katmanlarini tekrar hazirlar.`

## Uygulama Adimlari
- [x] Zorunlu dokumanlar okundu: `AGENTS.md`, `docs/REPO_DISCIPLINE_TR.md`, `docs/MULTI_AGENT_RULES_TR.md`
- [x] Branch dogrulandi: `agent/codex/task-088`
- [x] Lock kapsam disina cikilmadi
- [x] Deterministic local hesap fixture katmani iki appte eklendi
- [x] Recovery komutu ve smoke entegrasyonu tanimlandi
- [x] Goreve ozel test/dogrulama calistirildi

## Kabul Kriterleri
- [x] Base seed local hesaplardan ayrik kalir; hesaplar ayri command ile geri kurulabilir
- [x] Reset sonrasi local hesap + smoke + review demo katmanlari resmi sirayla tekrar hazirlanabilir
- [x] Smoke scripti hesap fixture katmanini fixture-bagimli kontrolllerden once hazirlar
- [x] `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick` PASS

## Teslimde Zorunlu Kanit
- [x] `git branch --show-current` -> `agent/codex/task-088`
- [x] `git branch -vv` -> `agent/codex/task-088 [origin/agent/codex/task-088]`
- [x] `git status --short` -> `TASK-088 kapsamindaki dosyalar degisik; repo genelinde Zone.Identifier drift mevcut`
- [x] `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick` -> `PASS`
- [x] Goreve ozel test/komut sonucu:
  - `docker exec orkestram-local-web php artisan test --filter=LocalAccountFixtureCommandTest` -> `PASS`
  - `docker exec izmirorkestra-local-web php artisan test --filter=LocalAccountFixtureCommandTest` -> `PASS`
  - `docker exec orkestram-local-web php artisan local:prepare-account-fixture` -> `PASS`
  - `docker exec izmirorkestra-local-web php artisan local:prepare-account-fixture` -> `PASS`
- [x] `Edit Source == Mount Source` kaniti `n/a (UI gorevi degil)`
- [x] Commit hash (`d832d22`)

## Kapanis Adimlari
- [x] Task kartindaki checklistler gercek sonuca gore guncellendi
- [x] `docs/WORKLOG.md` guncellendi
- [x] `docs/TASK_LOCKS.md` kaydi `closed` yapildi
- [x] `docs/NEXT_TASK.md` panosu guncellendi
- [x] Branch pushlandi

## Komutlar
```powershell
docker exec orkestram-local-web php artisan test --filter=LocalAccountFixtureCommandTest
docker exec izmirorkestra-local-web php artisan test --filter=LocalAccountFixtureCommandTest
docker exec orkestram-local-web php artisan local:prepare-account-fixture
docker exec izmirorkestra-local-web php artisan local:prepare-account-fixture
powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick
powershell -ExecutionPolicy Bypass -File scripts/close-task.ps1 -TaskId TASK-088 -Agent codex -ClosureNote "deterministic account fixture ve reset recovery akisi tamamlandi" -WorklogTitle "TASK-088 deterministic account fixture" -WorklogSummary "local account fixture ayristirildi","smoke helper zorunlu site varsayimi duzeltildi","reset recovery komutu destructive olmayan resmi sira ile tamamlandi" -Files "docs/tasks/TASK-088.md","docs/DEMO_FIXTURE_STANDARD_TR.md","scripts/smoke-test.ps1","local-rebuild/apps/orkestram/routes/console.php","local-rebuild/apps/izmirorkestra/routes/console.php","local-rebuild/apps/orkestram/database/seeders/LocalAccountFixtureSeeder.php","local-rebuild/apps/izmirorkestra/database/seeders/LocalAccountFixtureSeeder.php","local-rebuild/apps/orkestram/tests/Feature/LocalAccountFixtureCommandTest.php","local-rebuild/apps/izmirorkestra/tests/Feature/LocalAccountFixtureCommandTest.php" -Commands "docker exec orkestram-local-web php artisan test --filter=LocalAccountFixtureCommandTest","docker exec izmirorkestra-local-web php artisan test --filter=LocalAccountFixtureCommandTest","powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick" -Result PASS -BranchPushed
```

## Risk / Not
- En buyuk risk, recovery komutunun reset yaptigi varsayimiyla calistirilmasi olur; mevcut tasarim yalniz var olan DB uzerinde idempotent hazirlama yapar.
- `scripts/smoke-test.ps1` icindeki helper daha once tum komutlara zorunlu `--site` ekliyordu; bu task ile ortak helper yalniz ihtiyac oldugunda `--site` gonderir hale getirildi.
- Repo genelindeki `Zone.Identifier` metadata silinmeleri bu taskin kapsami degildir; kapanis kanitlari TASK-088 dosya seti ve `pre-pr PASS` uzerinden alinmistir.


