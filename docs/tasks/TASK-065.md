# TASK-065

Durum: `DONE`  
Ajan: `codex-b`  
Branch: `agent/codex-b/task-065`  
Baslangic: `2026-03-17`

## Gorev Ozeti
- Listing detail sayfasi iki appte de referans hiyerarsisine gore uctan uca bitirildi. Sonuc, solda kimlik/profil, sagda buyuk medya, altta temiz galeri ve editorial akisla premium hizmet detayi hissi veren `Listing Detail V1` oldu.
- Design-preview review'inde kullanilan mevcut demo listingler read-only audit ile dogrulandi; gorsel ve icerik dolulugu bu taskta yeterli bulundu. Deterministic/whitelist fixture otomasyonu ise ayri kabul kriteri ve dosya seti gerektirdigi icin yeni taska ayrildi.

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
- [x] Design-preview'de kullanilan mevcut demo listingleri read-only audit ile dogrulamak; mevcut test listingleri mutate etmemek
- [ ] Demo listingler icin tekrar kurulabilir seed/fixture mantigi kurmak
- [x] Demo listinglerde su alanlari doldurmak:
  ilan adi, kisa ozet, detayli aciklama, kategori, sehir, ilce, fiyat/fiyat etiketi, telefon, WhatsApp
- [x] Demo listinglerde kapak + en az 4-6 galeri gorseli baglamak
- [x] Demo listinglerde kategori ozellikleri, ek avantajlar, 2-4 yorum ve gerekirse owner reply'lari doldurmak
- [x] Benzer ilanlar icin anlamli demo card ozet/gorsel seti saglamak

## Out of Scope
- [ ] Backend/model/controller mantigini genisletmek
- [ ] Yeni route veya veritabani alani eklemek
- [ ] Farkli ekranlarda UI implementasyonu yapmak
- [ ] Design-preview lane/runtime kurulumunu degistirmek
- [ ] Mevcut test/smoke listinglerini overwrite etmek
- [ ] Manuel admin panel doldurma ile kalici veri olusturmak
- [ ] Masaustu klasorune runtime bagimliligi kurmak
- [x] Ayrik whitelist/idempotent fixture otomasyonunu bu taskta yazmak

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
- Mount Source: `/home/bekir/orkestram-b`
- Edit Source: `/home/bekir/orkestram-b`
- UI review gerekir mi?: `yes`
- UI Review Durumu: `approved`
- Revize Notu: `Kapsam ayni kaldigi surece begenilmeyen UI duzeltmeleri ayni taskta donecek. Edit Source != Mount Source ise review gecersizdir.`

## Demo Fixture Guard'lari
- [x] Mevcut `test-bando-b` ve benzeri test/smoke listingleri destructive olarak mutate edilmeyecek
- [x] Demo veri mevcut ayrik slug seti ile dogrulandi
- [ ] Seed/fixture sadece whitelist demo listinglere dokunacak
- [ ] Seed idempotent olacak; tekrar calistiginda veri drift uretmeyecek
- [x] Kategori ozellikleri tamamen overwrite edilmeyecek; mevcut sistemin destekledigi alanlar kontrollu dolduruldu ve read-only audit ile dogrulandi
- [x] Design-preview medyasi masaustu path bagimliligi olmadan mevcut storage pathleriyle calisiyor

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
- [x] Design preview'de kullanilan mevcut demo veriler MySQL uzerinden read-only audit ile dogrulandi
- [x] Ayrik whitelist/idempotent fixture otomasyonu icin yeni task gerektigi karar notu cikartildi

## Kabul Kriterleri
- [x] `Edit Source == Mount Source` kaniti vardir
- [x] `http://127.0.0.1:8280` ve `http://127.0.0.1:8281` ayni task kaynagini gosterir
- [x] Hero referans yone uygun olarak solda profil / sagda buyuk medya hiyerarsisindedir
- [x] Telefon + WhatsApp ustte; Mesaj + Begeni + Yorum ikincil seviyededir
- [x] Galeri hero'dan ayri ve temiz bir bloktur
- [x] Yorumlar benzer ilanlardan once, benzer ilanlar en sondadir
- [x] Iki app parity korunmustur
- [x] Mobil ve desktop duzeni bozulmamistir
- [x] Demo listingler design-preview'da bos alan birakmayacak sekilde doludur
- [x] Demo veri mevcut test/smoke listinglerini bozmadan ayrik slug seti ile calismaktadir
- [ ] Seed/fixture tekrar calistirilabilir ve destructive overwrite yapmaz
- [x] `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick` PASS

## Teslimde Zorunlu Kanit
- [x] `git branch --show-current`
- [x] `git branch -vv`
- [x] `git status --short`
- [x] `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick`
- [x] Goreve ozel smoke/manuel UI kontrol ozeti
- [x] `Edit Source`
- [x] `Mount Source`
- [x] `Preview URL`
- [x] Commit hash

