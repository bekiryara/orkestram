# TASK-076

Durum: `DOING`  
Ajan: `codex`  
Branch: `agent/codex/task-076`  
Baslangic: `2026-03-19 06:12`

## Gorev Ozeti
- Task acilis recovery akisi ve NEXT_TASK aktif sayim hatasi duzeltilecek

## Task Karari
- [ ] mevcut task devam
- [ ] task genisletme
- [x] yeni task

## In Scope
- [ ] `scripts/start-task.ps1` icindeki NEXT_TASK aktif sayim mantigi duzeltilecek
- [ ] `TASK-074` ve `TASK-075` icin merkezi panolar senkronize edilecek
- [ ] Koordinator recovery/handoff kayitlari resmi duruma getirilecek

## Out of Scope
- [ ] `TASK-074` kapsamindaki lifecycle icerigini yazmak
- [ ] `TASK-075` kapsamindaki fixture standardini yazmak
- [ ] Urun kodu veya runtime davranisi degistirmek

## Lock Dosyalari
- `docs/tasks/TASK-076.md`
- `docs/TASK_LOCKS.md`
- `docs/NEXT_TASK.md`
- `docs/SESSION_HANDOFF_TR.md`
- `scripts/start-task.ps1`

## Preview Kontrati
- Lane: `n/a`
- Preview URL: `n/a`
- Mount Source: `n/a`
- Edit Source: `n/a`
- UI review gerekir mi?: `no`
- UI Review Durumu: `n/a`
- Revize Notu: `n/a`

## Uygulama Adimlari
- [ ] Zorunlu dokumanlar okundu: `AGENTS.md`, `docs/REPO_DISCIPLINE_TR.md`, `docs/MULTI_AGENT_RULES_TR.md`
- [ ] Branch dogrulandi: `agent/<ajan>/<task-id>`
- [ ] Lock kapsam disina cikilmadi
- [ ] Gorev kapsamindaki degisiklikler tamamlandi
- [ ] Goreve ozel test/dogrulama calistirildi

## Kabul Kriterleri
- [ ] `start-task.ps1` yalniz gercek aktif gorevleri sayarak task acabilir
- [ ] `TASK-074`, `TASK-075` ve `TASK-076` aktif durumlari merkezi panolarda birebir gorunur
- [ ] Session handoff aktif ajan/task durumunu dogru yansitir
- [ ] `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick` PASS

## Teslimde Zorunlu Kanit
- [ ] `git branch --show-current`
- [ ] `git branch -vv`
- [ ] `git status --short`
- [ ] `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick`
- [ ] Goreve ozel test/komut sonucu
- [ ] `Edit Source == Mount Source` kaniti
- [ ] Commit hash

## Kapanis Adimlari
- [ ] Task kartindaki checklistler gercek sonuca gore guncellendi
- [ ] `docs/WORKLOG.md` guncellendi
- [ ] `docs/TASK_LOCKS.md` kaydi `closed` yapildi
- [ ] `docs/NEXT_TASK.md` panosu guncellendi
- [ ] Branch pushlandi

## Komutlar
```powershell
powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick
```

## Risk / Not
- Risk, recovery sirasinda yalanci aktif kayit veya ikinci bir task acma denemesiyle panoyu tekrar bozmak; yalniz mevcut 074/075 kayitlari korunarak onarim yapilacak.
