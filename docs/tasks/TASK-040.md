# TASK-040

Durum: `DONE`  
Ajan: `codex`  
Branch: `agent/codex/task-040`  
Baslangic: `2026-03-16 11:00`
Kapanis: `2026-03-16 12:16`

## Ozet
- Ana sayfa ve ilan detay CTA/dugme hiyerarsisini endustri standardina yaklastirmak icin paralel ajan planinin koordinasyonu ve entegrasyonu tamamlandi.

## In Scope
- [x] Paralel ajanlar icin TASK-037/038/039 kartlarini acmak.
- [x] Koordinator lock/pano kayitlarini aktif gorev modeline cekmek.
- [x] Ajanlardan gelen ciktilari lock cakismasi olmadan entegre etmek.
- [x] Kapanista `pre-pr -Mode quick` PASS almak.

## Out of Scope
- [ ] Deploy islemi
- [ ] Back-end is kurali refactoru

## Lock Dosyalari
- `docs/tasks/TASK-040.md`
- `docs/tasks/TASK-037.md`
- `docs/tasks/TASK-038.md`
- `docs/tasks/TASK-039.md`
- `docs/TASK_LOCKS.md`
- `docs/NEXT_TASK.md`
- `docs/WORKLOG.md`
- `local-rebuild/apps/orkestram/resources/views/frontend/home.blade.php`
- `local-rebuild/apps/izmirorkestra/resources/views/frontend/home.blade.php`
- `local-rebuild/apps/orkestram/public/assets/v1.css`
- `local-rebuild/apps/izmirorkestra/public/assets/v1.css`
- `local-rebuild/apps/orkestram/resources/views/frontend/listing.blade.php`
- `local-rebuild/apps/izmirorkestra/resources/views/frontend/listing.blade.php`

## Kabul Kriteri
- [x] Paralel gorev kartlari cakismaz dosya alanlariyla tanimlandi.
- [x] Koordinator panosu `ACTIVE` duruma cekildi ve kapanista tekrar `IDLE` yapildi.
- [x] Entegrasyon sonrasi kapanis kaniti verildi (`branch/status/pre-pr`).

## Komutlar
```powershell
powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick
```

## Notlar
- `TASK-037` kod teslimi `origin/agent/codex-a/task-037` uzerinden merge edildi (`4099e57`).
- `TASK-038` ve `TASK-039` branchleri `main` ile ayni oldugu icin ilgili kapsam koordinator tarafinda `task-040` icinde tamamlandi.
