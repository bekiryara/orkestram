# Orkestram + IzmirOrkestra Yeniden Kurulum Master Plani

Durum:
- Kodlama basladi ve local v1 aktif gelisiyor.
- Mimari, kabul kriterleri ve gecis stratejisi netlestirildi.
- Yonlendirmeler (301) en son asamada acilacak.

## 1) Teknoloji Karari (Net)

Bu proje icin secilen ana stack:

1. Backend: `PHP 8.2/8.3 + Laravel 11`
2. Frontend: `Blade + Vite + Tailwind CSS`
3. Veritabani: `MySQL 8` (hosting uyumu icin)
4. Runtime: Docker (local gelistirme)
5. Deploy hedefi: Turhost cPanel (zip ile yukleme)

Neden bu secim:
- Mevcut hosting (cPanel/PHP) ile en uyumlu.
- SEO ve URL kontrolu Laravel tarafinda temiz yonetilir.
- Gelecekte AI otomasyonu eklemek kolaydir.

Not:
- Node sadece frontend build (Vite/Tailwind) icin kullanilacak.
- Ana is mantigi PHP/Laravel tarafinda kalacak.
- Hostingte PHP 8.4 olmadigi icin Laravel 12 degil, Laravel 11 tabani kullanilacak.

## 2) Proje Kapsami

Hedef:
- `orkestram.net` ve `izmirorkestra.net` icin temiz, hizli, yonetilebilir yeni sistem.
- URL kaybi olmamasi.
- Canli geciste minimum risk.

Kapsam disi (v1):
- Gelismis marketplace/tenant sistemi
- Cok karmasik panel modulleri
- Tam otomatik AI publish (once manuel onayli akis)

## 3) Domain ve Icerik Modeli

Tek kod tabani, iki site:
- Site A: orkestram.net
- Site B: izmirorkestra.net

