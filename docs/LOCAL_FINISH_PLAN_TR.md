# Local Bitirme Plani (Deploy Kilitli)

Kural:
1. Bu plan tamamlanmadan deploy yok.
2. Kullanici `tamam` demeden cPanel/canliya gecis yok.

Durum (2026-03-09):
1. Faz 1: Kismi Tamam
2. Faz 2: Kismi Tamam
3. Faz 3: Kismi Tamam
4. Faz 4: Acik
5. Faz 5: Acik
6. Faz 6: Acik
7. Mimari Refactor Sprint: Basladi (`CLEAN_ARCHITECTURE_SPRINT_PLAN_TR.md`)
8. Deterministik teslim akisi aktif (`DETERMINISTIC_DELIVERY_FLOW_TR.md`)
9. Kategori omurga plani aktif (`CATEGORY_TAXONOMY_PLAN_TR.md`)

## Faz 1 - Admin Ilan Modeli (Profesyonel Giris)

Hedef:
- Ilan verisinin duzgun ve standard girilmesi.

Yapilacaklar:
1. `listing` alanlarini netlestir:
   - baslik, slug, sehir, ilce, hizmet_turu
   - fiyat, ozet, detay_icerik
   - whatsapp, telefon
   - seo_title, seo_description
   - durum (draft/published)
2. Zorunlu alan validasyonlari.
3. Admin form UX iyilestirmesi (bolumlu form).

Kabul:
1. Eksik zorunlu alanla kayit alinmaz.
2. Kayit ac/guncelle/yayin akisi sorunsuz calisir.
3. Dokuman guncellemesi zorunlu (`PROJECT_STATUS_TR.md`).

## Faz 2 - Medya Sistemi (Resim Yukleme)

Hedef:
- Ilan karti ve detay sayfasi icin stabil gorsel akisi.

Yapilacaklar:
1. Kapak gorsel alanini ekle.
2. Galeri alanini ekle.
3. Dosya kayit yolu:
   - `public/uploads/listings/{slug}/`
4. Gorsel fallback mekanizmasi (bossa default).
5. Adminde gorsel onizleme.

Kabul:
1. Yeni resim yuklenir, kartta/detayda gorunur.
2. Eksik resimde fallback calisir.
3. Upload hatasi varsa formda acik hata mesaji gorulur (sessiz 500 yok).

## Faz 3 - Frontend Listing UX Final

Hedef:
- Kart, filtre, detay akisini profesyonel seviyeye cekmek.

Yapilacaklar:
1. Kart standardi:
   - kapak, baslik, konum, fiyat, ozet, CTA
2. Liste filtreleri:
   - sehir, ilce, hizmet, arama, siralama
3. Detay:
   - hero alan, fiyat/ozellikler, galeri, CTA paneli
4. Benzer ilanlar blogu.

Kabul:
1. Mobil ve desktop duzgun.
2. 3 tikta ana sayfadan ilana ulasilir.

## Faz 4 - Tema Sistemi (Iki Domain Ayrimi)

Hedef:
- Ayni altyapi, farkli tema.

Yapilacaklar:
1. `orkestram` tema tokenlari.
2. `izmirorkestra` tema tokenlari.
3. Baslik/metin tonu site bazli ayrim.

Kabul:
1. Iki domain ayni kodla, farkli gorunumle acilir.

## Faz 5 - Local QA ve Onay Kapisi

Hedef:
- Canli oncesi teknik kaliteyi localde kesinlestirmek.

Yapilacaklar:
1. Smoke test:
   - `/`, `/ilanlar`, 1 listing detay, 1 sehir sayfasi
2. Hata log kontrolu.
3. Hiz olcum:
   - warm response sureleri
4. Onay raporu ciktisi.

Kabul:
1. Kullanici son onay vermeden deploy adimi acilmaz.

## Faz 6 - Icerik Hazirlama (En Son)

Hedef:
- Gercek icerik ve URL esleme operasyonunu kapanisa almak.

Yapilacaklar:
1. Gercek 5-6 ilanin final icerik girisi.
2. Son asama sehir/listing sayfa doldurma (ihtiyac kadar).
3. SEO alanlari + URL mapping final kontrol.
4. 301 planini canli oncesi son kilit.

Kabul:
1. Gercek icerikler ve URL eslemeleri QA'dan gecer.

## Deploy Kilidi (Zorunlu)

Bu kosullar olmadan deploy YASAK:
1. Faz 1-6 tamamlanmis olacak.
2. Kullanici acikca `tamam, deploy et` yazacak.
3. Son smoke test PASS olacak.
