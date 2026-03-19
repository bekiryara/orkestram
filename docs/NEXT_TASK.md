# NEXT TASK (Koordinasyon Panosu)

Durum: `ACTIVE`  
Koordinator: `codex`  
Kaynak Gercek: `docs/TASK_LOCKS.md`

## Aktif Gorevler (Merkezi Koordinasyon)
1. `TASK-069` - codex-b ve codex-c stale worktree temsilci diff ve cleanup risk siniflamasini cikar

## Son Koordinator Kapanisi
1. `TASK-068` - stale worktree icin `koru | devral | temizle` karar siniflari, destructive cleanup guvenceleri ve handoff siniflama standardi resmi hale getirildi.
2. `TASK-067` - `OPERATING_MODEL_TR`, `SESSION_HANDOFF_TR` ve `scripts/agent-status.ps1` eklendi; stale worktree gorunurlugu ve handoff operasyonu resmi disipline baglandi.
3. `TASK-066` - Repo genelinde en fazla 3 aktif task, ayni-task revize, task genisletme ve merkezi koordinasyon dosyasi kurallari AGENTS, disiplin, multi-agent, checklist, task template ve start-task akisi seviyesinde resmi hale getirildi.

## Son Kapanis
1. `TASK-068` - Stale worktree cleanup/devralma icin resmi karar siniflari kuruldu; ancak `codex-a`, `codex-b` ve `codex-c` worktree'lerinde yikici temizlik bu taskta yapilmadi.
2. `TASK-067` - Stale worktree'ler temizlenmedi; fakat artik hangi ajan/worktree'nin stale aday oldugu dosya tabanli ve script tabanli gorunur hale geldi.
3. `TASK-066` - Tek active task siniri kaldirildi; repo-geneli 3 aktif task siniri, tek-ajan tek-task, task genisletme ve merkezi koordinasyon kurali pre-pr PASS ile sabitlendi.

## Kapanis Kurali (Zorunlu)
1. Kapanis kaniti olmadan task `closed` edilemez.
2. Zorunlu kanit paketi:
   - `git branch --show-current`
   - `git branch -vv`
   - `git status --short`
   - `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick` sonucu `PASS`
3. Kanit yoksa task durumu `active` kalir.
4. Bu pano ile `docs/TASK_LOCKS.md` birebir senkron tutulur; paralel kapanis ve sira degisikligi yalniz koordinator tarafindan islenir.

