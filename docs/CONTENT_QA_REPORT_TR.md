# Content QA Raporu (TR)

## 1. Ozet
- Bu rapor 2026-03-09 tarihinde docs altindaki import ve mapping dosyalari baz alinarak olusturuldu.
- Import sablonlari gercek kullanima uygun ornek satirlarla guncellendi.
- URL mapping dosyasina kalite etiketi (quality_label) eklendi.

## 2. URL mapping durumu (exact/close/missing sayilari)
- Toplam satir: 284
- exact: 185
- close: 99
- missing: 0

Kalite etiketi kurali:
- exact: old_url ve new_url path'i birebir ayni
- close: niyet yakin (hub yonlendirmesi veya manuel dogrulama gerekli)
- missing: new_url bos veya karsilik tanimsiz

## 3. SEO eksikleri
Kontrol edilen alanlar: title, meta_description, slug

Eksik alanlar:
- Eksik kayit bulunmadi.

Yinelenen slug:
- Yinelenen slug bulunmadi.

Yinelenen title:
- Yinelenen title bulunmadi.

## 4. Gorsel eksikleri
Not: Mevcut import sablonlarinda kapak gorseli icin kolon bulunmadigi icin sayfa/ilan kayitlari gorsel acisindan eksik kabul edilmistir.

Eksik gorseller (sayfa/ilan):
- page:ana-sayfa | kapak_gorseli_alani_yok
- page:hakkimizda | kapak_gorseli_alani_yok
- page:iletisim | kapak_gorseli_alani_yok
- listing:beste-muzik | kapak_gorseli_alani_yok
- listing:grup-moda | kapak_gorseli_alani_yok
- listing:izmir-bandosu | kapak_gorseli_alani_yok

## 5. Canliya gecis oncesi kalan isler
1. Import pipeline icin kapak gorseli alani (or: cover_image_url) kararini ver ve importer'a ekle.
2. close etiketli URL'leri manuel inceleyip kritik olanlari exact hedeflere cek.
3. missing etiketli satir varsa hedef URL tanimlarini tamamla.
4. Import sonrasi admin panelde slug/canonical ve yayin tarihlerini son kez dogrula.


