# TASK-070

Durum: `DONE`  
Ajan: `codex`  
Branch: `agent/codex/task-070`  
Baslangic: `2026-03-19 03:34`

## Gorev Ozeti
- codex-b ve codex-c stale worktree cleanup uygula ve temiz kaniti topla

## Task Karari
- [ ] mevcut task devam
- [ ] task genisletme
- [x] yeni task

## In Scope
- [x] `codex-b` ve `codex-c` stale worktree'lerinde cleanup oncesi temsilci kaniti tekrar almak
- [x] Yalniz `codex-b` ve `codex-c` icin kontrollu `git restore --worktree .` uygulamak
- [x] Cleanup sonrasi temiz status ve `agent-status` kanitini toplamak

## Out of Scope
- [x] `codex-a` stale branch kararini bu taskta kapatmak
- [x] Urun kodu veya runtime davranisini degistirmek
- [x] `codex-b` ve `codex-c` disindaki worktree'lerde cleanup yapmak

## Lock Dosyalari
- `docs/tasks/TASK-070.md`
- `docs/TASK_LOCKS.md`
- `docs/NEXT_TASK.md`
- `docs/WORKLOG.md`
- `docs/SESSION_HANDOFF_TR.md`

## Preview Kontrati
- Lane: `n/a`
- Preview URL: `n/a`
- Mount Source: `n/a`
- Edit Source: `n/a`
- UI review gerekir mi?: `no`
- UI Review Durumu: `n/a`
- Revize Notu: `n/a`

## Uygulama Adimlari
- [x] Zorunlu dokumanlar okundu: `AGENTS.md`, `docs/REPO_DISCIPLINE_TR.md`, `docs/MULTI_AGENT_RULES_TR.md`
- [x] Branch dogrulandi: `agent/<ajan>/<task-id>`
- [x] Lock kapsam disina cikilmadi
- [x] Gorev kapsamindaki degisiklikler tamamlandi
- [x] Goreve ozel test/dogrulama calistirildi

## Kabul Kriterleri
- [x] `codex-b` cleanup oncesi temsilci drift kaniti tekrar alindi
- [x] `codex-c` cleanup oncesi temsilci drift kaniti tekrar alindi
- [x] `codex-b` cleanup sonrasi `git status --short` bos dondu
- [x] `codex-c` cleanup sonrasi `git status --short` bos dondu
- [x] `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick` PASS

## Teslimde Zorunlu Kanit
- [x] `git branch --show-current`
- [x] `git branch -vv`
- [x] `git status --short`
- [x] `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick`
- [x] Goreve ozel test/komut sonucu: cleanup oncesi temsilci `git diff`, cleanup sonrasi `git status --short`, `scripts/agent-status.ps1 -Detailed`
- [x] `Edit Source == Mount Source` kaniti
- [x] Commit hash

## Kapanis Adimlari
- [x] Task kartindaki checklistler gercek sonuca gore guncellendi
- [x] `docs/WORKLOG.md` guncellendi
- [x] `docs/TASK_LOCKS.md` kaydi `closed` yapildi
- [x] `docs/NEXT_TASK.md` panosu guncellendi
- [x] Branch pushlandi

## Komutlar
```powershell
wsl -e bash -lc "cd /home/bekir/orkestram-b && git diff -- docs/TASK_LOCKS.md && git diff --ignore-cr-at-eol --stat"
wsl -e bash -lc "cd /home/bekir/orkestram-c && git diff -- docs/TASK_LOCKS.md && git diff --ignore-cr-at-eol --stat"
wsl -e bash -lc "cd /home/bekir/orkestram-b && git restore --worktree . && git status --short"
wsl -e bash -lc "cd /home/bekir/orkestram-c && git restore --worktree . && git status --short"
powershell -ExecutionPolicy Bypass -File scripts/agent-status.ps1 -Detailed
git push -u origin agent/codex/task-070
powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick
```

## Risk / Not
- Bu task yalniz `codex-b` ve `codex-c` cleanup'ini uyguladi; `codex-a` stale branch'i ayri karar/task gerektirir.
