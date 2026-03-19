# NEXT TASK (Koordinasyon Panosu)

Durum: `READY`  
Koordinator: `codex`  
Kaynak Gercek: `docs/TASK_LOCKS.md`

## Aktif Gorevler (Merkezi Koordinasyon)
1. `YOK` - aktif koordinasyon gorevi bulunmuyor

## Son Koordinator Kapanisi
1. `TASK-080` - owner coverage parity iki appte tamamlandi; branch merge hazir duruma getirildi
2. `TASK-079` - owner coverage write-path teslimi merkezi kayitlarda kapatildi
3. `TASK-078` - `TASK-074` ve `TASK-075` icin ayrik merge task gerekmez karari merkezi kayda alindi

## Son Kapanis
1. `TASK-080` - iki app owner coverage write-path parity tamamlandi; `OwnerPanelActionsTest` iki appte PASS ve branch merge hazir
2. `TASK-079` - owner coverage write-path teslimi commit `8d06a46` ve docs duzeltme `22cd676` ile kabul edilip aktif pano kapatildi
3. `TASK-078` - `TASK-074` ve `TASK-075` icin ayrik merge task gerekmez karari merkezi kayda alindi

## Merge Bekleyen Degerlendirme
1. `TASK-080` - `agent/codex/task-080` uzerinde iki app owner parity tamam; merge karari bu branch uzerinden verilebilir

## Kapanis Kurali (Zorunlu)
1. Kapanis kaniti olmadan task `closed` edilemez.
2. Zorunlu kanit paketi:
   - `git branch --show-current`
   - `git branch -vv`
   - `git status --short`
   - `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick` sonucu `PASS`
3. Kanit yoksa task durumu `active` kalir.
4. Bu pano ile `docs/TASK_LOCKS.md` birebir senkron tutulur; paralel kapanis ve sira degisikligi yalniz koordinator tarafindan islenir.
