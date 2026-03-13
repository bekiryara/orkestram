# Ilan UX Akis Plani (v1)

Amac:
- Ilanlarin daginik gorunmesini bitirmek.
- Kullaniciya net bir gezinme akisi vermek.
- SEO degerini koruyarak, il/ilce + hizmet niyetine uygun sayfa hiyerarsisi kurmak.
- Iki domain icin tek altyapi, site bazli farkli tema ile yonetmek.

Kapsam:
- Listeleme sayfasi
- Filtreleme/siralama
- Ilan karti
- Ilan detay (single)
- Breadcrumb ve ic link yapisi
- Donusum alanlari (WhatsApp / Teklif formu / Arama)

Site stratejisi:
1. `orkestram.net`: Once burada tam UX ve icerik akisi tamamlanir.
2. `izmirorkestra.net`: Ayni altyapi uzerinde ayri tema/icerik ile uyarlanir.
3. 301 yonlendirmeler sistem stabil olduktan sonra acilir.

## 1) Sayfa Tipleri

1. `Ana Sayfa`
2. `Kategori Sayfasi` (orn: dugun-orkestrasi)
3. `Sehir Sayfasi` (orn: izmir-orkestra)
4. `Ilce Sayfasi` (orn: izmir-karsiyaka-orkestra)
5. `Ilan Liste Sayfasi` (kategori+konum kombinasyonlari)
6. `Ilan Detay Sayfasi` (single listing)

## 2) Kullanici Akisi

1. Kullanici ana sayfadan kategori veya sehir secer.
2. Liste sayfasinda filtrelerle sonuca iner:
   - sehir
   - ilce
   - butce araligi
   - muzik turu
   - etkinlik tipi
3. Kullanicinin primary aksiyonu:
   - `Detaylari Incele`
4. Detay sayfasinda primary CTA:
   - `WhatsApp ile Ulas`
   - `Teklif Al` (form)
   - `Telefon` (varsa)

## 3) Liste Sayfasi Kurallari

Zorunlu bloklar:
1. H1 (arama niyeti ile uyumlu)
2. Kisa aciklama (80-160 kelime)
3. Filtre bar
4. Sonuc adedi + siralama
5. Ilan kart gridi
6. Sayfalama
7. Alt SEO metni (250-500 kelime, tekrarsiz)
8. FAQ (2-5 soru)

Siralama secenekleri:
1. Onerilen
2. Fiyat artan
3. Fiyat azalan
4. En yeni

## 4) Ilan Karti Standardi

Her kartta:
1. Kapak gorsel (sabit oran)
2. Ilan basligi
3. Konum (il/ilce)
4. Baslangic fiyat
5. Kisa puan/guven sinyali (opsiyonel)
6. CTA: `Detaylari Incele`

Kalite kurali:
- Baslik 60 karakter alti.
- Gorsel yoksa fallback gorsel kullan.
- Kart CTA metni tum sitede tek tip kalir.

## 5) Ilan Detay (Single) Standardi

Zorunlu bolumler:
1. Baslik + konum + mini guven rozetleri
2. Gorsel galeri / video
3. Kisa ozet (ilk ekran)
4. Paketler veya fiyat bilgisi
5. Hizmet kapsami (madde madde)
6. SSS
7. Benzer ilanlar (3-6 adet)
8. CTA paneli (sticky mobilde)

SEO alanlari:
1. Unique title
2. Unique meta description
3. Canonical
4. JSON-LD (`LocalBusiness` veya uygun tip)

## 6) Bilgi Mimarisi ve Ic Link

Breadcrumb:
1. Ana Sayfa
2. Kategori
3. Sehir/Ilce
4. Ilan

Ic linkleme:
1. Sehir sayfasindan ilgili ilce sayfalarina link
2. Ilce sayfasindan ilgili ilanlara link
3. Ilan detaydan kategori + sehir sayfalarina geri link

## 7) Admin Panel Icerik Akisi

Ilan alanlari (minimum):
1. Baslik
2. Slug
3. Site (`orkestram` / `izmirorkestra`)
4. Kategori
5. Sehir
6. Ilce
7. Baslangic fiyat
8. Kapak + galeri
9. Kisa aciklama
10. Detay icerik
11. SEO title/description
12. Yayin durumu

Yayin sureci:
1. Taslak kaydet
2. Onizleme
3. Yayinla
4. Guncelleme tarihi logla

## 8) Fazli Uygulama Plani

Faz A (hemen):
1. Ilan kart standardi
2. Liste sayfasi filtre/siralama
3. Ilan detay iskeleti

Faz B:
1. FAQ ve schema
2. Benzer ilanlar algoritmasi
3. Donusum takip olaylari

Faz C:
1. AI destekli icerik zenginlestirme (manuel onayli)
2. Dinamik landing varyasyonlari

## 9) Kabul Kriterleri

1. Kullanici en fazla 3 tikla ana sayfadan ilana ulasir.
2. Liste sayfasi CLS problemi olmadan acilir.
3. Ilan detayda en az 2 CTA gorunur.
4. Mobilde sticky CTA calisir.
5. Her yayindaki ilanda SEO title + meta doludur.
6. 404'e dusen listing URL orani gecis sonrasi azalir.

## 10) Olcumleme (Ne Kazandirir)

1. Organik tiklanma artisi (kategori/sehir niyeti)
2. Detay sayfasina gecis orani artisi (CTR)
3. WhatsApp / form donusum artisi
4. Hemen cikma orani dususu
5. Site ici gezinme derinligi artisi

Izlenecek KPI:
1. Listing card CTR
2. Detail page conversion rate
3. Organic sessions (listing + city pages)
4. Indexed URL sayisi
5. 404 hit sayisi

