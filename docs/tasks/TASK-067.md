# TASK-067

Durum: `DONE`  
Ajan: `codex`  
Branch: `agent/codex/task-067`  
Baslangic: `2026-03-19 01:42`

## Gorev Ozeti
- Stale worktree gorunurlugu, ajan durum panosu ve session handoff modelini resmi operasyon disiplinine bagla

## Task Karari
- [ ] mevcut task devam
- [ ] task genisletme
- [x] yeni task

## In Scope
- [x] `OPERATING_MODEL_TR.md` ile tek merkez operasyon modelini eklemek
- [x] `SESSION_HANDOFF_TR.md` ile dosya tabanli operasyon hafizasini eklemek
- [x] `scripts/agent-status.ps1` ile salt-okuma ajan durum panosu eklemek
- [x] AGENTS, repo disiplini, multi-agent, teslim checklisti ve lock matrisi dokumanlarini handoff/gorunurluk modeline hizalamak
- [x] `orkestram-a`, `orkestram-b`, `orkestram-c` ve koordinator worktree'lerinin stale aday durumunu kanitli sekilde raporlamak

## Out of Scope
- [x] Stale worktree'leri bu taskta temizlemek
- [x] Diger ajan worktree'lerinde commit veya revert yapmak
- [x] UI/runtime lane davranisini degistirmek
- [x] Demo fixture otomasyonunu bu taskta tamamlamak

## Lock Dosyalari
- `docs/tasks/TASK-067.md`
- `docs/TASK_LOCKS.md`
- `docs/NEXT_TASK.md`
- `docs/OPERATING_MODEL_TR.md`
- `docs/SESSION_HANDOFF_TR.md`
- `AGENTS.md`
- `docs/REPO_DISCIPLINE_TR.md`
- `docs/MULTI_AGENT_RULES_TR.md`
- `docs/AGENT_DELIVERY_CHECKLIST_TR.md`
- `docs/AGENT_LOCK_MATRIX_TR.md`
- `scripts/agent-status.ps1`
- `docs/WORKLOG.md`

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
- [x] `OPERATING_MODEL_TR.md` tek merkez operasyon modeli olarak eklendi
- [x] `SESSION_HANDOFF_TR.md` aktif task, ajan, stale worktree ve sonraki adim alanlariyla eklendi
- [x] `scripts/agent-status.ps1` ajan branch/upstream/status/stale aday raporu uretiyor
- [x] AGENTS, repo disiplini, multi-agent, teslim checklisti ve lock matrisi handoff/gorunurluk kurallarina hizalandi
- [x] `orkestram-a`, `orkestram-b`, `orkestram-c` stale adaylari kanitli sekilde raporlandi
- [x] `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick` PASS

## Teslimde Zorunlu Kanit
- [x] `git branch --show-current`
- [x] `git branch -vv`
- [x] `git status --short`
- [x] `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick`
- [x] Goreve ozel test/komut sonucu
- [x] `Edit Source == Mount Source` kaniti
- [ ] Commit hash

## Kapanis Adimlari
- [x] Task kartindaki checklistler gercek sonuca gore guncellendi
- [x] `docs/WORKLOG.md` guncellendi
- [x] `docs/TASK_LOCKS.md` kaydi `closed` yapildi
- [x] `docs/NEXT_TASK.md` panosu guncellendi
- [x] Branch pushlandi

## Komutlar
```powershell
powershell -ExecutionPolicy Bypass -File scripts/agent-status.ps1 -Detailed
git push -u origin agent/codex/task-067
powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick
```

## Risk / Not
- Bu task stale worktree'leri temizlemedi; yalniz gorunurluk, handoff ve karar sistemi kuruldu. `codex-a`, `codex-b` ve `codex-c` icin stale temizlik veya devralma karari ayri task gerektirir.
