# TASK-078

Durum: `DOING`  
Ajan: `codex`  
Branch: `agent/codex/task-078`  
Baslangic: `2026-03-19 14:54`

## Gorev Ozeti
- Merge taskinin ne zaman gerekli oldugu ve ne zaman ayni taskta kapanabilecegi repo disiplinine yazilacak

## Task Karari
- [ ] mevcut task devam
- [ ] task genisletme
- [x] yeni task

## In Scope
- [ ] Merge taskinin ne zaman gerekli oldugu net kurala baglanacak
- [ ] Tek owner dusuk riskli teslimlerde merge'in ayni taskta kapanabilecegi yazilacak
- [ ] Koordinatorun merge taski acmadan once soracagi istisna sorusu resmi dokumanlara islenecek

## Out of Scope
- [ ] TASK-074 veya TASK-075 owner branch'lerini merge etmek
- [ ] Owner branch icerigini duzenlemek
- [ ] Yeni PR/merge uygulama taski acmak

## Lock Dosyalari
- `docs/tasks/TASK-078.md`
- `docs/TASK_LOCKS.md`
- `docs/NEXT_TASK.md`
- `docs/PR_FLOW_TR.md`
- `docs/REPO_DISCIPLINE_TR.md`
- `docs/MULTI_AGENT_RULES_TR.md`
- `docs/SESSION_HANDOFF_TR.md`
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
- [ ] Lock kapsam disina cikilmadi
- [ ] Gorev kapsamindaki degisiklikler tamamlandi
- [ ] Goreve ozel test/dogrulama calistirildi

## Kabul Kriterleri
- [ ] Merge taskinin varsayilan degil istisna oldugu resmi olarak yazilir
- [ ] Ayrı merge taski acilacak kosullar ve acilmayacak kosullar ayrilir
- [ ] powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick PASS

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
powershell -ExecutionPolicy Bypass -File scripts/close-task.ps1 -TaskId TASK-078 -Agent agent-name -ClosureNote "kisa kapanis ozeti" -WorklogTitle "baslik" -WorklogSummary "madde-1" -Files "dosya-1" -Commands "komut-1" -Result PASS
```

## Risk / Not
- Risk, merge icin her teslimde ikinci task zorunlulugu olusup repo yukunu gereksiz artirmak; bu task merge taskini istisna modeline cekiyor.

