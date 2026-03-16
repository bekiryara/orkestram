# TASK-039

Durum: `DONE`  
Ajan: `codex-c`  
Branch: `agent/codex-c/task-039`  
Baslangic: `2026-03-16 11:00`
Kapanis: `2026-03-16 12:16`

## Ozet
- Ilan detay sayfasinda karar odakli ust bolum korunup aksiyonlar primary/secondary hiyerarsiye cekildi.

## In Scope
- [x] Ust blokta net baslik + fiyat + ana CTA hiyerarsisi.
- [x] Yorum/form bolumunu asagi akista ikincil seviyeye cekmek.
- [x] Iki appte parity.

## Out of Scope
- [ ] Ana sayfa hero duzeni
- [ ] Global CSS tasarim sistemi

## Lock Dosyalari
- `local-rebuild/apps/orkestram/resources/views/frontend/listing.blade.php`
- `local-rebuild/apps/izmirorkestra/resources/views/frontend/listing.blade.php`
- `docs/tasks/TASK-039.md`

## Kabul Kriteri
- [x] Ust bolumde tek ana aksiyon net gorunur.
- [x] Yorum/form bolumu karar akisinin arkasinda kalir.
- [x] `pre-pr` PASS.

## Komutlar
```powershell
powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick
```

## Notlar
- `origin/agent/codex-c/task-039` branch'inde kod commit farki olmadigi icin kapsam `task-040` uzerinden koordinator tarafinda tamamlandi.
