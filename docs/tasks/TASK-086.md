# TASK-086

Durum: `DONE`  
Ajan: `codex`  
Branch: `agent/codex/task-086`  
Baslangic: `2026-03-20 08:46`

## Gorev Ozeti
- Baseline, smoke ve review-demo fixture katmanlarini ayrik komut ve standartla netlestir

## Task Karari
- [ ] mevcut task devam
- [ ] task genisletme
- [x] yeni task

## In Scope
- [x] `smoke` fixture listinglerini meta isaretleme ile resmi katmana baglamak
- [x] `review_demo` icin ayrik whitelist bando fixture komutu eklemek
- [x] Eksik `docs/DEMO_FIXTURE_STANDARD_TR.md` dokumanini repo icine geri koymak
- [x] Iki appte parity fixture komut testleri eklemek

## Out of Scope
- [ ] Baseline seed icerigini buyutmek veya yeni kalici kategori/import sistemi yazmak
- [ ] UI blade/CSS degisikligi yapmak
- [ ] Review demo yorum/like datasetini uctan uca genisletmek
- [ ] `agent-status` false-positive driftini bu taskta cozmeye calismak

## Lock Dosyalari
- `docs/tasks/TASK-086.md`
- `docs/TASK_LOCKS.md`
- `docs/NEXT_TASK.md`
- `docs/WORKLOG.md`
- `docs/SESSION_HANDOFF_TR.md`
- `docs/DEMO_FIXTURE_STANDARD_TR.md`
- `local-rebuild/apps/orkestram/routes/console.php`
- `local-rebuild/apps/izmirorkestra/routes/console.php`
- `local-rebuild/apps/orkestram/tests/Feature/FixtureCommandTest.php`
- `local-rebuild/apps/izmirorkestra/tests/Feature/FixtureCommandTest.php`

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
- Upstream Durumu: `origin/agent/codex/task-086`
- Not: `apply_patch` sandbox kirildigi icin shell fallback ile kontrollu yazim yapildi; hedefli fixture testleri, validate ve pre-pr PASS.`

## Uygulama Adimlari
- [x] Zorunlu dokumanlar okundu: `AGENTS.md`, `docs/REPO_DISCIPLINE_TR.md`, `docs/MULTI_AGENT_RULES_TR.md`
- [x] Branch dogrulandi: `agent/<ajan>/<task-id>`
- [x] Lock kapsam disina cikilmadi
- [x] Gorev kapsamindaki degisiklikler tamamlandi
- [x] Goreve ozel test/dogrulama calistirildi

## Kabul Kriterleri
- [x] `baseline`, `smoke` ve `review_demo` katmanlari komut ve dokuman seviyesinde ayrilir
- [x] `review_demo` komutu whitelist demo sluglarina dokunur ve smoke sluglarini mutate etmez
- [x] `smoke` listingleri `meta_json.fixture_layer=smoke` isaretiyle uretilir
- [x] Iki appte parity fixture komut testleri PASS verir
- [x] `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick` PASS

## Teslimde Zorunlu Kanit
- [x] `git branch --show-current`
- [x] `git branch -vv`
- [x] `git status --short`
- [x] `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick`
- [x] Goreve ozel test/komut sonucu
- [x] `Edit Source == Mount Source` kaniti `n/a (UI gorevi degil)`
- [x] Commit hash: `e2f0088`

## Kapanis Adimlari
- [x] Task kartindaki checklistler gercek sonuca gore guncellendi
- [x] `docs/WORKLOG.md` guncellendi
- [x] `docs/TASK_LOCKS.md` kaydi `closed` yapildi
- [x] `docs/NEXT_TASK.md` panosu guncellendi
- [x] Branch pushlandi

## Komutlar
```powershell
docker exec orkestram-local-web php artisan test --filter=FixtureCommandTest
docker exec izmirorkestra-local-web php artisan test --filter=FixtureCommandTest
powershell -ExecutionPolicy Bypass -File scripts/validate.ps1 -App both
powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick
git push -u origin agent/codex/task-086
```

## Risk / Not
- `apply_patch` sandbox katmani bu oturumda kirik oldugu icin shell fallback kullanildi; degisiklikler sonrasinda dosyalar tekrar okunarak dogrulandi.
- `scripts/agent-status.ps1` halen false-positive kirli status raporluyor; WSL `git status --short` temiz kaniti kaynak gercek olarak baz alinmali.
- `izmirorkestra/storage` host tarafinda izinli okunamadigi icin review demo medya path'i repo-sozlesmesiyle yazildi; runtime kaniti smoke/validate tarafinda bozulmadi.
