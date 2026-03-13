# UI Design Lock V1 (Kilit Karar)

Tarih: 2026-03-11  
Durum: Kilitli (degisiklik sadece yeni versiyonla)

## 1) Amaç
- Tum ekranlarda tek tasarim dili.
- Rastgele CSS/komponent ayrismasini bitirmek.
- Yeni ajan/gelistirici katkilarinda standart sapmasini engellemek.

## 2) Kilitlenen Kararlar
1. UI omurgasi: `Bootstrap 5.3.x`
2. Tema katmani: `v1.css` sadece marka override ve az sayida scoped duzeltme
3. Diller:
- `frontend`, `portal`, `auth`, `admin` ayni komponent mantigini kullanir
4. Yasaklar:
- Kalici inline style
- Ayni isi yapan ikinci buton/form/kart sistemi
- Sayfa bazli rastgele renk/font setleri

## 3) Marka Paleti (V1 Kilit)
1. Ana renk: `#0F7A7A`
2. Yardimci renk: `#EA5F2D`
3. Zemin: `#F4EFE6`
4. Metin: `#1F1B16`

## 4) URL / Islev Kapsami
- Bu kilit sadece gorunum standardini kapsar.
- Is kurallari (kategori, ilan, SEO davranisi) bu dokumanla degismez.

## 5) Uygulama Kurali
1. Yeni ekran: Once Bootstrap component
2. Sonra mevcut ortak class
3. Mecbur kalinirsa kucuk scoped CSS

## 6) Degisiklik Protokolu
- V1 degisimi yasak.
- Ihtiyac varsa `UI_DESIGN_LOCK_V2_TR.md` acilir.
- V2'de farklar ve nedenleri acik yazilir.

