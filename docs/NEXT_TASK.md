# NEXT TASK (Koordinasyon Panosu)

Durum: `IDLE`  
Koordinator: `codex`  
Kaynak Gercek: `docs/TASK_LOCKS.md`

## Aktif Gorevler (Tek Kaynak)
1. Aktif koordinator gorevi yok.

## Son Koordinator Kapanisi
1. `TASK-053` - Media hardening implementasyonu + canonical WIP recovery tamamlandi (pre-pr PASS).
2. `TASK-052` - Medya hardening plani + listing detail ust akisi guncellemesi tamamlandi (pre-pr PASS).
3. `TASK-046` - `TASK-043` ana hatta merge edilip runtime hizasi dogrulandi.

## Son Kapanis
1. `TASK-053` - Media hardening implementasyonu + canonical WIP recovery tamamlandi (pre-pr PASS).
2. `TASK-052` - Medya hardening plani + listing detail ust akisi guncellemesi tamamlandi (pre-pr PASS).
3. `TASK-046` - Listing filtre UX ana hata tasindi (pre-pr PASS).

## Kapanis Kurali (Zorunlu)
1. Kapanis kaniti olmadan task `closed` edilemez.
2. Zorunlu kanit paketi:
   - `git branch --show-current`
   - `git branch -vv`
   - `git status --short`
   - `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick` sonucu `PASS`
3. Kanit yoksa task durumu `active` kalir.

