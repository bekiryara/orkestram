# TASK-090

Durum: `DOING`  
Ajan: `codex`  
Branch: `agent/codex/task-090`  
Baslangic: `2026-03-21 05:01`

## Gorev Ozeti
- Structured fiyat modeli gecisi: public filtre/sort ve detail JSON-LD hattini `price_label` parse yerine `price_min`, `price_max`, `currency`, `price_type` kaynak modeline tasimak.

## Task Karari
- [ ] mevcut task devam
- [ ] task genisletme
- [x] yeni task

## In Scope
- [x] `PublicController` icindeki `readPriceRange`, `applyListingSortAndPriceFilter` ve ilgili filtre/siralama hattini `price_label` parse yerine `price_min`, `price_max`, `currency`, `price_type` alanlariyla calisacak sekilde yeniden kurmak
- [x] Detail sayfa JSON-LD `Offer` verisini `price_label` regex parse veya `meta_json.price_currency` fallback yerine structured fiyat alanlarindan uretmek
- [x] Listing card ve detail fiyat gosterimini ayni veri modeline baglayip iki appte parity korumak
- [x] Legacy kayitlar icin acik fallback kurali tanimlamak: structured fiyat bos ise `price_label` yalniz display fallback olabilir; public numeric filtre/sort icin ana kaynak olmaz
- [x] Iki appte feature testlerini structured fiyat modeline gore guncellemek veya yeni test eklemek

## Out of Scope
- [x] Admin/owner fiyat formunun tasarimini veya alan setini degistirmek
- [x] Migration, toplu veri backfill veya canli veri temizligi yapmak
- [x] Yeni para birimi/kur donusum mantigi eklemek
- [x] Search, kategori attribute veya coverage mantigini fiyat disi alanlarda degistirmek
- [x] UI review lane veya design-preview akisini bu task icine almak

## Lock Dosyalari
- `docs/tasks/TASK-090.md`
- `docs/TASK_LOCKS.md`
- `docs/NEXT_TASK.md`
- `docs/WORKLOG.md`
- `docs/SESSION_HANDOFF_TR.md`
- `local-rebuild/apps/orkestram/app/Models/Listing.php`
- `local-rebuild/apps/orkestram/app/Http/Controllers/PublicController.php`
- `local-rebuild/apps/orkestram/resources/views/frontend/listing.blade.php`
- `local-rebuild/apps/orkestram/resources/views/frontend/partials/listing-card.blade.php`
- `local-rebuild/apps/orkestram/tests/Feature/CategorySystemFlowTest.php`
- `local-rebuild/apps/izmirorkestra/app/Models/Listing.php`
- `local-rebuild/apps/izmirorkestra/app/Http/Controllers/PublicController.php`
- `local-rebuild/apps/izmirorkestra/resources/views/frontend/listing.blade.php`
- `local-rebuild/apps/izmirorkestra/resources/views/frontend/partials/listing-card.blade.php`
- `local-rebuild/apps/izmirorkestra/tests/Feature/CategorySystemFlowTest.php`

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
- Upstream Durumu: `origin/agent/codex/task-090`
- Not: `Bu task urun davranisini degistirir; ana risk public filtre/siralama ve detail JSON-LD regresyonudur. Fiyat parse mantigi tek seferde structured modele alinacak, ancak display fallback acik ve kontrollu tutulacaktir.`

## Uygulama Adimlari
- [x] Zorunlu dokumanlar okundu: `AGENTS.md`, `docs/REPO_DISCIPLINE_TR.md`, `docs/MULTI_AGENT_RULES_TR.md`
- [x] Branch dogrulandi: `agent/codex/task-090`
- [x] Lock kapsam disina cikilmadi
- [ ] Once iki appte mevcut fiyat davranisinin dayandigi noktalar haritalanacak: model, public controller, listing card, detail JSON-LD ve parity testleri
- [ ] Orkestram uygulamasinda fiyat filter/sort hattini structured alanlara geciren minimal cekirdek degisiklik yapilacak
- [ ] Ayni degisiklik izmirorkestra tarafina parity ile tasinacak
- [ ] Legacy display fallback kurali testle kilitlenecek; numeric davranis structured alanlardan gelecegi netlestirilecek
- [ ] Gorev kapsamindaki degisiklikler tamamlandi
- [ ] Goreve ozel test/dogrulama calistirildi

## Kabul Kriterleri
- [ ] `/ilanlar` icindeki `price_asc`, `price_desc`, `price_min`, `price_max` davranisi `price_label` parse etmeden structured fiyat alanlariyla calisir
- [ ] `/hizmet/<slug>` ve sehir/semt varyantlarindaki fiyat araligi filtreleri ayni structured fiyat hattini kullanir
- [ ] Detail JSON-LD `Offer` verisi `price_min`, `price_max`, `currency`, `price_type` alanlarindan uretilir; `meta_json.price_currency` veya regex parse ana kaynak olmaz
- [ ] Listing card ve detail fiyat metni ayni helper/veri modeline baglanir; iki appte ayni sonucu verir
- [ ] Structured fiyat bos legacy kayitlarda display fallback acik, fakat numeric filtre/sort davranisinin fallback kurali task icinde acikca test edilir
- [ ] Orkestram ve izmirorkestra tarafinda ilgili feature testleri PASS olur
- [ ] `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick` PASS

## Teslimde Zorunlu Kanit
- [ ] `git branch --show-current`
- [ ] `git branch -vv`
- [ ] `git status --short`
- [ ] `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick`
- [ ] Goreve ozel test/komut sonucu: `CategorySystemFlowTest` ve gerekiyorsa ek fiyat/parity testleri
- [x] `Edit Source == Mount Source` kaniti `n/a`
- [ ] Commit hash

## Kapanis Adimlari
- [ ] Task kartindaki checklistler gercek sonuca gore guncellendi
- [ ] `docs/WORKLOG.md` guncellendi
- [ ] `docs/TASK_LOCKS.md` kaydi `closed` yapildi
- [ ] `docs/NEXT_TASK.md` panosu guncellendi
- [ ] Branch pushlandi

## Komutlar
```powershell
wsl -e bash -lc "cd /home/bekir/orkestram-k && git branch --show-current && git branch -vv && git status --short"
docker exec orkestram-local-web php artisan test --filter=CategorySystemFlowTest
docker exec izmirorkestra-local-web php artisan test --filter=CategorySystemFlowTest
powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick
powershell -ExecutionPolicy Bypass -File scripts/close-task.ps1 -TaskId TASK-090 -Agent codex -ClosureNote "structured fiyat modeli gecisi tamamlandi" -WorklogTitle "TASK-090 structured pricing transition" -WorklogSummary "public filtre ve detail fiyat modeli structured alanlara tasindi" -Files "dosya-1" -Commands "komut-1" -Result PASS
```

## Risk / Not
- Ana risk `price_label` tabanli legacy kayitlarin public listelerde beklenmedik sekilde kaybolmasi veya siralamanin degismesidir; fallback karari testlerle sabitlenmeden kapanis yapilmaz.
- JSON-LD cikisi SEO etkili oldugu icin fiyat/currency alanlari task sonunda manuel kanit ve feature test ile birlikte dogrulanir.
- Iki app parity zorunludur; orkestram tarafi bitmeden izmirorkestra aynalanmis kabul edilmez.
- Geri donus plani: structured fiyat hattinda regresyon gorulurse `price_label` parse davranisi ayni task icinde kontrollu fallback olarak gecici tutulur; ancak task kapanisinda son model acikca belgeye yazilir.
