# TASK-060

Durum: `DONE`  
Ajan: `codex-b`  
Branch: `agent/codex-b/task-060`  
Baslangic: `2026-03-17 02:13`

## Gorev Ozeti
- TASK-058 ciktisi tasarimdan reddedildi. Bu gorev listing detail sayfasini kafaya gore degil, zorunlu tek-H1/tek-hero standardina gore yeniden kurar.

## In Scope
- [x] Sayfayi buyuk tek hero medya + tek ana baslik hiyerarsisine gore yeniden kurmak
- [x] Medya altina ince muted kimlik satiri koymak: kategori, sehir, hizmet tipi gibi
- [x] Ust bolumde fiyat ve ana CTA hiyerarsisini netlestirmek
- [x] Tekrarlayan kutulari ve ayni agirlikta section daginikligini azaltmak
- [x] Icerik akisina su sirayi zorunlu uygulamak: hero -> ozet -> detay -> teknik bilgiler -> yorumlar -> en sonda benzer ilanlar
- [x] Iki appte parity koruyarak blade ve CSS tarafini birlikte guncellemek

## Out of Scope
- [ ] Backend/controller/model/media servis degisiklikleri
- [ ] Yeni route, migration veya veri modeli eklemek

## Lock Dosyalari
- `docs/tasks/TASK-060.md`
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
- [x] TASK-058 ciktisi incelendi ve tekrar eden/hatali hiyerarsi notlandi
- [x] Lock kapsam disina cikmadan iki appte blade/CSS parity guncellendi
- [x] Goreve ozel manuel inceleme + smoke/pre-pr calistirildi

## Kabul Kriterleri
- [x] Sayfada tek ana baslik hissi vardir; birden fazla ana baslik/agirliga sahip section yoktur
- [x] Buyuk tek hero medya en ustte baskin sekilde yer alir
- [x] Medya altinda ince/muted kimlik satiri vardir; kirik veya daginik gorunmez
- [x] Fiyat ve ana CTA ust bolumde net hiyerarsiyle cozulur
- [x] Tekrarlayan kutu/baslik hissi azaltilmistir; sayfa form ekranina benzemez
- [x] `Yorumlar` bolumu `Benzer Ilanlar` bolumunun ustundedir
- [x] `Benzer Ilanlar` sayfanin en son bolumudur
- [x] Tasarim iki appte parity ile uygulanir; mobil ve desktop bozulmaz
- [x] `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick` PASS

## Teslimde Zorunlu Kanit
- [x] `git branch --show-current`
- [x] `git branch -vv`
- [x] `git status --short`
- [x] `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick`
- [x] Goreve ozel manuel inceleme ozeti: hero, muted satir, yorumlar, benzer ilanlar sirasi
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
```

## Risk / Not
- TASK-058 sonucu tasarimdan reddedildi; bu gorev mevcut ciktiyi yamalamak degil, standart layout contract'ina gore yeniden kurmaktir
- Referans ekran yonu sonraki revizyonda daha da sertlestirildi: solda kimlik/profil, sagda buyuk ana gorsel, altta galeri, sonra aciklama, sonra yorumlar, en sonda benzer ilanlar
- Ayni kimlik bilgisini iki kez tekrar etmek, ust alani kutu kutu portale cevirmek ve fallback gorseli hero'yu oldurecek kadar buyutmek yasaktir
