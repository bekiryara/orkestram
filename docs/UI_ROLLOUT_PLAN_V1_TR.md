# UI Rollout Plan V1

Tarih: 2026-03-11  
Bagli kilit: `UI_DESIGN_LOCK_V1_TR.md`

## 1) Hedef
- Tum uygulamayi tek tasarim diline almak.
- Bootstrap tabanli ortak komponent kullanimi.
- Iki app parity: `orkestram` ve `izmirorkestra` birebir.

## 2) Fazlar
1. Faz A (Tamamlanmis)
- Bootstrap include yapisi eklendi (`bootstrap-head`, `bootstrap-foot`)
- Auth/Portal temel gecis basladi

2. Faz B (Simdi)
- `auth` ekranlarinda ortak layout
- kopya CSS temizligi
- `hesabim` sayfasinda bootstrap form/table/alert standardi

3. Faz C
- `frontend` tam gecis:
  - ana sayfa
  - ilanlar
  - ilan detay
  - hizmet detay
  - sehir sayfasi
 - mesaj girisleri:
   - listing detaydan mesaj aksiyonu
   - guest->login->hedefe donus (`next`) akisi

4. Faz D
- `admin` tam gecis:
  - listeler
  - formlar
  - toplu islem toolbarlari
 - mesaj moderasyon/listeleri (Mesaj V1 ile uyumlu)

## 3) Uygulama Kurallari
1. Yeni global CSS yazilmaz.
2. Inline style kalici cozum olarak kullanilmaz.
3. Her ekran degisikligi iki appte birlikte uygulanir.
4. Her faz sonunda smoke test PASS zorunludur.

## 4) Kabul Kriteri
1. Header/footer dili tum ekranlarda uyumlu.
2. Buton/form/tablo siniflari ortak dilde.
3. Mobilde kirilma yok.
4. Iki app parity bozulmamis.
5. Mesaj ekranlarinda (customer `Mesajlarim`, owner `Ilan Mesajlari`) form + liste dili bootstrap kataloguyla birebir.
6. Listing detaydan mesaja gecis akisi slug elle yazmadan tamamlanir.

## 5) Not
- Bu plan sadece gorunumu kapsar.
- Kategori/SEO/is kurali degisiklikleri ayri planda ilerler.
- Mesaj davranis/planning kaynagi: `MESAJ_V1_TR.md`.
- Legacy engagement planlari sadece arsivdedir: `docs/archive/legacy-engagement/`.
