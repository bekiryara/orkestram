# TASK-027

Durum: `DONE`  
Ajan: `codex-b`  
Branch: `agent/codex-b/task-027`  
Baslangic: `2026-03-14 00:00`

## Ozet
- Listing detayinda fiyat icin structured data (`Offer`) ekle ve testle kilitle.

## In Scope
- [x] Listing detail JSON-LD icine `Offer` (`price`, `priceCurrency`) eklenir.
- [x] Fiyat eksik kayitlarda guvenli fallback uygulanir.
- [x] Iki app parity saglanir.
- [x] Structured data varligini dogrulayan feature test eklenir.
- [x] `pre-pr -Mode quick` PASS.

## Out of Scope
- [ ] Fiyat veri modeli/migration (TASK-025).
- [ ] Public filtre/siralama (TASK-026).

## Lock Dosyalari
- `docs/tasks/TASK-027.md`
- `local-rebuild/apps/orkestram/resources/views/frontend/listing.blade.php`
- `local-rebuild/apps/izmirorkestra/resources/views/frontend/listing.blade.php`
- `local-rebuild/apps/orkestram/tests/Feature/**`
- `local-rebuild/apps/izmirorkestra/tests/Feature/**`

## Kabul Kriteri
- [x] Listing detail HTML'de Offer schema tutarli uretilir.
- [x] Iki appte ayni alanlar cikar.
- [x] Quick gate PASS.

## Komutlar
```powershell
powershell -ExecutionPolicy Bypass -File D:\orkestram\scripts\pre-pr.ps1 -Mode quick
```

## Notlar
- SEO etkisi icin field isimleri schema.org standardina uygun yazilmalidir.