Canli document root (cPanel'den dogrulandi):
1. `orkestram.net -> /public_html`
2. `izmirorkestra.net -> /izmirorkestra.net`

Icerik tipleri:
1. `pages` (statik/kurumsal sayfalar)
2. `listings` (grup/ilan/profil benzeri sayfalar)
3. `city_pages` (il/ilce landing sayfalari)
4. `blog_posts` (SEO icerikleri)
5. `redirects` (eski -> yeni URL, en son aktif)

## 4) URL Stratejisi (Temel Kural)

1. Once yeni site calisacak.
2. Hedef sayfalar hazir olmadan redirect acilmayacak.
3. Degerli eski URL'ler mumkunse birebir korunacak.
4. Mecburi degisikliklerde birebir niyet eslesmeli 301 kullanilacak.
5. Toplu anasayfa/kategoriye yonlendirme yapilmayacak.

## 5) Fazlar ve Cikti Listesi

## Faz 0 - Plan ve Hazirlik
Sure: 1 gun

Yapilacaklar:
- Mevcut SQL dump'tan URL envanteri cikarma
- Kritiklik siniflandirmasi (degerli/normal/merge/drop)
- Yeni bilgi mimarisi kesinlestirme

Cikti:
- `docs/url_inventory.csv`
- `docs/url_priority.csv`
- `docs/information_architecture.md`
- `docs/LISTING_UX_FLOW_TR.md`

Kabul kriteri:
- Tum eski URL'ler listede olmali.

## Faz 1 - Local Altyapi Kurulumu
Sure: 1-2 gun

Yapilacaklar:
- Docker compose ile local servisler
- Laravel proje iskeleti
- Ortam degiskenleri ve temel app calisirliligi

Cikti:
- Calisan local URL (tek komutla aya kalkma)
- `README_LOCAL.md`

Kabul kriteri:
- `docker compose up -d` sonrasi ana sayfa aciliyor.

## Faz 2 - Veri Modeli ve Admin Cekirdegi
Sure: 2 gun

Yapilacaklar:
- Migration'lar (pages, listings, city_pages, blog_posts, redirects)
- Basit admin CRUD (taslak/yayin durumu)
- SEO alanlari (title, meta, canonical)

Cikti:
- Calisan yonetim paneli v1

Kabul kriteri:
- Yeni sayfa ekleme-duzenleme-yayin yapilabiliyor.

## Faz 3 - Tema ve Sayfa Sablonlari
Sure: 2-3 gun

Yapilacaklar:
- Ana sayfa tasarimi
- Hizmet sayfasi sablonu
- Sehir sayfasi sablonu
- Ilan/profil sayfasi sablonu
- Blog liste + detay sablonu

Cikti:
- Responsive ve hizli frontend v1

Kabul kriteri:
- Mobil/masaustu temel UX duzgun.

## Faz 4 - SEO ve Teknik Katman
Sure: 1-2 gun

Yapilacaklar:
- sitemap.xml
- robots.txt
- canonical ve OG etiketleri
- JSON-LD (Organization/Service/FAQ gerektiginde)
- 404 sayfasi ve loglama

Cikti:
- Teknik SEO checklist PASS

Kabul kriteri:
- Test URL'lerde meta/canonical/sitemap dogru.

## Faz 5 - Icerik Tasima
Sure: 2-4 gun

Yapilacaklar:
- Once ilk 20 kritik sayfa tasima
- Sonra kalanlarin batch tasinmasi
- Her sayfa icin kalite ve niyet kontrolu

Cikti:
- Yeni sitede dolu icerik

Kabul kriteri:
- Kritik sayfalar hazir + kontrol edilmis.

## Faz 6 - Yonlendirme (EN SON)
Sure: 1 gun

Yapilacaklar:
- `url_mapping.csv` kesinlestirme
- 301 kurallarini aktive etme
- 404/redirect testleri

Cikti:
- Canli gecise hazir redirect katmani

Kabul kriteri:
- Eski URL testlerinde dogru hedefe gidis.

## Faz 7 - Canliya Alma ve Stabilizasyon
Sure: 1-2 gun + 14 gun izleme

Yapilacaklar:
- Deploy paketi olusturma
- cPanel yukleme
- Smoke test
- 14 gun hata ve SEO izleme

Cikti:
- Stabil canli sistem

Kabul kriteri:
- Kritik hata yok, 404 anomali yok, index sorunu yok.

## 6) Klasorleme Standardi (Yeni Proje)

```text
D:\orkestram
  /project
    /app
    /bootstrap
    /config
    /database
    /public
    /resources
    /routes
    /storage
  /docs
    url_inventory.csv
    url_mapping.csv
    information_architecture.md
    go_live_checklist.md
  /exports
    orkestram/
    izmirorkestra/
```

## 6.1) Local Port Matrisi (Duzeltilmis)

Not:
- Yeni proje mevcut calisan servislerle cakismayacak.

Yeni local portlar:
1. Orkestram web: `http://localhost:8180`
2. IzmirOrkestra web: `http://localhost:8181`
3. Adminer/phpMyAdmin: `http://localhost:8188`
4. MySQL host port: `3307` (container 3306)

Kural:
- Bu plan kapsaminda 8080 kullanilmayacak.
- Portlar `.env` ile degistirilebilir kalacak.

## 7) Riskler ve Onlemler

Risk:
- URL uyumsuzlugu -> SEO kaybi
Onlem:
- Envanter + mapping + en son redirect

Risk:
- Ic icerik kalitesi dusuk kalmasi
Onlem:
- Sayfa bazli kalite checklist

Risk:
- Canlida beklenmeyen hata
Onlem:
- Geri donus plani + yedek

## 8) Geri Donus (Rollback) Plani

1. Canli gecis oncesi dosya + DB yedegi
2. Sorun cikarsa:
   - Eski site dosyalari geri
   - Eski DB geri
   - DNS/virtual host degisikligi varsa geri al

## 9) Bu Planda Netlesen Kararlar

1. Sifirdan degil, temiz v1 altyapi kurulacak.
2. Stack: Laravel + MySQL + Blade/Tailwind.
3. Iki site tek kod tabani ile yonetilecek.
4. Yonlendirmeler en son asamada aktif edilecek.
5. Once site calisacak, sonra URL eslestirme finalize edilecek.
6. Tasarim stratejisi: `ayni altyapi + farkli tema` (site bazli).
7. Uygulama sirasi:
   - Ilk tamamlama: `orkestram.net`
   - Sonra uyarlama: `izmirorkestra.net`
   - En son: 301 yonlendirmeleri aktif etme

## 9.1) Calismama Riskini Minimize Etme Kurallari (Zorunlu)

1. Deploy once staging/probe dosyasi ile dizin ve PHP kontrolu yapilacak.
2. Sunucuda `composer` calistirma zorunlu degil: paket `vendor` dahil yüklenecek (Plan B).
3. Canli gecisten once:
   - Dosya yedegi
   - Veritabani yedegi
   - `.env` kopyasi
4. `storage` ve `bootstrap/cache` izinleri dogrulanmadan yayin acilmayacak.
5. `APP_KEY`, DB baglantisi, `APP_URL` test edilmeden yayin acilmayacak.
6. Ilk geciste redirectler KAPALI kalacak (faz 6 harici).
7. Smoke test PASS olmadan DNS/kalici gecis yok:
   - Ana sayfa
   - 3 kritik hizmet sayfasi
   - 1 form post testi
   - 404 sayfasi

## 11) Sure Tahmini (Gercekci)

1. Faz 1 tamamlama (local stabil calisir): `0.5 - 1 gun`
2. Faz 2 (veri modeli + admin cekirdek): `1 - 2 gun`
3. Faz 3 (tema + temel sablonlar): `2 - 3 gun`
4. Faz 4 (teknik SEO katmani): `1 gun`
5. Faz 5 (icerik tasima ilk dalga): `2 - 4 gun`
6. Faz 6 (redirect final): `0.5 - 1 gun`
7. Faz 7 (canliya alma + stabilizasyon baslangic): `1 gun`

Toplam aktif kurulum/gelistirme:
- `8 - 13 gun` (icerik yogunluguna gore degisir)

Hizli minimum canli v1:
- `3 - 4 gun` (temel sayfalar + temel SEO + forms)

## 10) Derin Arastirma Sonucu: Hiz Mimarisi (Kanita Dayali)

Bu bolum resmi dokumanlara gore plana eklendi.

Hedef Core Web Vitals:
1. LCP <= 2.5s
2. INP <= 200ms
3. CLS <= 0.1

Mimari karar (hiz icin):
1. Laravel uygulama cache katmanlari:
   - `config:cache`
   - `route:cache`
   - `view:cache`
2. PHP tarafi:
   - OPcache aktif
   - PHP-FPM process ayarlari kontrol
3. HTTP katmani:
   - Brotli/Gzip sikistirma
   - Uygun `Cache-Control` header'lari (static assets uzun sureli)
4. CDN katmani:
   - Cloudflare ile static cache + edge optimizasyon
5. Frontend:
   - Vite build + code split
   - Kritik CSS ve gereksiz JS azaltma
6. Gorsel optimizasyon:
   - AVIF/WebP tercih
   - lazy-load + boyutlandirma

V1 performans hedefleri:
1. TTFB (cache hit): < 300ms
2. Ana sayfa LCP (mobil): < 2.5s
3. HTML boyutu: 150KB alti (hedef)
4. 404 orani: gecis sonrasi ilk 14 gunde hizla dusus

Performans dogrulama rutini:
1. Her onemli sayfada Lighthouse raporu (mobil/desktop)
2. Search Console Core Web Vitals izleme
3. Sunucu hata ve yavas sorgu loglari
4. Haftalik performans snapshot

Kaynaklar:
1. Laravel deployment optimization:
   - https://laravel.com/docs/11.x/deployment
2. Laravel cache:
   - https://laravel.com/docs/11.x/cache
3. PHP OPcache:
   - https://www.php.net/manual/en/book.opcache.php
4. cPanel cache docs:
   - https://docs.cpanel.net/knowledge-base/web-services/what-is-cache-control/
5. Core Web Vitals:
   - https://web.dev/articles/lcp
   - https://web.dev/articles/inp
   - https://web.dev/articles/cls
6. HTTP cache headers (reference):
   - https://developer.mozilla.org/en-US/docs/Web/HTTP/Caching
7. Image optimization and modern formats:
   - https://web.dev/articles/choose-the-right-image-format

## 12) UX Plan Dokumanlari

1. Ilan deneyimi ve gezinme akisi:
   - `docs/LISTING_UX_FLOW_TR.md`
2. Local bitirme ve deploy kilidi:
   - `docs/LOCAL_FINISH_PLAN_TR.md`
3. Repo disiplini ve teknik borc kapisi:
   - `docs/REPO_DISCIPLINE_TR.md`
