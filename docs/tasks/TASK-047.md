# TASK-047

Durum: `IN_PROGRESS`  
Ajan: `codex`  
Branch: `agent/codex/task-047`  
Baslangic: `2026-03-16 16:45`
Kapanis: `-`

## Ozet
- Listing sonuc ozetini screenshot hedefindeki daha hizli karar modeline yaklastirmak ve bu polish'i kapanmis task anlatilarindan ayri resmi kayda baglamak.

## In Scope
- [x] Sonuc kolonundaki tekrar eden `Aktif Filtreler / Filtreleri Sifirla` blogunu kaldirmak.
- [x] Ust sonuc satirini kategori odakli sayi ozeti verecek sekilde sadelestirmek.
- [x] Degisikligi kapanmis tasklardan ayri follow-up kaydi olarak belgelemek.

## Out of Scope
- [ ] Controller veya query mantigi degisikligi
- [ ] Yeni CSS sistemi veya listing card redesign

## Lock Dosyalari
- `docs/tasks/TASK-047.md`
- `docs/TASK_LOCKS.md`
- `docs/NEXT_TASK.md`
- `docs/WORKLOG.md`
- `local-rebuild/apps/orkestram/resources/views/frontend/listings.blade.php`
- `local-rebuild/apps/izmirorkestra/resources/views/frontend/listings.blade.php`

## Kabul Kriteri
- [x] Sonuc ozet satiri kategori odakli ve daha hizli taranabilir hale gelir.
- [x] Sag kolonun ustundeki tekrar aktif filtre blogu kaldirilir.
- [x] Iki app parity korur.
- [x] Eski task anlatilari geriye donuk yeni is yapmis gibi gorunmez.
- [ ] `pre-pr` PASS

## Komutlar
```powershell
powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick
```

## Notlar
- Runtime gorunumu `127.0.0.1:8180` ve `127.0.0.1:8181` uzerinden dogrulandi.
- Bu gorev yalniz son mile UX polish ve kayit temizligi icin acildi.
- `pre-pr` su an `izmirorkestra-local-web FeedbackModerationAccessTest` uzerinde `Admin panel yorum kaydi` beklentisi nedeniyle FAIL.
