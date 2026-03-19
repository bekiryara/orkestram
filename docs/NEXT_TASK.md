# NEXT TASK (Koordinasyon Panosu)

Durum: `ACTIVE`  
Koordinator: `codex`  
Kaynak Gercek: `docs/TASK_LOCKS.md`

## Aktif Gorevler (Merkezi Koordinasyon)
1. `TASK-077` - TASK-074 ve TASK-075 icin PR/merge hazirlik akisi ve merkezi siralama standardi cikarilacak

## Son Koordinator Kapanisi
1. `TASK-076` - task acilis recovery, aktif pano senkronu ve ajan teslimleri merkezi kapanisla tamamlandi
2. `TASK-075` - deterministic demo fixture standardi agent teslimi 8351cba ve pre-pr PASS ile tamamlandi
3. `TASK-074` - merge sonrasi preview/runtime lifecycle standardi agent teslimi 4f95fa0 ve pre-pr PASS ile tamamlandi

## Son Kapanis
1. `TASK-076` - task acilis recovery, aktif pano senkronu ve ajan teslimleri merkezi kapanisla tamamlandi
2. `TASK-075` - deterministic demo fixture standardi agent teslimi 8351cba ve pre-pr PASS ile tamamlandi
3. `TASK-074` - merge sonrasi preview/runtime lifecycle standardi agent teslimi 4f95fa0 ve pre-pr PASS ile tamamlandi

## Kapanis Kurali (Zorunlu)
1. Kapanis kaniti olmadan task `closed` edilemez.
2. Zorunlu kanit paketi:
   - `git branch --show-current`
   - `git branch -vv`
   - `git status --short`
   - `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick` sonucu `PASS`
3. Kanit yoksa task durumu `active` kalir.
4. Bu pano ile `docs/TASK_LOCKS.md` birebir senkron tutulur; paralel kapanis ve sira degisikligi yalniz koordinator tarafindan islenir.

