# Kategori Sistemi Kural Matrisi (TR)

Bu dokuman, `orkestram` ve `izmirorkestra` icin ortak ve deterministik davranis kurallarini kilitler.

## 1) Konum Davranisi (Mode A)

| Alan | Kural |
|---|---|
| Sehir filtre secenekleri | Sadece aktif ve yayinda olan ilanlardan gelir (listing-bound) |
| Ilce filtre secenekleri | Secilen sehir + aktif/yayinda ilanlarin bagli oldugu ilcelerden gelir |
| Buyuk-kucuk harf farki | Tekillestirme case-insensitive yapilir (`Izmir` ve `izmir` tek sehir olur) |
| Kaynak sirasi | Varsa `location_city_id/location_district_id` oncelikli; yoksa metin fallback |

## 2) Konum Davranisi (Mode B - Servis Alani)

| Alan | Kural |
|---|---|
| Sehir filtre secenekleri | Ilanda servis alani kapsami olan sehirler listing-bound olarak gelir |
| Ilce filtre secenekleri | Secilen sehir altinda servis alani kapsami olan ilceler listing-bound gelir |
| Kapsam mantigi | Filtre, ilanin `location` ve `service_area` bilgisini kapsama kurallarina gore birlikte degerlendirir |

## 3) Attribute Filtre Davranisi

| field_type | filter_mode | UI | Query Param | DB Sorgu Mantigi |
|---|---|---|---|---|
| `text` | `exact` | Tek input | `attr_{key}` | `normalized_value = value` |
| `number` | `exact` | Tek input (number) | `attr_{key}` | `normalized_value = value` |
| `number` | `range` | Min + Max input | `attr_{key}_min`, `attr_{key}_max` | `value_number >= min`, `value_number <= max` |
| `select` | `exact` | Tek secim | `attr_{key}` | `normalized_value = value` |
| `boolean` | `exact` | Evet/Hayir | `attr_{key}` | `normalized_value = value` |

Ek kurallar:
- `filter_mode=range` sadece `field_type=number` icin gecerlidir.
- `field_type != number` ise `filter_mode` zorunlu olarak `exact` kabul edilir.
- Bos veya gecersiz query degerleri filtreyi bozmaz; ilgili kosul uygulanmaz.
- `select/multiselect` filtre secenekleri, once yayindaki listing attribute degerlerinden uretilir; bossa `options_json` fallback kullanilir.

## 4) URL ve Parametre Standardi

| Tip | Format |
|---|---|
| Kategori | `/hizmet/{category_slug}` |
| Kategori + Sehir | `/hizmet/{category_slug}/{city_slug}` |
| Kategori + Sehir + Ilce | `/hizmet/{category_slug}/{city_slug}/{district_slug}` |
| Liste filtreleri | `/ilanlar?category={slug}&city={city}&district={district}&attr_*` |

## 5) Test Kilitleri

Bu davranislar asagidaki testlerle kilitlidir:

- `CategorySystemFlowTest::test_listing_filter_options_are_listing_bound_and_case_insensitive_unique`
- `CategorySystemFlowTest::test_city_filter_matches_location_and_service_area_by_coverage_mode`
- `CategorySystemFlowTest::test_listings_filter_prefers_location_ids_when_available`
- `CategorySystemFlowTest::test_listing_filters_can_use_category_attribute_values`
- `CategorySystemFlowTest::test_listing_filters_can_use_category_attribute_number_ranges`

## 6) Regressions (Kirmizi Cizgiler)

Asagidaki durumlar hata kabul edilir:

1. Ilanla bagi olmayan sehir/ilcelerin filtrede gorunmesi.
2. `Izmir` ve `izmir` gibi varyantlarin filtrede iki kayit cikmasi.
3. `number + range` alaninda min/max query verildigi halde tum ilanlarin donmesi.
4. `location_id` dolu kayitlarda metin fallback'in oncelik alarak yanlis eslesme yapmasi.

## 7) Uygulama Plani (Kart + Detay + Filtre)

Amac:
- Kategoriye ozel alanlarin filtre, listing karti ve listing detayinda tutarli sekilde gosterilmesi.

Kapsam:
1. Filtre:
   - Kategori seciliyse dinamik filtreler gorunur.
2. Kart:
   - Sadece `is_visible_in_card = true` olan attribute degerleri gosterilir.
3. Detay:
   - Sadece `is_visible_in_detail = true` olan attribute degerleri gosterilir.

Gosterim formati:
- `number` -> sayi metni (or: `60`)
- `boolean` -> `Evet` / `Hayir`
- `multiselect` -> `value_text` (yoksa `value_json` virgullu)
- `select/text` -> `value_text`

Kontrol listesi:
- [x] Public listing sorgusunda `attributeValues.attribute` eager-load var.
- [x] Public detay sorgusunda `attributeValues.attribute` eager-load var.
- [x] Listing kart view'inda dinamik attribute bloklari var.
- [x] Listing detay view'inda dinamik attribute bloklari var.
- [x] `is_visible_in_card` kurali birebir uygulanmis.
- [x] `is_visible_in_detail` kurali birebir uygulanmis.
- [x] Multiselect/boolean/number formatlari dogru.
- [x] CategorySystemFlowTest PASS (iki uygulama).
- [x] Smoke PASS (iki uygulama).
