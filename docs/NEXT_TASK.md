# NEXT TASK (Koordinasyon Panosu)

Durum: `IDLE`  
Koordinator: `codex`  
Kaynak Gercek: `docs/TASK_LOCKS.md`

## Aktif Gorevler (Tek Kaynak)
1. Aktif koordinator gorevi yok.

## Son Koordinator Kapanisi
1. `TASK-043` - Listing filtre UX toparlamasi paralel ajan teslimleriyle tamamlandi.
2. `TASK-042` - Koordinator cevap/teslim/task-acma standardi ve runtime hijyen checklisti sabitlendi.
3. `TASK-041` - `TASK-040` ana hatta merge edilip koordinasyon kapanisi hizalandi.

## Son Kapanis
1. `TASK-043` - Listing filtre UX toparlamasi tamamlandi (pre-pr PASS).
2. `TASK-042` - Koordinator disiplin standardizasyonu tamamlandi (pre-pr PASS).
3. `TASK-041` - `TASK-040` merge/kapanis hizasi tamamlandi (pre-pr PASS).

## Kapanis Kurali (Zorunlu)
1. Kapanis kaniti olmadan task `closed` edilemez.
2. Zorunlu kanit paketi:
   - `git branch --show-current`
   - `git branch -vv`
   - `git status --short`
   - `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick` sonucu `PASS`
3. Kanit yoksa task durumu `active` kalir.
