# TASK-049

Durum: `DOING`  
Ajan: `codex-a`  
Branch: `agent/codex-a/task-049`  
Baslangic: `2026-03-16 17:45`
Kapanis: `-`

## Ozet
- Listing detail sayfasini karar odakli bilgi ve aksiyon hiyerarsisine cekmek.

## In Scope
- [ ] `listing.blade.php` iki appte ust karar alanini hedef siralamaya gore yeniden kurmak.
- [ ] Bir primary, en fazla bir secondary CTA modelini korumak.
- [ ] Yorumlar, yorum formu ve uzun aciklamayi alt seviyeye itmek.

## Out of Scope
- [ ] Runtime dosya hatti veya upload servisi duzeltmesi
- [ ] Yeni buton eklemek

## Lock Dosyalari
- `local-rebuild/apps/orkestram/resources/views/frontend/listing.blade.php`
- `local-rebuild/apps/izmirorkestra/resources/views/frontend/listing.blade.php`
- `docs/tasks/TASK-049.md`

## Kabul Kriteri
- [ ] Ana gorsel, ilan adi, fiyat, sehir/ilce/hizmet tipi ve ozellikler yorumlardan once gelir.
- [ ] Ust karar panelinde gereksiz aksiyonlar azalir.
- [ ] Iki app parity korur.

## Komutlar
```powershell
powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick
```
