# TASK-078

Durum: `DONE`  
Ajan: `codex`  
Branch: `agent/codex/task-078`  
Baslangic: `2026-03-19 14:54`

## Gorev Ozeti
- Merge taskinin ne zaman gerekli oldugu ve ne zaman ayni taskta kapanabilecegi repo disiplinine yazilacak

## Task Karari
- [x] mevcut task devam
- [ ] task genisletme
- [ ] yeni task

## In Scope
- [x] Merge taskinin ne zaman gerekli oldugu net kurala baglanacak
- [x] Tek owner dusuk riskli teslimlerde merge'in ayni taskta kapanabilecegi yazilacak
- [x] Koordinatorun merge taski acmadan once soracagi istisna sorusu resmi dokumanlara islenecek

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
- [x] Lock kapsam disina cikilmadi
- [x] Gorev kapsamindaki degisiklikler tamamlandi
- [x] Goreve ozel test/dogrulama calistirildi
- [x] Merkezi kapanis drift'i `TASK-078 aktif task` modeliyle hizalandi
- [x] `TASK-074` ve `TASK-075` icin ayrik merge task gerekmez karari merkezi kayda islendi

## Kabul Kriterleri
- [x] Merge taskinin varsayilan degil istisna oldugu resmi olarak yazilir
- [x] Ayri merge taski acilacak kosullar ve acilmayacak kosullar ayrilir
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
wsl -e bash -lc "cd /home/bekir/orkestram-k && git branch --show-current && git branch -vv && git status --short"
powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick
```

## Risk / Not
- Risk, merge icin her teslimde ikinci task zorunlulugu olusup repo yukunu gereksiz artirmakti; bu task merge taskini istisna modeline cekti.
- Bundan sonra ayri merge taski yalniz yeni operasyon riski veya yeni kabul kriteri doguruyorsa acilacak.
- Merkezi kayda islenen net karar: `TASK-074` ve `TASK-075` icin ayrik merge task gerekmez; owner task akisi icinde ilerlenir.
