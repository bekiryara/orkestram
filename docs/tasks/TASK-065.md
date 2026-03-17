# TASK-065

Durum: `DOING`  
Ajan: `codex-b`  
Branch: `agent/codex-b/task-065`  
Baslangic: `2026-03-17`

## Gorev Ozeti
- Listing detail sayfasi iki appte de referans hiyerarsisine gore uctan uca bitirilecek. Hedef, kafaya gore bolum cogelten portal goruntusu degil; solda kimlik/profil, sagda buyuk medya, altta temiz galeri ve editorial akisla premium hizmet detayi hissi veren bir detail sayfasi.
- UI review'in saglikli yapilabilmesi icin mevcut test listingleri bozmadan, sadece design-preview amacli deterministic demo listing fixture seti de ayni taskta kurulacak.

## In Scope
- [x] Hero alanini referans yone gore yeniden kurmak:
  solda kimlik/profil/meta/guven, sagda buyuk ana medya
- [x] Telefon + WhatsApp ana hizli iletisim aksiyonlarini ust hiyerarside tutmak
- [x] Mesaj Gonder + Begeni Birak + Yorum aksiyonlarini ikincil seviyede cozumlemek
- [x] Galeriyi hero'dan ayri, temiz bir alt blok olarak cozumlemek
- [x] Kisa tanitim/ozet, teknik bilgiler, yorumlar ve en sonda benzer ilanlar akisini netlestirmek
- [x] Tek ana baslik hissini korumak; tekrar eden section/baslik/coklu kutu goruntusunu azaltmak
- [x] Mobil uyumu hero, galeri ve CTA hiyerarsisini bozmadan tamamlamak
- [x] `orkestram` ve `izmirorkestra` parity'sini korumak
- [ ] Design-preview icin yeni demo listing seti olusturmak; mevcut test listingleri mutate etmemek
- [ ] Demo listingler icin tekrar kurulabilir seed/fixture mantigi kurmak
- [ ] Demo listinglerde su alanlari doldurmak:
  ilan adi, kisa ozet, detayli aciklama, kategori, sehir, ilce, fiyat/fiyat etiketi, telefon, WhatsApp
- [ ] Demo listinglerde kapak + en az 4-6 galeri gorseli baglamak
- [ ] Demo listinglerde kategori ozellikleri, ek avantajlar, 2-4 yorum ve gerekirse owner reply'lari doldurmak
- [ ] Benzer ilanlar icin anlamli demo card ozet/gorsel seti saglamak

## Out of Scope
- [ ] Backend/model/controller mantigini genisletmek
- [ ] Yeni route veya veritabani alani eklemek
- [ ] Farkli ekranlarda UI implementasyonu yapmak
- [ ] Design-preview lane/runtime kurulumunu degistirmek
- [ ] Mevcut test/smoke listinglerini overwrite etmek
- [ ] Manuel admin panel doldurma ile kalici veri olusturmak
- [ ] Masaustu klasorune runtime bagimliligi kurmak

## Lock Dosyalari
- `docs/tasks/TASK-065.md`
- `docs/TASK_LOCKS.md`
- `docs/NEXT_TASK.md`
- `local-rebuild/apps/orkestram/resources/views/frontend/listing.blade.php`
- `local-rebuild/apps/izmirorkestra/resources/views/frontend/listing.blade.php`
- `local-rebuild/apps/orkestram/public/assets/v1.css`
- `local-rebuild/apps/izmirorkestra/public/assets/v1.css`
- `local-rebuild/apps/orkestram/database/seeders/**`
- `local-rebuild/apps/izmirorkestra/database/seeders/**`
- `local-rebuild/apps/orkestram/database/factories/**`
- `local-rebuild/apps/izmirorkestra/database/factories/**`
- `local-rebuild/apps/orkestram/app/Console/Commands/**`
- `local-rebuild/apps/izmirorkestra/app/Console/Commands/**`
- `local-rebuild/apps/orkestram/storage/app/public/**`
- `local-rebuild/apps/izmirorkestra/storage/app/public/**`
- `docs/WORKLOG.md`

## Preview Kontrati
- Lane: `design`
- Preview URL: `http://127.0.0.1:8280` ve `http://127.0.0.1:8281`
- Mount Source: `task basinda ajan tarafindan kanitlanacak`
- Edit Source: `task basinda ajan tarafindan kanitlanacak`
- UI review gerekir mi?: `yes`
- UI Review Durumu: `approved`
- Revize Notu: `Kapsam ayni kaldigi surece begenilmeyen UI duzeltmeleri ayni taskta donecek. Edit Source != Mount Source ise review gecersizdir.`

## Demo Fixture Guard'lari
- [ ] Mevcut `test-bando-b` ve benzeri test/smoke listingleri destructive olarak mutate edilmeyecek
- [ ] Demo veri yeni ve ayrik slug seti ile kurulacak
- [ ] Seed/fixture sadece whitelist demo listinglere dokunacak
- [ ] Seed idempotent olacak; tekrar calistiginda veri drift uretmeyecek
- [ ] Kategori ozellikleri tamamen overwrite edilmeyecek; mevcut sistemin destekledigi alanlar kontrollu doldurulacak
- [ ] Masaustundeki gorseller once sabit fixture/media klasorune alinacak; direkt masaustu path'ine bagimlilik kurulmayacak

