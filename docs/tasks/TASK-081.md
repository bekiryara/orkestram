# TASK-081

Durum: `DONE`  
Ajan: `codex`  
Branch: `agent/codex/task-081`  
Baslangic: `2026-03-20 00:39`

## Gorev Ozeti
- Kanitli satir-sonu encoding drift cleanup ve stale gorunurlugu kapatma

## Task Karari
- [ ] mevcut task devam
- [x] task genisletme
- [ ] yeni task

## In Scope
- [x] Koordinator worktree'de kanitli satir-sonu/encoding drift'i temizlemek
- [x] `codex-b` ve `codex-c` stale drift gorunurlugunu temiz status'a getirmek
- [x] Merkezi handoff, lock ve worklog kayitlarinda cleanup sonucunu kayda almak

## Out of Scope
- [ ] Yeni urun ozelligi gelistirmek
- [ ] Owner coverage parity koduna tekrar dokunmak
- [ ] UI preview/review gorevi acmak

## Lock Dosyalari
- `docs/tasks/TASK-081.md`
- `docs/TASK_LOCKS.md`
- `docs/NEXT_TASK.md`
- `docs/SESSION_HANDOFF_TR.md`
- `docs/WORKLOG.md`
- `docs/tasks/TASK-066.md`
- `docs/tasks/TASK-067.md`
- `docs/tasks/TASK-068.md`
- `docs/tasks/TASK-069.md`
- `docs/tasks/TASK-070.md`
- `docs/tasks/TASK-071.md`
- `docs/tasks/TASK-074.md`
- `docs/tasks/TASK-075.md`
- `docs/tasks/TASK-076.md`
- `docs/tasks/TASK-077.md`
- `docs/tasks/TASK-080.md`
- `docs/DEMO_FIXTURE_STANDARD_TR.md`
- `scripts/agent-status.ps1`

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
- [x] Koordinator worktree'de kanitli drift dosyalari temizlenir
- [x] `codex-b` ve `codex-c` worktree'leri temiz status'a gelir
- [x] Cleanup sonucu merkezi kayitlara islenir
- [x] `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick` PASS

## Teslimde Zorunlu Kanit
- [x] `git branch --show-current`
- [x] `git branch -vv`
- [x] `git status --short`
- [x] `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick`
- [x] Goreve ozel test/komut sonucu
- [ ] `Edit Source == Mount Source` kaniti
- [x] Commit hash

## Kapanis Adimlari
- [x] Task kartindaki checklistler gercek sonuca gore guncellendi
- [x] `docs/WORKLOG.md` guncellendi
- [x] `docs/TASK_LOCKS.md` kaydi `closed` yapildi
- [x] `docs/NEXT_TASK.md` panosu guncellendi
- [x] Branch pushlandi

## Komutlar
```powershell
wsl -e bash -lc "cd /home/bekir/orkestram-k && git diff -- docs/tasks/TASK-066.md docs/tasks/TASK-067.md docs/tasks/TASK-068.md docs/tasks/TASK-069.md docs/tasks/TASK-070.md docs/tasks/TASK-071.md docs/tasks/TASK-074.md docs/tasks/TASK-075.md docs/tasks/TASK-076.md docs/tasks/TASK-077.md docs/tasks/TASK-080.md scripts/agent-status.ps1"
wsl -e bash -lc "cd /home/bekir/orkestram-k && git restore docs/tasks/TASK-066.md docs/tasks/TASK-067.md docs/tasks/TASK-068.md docs/tasks/TASK-069.md docs/tasks/TASK-070.md docs/tasks/TASK-071.md docs/tasks/TASK-074.md docs/tasks/TASK-075.md docs/tasks/TASK-076.md docs/tasks/TASK-077.md docs/tasks/TASK-080.md scripts/agent-status.ps1"
wsl -e bash -lc "cd /home/bekir/orkestram-b && git restore docs/tasks/TASK-074.md"
wsl -e bash -lc "cd /home/bekir/orkestram-c && git restore docs/DEMO_FIXTURE_STANDARD_TR.md docs/tasks/TASK-075.md"
powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick
```

## Risk / Not
- Risk, stale gorunurlugu temizlerken icerik degeri olan dosyalari yanlislikla restore etmekti; bu task yalniz `git diff` ile icerik farki tasimadigi kanitlanan satir-sonu/encoding drift dosyalarini temizledi.
