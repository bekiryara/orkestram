# TASK-071

Durum: `DONE`  
Ajan: `codex`  
Branch: `agent/codex/task-071`  
Baslangic: `2026-03-19 04:05`

## Gorev Ozeti
- codex-a task-056 stale branch drift mi yarim is mi kararini netlestir ve guvenliyse cleanup uygula

## Task Karari
- [ ] mevcut task devam
- [x] task genisletme
- [ ] yeni task

## In Scope
- [x] `codex-a` worktree'sinde temsilci diff ve task-056 kaydini okuyup stale sinifi belirlemek
- [x] Kirin satir-sonu/encoding drift oldugunu kanitlarsa kontrollu cleanup uygulamak
- [x] Sonucu `SESSION_HANDOFF_TR.md` ve merkezi koordinasyon kayitlarina islemek

## Out of Scope
- [x] Urun kodu veya runtime davranisini degistirmek
- [x] `codex-b` ve `codex-c` worktree'lerinde ek is yapmak
- [x] Belirsiz veya yarim is gorulseydi otomatik cleanup yapmak

## Lock Dosyalari
- `docs/tasks/TASK-071.md`
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
- [x] `codex-a` icin task-056 karti ve branch/upstream kaniti okundu
- [x] `codex-a` temsilci diff + `ignore-cr-at-eol` sonucu resmi karar kaydina islendi
- [x] `codex-a` cleanup sonrasi `git status --short` bos doner
- [x] `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick` PASS

## Teslimde Zorunlu Kanit
- [x] `git branch --show-current`
- [x] `git branch -vv`
- [x] `git status --short`
- [x] `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick`
- [x] Goreve ozel test/komut sonucu: `codex-a` icin task karti okuma + temsilci `git diff` + cleanup sonrasi `git status --short`
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
wsl -e bash -lc "cd /home/bekir/orkestram-a && git diff -- docs/TASK_LOCKS.md && git diff --ignore-cr-at-eol --stat"
wsl -e bash -lc "cd /home/bekir/orkestram-a && git restore --worktree . && git status --short"
powershell -ExecutionPolicy Bypass -File scripts/agent-status.ps1 -Detailed
git push -u origin agent/codex/task-071
powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick
```

## Risk / Not
- `codex-a` tarafinda gercek yari is tespit edilseydi cleanup uygulanmayacakti; mevcut kanit drift sinifina isaret etti ve cleanup guvenli tamamlandi.
