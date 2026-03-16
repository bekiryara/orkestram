# TASK-044

Durum: `TODO`  
Ajan: `codex-a`  
Branch: `agent/codex-a/task-044`  
Baslangic: `2026-03-16 15:05`

## Ozet
- Listing filtre paneli yapisini ve aktif filtrelerin okunurlugunu iki appte parity ile iyilestirmek.

## In Scope
- [ ] `listings.blade.php` icinde filtre bloklarini daha anlasilir gruplamak.
- [ ] Aktif filtreleri sonuc alaninda gorunur hale getirmek.
- [ ] Temizle/uygula akisinda metin ve hiyerarsiyi netlestirmek.

## Out of Scope
- [ ] Global CSS tasarim sistemi refactoru
- [ ] Controller veya query mantigi degisikligi

## Lock Dosyalari
- `local-rebuild/apps/orkestram/resources/views/frontend/listings.blade.php`
- `local-rebuild/apps/izmirorkestra/resources/views/frontend/listings.blade.php`
- `docs/tasks/TASK-044.md`

## Kabul Kriteri
- [ ] Filtre paneli daha anlasilir olur.
- [ ] Aktif filtreler sonuc alaninda belirgin gorunur.
- [ ] Temizle/uygula akisi net metinlerle korunur.
- [ ] `pre-pr` PASS

## Komutlar
```powershell
powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick
```

## Notlar
- Blade duzeni iki appte parity kalacak sekilde ayni mantikla guncellenmelidir.
