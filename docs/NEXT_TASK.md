# NEXT TASK (Koordinasyon Panosu)

Durum: `READY`  
Koordinator: `codex`  
Kaynak Gercek: `docs/TASK_LOCKS.md`

## Aktif Gorevler (Merkezi Koordinasyon)
1. `YOK` - `TASK-071` codex-a stale branch cleanup'ini pre-pr PASS ile kapatti.

## Son Koordinator Kapanisi
1. `TASK-071` - codex-a task-056 stale branch'i drift olarak dogrulandi ve kontrollu cleanup ile temizlendi.
2. `TASK-070` - codex-b ve codex-c worktree'leri kontrollu restore ile temizlendi; stale aday durumlari kapatildi.
3. `TASK-069` - codex-b ve codex-c stale worktree'lerinin temsilci diff'i satir-sonu/encoding drift olarak siniflandi; sonraki cleanup taski icin zemin hazirlandi.

## Son Kapanis
1. `TASK-071` - codex-a stale branch cleanup'i tamamlandi; boylece repo genel stale worktree problemi kapandi.
2. `TASK-070` - `codex-b` ve `codex-c` icin kontrollu cleanup uygulandi; her iki worktree de temiz duruma dondu.
3. `TASK-069` - `codex-b` ve `codex-c` icin cleanup riskinin urun kodu degil, satir-sonu/encoding drift oldugu temsilci kanitla belirlendi.

## Kapanis Kurali (Zorunlu)
1. Kapanis kaniti olmadan task `closed` edilemez.
2. Zorunlu kanit paketi:
   - `git branch --show-current`
   - `git branch -vv`
   - `git status --short`
   - `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick` sonucu `PASS`
3. Kanit yoksa task durumu `active` kalir.
4. Bu pano ile `docs/TASK_LOCKS.md` birebir senkron tutulur; paralel kapanis ve sira degisikligi yalniz koordinator tarafindan islenir.
