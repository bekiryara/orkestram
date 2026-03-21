# TASK-092

Durum: `DONE`  
Ajan: `codex`  
Branch: `agent/codex/task-092`  
Baslangic: `2026-03-22 02:06`

## Gorev Ozeti
- SimplePricingV1 validation ve UI sadelestirme: price_type bazli kurallar, owner-admin parity ve label_only temizligi

## Task Karari
- [ ] mevcut task devam
- [ ] task genisletme
- [x] yeni task

## In Scope
- [x] `price_type` secimine gore admin ve owner yazma akisinda ayni validation kurallarini uygulamak
- [x] `label_only` secenegini SimplePricingV1 siniri icinde devre disi birakmak veya etkisizlestirmek
- [x] Form akisini "once fiyat tipi, sonra ilgili alanlar" modeline sadelestirmek ve min-max kafa karisikligini azaltmak
- [x] `price_label` alanini hakikat kaynagi degil, yalniz display/yardimci metin roluyle sinirlamak
- [x] Orkestram tarafinda bu davranisi feature testlerle kilitlemek

## Out of Scope
- [x] StructuredPricingV1 publish/pricing_mode gecisi
- [x] Talep veya rezervasyon aninda fiyat baglama davranisi
- [x] Izmirorkestra parity tasimasi
- [x] Veri migration/backfill veya canli veri temizligi
- [x] Public filtre/siralama veya listing detail structured fiyat davranisini degistirmek

## Lock Dosyalari
- `docs/tasks/TASK-092.md`
- `docs/TASK_LOCKS.md`
- `docs/NEXT_TASK.md`
- `docs/WORKLOG.md`
- `docs/SESSION_HANDOFF_TR.md`
- `local-rebuild/apps/orkestram/app/Models/Listing.php`
- `local-rebuild/apps/orkestram/app/Http/Requests/Admin/StoreListingRequest.php`
- `local-rebuild/apps/orkestram/app/Http/Requests/Admin/UpdateListingRequest.php`
- `local-rebuild/apps/orkestram/app/Http/Controllers/Owner/OwnerDashboardController.php`
- `local-rebuild/apps/orkestram/resources/views/portal/owner/listings-create.blade.php`
- `local-rebuild/apps/orkestram/resources/views/portal/owner/listings-edit.blade.php`
- `local-rebuild/apps/orkestram/resources/views/admin/listings/form.blade.php`
- `local-rebuild/apps/orkestram/tests/Feature/AdminListingMediaFlowTest.php`
- `local-rebuild/apps/orkestram/tests/Feature/OwnerPanelActionsTest.php`

## Preview Kontrati
- Lane: `n/a`
- Preview URL: `n/a`
- Mount Source: `n/a`
- Edit Source: `n/a`
- UI review gerekir mi?: `no`
- UI Review Durumu: `n/a`
- Revize Notu: `n/a`

## Runtime Kontrati
- Runtime Source: `n/a`
- Preview Source: `n/a`
- Git Katmani: `WSL`
- Script Katmani: `PowerShell`
- App/Test Katmani: `container`
- Runtime Readiness: `ready`
- Upstream Durumu: `origin/agent/codex/task-092`
- Not: `Task 092 yalniz form/validation sadelestirmesini kapsadi. Publish guard ve islem aninda fiyat baglama sonraki tasklara aittir.`

## Uygulama Adimlari
- [x] Zorunlu dokumanlar okundu: `AGENTS.md`, `docs/REPO_DISCIPLINE_TR.md`, `docs/MULTI_AGENT_RULES_TR.md`
- [x] Branch dogrulandi: `agent/codex/task-092`
- [x] Lock kapsam disina cikilmadi
- [x] Orkestram icinde mevcut admin-owner simple pricing write-path davranisi satir bazli haritalandi
- [x] Validation kurallari `price_type` odakli tek modele toplandi
- [x] Admin ve owner formlari "tek fiyat" ile "fiyat araligi" davranisini acik ayiran akisa sadelestirildi
- [x] `label_only` secenegi task kararina gore etkisizlestirildi ve testler buna gore guncellendi
- [x] Gorev kapsamindaki degisiklikler tamamlandi
- [x] Goreve ozel test/dogrulama calistirildi

## Kabul Kriterleri
- [x] Admin listing create-update akisi `price_type` secimine gore tutarli validation verir; tek fiyat senaryosunda anlamsiz `max` zorlamasi kalmaz
- [x] Owner listing create-update akisi adminle ayni fiyat mantigina hizalanir; owner tarafinda eksik kalan `price_label`/fiyat tipi tutarsizliklari kapanir
- [x] Formlar kullaniciyi "tek fiyat" ve "fiyat araligi" arasinda net secime zorlar; ilgisiz alanlar kafa karistirici sekilde ana akista kalmaz
- [x] `label_only` secenegi SimplePricingV1 icin yeni gri alan uretmez; display rolune cekilir veya devre disi birakilir
- [x] `price_label` numeric hakikat kaynagi gibi kullanilmaz; display yardimcisi rolunde kalir
- [x] Orkestram tarafindaki ilgili feature testleri PASS olur
- [x] `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick` PASS

## Teslimde Zorunlu Kanit
- [x] `git branch --show-current`
- [x] `git branch -vv`
- [x] `git status --short`
- [x] `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick`
- [x] Goreve ozel test/komut sonucu: `php artisan test --filter=OwnerPanelActionsTest`, `php artisan test --filter=AdminListingMediaFlowTest`
- [x] `Edit Source == Mount Source` kaniti `n/a`
- [x] Commit hash: `f1daeb3`

## Kapanis Adimlari
- [x] Task kartindaki checklistler gercek sonuca gore guncellendi
- [x] `docs/WORKLOG.md` guncellendi
- [x] `docs/TASK_LOCKS.md` kaydi `closed` yapildi
- [x] `docs/NEXT_TASK.md` panosu guncellendi
- [x] Branch pushlandi

## Komutlar
```powershell
wsl -e bash -lc "cd /home/bekir/orkestram-k && pwd && git rev-parse --show-toplevel && git branch --show-current && git status --short"
wsl -e bash -lc "cd /home/bekir/orkestram-k/local-rebuild && docker compose exec -T orkestram-web php artisan test --filter=OwnerPanelActionsTest"
wsl -e bash -lc "cd /home/bekir/orkestram-k/local-rebuild && docker compose exec -T orkestram-web php artisan test --filter=AdminListingMediaFlowTest"
powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick
powershell -ExecutionPolicy Bypass -File scripts/close-task.ps1 -TaskId TASK-092 -Agent codex -ClosureNote "simple pricing v1 validation ve ui sadelestirme tamamlandi" -WorklogTitle "TASK-092 simple pricing v1 validation ve ui sadelestirme" -WorklogSummary "admin-owner parity kuruldu" -Files "docs/tasks/TASK-092.md" -Commands "powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick" -Result PASS
```

## Risk / Not
- Bu task tamamlandiginda bile SimplePricingV1 tam kapanmis sayilmaz; `pricing_mode/publish guard` ve `request-reservation price binding` icin ayri tasklar planlanmistir.
- Geri donus ihtiyacinda ilk bakilacak alanlar `Listing` modelindeki fiyat yardimcilari, admin request validation ve owner dashboard write-path olacaktir.
