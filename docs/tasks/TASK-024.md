# TASK-024

Durum: `DOING`  
Ajan: `codex-b`  
Branch: `agent/codex-b/task-024`  
Baslangic: `2026-03-14 00:00`

## Ozet
- Listing card v2 tasarimini sabit yerlesim sozlesmesiyle uygula (iki app parity).

## In Scope
- [x] Kartta alan yerlesimi asagidaki sozlesmeye gore birebir uygulanir.
- [x] Yorum sayisi gercek veriden gelir.
- [x] Fiyat `price_label` alanindan gelir.
- [x] Kart ozellik satirlari kategori attribute sisteminden (kartta gorunur secilenler) gelir.
- [x] Iki app parity saglanir.
- [ ] `pre-pr -Mode quick` PASS.

## Out of Scope
- [x] Rating motoru (gercek puan hesaplama) gelistirme.
- [x] Yeni DB migration.
- [x] Mesaj/yorum business logic degisikligi.

## UI Sozlesmesi (Zorunlu)
1. Ust blok:
   - Buyuk kapak gorsel (sabit oran, kart genisligi).
2. Baslik satiri:
   - Sol: ilan adi.
   - Sag: yildiz + puan (`* 0.0` placeholder veya mevcut veri).
3. Ikinci satir:
   - Sol: Ozellik-1.
   - Sag: yorum sayisi.
4. Ucuncu satir:
   - Sol: Ozellik-2.
   - Sag: fiyat (guclu vurgu, kalin, buyuk).
5. CTA:
   - Kart altinda tek tip buton: `Detaylari Incele`.

## Veri Kaynagi
- Baslik: `listing.name`
- Fiyat: `listing.price_label`
- Ozellikler: `listing attribute values` (kart gorunur alanlar)
- Yorum sayisi: `listing_feedback(kind=comment, visibility=public, status=approved)` count
- Puan: rating sistemi yoksa placeholder

## Lock Dosyalari
- `docs/tasks/TASK-024.md`
- `local-rebuild/apps/orkestram/resources/views/frontend/partials/listing-card.blade.php`
- `local-rebuild/apps/izmirorkestra/resources/views/frontend/partials/listing-card.blade.php`
- `local-rebuild/apps/orkestram/public/assets/v1.css`
- `local-rebuild/apps/izmirorkestra/public/assets/v1.css`

## Kabul Kriteri
- [x] Kart alanlari iki appte ayni konumda.
- [x] Fiyat her kartta ayni bolgede ve vurgulu.
- [x] Ozellik satirlari adminde karta acilan alanlardan gelir.
- [x] Yorum sayisi gercek veriden gorunur.
- [ ] `pre-pr` PASS.
