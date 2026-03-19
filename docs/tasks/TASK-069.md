# TASK-069

Durum: `DONE`  
Ajan: `codex`  
Branch: `agent/codex/task-069`  
Baslangic: `2026-03-19 03:06`

## Gorev Ozeti
- codex-b ve codex-c stale worktree temsilci diff ve cleanup risk siniflamasini cikar

## Task Karari
- [ ] mevcut task devam
- [ ] task genisletme
- [x] yeni task

## In Scope
- [x] `codex-b` ve `codex-c` worktree'lerinde temsilci diff almak
- [x] `main` uzerindeki kirli durumun icerik riski mi satir-sonu/encoding drift'i mi oldugunu siniflamak
- [x] Sonucu `SESSION_HANDOFF_TR.md` ve merkezi koordinasyon kayitlarina islemek

## Out of Scope
- [x] `codex-b` ve `codex-c` worktree'lerinde bu task icinde destructive cleanup yapmak
- [x] `codex-a` stale branch kararini bu taskta kapatmak
- [x] Urun kodu veya runtime davranisini degistirmek

## Lock Dosyalari
- `docs/tasks/TASK-069.md`
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
- [x] `codex-b` icin temsilci diff ve `ignore-cr-at-eol` kaniti kayda alindi
- [x] `codex-c` icin temsilci diff ve `ignore-cr-at-eol` kaniti kayda alindi
- [x] `SESSION_HANDOFF_TR.md` icinde `codex-b` ve `codex-c` karar sinifi resmi sonuca gore guncellendi
- [x] `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick` PASS

## Teslimde Zorunlu Kanit
- [x] `git branch --show-current`
- [x] `git branch -vv`
- [x] `git status --short`
- [x] `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick`
- [x] Goreve ozel test/komut sonucu: `codex-b` ve `codex-c` icin temsilci `git diff` + `git diff --ignore-cr-at-eol --stat`
- [x] `Edit Source == Mount Source` kaniti
- [x] Commit hash: `d6c3cee`

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
git push -u origin agent/codex/task-069
powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick
```

## Risk / Not
- Bu task yalniz temsilci diff ve risk siniflamasi yapar; cleanup ayri resmi taskta uygulanir.
