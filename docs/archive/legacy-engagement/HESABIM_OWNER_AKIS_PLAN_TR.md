# DEPRECATED

Bu dokuman legacy engagement modeline aittir. Aktif mesaj sistemi icin MESAJ_V1_TR.md kullanin.

# Hesabim + Ilan Yonetimi Final Plani (Deterministik)

Tarih: 2026-03-12
Durum: Aktif Plan
Kapsam: `/hesabim` kisisel panel + `/owner` ilan operasyon paneli

## Final Urun Modeli
1. `/hesabim` kisisel hesap ve musteri tarafidir.
2. `/owner` ilan/hizmet operasyon tarafidir.
3. Iki panel ayni tasarim sistemini kullanir, farkli isi cozer.
4. Bu ayrim sabittir, karismaz.

## Terminoloji Karari
1. Sistem ici isimler degismez:
   - route: `/owner`
   - role key: `owner`
   - internal flag: `is_owner`
2. Kullaniciya gorunen isimler:
   - panel adi: `Ilan Yonetimi`
   - role badge: `Ilan Veren`
   - gecis butonu: `Ilan Yonetimine Gec`
   - donus butonu: `Hesap Paneline Don`
3. Kullanilmamasi gereken gorunur metinler:
   - `Owner Paneli`
   - `Ilan Sahibi Paneli`
   - `Owner Dashboard`
4. `Ilan Sahibi` ifadesi sadece sahiplik/yetki baglaminda kullanilir.

## `/hesabim` Final Yapi
1. Genel Bakis
2. Taleplerim
3. Mesajlarim
4. Yorumlarim
5. Sorularim
6. Profilim
7. Guvenlik

### `/hesabim` owner aksiyonu
1. `Ilan Yonetimine Gec` (sidebar sekmesi degil, buton)
2. Konum: `Genel Bakis` ust sag
3. Gorunurluk: sadece owner yetkisi olan kullanici
4. Hedef: `/owner`

## `/owner` Final Yapi
1. Genel Bakis
2. Ilanlarim
3. Isler / Talepler
4. Ilan Mesajlari
5. Yorumlar
6. Sorular
7. Sahiplik ve Yetki

## Route Davranisi (Deterministik)
1. `/hesabim` -> hesap paneli
2. `/owner` -> owner genel bakis
3. `/owner` root ile owner menudeki `Genel Bakis` ayni sayfadir
4. `/hesabim` ve `/owner` ayni icerigi gostermez
5. Active state:
   - `/owner` root: `Genel Bakis`
   - `/owner/listings`: `Ilanlarim`
   - `/owner/...leads...`: `Isler / Talepler`
   - `/owner/engagements?kind=message`: `Ilan Mesajlari`
   - `/owner/engagements?kind=comment`: `Yorumlar`
   - `/owner/engagements?kind=question`: `Sorular`

## Ust Seviye Akis
1. Normal kullanici:
   - login -> `/hesabim`
2. Owner rolune sahip kullanici:
   - login -> `/hesabim`
   - `/hesabim > Genel Bakis` icinden `Ilan Yonetimine Gec` ile `/owner`

## `/hesabim` Genel Bakis Veri Modeli
1. Acik Taleplerim
2. Okunmamis Mesajlar
3. Cevaplanan Sorular
4. Profil Tamamlama
5. Son Talepler
6. Son Mesajlar
7. Son Hareketler
8. Owner ise ek kart:
   - `Ilan Veren hesabinizi aktif`
   - `Ilan Yonetimine Gec`

## `/owner` Genel Bakis Veri Modeli
### A) Ana metrikler
1. Yayindaki Ilanlar
2. Yeni Talepler
3. Okunmamis Ilan Mesajlari
4. Yanit Bekleyen Sorular

### B) Destek metrikler
1. Toplam Ilan
2. Taslak / Pasif Ilanlar
3. Toplam Etkilesim
4. Yeni Yorumlar

### C) Operasyon bloklari
1. Son Gelen Talepler
2. Son Mesajlar
3. Yanit Bekleyen Sorular

## SIKI Kurallar
1. `/owner` dashboard icinde tekrar navigation butonlari olmayacak.
2. `/hesabim` sidebarina owner operasyon sekmeleri karismayacak.
3. `/owner` sadece owner yetkisi olan kullaniciya acik olacak.
4. Owner gecis butonu sadece owner yetkisi varsa gorunur.
5. Iki app parity zorunludur (`orkestram` + `izmirorkestra`).
6. Uygulama ve testler Windows -> WSL senkron disipliniyle kosulur.

## Tasarim Sistemi Kurali
1. `/hesabim` ve `/owner` ayni UI omurgasi:
   - ayni ust bar
   - ayni kart stili
   - ayni sidebar bileseni
   - ayni buton sistemi
   - ayni spacing/hiyerarsi
2. Icerik dili baglama gore ayrilir:
   - `/hesabim`: kisisel
   - `/owner`: operasyon

## Final Kabul Kriterleri
1. `/owner` acildiginda ilk ekran `Genel Bakis`.
2. `/owner` sidebar ilk item `Genel Bakis`.
3. Kullaniciya ham teknik `Owner Paneli` metni gorunmez.
4. Panel ana basligi `Ilan Yonetimi`.
5. Role badge gerekiyorsa `Aktif Rol: Ilan Veren`.
6. `/hesabim` kisisel ozet, `/owner` operasyon ozeti verir.
7. Owner gecis butonu yalniz uygun kullaniciya gorunur.
8. Empty state + permission + active state davranislari deterministik.

