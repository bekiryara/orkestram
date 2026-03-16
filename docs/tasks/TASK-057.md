# TASK-057

Durum: `DONE`  
Ajan: `codex`  
Branch: `agent/codex/task-057`  
Baslangic: `2026-03-17 01:10`

## Gorev Ozeti
- Ajan teslim disiplini ve task sablonu zorunlu checklist sertlestirmesi

## In Scope
- [x] `docs/tasks/_TEMPLATE.md` icine zorunlu uygulama, teslim ve kapanis checklistleri eklendi
- [x] `scripts/start-task.ps1` ile yeni task dosyalari placeholder degil zorunlu alanlarla acilir hale getirildi
- [x] `docs/AGENT_DELIVERY_CHECKLIST_TR.md`, `docs/MULTI_AGENT_RULES_TR.md` ve `AGENTS.md` icinde eksik teslimin reddedilecegi netlestirildi

## Out of Scope
- [ ] Uygulama/runtime/media kodunu degistirmek
- [ ] Aktif tasklarin scope'unu geriye donuk genisletmek

## Lock Dosyalari
- `docs/tasks/TASK-057.md`
- `docs/TASK_LOCKS.md`
- `docs/NEXT_TASK.md`
- `docs/tasks/_TEMPLATE.md`
- `scripts/start-task.ps1`
- `docs/AGENT_DELIVERY_CHECKLIST_TR.md`
- `docs/MULTI_AGENT_RULES_TR.md`
- `AGENTS.md`
- `docs/WORKLOG.md`

## Uygulama Adimlari
- [x] Mevcut template ve task-acma akisi incelendi
- [x] Yeni template zorunlu checklistlerle guncellendi
- [x] Start-task akisi yeni template ile uyumlu hale getirildi
- [x] Teslim disiplin belgeleri sertlestirildi

## Kabul Kriterleri
- [x] Yeni task dosyalari artik zorunlu uygulama/teslim/kapanis checklistleri ile acilir
- [x] Ajan isi bitirince neyi isaretleyecegini ve hangi kaniti sunacagini task kartinda gorur
- [x] Koordinator eksik teslimle task kapatmaz kurali belgelerde acik yazilir
- [x] `pre-pr` PASS

## Teslimde Zorunlu Kanit
- [x] `git branch --show-current`
- [x] `git branch -vv`
- [x] `git status --short`
- [x] `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick`
- [x] Yeni template/task acma akisinin ornek kaniti
- [x] Commit hash

## Kapanis Adimlari
- [x] Task kartindaki checklistler gercek sonuca gore guncellendi
- [x] `docs/WORKLOG.md` guncellendi
- [x] `docs/TASK_LOCKS.md` kaydi `closed` yapildi
- [x] `docs/NEXT_TASK.md` panosu guncellendi
- [x] Branch pushlandi

## Komutlar
```powershell
powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick
```

## Risk / Not
- Bu gorev disiplin ve surec sertlestirmesidir; uygulama davranisini degistirmez
- Eski task dosyalari geriye donuk otomatik migrate edilmeyecek; yeni ve aktif tasklar yeni standarda gore yonetilecek
