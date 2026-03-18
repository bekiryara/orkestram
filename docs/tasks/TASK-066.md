# TASK-066

Durum: `DONE`  
Ajan: `codex`  
Branch: `agent/codex/task-066`  
Baslangic: `2026-03-19 00:01`

## Gorev Ozeti
- Tek active task yerine kontrollu paralel task, task genisletme ve koordinasyon dosyalarinin merkezi yonetim kurallarini resmi disiplinde sabitle

## Task Karari
- [ ] mevcut task devam
- [x] task genisletme
- [ ] yeni task

## In Scope
- [x] Repo genelinde en fazla 3 aktif task modelini resmi kurala baglamak
- [x] Ayni kapsam revizesi icin yeni task acilmamasi kuralini netlestirmek
- [x] Task genisletme ile yeni task acma karar agacini belgelemek
- [x] `docs/NEXT_TASK.md`, `docs/TASK_LOCKS.md` ve `docs/WORKLOG.md` icin koordinator kontrollu merkezi alan kuralini eklemek
- [x] `scripts/start-task.ps1` akisini tek-active yerine en fazla 3 active task modeline hizalamak
- [x] Task template'i yeni karar modeline gore guncellemek

## Out of Scope
- [x] UI veya runtime lane davranisini degistirmek
- [x] Lock cakismasi kurallarini gevsetmek
- [x] 3 task sinirinin uzerine cikmak
- [x] Geriye donuk tum eski task dosyalarini migrate etmek

## Lock Dosyalari
- `docs/tasks/TASK-066.md`
- `docs/TASK_LOCKS.md`
- `docs/NEXT_TASK.md`
- `AGENTS.md`
- `docs/REPO_DISCIPLINE_TR.md`
- `docs/MULTI_AGENT_RULES_TR.md`
- `docs/tasks/_TEMPLATE.md`
- `scripts/start-task.ps1`
- `docs/AGENT_DELIVERY_CHECKLIST_TR.md`
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
- [x] Repo genelinde en fazla 3 aktif task kurali AGENTS, repo disiplini ve multi-agent belgelerinde ayni dille tanimlanmistir
- [x] Ayni kapsam revizesi, task genisletme ve yeni task acma ayrimi resmi karar kurali olarak yazilmistir
- [x] `docs/NEXT_TASK.md` aktif gorev panosu birden fazla aktif taski destekleyecek sekilde resmi olarak tanimlanmistir
- [x] `scripts/start-task.ps1` tek active task blokajini kaldirip 3 active task sinirini uygular
- [x] `docs/tasks/_TEMPLATE.md` icinde task karari alani bulunur
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
git push -u origin agent/codex/task-066
powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick
```

## Risk / Not
- Koordinasyon dosyalari merkezi alan oldugu icin kural gevsetilirken lock cakismasi ve belge drift riski artabilir; bu nedenle en fazla 3 aktif task ve koordinator kontrollu entegrasyon birlikte uygulandi.
