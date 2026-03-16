# TASK-050

Durum: `DOING`  
Ajan: `codex-b`  
Branch: `agent/codex-b/task-050`  
Baslangic: `2026-03-16 17:45`
Kapanis: `-`

## Ozet
- Listing detail ve listing card sunumunu yeni hiyerarsiyi destekleyecek sekilde parity ile polish etmek.

## In Scope
- [ ] Gerekirse `listing-card.blade.php` iki appte karar hizini destekleyecek presentation ayarlarini yapmak.
- [ ] Gerekirse ilgili `v1.css` katmaninda detail hiyerarsi, spacing ve CTA sunumunu desteklemek.
- [ ] `codex-a` detail duzenine gorsel parity vermek.

## Out of Scope
- [ ] Runtime media path veya upload storage fix'i
- [ ] Yeni CTA eklemek

## Lock Dosyalari
- `local-rebuild/apps/orkestram/resources/views/frontend/partials/listing-card.blade.php`
- `local-rebuild/apps/izmirorkestra/resources/views/frontend/partials/listing-card.blade.php`
- `local-rebuild/apps/orkestram/public/assets/v1.css`
- `local-rebuild/apps/izmirorkestra/public/assets/v1.css`
- `docs/tasks/TASK-050.md`

## Kabul Kriteri
- [ ] Detail hiyerarsisini destekleyen stil/sunum parity ile gelir.
- [ ] Listing card ve detail presentation ayni karar modelini destekler.
- [ ] Iki app parity korur.

## Komutlar
```powershell
powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick
```
