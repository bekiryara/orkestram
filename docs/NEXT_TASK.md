# NEXT TASK (Koordinasyon Panosu)

Durum: `READY`  
Koordinator: `codex`  
Kaynak Gercek: `docs/TASK_LOCKS.md`

## Aktif Gorevler (Merkezi Koordinasyon)
1. `YOK` - aktif koordinasyon gorevi bulunmuyor

## Son Koordinator Kapanisi
1. `TASK-079` - owner coverage write-path teslimi merkezi kayitlarda kapatildi
2. `TASK-078` - `TASK-074` ve `TASK-075` icin ayrik merge task gerekmez karari merkezi kayda alindi
3. `TASK-077` - owner branch PR hazir / merge hazir standardi ve varsayilan sira modeli kayda alindi

## Son Kapanis
1. `TASK-079` - owner coverage write-path teslimi commit `8d06a46` ve docs duzeltme `22cd676` ile kabul edilip aktif pano kapatildi
2. `TASK-078` - `TASK-074` ve `TASK-075` icin ayrik merge task gerekmez karari merkezi kayda alindi
3. `TASK-077` - owner branch PR hazir / merge hazir standardi ve varsayilan sira modeli kayda alindi

## Merge Bekleyen Degerlendirme
1. `TASK-079` - task karti merkezi olarak normalize edildi; ancak `izmirorkestra` owner parity yazim hattinda ayni degisiklik bulunmadigi icin merge karari ayrica verilecek

## Kapanis Kurali (Zorunlu)
1. Kapanis kaniti olmadan task `closed` edilemez.
2. Zorunlu kanit paketi:
   - `git branch --show-current`
   - `git branch -vv`
   - `git status --short`
   - `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick` sonucu `PASS`
3. Kanit yoksa task durumu `active` kalir.
4. Bu pano ile `docs/TASK_LOCKS.md` birebir senkron tutulur; paralel kapanis ve sira degisikligi yalniz koordinator tarafindan islenir.