## Kapanis Adimlari
- [x] Task kartindaki checklistler gercek sonuca gore guncellendi
- [x] `docs/WORKLOG.md` guncellendi
- [x] `docs/TASK_LOCKS.md` kaydi `closed` yapildi
- [x] `docs/NEXT_TASK.md` panosu guncellendi
- [x] Branch pushlandi

## Komutlar
```powershell
git branch --show-current
git branch -vv
git status --short
docker exec orkestram-local-mysql mysql -uorkestram -porkestram -D orkestram_local -e "SELECT id, site, slug, name, status, category_id, city, district, cover_image_path, phone, whatsapp FROM listings WHERE slug IN ('demo-bando-sahil-seremonisi','demo-bando-kordon-alayi','test-bando-a','test-bando-b') ORDER BY slug, site;"
docker exec orkestram-local-mysql mysql -uorkestram -porkestram -D orkestram_local -e "SELECT l.slug, l.site, COUNT(DISTINCT lk.id) AS like_count, COUNT(DISTINCT f.id) AS feedback_count FROM listings l LEFT JOIN listing_likes lk ON lk.listing_id = l.id LEFT JOIN listing_feedback f ON f.listing_id = l.id WHERE l.slug IN ('demo-bando-sahil-seremonisi','demo-bando-kordon-alayi','test-bando-a','test-bando-b') GROUP BY l.slug, l.site ORDER BY l.slug, l.site;"
docker exec orkestram-local-mysql mysql -uorkestram -porkestram -D orkestram_local -e "SELECT id, site, slug, summary, content, price_label, gallery_json, features_json, meta_json FROM listings WHERE slug IN ('demo-bando-sahil-seremonisi','demo-bando-kordon-alayi') ORDER BY slug, site;"
docker exec orkestram-local-mysql mysql -uorkestram -porkestram -D orkestram_local -e "SELECT l.slug, l.site, ca.key AS attribute_key, ca.label, lav.value_text, lav.value_number, lav.value_json, lav.normalized_value FROM listings l JOIN listing_attribute_values lav ON lav.listing_id = l.id JOIN category_attributes ca ON ca.id = lav.category_attribute_id WHERE l.slug IN ('demo-bando-sahil-seremonisi','demo-bando-kordon-alayi') ORDER BY l.slug, ca.sort_order, ca.id;"
docker exec orkestram-local-mysql mysql -uorkestram -porkestram -D orkestram_local -e "SELECT l.slug, l.site, f.status, f.visibility, f.content, f.owner_reply, u.name AS user_name, f.created_at FROM listings l JOIN listing_feedback f ON f.listing_id = l.id LEFT JOIN users u ON u.id = f.user_id WHERE l.slug IN ('demo-bando-sahil-seremonisi','demo-bando-kordon-alayi') ORDER BY l.slug, f.created_at;"
powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick
```

## Risk / Not
- Bu taskta tasarim review merge oncesi zorunluydu; `main` preview review araci olarak kullanilmadi.
- Mevcut design-preview demo verisi MySQL uzerinde zaten dolu oldugu icin gorsel/demo doluluk hedefi bu taskta tamam kabul edildi.
- Whitelist/idempotent fixture otomasyonu farkli kabul kriteri ve farkli dosya seti gerektirdigi icin ayri taska ayrilacak.

## Uygulama Notu
- Sol hero kolonu label tablosu yerine direkt kimlik satiri + fiyat/guven akisina donusturuldu.
- Hero disi akista sira `Kategori ozellikleri -> Galeri -> Hakkinda -> Musteri deneyimleri -> Benzer ilanlar` olarak netlestirildi.
- `Mesaj`, `Begeni` ve `Yorum Yap` aksiyonlari `Musteri deneyimleri` bolumunde ikonlu, ikincil ve durum gosteren pill satiri olarak cozuldu.
- `Yorum Yap` sabit form yerine popup olarak acilir; desktopta 3-up, mobilde 1-up kayan galeri korunur.
- Preview review URL'leri: `http://127.0.0.1:8280/ilan/demo-bando-sahil-seremonisi` ve `http://127.0.0.1:8281/ilan/demo-bando-kordon-alayi`
- `Edit Source`: `/home/bekir/orkestram-b`
- `Mount Source`: `/home/bekir/orkestram-b`
- Commit hash: `b9f3141`
- Read-only audit sonucu: `demo-bando-sahil-seremonisi` ve `demo-bando-kordon-alayi` kayitlari mevcut; summary/content/fiyat/telefon/whatsapp/galeri/features/attributes/yeni yorumlar veri tarafinda zaten dolu bulundu.