## Zorunlu UI Standardi
- [x] Referans yone yakin iki kolonlu editorial hero olacak
- [x] Solda profil/kimlik/meta/guven alani olacak
- [x] Sagda buyuk ana medya olacak
- [x] Hero icinde gereksiz kutu yiginlari olmayacak
- [x] Telefon Ara + WhatsApp ustte, net ve mevcut renk mantigiyla korunacak
- [x] Mesaj Gonder + Begeni + Yorum ikincil seviyede cozumlenecek
- [x] Galeri hero'dan ayri olacak; sikisik thumbnail seridi gibi durmayacak
- [x] Kisa tanitim/ozet sonra gelecek
- [x] Teknik bilgiler ozetten sonra gelecek
- [x] Yorumlar benzer ilanlardan once gelecek
- [x] Benzer ilanlar sayfanin en sonunda olacak
- [x] Referansta olmayan basliklar/bolumler kafaya gore icat edilmeyecek
- [x] Tek H1 hissi korunacak
- [x] Mobilde hero tek kolona inince hiyerarsi bozulmayacak

## Uygulama Adimlari
- [x] Zorunlu dokumanlar okundu: `AGENTS.md`, `docs/REPO_DISCIPLINE_TR.md`, `docs/MULTI_AGENT_RULES_TR.md`
- [x] Branch dogrulandi: `agent/<ajan>/<task-id>`
- [x] Lock kapsam disina cikilmadi
- [x] `Edit Source == Mount Source` kaniti task basinda verildi
- [x] UI v1 kapsamindaki degisiklikler tamamlandi
- [x] Goreve ozel test/dogrulama calistirildi

## Kabul Kriterleri
- [x] `Edit Source == Mount Source` kaniti vardir
- [x] `http://127.0.0.1:8280` ve `http://127.0.0.1:8281` ayni task kaynagini gosterir
- [x] Hero referans yone uygun olarak solda profil / sagda buyuk medya hiyerarsisindedir
- [x] Telefon + WhatsApp ustte; Mesaj + Begeni + Yorum ikincil seviyededir
- [x] Galeri hero'dan ayri ve temiz bir bloktur
- [x] Yorumlar benzer ilanlardan once, benzer ilanlar en sondadir
- [x] Iki app parity korunmustur
- [x] Mobil ve desktop duzeni bozulmamistir
- [ ] Demo listingler design-preview'da bos alan birakmayacak sekilde doludur
- [ ] Demo veri mevcut test/smoke listinglerini bozmadan ayrik slug seti ile kurulmustur
- [ ] Seed/fixture tekrar calistirilabilir ve destructive overwrite yapmaz
- [x] `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick` PASS

## Teslimde Zorunlu Kanit
- [x] `git branch --show-current`
- [ ] `git branch -vv`
- [ ] `git status --short`
- [x] `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick`
- [x] Goreve ozel smoke/manuel UI kontrol ozeti
- [x] `Edit Source`
- [x] `Mount Source`
- [x] `Preview URL`
- [ ] Commit hash

## Kapanis Adimlari
- [ ] Task kartindaki checklistler gercek sonuca gore guncellendi
- [x] `docs/WORKLOG.md` guncellendi
- [ ] `docs/TASK_LOCKS.md` kaydi `closed` yapildi
- [ ] `docs/NEXT_TASK.md` panosu guncellendi
- [ ] Branch pushlandi

## Komutlar
```powershell
powershell -ExecutionPolicy Bypass -File scripts/smoke-test.ps1 -App orkestram -Lane design
powershell -ExecutionPolicy Bypass -File scripts/smoke-test.ps1 -App izmirorkestra -Lane design
powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick
```

## Risk / Not
- Bu taskta tasarim review merge oncesi zorunludur; `main` preview review araci olarak kullanilmayacak.
- Demo fixture isi mevcut test listinglerini bozmadan, yeni ve ayrik slug seti ile deterministic sekilde kurulacak.
- UI v1 preview onayi alindi; task, genisletilen deterministic demo fixture kapsami tamamlanana kadar acik kalir.

## Uygulama Notu
- Sol hero kolonu label tablosu yerine direkt kimlik satiri + fiyat/guven akisina donusturuldu.
- Hero disi akista sira `Kategori ozellikleri -> Galeri -> Hakkinda -> Musteri deneyimleri -> Benzer ilanlar` olarak netlestirildi.
- `Mesaj`, `Begeni` ve `Yorum Yap` aksiyonlari `Musteri deneyimleri` bolumunde ikonlu, ikincil ve durum gosteren pill satiri olarak cozuldu.
- `Yorum Yap` sabit form yerine popup olarak acilir; desktopta 3-up, mobilde 1-up kayan galeri korunur.
- Preview review URL'leri: `http://127.0.0.1:8280/ilan/demo-bando-sahil-seremonisi` ve `http://127.0.0.1:8281/ilan/demo-bando-kordon-alayi`
- `Edit Source`: `/home/bekir/orkestram-b`
- `Mount Source`: `/home/bekir/orkestram-b`
- Not: Yildiz puani gorsel placeholder olarak `4.9` ile gosterildi; gercek rating backend'i bu task kapsaminda degildi.
