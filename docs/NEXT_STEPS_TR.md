# Sonraki Adimlar (Guncel)

## Tamamlananlar
1. Local altyapi ayakta: iki site + MySQL + admin arayuzleri calisiyor.
2. `pages/listings/city_pages/redirects` migration ve admin CRUD aktif.
3. Temel SEO endpointleri (`robots.txt`, `sitemap.xml`) aktif.
4. Icerik import sablonlari guncel ve QA raporu olusturuldu.
5. Admin guvenligi eklendi: Basic Auth + admin rate limit.
6. Feature test temeli eklendi.
7. Mesaj sistemi tek kaynak dokumani kilitlendi: `MESAJ_V1_TR.md`.
8. Etkilesim merkezi temizlik plani kilitlendi: `ETKILESIM_MERKEZI_TEMIZLIK_PLANI_TR.md`.
9. Feedback sistemi tek kaynak dokumani kilitlendi: `FEEDBACK_V1_TR.md`.
10. Yetki/route terminolojisi `feedback` + `message` olarak netlestirildi; legacy `engagement` aliaslari kaldirildi.
11. Controller/test adlandirmasi da `feedback` diline tasindi (`FeedbackController`, `ListingFeedbackFlowTest`, `FeedbackModerationAccessTest`).

## Simdi (Oncelikli)
1. Hesabim + Owner hibrit akis Faz 2:
   - `/hesabim` menu yapisini plan kilidine gore koru:
     - `Genel Bakis`, `Taleplerim`, `Mesajlarim`, `Yorumlarim`, `Profilim`, `Guvenlik`
   - `/owner` menu yapisini plan kilidine gore koru:
     - `Genel Bakis`, `Ilanlarim`, `Isler / Talepler`, `Ilan Mesajlari`, `Yorumlar`, `Sahiplik ve Yetki Ayarlari`
   - owner gecis aksiyonunu tek kapida tut:
     - `/hesabim > Genel Bakis` icinde `Ilan Yonetimine Gec` (`btn-primary`)
   - gorunur rol etiketi standardi:
     - `listing_owner` => `Ilan Veren`
   - `/hesabim` ve `/owner` ekranlarinda ayni UI iskeletini koru (menu yerlesimi + kart/tablo/buton hiyerarsisi)
   - Mesaj/sohbet davranisinda tek referans olarak `MESAJ_V1_TR.md` kullan.
   - `UI_LABEL_SOZLUGU_TR.md` ile satir satir label uyum taramasi (sapma listesi + duzeltme)
   - son UI temizlik/polish turu + mobil kirilim son kontrol
2. Release hattinda PASS olmadan paketleme kilidini netlestir.
3. Performans baseline olcum raporu cikar.
4. Runtime izin sertlestirme:
   - `storage` + `bootstrap/cache` izinlerini kalici startup adimina al
   - `storage/framework/views` yazma hatasi icin preflight FAIL/PASS kontrolu ekle
5. Service area fallback daraltma:
   - backfill eslesmeyen `listing_service_areas` satirlarini raporla
   - string fallback'i adim adim azaltip tam ID zorunlulugu gecis planini kilitle
6. Final calistirma disiplini:
   - en son adimda Windows -> WSL senkronu al (`scripts/dev-up.ps1 -App both`)
   - senkron sonrasi smoke + kritik regression testlerini kos

## Sonraki Sprint
1. cPanel predeploy checklist ile canli gecis provasini tamamla.
2. Son fazda URL mapping ve sehir/listing sayfa operasyonunu kapat.
3. Hibrit akis genisletme:
   - owner `Sahiplik ve Yetki Ayarlari` ekranini operasyonel aksiyonlarla genislet
   - `/owner` altinda ilan mesajlari/yorumlar icin filtre ve hizli islem derinligi arttir
