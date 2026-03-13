# Kategori Ozellik Sozlesmesi (Faz 1)

Tarih: 2026-03-12  
Durum: Faz 1 omurga aktif

## Amac
1. Kategori bazli dinamik ilan alanlarini deterministik hale getirmek.
2. Filtrede hangi alanlarin gosterilecegini veriyle yonetmek.
3. Iki appte (`orkestram`, `izmirorkestra`) ayni kural setini kilitlemek.

## Kapsam (Faz 1)
1. Veri modeli omurgasi:
   - `category_attributes`
   - `listing_attribute_values`
2. Model iliskileri:
   - `Category -> attributes`
   - `Listing -> attributeValues`
3. Bu fazda UI yok; sadece schema + domain omurgasi.

## Ana Kurallar
1. Her ozellik bir kategoriye baglidir.
2. Ozellik anahtari (`key`) kategori icinde unique olmalidir.
3. Ozellik aktif/pasif olabilir (`is_active`).
4. Filtrede gorunurluk veriyle yonetilir (`is_filterable`).
5. Zorunluluk veriyle yonetilir (`is_required`).
6. Kart/detay gorunurlugu veriyle yonetilir (`is_visible_in_card`, `is_visible_in_detail`).

## Alan Tipleri
1. `text`
2. `number`
3. `select`
4. `multiselect`
5. `boolean`

## Deger Saklama Prensibi
1. Tek metin degeri: `value_text`
2. Sayisal deger: `value_number`
3. Evet/Hayir: `value_bool`
4. Coklu secim veya kompleks deger: `value_json`
5. Her ilan + ozellik cifti tek satirdir (`unique(listing_id, category_attribute_id)`).

## Sonraki Fazlar
1. Faz 2:
   - Admin kategori ozellik yonetimi (CRUD)
   - Kategoriye gore dinamik ilan formu
2. Faz 3:
   - Public filtrede dinamik alanlar
   - Coverage matrisiyle filtre davranisi entegrasyonu
3. Faz 4:
   - Gecmis ilan verisi backfill/normalizasyon
   - Uctan uca regression test matrisi

