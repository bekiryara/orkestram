# TASK-058

Durum: `DONE`  
Ajan: `codex-b`  
Branch: `agent/codex-b/task-058`  
Baslangic: `2026-03-17 01:21`

## Gorev Ozeti
- Listing detail sayfasini standartlara uygun wow hero, benzer ilan ve yorum hiyerarsisi ile yeniden duzenleme

## In Scope
- [x] Listing detail hero alanini wow etkili, gorsel odakli ve ustte konumlanmis sekilde yeniden kurmak
- [x] Baslik altinda gri/muted kimlik satiri (kategori, sehir, hizmet tipi gibi) ve net fiyat/CTA hiyerarsisi olusturmak
- [x] `Benzer Ilanlar` bolumunu `Yorumlar` bolumunun sonrasina alarak yorumlari sayfanin alt bolumune sabitlemek
- [x] Iki appte parity koruyarak blade ve CSS tarafini birlikte guncellemek

## Out of Scope
- [ ] Backend controller/model/media servis degisiklikleri
- [ ] Yeni route veya veritabani alani eklemek

## Lock Dosyalari
- `docs/tasks/TASK-058.md`
- `docs/TASK_LOCKS.md`
- `docs/NEXT_TASK.md`
- `local-rebuild/apps/orkestram/resources/views/frontend/listing.blade.php`
- `local-rebuild/apps/izmirorkestra/resources/views/frontend/listing.blade.php`
- `local-rebuild/apps/orkestram/public/assets/v1.css`
- `local-rebuild/apps/izmirorkestra/public/assets/v1.css`
- `docs/WORKLOG.md`

## Uygulama Adimlari
- [x] Zorunlu dokumanlar okundu: `AGENTS.md`, `docs/REPO_DISCIPLINE_TR.md`, `docs/MULTI_AGENT_RULES_TR.md`
- [x] Branch dogrulandi: `agent/<ajan>/<task-id>`
- [x] Mevcut listing detail hiyerarsisi okundu ve sorunlu kisimlar notlandi
- [x] Lock kapsam disina cikmadan iki appte blade/CSS parity guncellendi
- [x] Goreve ozel smoke/manuel dogrulama ve `pre-pr` calistirildi

## Kabul Kriterleri
- [x] Hero bolumu sayfanin en ustunde guclu bir gorsel odak, baslik, muted meta satiri ve net CTA hiyerarsisi ile acilir
- [x] Ana gorsel/geleri ustte kalir; gorsel altinda okunakli muted kimlik satiri ve baslik yer alir
- [x] `Benzer Ilanlar` bolumu `Yorumlar` bolumunun altinda, sayfanin en son bolumu olarak konumlanir
- [x] `Yorumlar` bolumu sayfanin alt bolumunde, `Benzer Ilanlar` oncesinde yer alir
- [x] Tasarim iki appte parity ile uygulanir; mobil ve desktop duzeni bozulmaz
- [x] `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick` PASS

## Teslimde Zorunlu Kanit
- [x] `git branch --show-current`
- [x] `git branch -vv`
- [x] `git status --short`
- [x] `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick`
- [ ] Goreve ozel smoke/manuel dogrulama ozeti
- [ ] Commit hash

## Kapanis Adimlari
- [x] Task kartindaki checklistler gercek sonuca gore guncellendi
- [x] `docs/WORKLOG.md` guncellendi
- [x] `docs/TASK_LOCKS.md` kaydi `closed` yapildi
- [x] `docs/NEXT_TASK.md` panosu guncellendi
- [ ] Branch pushlandi

## Komutlar
```powershell
powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick
```

## Risk / Not
- Amac mevcut listing detail sayfasini daha guclu, daha okunakli ve daha premium hissettiren bir hiyerarsiye cekmek
- Safe/ortalama UI istemiyoruz; mevcut site diline uyan ama belirgin sekilde daha guclu bir detay sayfasi bekleniyor
- Benzer ilanlar yorumlardan once gelmeli; yorumlar en altta kalmali