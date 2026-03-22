# TASK-094

Durum: `DONE`  
Ajan: `codex`  
Branch: `agent/codex/task-094`  
Baslangic: `2026-03-22 03:54`

## Gorev Ozeti
- SimplePricingV1 request-reservation price binding: mevcut customer request akisinda islem ani fiyat snapshot kaydini kapat

## Task Karari
- [x] mevcut task devam
- [ ] task genisletme
- [ ] yeni task

## In Scope
- [x] `customer_requests` kaydina simple pricing snapshot alanlarini eklemek
- [x] Customer request create write-path'inde islem anindaki simple fiyat truth source alanlarini kayda baglamak
- [x] Sonradan listing fiyati degisse bile eski customer request kaydinin fiyat gercegini korumak
- [x] Customer ve owner ekranlarinda kayda baglanan fiyat bilgisinin okunabilir gorunmesini saglamak
- [x] Orkestram tarafinda request binding davranisini feature testlerle kilitlemek

## Out of Scope
- [x] Yeni rezervasyon sistemi veya ayri booking akisi icat etmek
- [x] StructuredPricingV1 snapshot veya resolver omurgasini yazmak
- [x] Izmirorkestra parity tasimasi
- [x] Public listing detail fiyat CTA tasarimini yeniden kurmak
- [x] Canli veri backfill veya eski request kayitlarini manuel donusturmek

## Lock Dosyalari
- `docs/tasks/TASK-094.md`
- `docs/TASK_LOCKS.md`
- `docs/NEXT_TASK.md`
- `docs/WORKLOG.md`
- `docs/SESSION_HANDOFF_TR.md`
- `local-rebuild/apps/orkestram/database/migrations/*customer*request*.php`
- `local-rebuild/apps/orkestram/app/Models/CustomerRequest.php`
- `local-rebuild/apps/orkestram/app/Http/Controllers/Customer/CustomerDashboardController.php`
- `local-rebuild/apps/orkestram/resources/views/portal/customer/dashboard.blade.php`
- `local-rebuild/apps/orkestram/resources/views/portal/customer/requests.blade.php`
- `local-rebuild/apps/orkestram/resources/views/portal/owner/leads.blade.php`
- `local-rebuild/apps/orkestram/tests/Feature/OwnerCustomerDbFlowTest.php`
- `local-rebuild/apps/orkestram/tests/Feature/CustomerOwnerRoleAccessTest.php`

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
- Preview Source: `/home/bekir/orkestram-k`
- Git Katmani: `WSL`
- Script Katmani: `PowerShell`
- App/Test Katmani: `container`
- Runtime Readiness: `READY`
- Upstream Durumu: `origin/agent/codex/task-094`
- Not: `Task acilisindaki git fetch --all --prune denemesi canonical/a/b/c remote drift nedeniyle ENV_BLOCKED verdi; implementasyon, test ve push akisina engel olmadi.`

## Uygulama Adimlari
- [x] Zorunlu dokumanlar okundu: `AGENTS.md`, `docs/REPO_DISCIPLINE_TR.md`, `docs/MULTI_AGENT_RULES_TR.md`
- [x] Branch dogrulandi: `agent/codex/task-094`
- [x] Lock kapsam disina cikilmadi
- [x] Mevcut customer request write-path, customer panel ve owner lead okuma alanlarini satir bazli haritalamak
- [x] Simple pricing truth source alanlarini request snapshot olarak migration/model/controller seviyesinde uygulamak
- [x] Customer request ve owner lead ekranlarinda snapshot fiyat okunurlugunu eklemek
- [x] Request binding davranisini feature testlerle kilitlemek
- [x] Gorev kapsamindaki degisiklikler tamamlandi
- [x] Goreve ozel test/dogrulama calistirildi

## Kabul Kriterleri
- [x] `customer_requests` kaydinda simple pricing snapshot alanlari vardir
- [x] Customer request olustugunda `pricing_mode`, `price_type`, `price_min`, `price_max` ve `currency` islem anindaki gercekle kayda gecilir
- [x] Sonradan listing fiyati degisse bile eski request kaydinin fiyat alani degismez
- [x] Request kaydi olusturulurken invalid simple fiyatli listing icin kontrollu hata verilir veya request olusmaz
- [x] Owner/customer ekranlarinda kayda baglanan fiyat bilgisi okunabilir sekilde gorunur
- [x] Orkestram tarafindaki ilgili feature testleri PASS olur
- [x] `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick` PASS

## Teslimde Zorunlu Kanit
- [x] `git branch --show-current`
- [x] `git branch -vv`
- [x] `git status --short`
- [x] `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick`
- [x] Goreve ozel test/komut sonucu
- [x] `Edit Source == Mount Source` kaniti
- [x] Commit hash

## Kanit Notlari
- `git branch --show-current` -> `agent/codex/task-094`
- `wsl -e bash -lc "cd /home/bekir/orkestram-k && pwd && git rev-parse --show-toplevel && git branch --show-current && git status --short"` -> `/home/bekir/orkestram-k` dogrulandi
- `docker compose exec -T orkestram-web php artisan test --filter=OwnerCustomerDbFlowTest` -> `PASS`
- `docker compose exec -T orkestram-web php artisan test --filter=CustomerOwnerRoleAccessTest` -> `PASS`
- `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick` -> `PASS`
- Commit hash final push sonrasi `git rev-parse --short HEAD` ile raporlanacaktir

## Kapanis Adimlari
- [x] Task kartindaki checklistler gercek sonuca gore guncellendi
- [x] `docs/WORKLOG.md` guncellendi
- [x] `docs/TASK_LOCKS.md` kaydi `closed` yapildi
- [x] `docs/NEXT_TASK.md` panosu guncellendi
- [x] Branch pushlandi

## Komutlar
```powershell
wsl -e bash -lc "cd /home/bekir/orkestram-k && pwd && git rev-parse --show-toplevel && git branch --show-current && git status --short"
wsl -e bash -lc "cd /home/bekir/orkestram-k/local-rebuild && docker compose exec -T orkestram-web php artisan test --filter=OwnerCustomerDbFlowTest"
wsl -e bash -lc "cd /home/bekir/orkestram-k/local-rebuild && docker compose exec -T orkestram-web php artisan test --filter=CustomerOwnerRoleAccessTest"
powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick
powershell -ExecutionPolicy Bypass -File scripts/close-task.ps1 -TaskId TASK-094 -Agent codex -ClosureNote "simple pricing v1 request binding tamamlandi" ... -BranchPushed
```

## Risk / Not
- Bu task bugunun kodunda gorunen `customer request / teklif al` akisina odaklanir; ayri rezervasyon sistemi bugun kanitli olarak mevcut degildir.
- SimplePricingV1 uygulama seviyesi kapanisi icin bu task zorunludur; bu task bitmeden simple taraf tam kapanmis sayilmazdi.
- Geri donus ihtiyacinda ilk bakilacak alanlar `customer_requests` schema degisikligi ve `CustomerDashboardController@store` write-path'i olacaktir.

