# TASK-077

Durum: `DONE`  
Ajan: `codex`  
Branch: `agent/codex/task-077`  
Baslangic: `2026-03-19 14:20`

## Gorev Ozeti
- TASK-074 ve TASK-075 icin PR/merge hazirlik akisi ve merkezi siralama standardi cikarilacak

## Task Karari
- [ ] mevcut task devam
- [ ] task genisletme
- [x] yeni task

## In Scope
- [x] TASK-074 ve TASK-075 icin merge/PR siralama kurali yazilacak
- [x] Owner branch review ve merge oncesi zorunlu koordinator checklisti netlestirilecek
- [x] Merkezi pano ve handoff uzerinde merge-hazirlik gorunurlugu standardi yazilacak

## Out of Scope
- [ ] TASK-074 veya TASK-075 owner branch icerigini degistirmek
- [ ] Demo fixture implementasyonu yapmak
- [ ] Preview/runtime kodu veya urun davranisi degistirmek

## Lock Dosyalari
- `docs/tasks/TASK-077.md`
- `docs/TASK_LOCKS.md`
- `docs/NEXT_TASK.md`
- `docs/PR_FLOW_TR.md`
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
- [x] Lock kapsam disina cikilmadi
- [x] Gorev kapsamindaki degisiklikler tamamlandi
- [x] Goreve ozel test/dogrulama calistirildi

## Kabul Kriterleri
- [x] TASK-074 ve TASK-075 icin hangi branch'in ne zaman PR veya merge hazir sayilacagi net kurala baglanir
- [x] Koordinatorun owner branch teslimini merkezi olarak nasil siralayacagi yazilir
- [x] powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick PASS

## Teslimde Zorunlu Kanit
- [x] `git branch --show-current`
- [x] `git branch -vv`
- [x] `git status --short`
- [x] `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick`
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
powershell -ExecutionPolicy Bypass -File scripts/close-task.ps1 -TaskId TASK-077 -Agent agent-name -ClosureNote "kisa kapanis ozeti" -WorklogTitle "baslik" -WorklogSummary "madde-1" -Files "dosya-1" -Commands "komut-1" -Result PASS
```

## Risk / Not
- Risk, koordinatorun owner ajan isine girmesi veya PR siralama yerine dogrudan merge akisina kaymasiydi; bu taskta yalniz merkezi hazirlik ve siralama standardi kayda alindi.
- Varsayilan merge kuyrugu TASK-074 sonra TASK-075 olarak yazildi; uygulama gorevi ayri taskta ele alinacak.

