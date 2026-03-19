# TASK-076

Durum: `DONE`  
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
- [x] `scripts/start-task.ps1` icindeki NEXT_TASK aktif sayim mantigi duzeltilecek
- [ ] `TASK-074` ve `TASK-075` icin merkezi panolar senkronize edilecek
- [x] Koordinator recovery/handoff kayitlari resmi duruma getirilecek

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
- [x] Zorunlu dokumanlar okundu: `AGENTS.md`, `docs/REPO_DISCIPLINE_TR.md`, `docs/MULTI_AGENT_RULES_TR.md`
- [x] Branch dogrulandi: `agent/<ajan>/<task-id>`
- [x] Lock kapsam disina cikilmadi
- [x] Gorev kapsamindaki degisiklikler tamamlandi
- [x] Goreve ozel test/dogrulama calistirildi

## Kabul Kriterleri
- [x] `start-task.ps1` yalniz gercek aktif gorevleri sayarak task acabilir
- [x] `TASK-074`, `TASK-075` ve `TASK-076` aktif durumlari merkezi panolarda birebir gorunur
- [x] Session handoff aktif ajan/task durumunu dogru yansitir
- [x] `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick` PASS

## Teslimde Zorunlu Kanit
- [ ] `git branch --show-current`
- [ ] `git branch -vv`
- [ ] `git status --short`
- [ ] `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick`
- [x] Goreve ozel test/komut sonucu
- [ ] `Edit Source == Mount Source` kaniti
- [ ] Commit hash

## Kapanis Adimlari
- [x] Task kartindaki checklistler gercek sonuca gore guncellendi
- [x] `docs/WORKLOG.md` guncellendi
- [x] `docs/TASK_LOCKS.md` kaydi `closed` yapildi
- [x] `docs/NEXT_TASK.md` panosu guncellendi
- [ ] Branch pushlandi

## Komutlar
```powershell
powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick
```

## Risk / Not
- Risk, recovery sirasinda yalanci aktif kayit veya ikinci bir task acma denemesiyle panoyu tekrar bozmakti; start-task.ps1 sayim duzeltmesi ile bu risk kapatildi.
- Merkezi kapanista owner ajan scope dosyalari koordinator branch'ine tasinmadi; yalniz lock/pano/handoff/worklog kapanisi yapildi.
- Ajan teslim kanitlari: TASK-074 commit 4f95fa0 + pre-pr PASS, TASK-075 commit 8351cba + pre-pr PASS.



