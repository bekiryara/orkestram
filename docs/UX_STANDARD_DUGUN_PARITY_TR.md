# UX Standardi - Dugun Parity (TR)

Tarih: 2026-03-13  
Gorev: TASK-017  
Ajan: codex-b

## Hedef
- `listing -> detay -> hesabim -> owner` akislarinda gorunen kaliteyi tek standarda baglamak.
- 360px / 768px / 1024px kirilimlarinda tasma, hizalama ve aksiyon erisimi sorunlarini sifirlamak.
- `orkestram` ve `izmirorkestra` arasi tam parity korumak.

## Kural Seti

### 1) Izgara ve kart standardi
- Liste kartlari tum sayfalarda ayni spacing ritmini izler (`gap`, `padding`, `radius`).
- Kart iceriginde CTA her zaman kart altina yaslanir (dikey hiyerarsi sabit).
- Uzun metinler kart tasmasi yaratmaz; kart yuksekligi uyumlu kalir.

### 2) Tipografi ve metin okunurlugu
- Baslik/alt baslik satir yuksekligi mobilde sikismayi onleyecek sekilde olmalidir.
- `page-subtitle` benzeri aciklama metinleri 72 karakter satir uzunlugu civarinda tutulur.
- Meta bilgiler (sehir, ilce, kategori) ana basligi bastirmayacak sekilde ikincil tonla gosterilir.

### 3) Responsive davranis
- `1024px`: masaustu-tablet gecisinde sidebar/genislik dengesi bozulmaz.
- `768px`: detay sayfasi tek kolona iner, sticky filtre paneli statik moda doner.
- `480px ve alti`: CTA butonlari tam genislikte, kart/hero padding daraltmis ve ergonomik olur.

### 4) Aksiyon hiyerarsisi
- Birincil aksiyon (`btn-primary`) her ekranda en gorunur konumda.
- Ikincil aksiyonlar (`btn`, `btn-secondary`) birincili golgelemez.
- Mobilde ust uste gelen aksiyonlarda tiklanabilir alanlar minimum 38px yukseklikte kalir.

### 5) Parity kurali
- `orkestram` ve `izmirorkestra` icin ayni layout hiyerarsisi + ayni breakpoint davranisi korunur.
- CSS/Blade degisiklikleri iki uygulamada birlikte yapilir; tek tarafta birakilmaz.

## Gorsel QA Checklist

### A) 360px kontrolu
- [ ] Hero bolumunde baslik + CTA satir tasmasi yok.
- [ ] Listing kartlari tek sutunda, butonlar tam-genislikte ve ulasilabilir.
- [ ] Detay sayfasinda galeri, bilgi bloklari ve CTA paneli ust uste binmiyor.
- [ ] Breadcrumb satiri tasmiyor, satir kirimi kontrollu.

### B) 768px kontrolu
- [ ] Listing filtre paneli ve sonuc paneli hiyerarsi kaybetmiyor.
- [ ] Grid 2 sutunda dengeli, kart yukseklikleri gozle uyumlu.
- [ ] Detay sayfasi bloklari okunur spacing ile akiyor.

### C) 1024px kontrolu
- [ ] Sidebar + sonuc alani oranlari dengeli.
- [ ] Kart gridinde beklenmeyen satir atlama/uzama yok.
- [ ] Tipografi desktop ritmiyle tutarli.

### D) Portal/Owner hiz testi
- [ ] Hesabim ve Owner ekranlarinda kart spacing bozulmasi yok.
- [ ] Mesaj/aksiyon bolumlerinde sticky/scroll davranisi mobilde stabil.

## Internetten Indirilen Gorseller - Risk Not Listesi

Bu tur boyunca codebase icinde kaynak/lisans metadatasiyla eslenmis yeni bir internet gorseli tespiti yapilmadi.

| ilan/slg | kaynak/lisans riski | degistirme ihtiyaci | not |
|---|---|---|---|
| `tespit-yok` | Bilinmiyor (metadata yoksa varsayilan risk: orta) | Gerekirse lisansli stok veya kurum ici gorselle degistir | Sonraki adimda medya envanteri + lisans kayit tablosu onerilir |

## Uygulanan Teknik Duzenlemeler (TASK-017)
- Frontend kart/grid/spacing standardi `v1.css` uzerinden teklesitirildi.
- 1024/768/480 kirilimlari icin responsive duzeltmeler eklendi.
- Iki app CSS birebir esit tutuldu.

## TASK-019 - Devralinan Baseline
- Mevcut `v1.css` token yapisi (`:root` + `.site-izmir`) ve responsive kirilimlari devralindi.
- Home/listings sayfalarindaki mevcut grid/container semantigi korunarak kirilmama odagi surduruldu.
- Is kurali degisimi yapilmadan sadece sunum katmani hedeflendi.

## TASK-019 - Eklenenler
- Frontend layout icinde header/footer cagirilari ortak frontend partiallara tasindi:
  - `frontend.partials.site-shell-header`
  - `frontend.partials.site-shell-footer`
- Iki app icin ayni yapida partial dosyalari eklendi, parity korundu.
- `v1.css` icinde ortak iskelet tokenlari netlestirildi:
  - `--container-max`
  - `--space-section`
  - `--space-grid`
  - `--space-card`
- Bu tokenlar dogrudan temel iskelet siniflarina baglandi:
  - `.wrap`
  - `.section`
  - `.grid`
  - `.card`
  - `.listing-layout`
