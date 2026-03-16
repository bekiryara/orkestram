# TASK-045

Durum: `TODO`  
Ajan: `codex-b`  
Branch: `agent/codex-b/task-045`  
Baslangic: `2026-03-16 15:05`

## Ozet
- Listing filtre deneyimi icin mobil filtre akisi, aktif filtre sunumu ve filtre aksiyon alaninin CSS tarafini toparlamak.

## In Scope
- [ ] Listing filtre paneli icin mobilde rahat acilir/kapanir veya rahat okunur akisi destekleyen stil katmanini kurmak.
- [ ] Aktif filtre chip/ozet alanini belirginlestirmek.
- [ ] Temizle/uygula buton hiyerarsisini ve sticky/alt aksiyon konumunu iyilestirmek.

## Out of Scope
- [ ] Blade icerik metinlerini yeniden yazmak
- [ ] Listing detay sayfasi stilleri

## Lock Dosyalari
- `local-rebuild/apps/orkestram/public/assets/v1.css`
- `local-rebuild/apps/izmirorkestra/public/assets/v1.css`
- `docs/tasks/TASK-045.md`

## Kabul Kriteri
- [ ] Mobil filtre kullanimi daha rahat olur.
- [ ] Aktif filtreler belirgin ve taranabilir gorunur.
- [ ] Temizle/uygula aksiyonlari hiyerarsik olarak netlesir.
- [ ] `pre-pr` PASS

## Komutlar
```powershell
powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick
```

## Notlar
- Stil degisiklikleri iki appte parity ile ayni tasarim dili uzerinden ilerlemelidir.
