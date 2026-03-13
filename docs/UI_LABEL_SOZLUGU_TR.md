# UI Label Sozlugu (TR)

Tarih: 2026-03-12  
Durum: Aktif Standard

## Amac
- Portal ekranlarinda kullanilan gorunur metinleri tek kaynaktan yonetmek.
- `hesabim`, `owner`, `customer` ve ortak menulerde terim dagilimini engellemek.

## Genel Kurallar
1. Son kullaniciya gosterilen tum label'lar Turkce olacak.
2. Ayni anlam icin tek label kullanilacak.
3. Kod icindeki teknik adlar (slug/route/controller) degistirilmeyebilir; UI metni bu sozluge gore gosterilir.

## Rol Label'lari
- `listing_owner` -> `Ilan Veren`
- `customer` -> `Musteri`
- `support_agent` -> `Destek`

## Ortak Menu Label'lari
- `Hesabim`
- `Ilanlar`
- `Giris`
- `Kayit Ol`
- `Admin`

## `/hesabim` Standart Label'lari
- `Genel Bakis`
- `Taleplerim`
- `Mesajlarim`
- `Yorumlarim`
- `Profilim`
- `Guvenlik`
- Owner aksiyonu: `Ilan Yonetimine Gec`

## `/owner` Standart Label'lari
- Panel basligi: `Ilan Yonetimi`
- Role badge: `Aktif Rol: Ilan Veren`
- Gecis/donus butonlari:
  - `Ilan Yonetimine Gec`
  - `Hesap Paneline Don`
- Menu:
  - `Genel Bakis`
  - `Ilanlarim`
  - `Isler / Talepler`
  - `Ilan Mesajlari`
  - `Yorumlar`
  - `Sahiplik ve Yetki Ayarlari`
- Form/sayfa basliklari:
  - `Yeni Ilan`
  - `Ilan Duzenle`

## `/customer` Standart Label'lari
- `Talep Paneli`
- `Taleplerim`
- `Mesajlarim`
- `Hesabim`

## Yasakli / Kullanilmayacak Varyantlar
- `Owner Dashboard`
- `Owner Ilanlar`
- `Owner Leadler`
- `Yeni Owner Ilani`
- `Owner Ilani Duzenle`
- `Ilan Sahibi Paneli` (ana baslik olarak)
- `Etkilesim Merkezi` (owner panel ana menu etiketi olarak)
- `Etkilesimlerim` (mesaj merkezi etiketi olarak)

## Uygulama Notu
- Iki app parity disiplini zorunludur (`apps/orkestram` + `apps/izmirorkestra`).
- Label degisikliklerinde regression test + smoke tekrar calistirilir.
- SIKI KURAL: Owner kullanici hesap akisinda `/hesabim` menu yapisini kullanir.
- SIKI KURAL: `/owner` paneli `/hesabim` panelini ornek alir; ayni UI iskeleti korunur, yalnizca operasyon icerigi owner'a ozeldir.
