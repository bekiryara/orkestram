# TASK-060

Durum: `DOING`  
Ajan: `codex-b`  
Branch: `agent/codex-b/task-060`  
Baslangic: `2026-03-17 02:13`

## Gorev Ozeti
- TASK-058 ciktisi tasarimdan reddedildi. Bu gorev listing detail sayfasini kafaya gore degil, zorunlu tek-H1/tek-hero standardina gore yeniden kurar.

## In Scope
- [ ] Sayfayi buyuk tek hero medya + tek ana baslik hiyerarsisine gore yeniden kurmak
- [ ] Medya altina ince muted kimlik satiri koymak: kategori, sehir, hizmet tipi gibi
- [ ] Ust bolumde fiyat ve ana CTA hiyerarsisini netlestirmek
- [ ] Tekrarlayan kutulari ve ayni agirlikta section daginikligini azaltmak
- [ ] Icerik akisina su sirayi zorunlu uygulamak: hero -> ozet -> detay -> teknik bilgiler -> yorumlar -> en sonda benzer ilanlar
- [ ] Iki appte parity koruyarak blade ve CSS tarafini birlikte guncellemek

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
- [ ] Zorunlu dokumanlar okundu: `AGENTS.md`, `docs/REPO_DISCIPLINE_TR.md`, `docs/MULTI_AGENT_RULES_TR.md`
- [ ] Branch dogrulandi: `agent/<ajan>/<task-id>`
- [ ] TASK-058 ciktisi incelendi ve tekrar eden/hatali hiyerarsi notlandi
- [ ] Lock kapsam disina cikmadan iki appte blade/CSS parity guncellendi
- [ ] Goreve ozel manuel inceleme + smoke/pre-pr calistirildi

## Kabul Kriterleri
- [ ] Sayfada tek ana baslik hissi vardir; birden fazla ana baslik/agirliga sahip section yoktur
- [ ] Buyuk tek hero medya en ustte baskin sekilde yer alir
- [ ] Medya altinda ince/muted kimlik satiri vardir; kirik veya daginik gorunmez
- [ ] Fiyat ve ana CTA ust bolumde net hiyerarsiyle cozulur
- [ ] Tekrarlayan kutu/baslik hissi azaltilmistir; sayfa form ekranina benzemez
- [ ] `Yorumlar` bolumu `Benzer Ilanlar` bolumunun ustundedir
- [ ] `Benzer Ilanlar` sayfanin en son bolumudur
- [ ] Tasarim iki appte parity ile uygulanir; mobil ve desktop bozulmaz
- [ ] `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick` PASS

## Teslimde Zorunlu Kanit
- [ ] `git branch --show-current`
- [ ] `git branch -vv`
- [ ] `git status --short`
- [ ] `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick`
- [ ] Goreve ozel manuel inceleme ozeti: hero, muted satir, yorumlar, benzer ilanlar sirasi
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
```

## Risk / Not
- TASK-058 sonucu tasarimdan reddedildi; bu gorev mevcut ciktiyi yamalamak degil, standart layout contract'ina gore yeniden kurmaktir
- Safe/ortalama UI istemiyoruz; ama daginik section/coklu baslik hissi kesinlikle istemiyoruz
- Yasaklar: birden fazla ana baslik hissi, tekrar eden kutular, daginik CTA, benzer ilanlari yorumlardan once almak, hero'yu kucultmek

