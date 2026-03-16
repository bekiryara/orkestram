# TASK-043

Durum: `DOING`  
Ajan: `codex`  
Branch: `agent/codex/task-043`  
Baslangic: `2026-03-16 15:05`

## Ozet
- Listing filtre UX toparlamasini paralel ajanlarla koordine etmek, lock/pano kayitlarini acmak ve entegrasyon kapanisini yonetmek.

## In Scope
- [x] Listing filtre UX isi icin yeni koordinasyon taskini acmak.
- [x] Ajan gorevlerini cakismasiz dosya alanlariyla dagitmak.
- [ ] Ajan teslimlerini entegre etmek.
- [ ] `pre-pr` PASS ile resmi kapanis vermek.

## Out of Scope
- [ ] Back-end filtre mantigi refactoru
- [ ] Listing card tasarimini ayri feature olarak degistirmek

## Lock Dosyalari
- `docs/tasks/TASK-043.md`
- `docs/tasks/TASK-044.md`
- `docs/tasks/TASK-045.md`
- `docs/TASK_LOCKS.md`
- `docs/NEXT_TASK.md`
- `docs/WORKLOG.md`

## Kabul Kriteri
- [x] Ajan gorevleri cakismasiz lock alanlariyla acildi.
- [ ] Filtre paneli, aktif filtre okunurlugu ve mobil akisin entegrasyonu tamamlandi.
- [ ] Iki app parity korundu.
- [ ] `pre-pr` PASS

## Komutlar
```powershell
powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick
```

## Notlar
- `codex-c` bu turda bos tutuldu; entegrasyon sonrasi ek gorunum/qa ihtiyaci cikarsa ikinci dalga gorev acilacak.
