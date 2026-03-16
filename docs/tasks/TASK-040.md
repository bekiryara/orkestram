# TASK-040

Durum: `DOING`  
Ajan: `codex`  
Branch: `agent/codex/task-040`  
Baslangic: `2026-03-16 11:00`

## Ozet
- Ana sayfa ve ilan detay CTA/dugme hiyerarsisini endustri standardina yaklastirmak icin paralel ajan planinin koordinasyonu ve entegrasyonu.

## In Scope
- [x] Paralel ajanlar icin TASK-037/038/039 kartlarini acmak.
- [x] Koordinator lock/pano kayitlarini aktif gorev modeline cekmek.
- [ ] Ajanlardan gelen ciktilari lock cakismasi olmadan entegre etmek.
- [ ] Kapanista `pre-pr -Mode quick` PASS almak.

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

## Kabul Kriteri
- [x] Paralel gorev kartlari cakismaz dosya alanlariyla tanimlanir.
- [x] Koordinator panosu `ACTIVE` duruma cekilir.
- [ ] Entegrasyon sonrasi kapanis kaniti verilir (`branch/status/pre-pr`).

## Komutlar
```powershell
powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick
```

## Notlar
- `git fetch --all --prune` bu ortamda local WSL remote tanimlari nedeniyle hata verebilir; operasyon `origin` ile devam eder.
