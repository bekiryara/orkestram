# Paralel Sistem Gelistirme Plani (TR)

Durum:
- Icerik ekibi `docs` ve import kalitesi uzerinde calisirken, teknik ekip sistemin guvenlik, test ve operasyon katmanini paralel gelistirir.
- Hedef: canli gecis riskini dusurmek ve gecis gunu surprizini sifira yaklastirmak.

Guncel ilerleme (2026-03-09):
1. Faz 1 tamamlandi:
   - `admin/*` anonim erisime kapatildi.
   - admin rate limit eklendi.
2. Faz 2 kismi tamamlandi:
   - public/SEO route testleri ve admin auth testleri eklendi.
   - test DB izolasyonu sqlite `:memory:` olarak ayarlandi.
3. Faz 2 acik kalem:
   - redirect middleware davranis testlerinin genisletilmesi.

## 1) Amac (Net)

1. Admin ve redirect katmaninda guvenlik/regresyon risklerini azaltmak.
2. Kritik URL akislari icin test kapsamini uretim oncesi minimum seviyeye getirmek.
3. Release surecini "PASS olmadan deploy yok" prensibine baglamak.
4. Performans icin olculebilir baseline ve iyilestirme adimlari olusturmak.

## 2) Kapsam

Dahil:
1. Guvenlik sertlestirme (admin erisimi, rate limit, temel validation)
2. Feature/regresyon testleri
3. Smoke test kapsam genisletme
4. Redirect kalite guvencesi (loop/invalid hedef kontrolu)
5. Release checklist ve operasyon netligi

Kapsam disi (bu plan):
1. Buyuk UI redesign
2. Yeni domain/tenant mimarisi
3. Agir refactor

## 3) Fazlar ve Cikti Listesi

## Faz 1 - Guvenlik Sertlestirme
Sure: 0.5 - 1 gun

Yapilacaklar:
1. `admin/*` rotalarina zorunlu kimlik dogrulama middleware'i
2. Admin alanina rate limit politikasi
3. Kritik form alanlarinda validation netligi

Cikti:
1. Admin alaninin anonim erisime kapali olmasi
2. Asiri isteklerde kontrollu limit cevabi

Kabul kriteri:
1. Anonymous request ile `admin/*` 200 donmemeli.
2. Dogru kimlik ile admin CRUD sayfalari acilmali.

## Faz 2 - Test Kapsami
Sure: 1 - 1.5 gun

Yapilacaklar:
1. Public sayfalar: `/`, `/ilan/{slug}`, `/sehir/{slug}`, `/sayfa/{slug}`
2. Teknik SEO endpointleri: `/robots.txt`, `/sitemap.xml`
3. Admin erisim testleri (auth zorunlulugu)
4. Redirect middleware davranis testleri

Cikti:
1. Kritik akislari kapsayan feature test seti

Kabul kriteri:
1. Testler localde deterministik PASS olmali.
2. Redirect devre disi/aktif durumlari testle dogrulanmali.

## Faz 3 - Release ve Operasyon Sertlestirme
Sure: 0.5 - 1 gun

Yapilacaklar:
1. `smoke-test.ps1` kapsamina kritik rotalarin eklenmesi
2. `release.ps1` hattinda PASS kapisi netligi
3. Deploy oncesi kontrol adimlarinin dokumantasyon senkronu

Cikti:
1. Tek komut release akisinda guvenli gecis

Kabul kriteri:
1. Smoke FAIL durumunda paketleme veya gecis devam etmemeli.
2. Dokuman ve script komutlari birebir uyusmali.

## Faz 4 - Performans Baseline
Sure: 0.5 gun

Yapilacaklar:
1. Kritik endpoint latency olcumu (cold/warm)
2. Baseline raporu olusturma
3. Iyilestirme adimlarini onceliklendirme

Cikti:
1. Karsilastirilabilir performans olcum tablosu

Kabul kriteri:
1. En az 3 endpoint icin cold/warm olcum mevcut olmali.

## 4) Riskler ve Onlemler

Risk:
- Guvenlik degisiklikleri admin akisini kilitleyebilir.
Onlem:
- Kucuk adim, her adim sonrasi smoke/feature test.

Risk:
- Testler flaky olabilir.
Onlem:
- Seeded veri + deterministic assertion.

Risk:
- Performans duzeltmesi yan etki uretebilir.
Onlem:
- Baseline once, degisiklik sonra, karsilastirma zorunlu.

## 5) Rollback Plani

1. Her faz degisikligi atomik tutulacak.
2. Sorunlu fazda sadece ilgili dosyalar geri alinacak.
3. Release hattinda FAIL durumunda deploy tamamen durdurulacak.

## 6) Teslim Kriterleri

1. Admin anonim erisime kapali.
2. Kritik URL feature testleri PASS.
3. Smoke testi genisletilmis ve PASS kapisi aktif.
4. Redirect davranisi testlerle garanti altinda.
5. Performans baseline dokumani olusmus.
