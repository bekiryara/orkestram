# TASK-027

Durum: `TODO`  
Ajan: `codex-c`  
Branch: `agent/codex-c/task-027`  
Baslangic: `2026-03-14 00:00`

## Ozet
- Listing detayinda fiyat icin structured data (`Offer`) ekle ve testle kilitle.

## In Scope
- [ ] Listing detail JSON-LD icine `Offer` (`price`, `priceCurrency`) eklenir.
- [ ] Fiyat eksik kayitlarda guvenli fallback uygulanir.
- [ ] Iki app parity saglanir.
- [ ] Structured data varligini dogrulayan feature test eklenir.
- [ ] `pre-pr -Mode quick` PASS.

## Out of Scope
- [ ] Fiyat veri modeli/migration (TASK-025).
- [ ] Public filtre/siralama (TASK-026).

## Lock Dosyalari
- `docs/tasks/TASK-027.md`
- `local-rebuild/apps/orkestram/resources/views/frontend/listing-detail.blade.php`
- `local-rebuild/apps/izmirorkestra/resources/views/frontend/listing-detail.blade.php`
- `local-rebuild/apps/orkestram/tests/Feature/**`
- `local-rebuild/apps/izmirorkestra/tests/Feature/**`

## Kabul Kriteri
- [ ] Listing detail HTML'de Offer schema tutarli uretilir.
- [ ] Iki appte ayni alanlar cikar.
- [ ] Quick gate PASS.

## Komutlar
```powershell
powershell -ExecutionPolicy Bypass -File D:\orkestram\scripts\pre-pr.ps1 -Mode quick
```

## Notlar
- SEO etkisi icin field isimleri schema.org standardina uygun yazilmalidir.
