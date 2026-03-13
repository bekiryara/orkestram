# DEPRECATED

Bu dokuman legacy engagement modeline aittir. Aktif mesaj sistemi icin MESAJ_V1_TR.md kullanin.

# Owner Ilan Mesajlari Sohbet Spec (TR)

Tarih: 2026-03-12
Durum: Aktif Plan
Kapsam: `/owner/engagements?kind=message` (Ilan Mesajlari)

## Amac
`Ilan Mesajlari` ekranini moderasyon tablosu gorunumunden cikarip operasyonel inbox/sohbet akisina tasimak.

## Final Karar
1. Ekran tablo-merkezli degil, sohbet-merkezli olmalidir.
2. Iki sutunlu yapi kullanilir:
   - Sol: Konusmalar listesi
   - Sag: Secilen konusma detayi
3. Ustte filtreler bulunur.
4. Onay/moderasyon dili yerine mesaj dili kullanilir.

## Ekran Yapisi
1. Baslik: `Ilan Mesajlari`
2. Aciklama: `Ilanlariniza gelen mesajlari burada yonetin ve konusma gecmisini takip edin.`
3. Donus butonu: `Hesap Paneline Don`
4. Sol panel basligi: `Konusmalar`
5. Sag panel basligi: `Konusma Detayi`

## Filtreler
1. Durum sekmeleri:
   - `Tumu`
   - `Yeni`
   - `Yanit Bekleyen`
   - `Okunmamis`
   - `Arsiv`
2. Ilan filtresi:
   - `Tum Ilanlar`
   - secili ilana gore filtre

## Durum Etiketleri (UI)
1. `Yeni`
2. `Okundu`
3. `Yanit Bekliyor`
4. `Cevaplandi`
5. `Arsivlendi`

Not:
- Veri modeli mevcut durumda teknik status alanlariyla calisabilir.
- UI'da yalnizca yukaridaki etiketler gosterilir.

## Yardimci Aksiyonlar
1. `Gonder` (ana aksiyon)
2. `Ilana Git`
3. `Arsivle`
4. `Konusmayi Kapat`

## Yasaklar
1. Mesaj ekraninda `approved` gibi moderasyon/onay dili gorunmeyecek.
2. `Hizli Kaydet` aksiyonu kullanilmayacak.
3. `Ilanlarima Gelenler / Benim Gonderdiklerim` cift tablo yapisi kullanilmayacak.

## Gecis Kurali
Sohbet modeline tam gecis tamamlanana kadar:
1. Tek liste + tek detay paneli korunur.
2. Mesaj akisi tutarliligi bozulmaz.
3. Deterministik owner ayirimi korunur.

## Parity ve Dogrulama
1. Iki app parity zorunlu:
   - `apps/orkestram`
   - `apps/izmirorkestra`
2. Uygulama sonrasi:
   - hedef feature testler
   - smoke testi
3. Final adim:
   - Windows -> WSL senkronu sonra test/smoke

