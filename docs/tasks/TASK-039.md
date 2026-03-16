# TASK-039

Durum: `TODO`  
Ajan: `codex-c`  
Branch: `agent/codex-c/task-039`  
Baslangic: `2026-03-16 11:00`

## Ozet
- Ilan detay sayfasinda karar odakli ust bolum kurup, yorum/form bloklarini ikincil seviyeye almak.

## In Scope
- [ ] Ust blokta net baslik + fiyat + ana CTA hiyerarsisi.
- [ ] Yorum/form bolumunu asagi akista ikincil seviyeye cekmek.
- [ ] Iki appte parity.

## Out of Scope
- [ ] Ana sayfa hero duzeni
- [ ] Global CSS tasarim sistemi

## Lock Dosyalari
- `local-rebuild/apps/orkestram/resources/views/frontend/listing.blade.php`
- `local-rebuild/apps/izmirorkestra/resources/views/frontend/listing.blade.php`
- `docs/tasks/TASK-039.md`

## Kabul Kriteri
- [ ] Ust bolumde tek ana aksiyon net gorunur.
- [ ] Yorum/form bolumu karar akisinin arkasinda kalir.
- [ ] `pre-pr` PASS.

## Komutlar
```powershell
powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick
```
