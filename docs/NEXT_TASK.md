# NEXT TASK (Koordinasyon Panosu)

Durum: `ACTIVE`  
Koordinator: `codex`  
Kaynak Gercek: `docs/TASK_LOCKS.md`

## Aktif Gorevler (Merkezi Koordinasyon)
1. `TASK-075` - Deterministic demo fixture standardi ve review demo veri kurali yazilacak
2. `TASK-076` - Task acilis recovery akisi ve NEXT_TASK aktif sayim hatasi duzeltilecek

## Son Koordinator Kapanisi
1. `TASK-074` - Merge sonrasi preview/runtime lifecycle standardi yazildi; pre-pr PASS ve push tamam
2. `TASK-071` - codex-a task-056 stale branch'i drift olarak dogrulandi ve kontrollu cleanup ile temizlendi.
3. `TASK-070` - codex-b ve codex-c worktree'leri kontrollu restore ile temizlendi; stale aday durumlari kapatildi.

## Son Kapanis
1. `TASK-074` - Merge sonrasi preview/runtime lifecycle standardi yazildi; pre-pr PASS ve push tamam
2. `TASK-071` - codex-a stale branch cleanup'i tamamlandi; boylece repo genel stale worktree problemi kapandi.
3. `TASK-070` - `codex-b` ve `codex-c` icin kontrollu cleanup uygulandi; her iki worktree de temiz duruma dondu.

## Kapanis Kurali (Zorunlu)
1. Kapanis kaniti olmadan task `closed` edilemez.
2. Zorunlu kanit paketi:
   - `git branch --show-current`
   - `git branch -vv`
   - `git status --short`
   - `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick` sonucu `PASS`
3. Kanit yoksa task durumu `active` kalir.
4. Bu pano ile `docs/TASK_LOCKS.md` birebir senkron tutulur; paralel kapanis ve sira degisikligi yalniz koordinator tarafindan islenir.
