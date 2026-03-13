# NEXT TASK (Koordinasyon Panosu)

Durum: `ACTIVE`  
Koordinator: `codex`  
Kaynak Gercek: `docs/TASK_LOCKS.md`

## Aktif Gorevler (Tek Kaynak)
1. `TASK-020` - `codex` - Merge tren koordinasyonu, lock hijyeni ve gate standardizasyonu.
2. `TASK-021` - `codex-a` - Iki app acceptance/smoke toparlama ve regression temizligi.
3. `TASK-022` - `codex-b` - Frontend son entegrasyon parity kapanisi.

## Kapanis Kurali (Zorunlu)
1. Kapanis kaniti olmadan task `closed` edilemez.
2. Zorunlu kanit paketi:
   - `git branch --show-current`
   - `git status --short`
   - `powershell -ExecutionPolicy Bypass -File D:\orkestram\scripts\pre-pr.ps1 -Mode quick` sonucu `PASS`
3. Kanit yoksa task durumu `active` kalir.
