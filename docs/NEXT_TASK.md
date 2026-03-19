# NEXT TASK (Koordinasyon Panosu)

Durum: `ACTIVE`  
Koordinator: `codex`  
Kaynak Gercek: `docs/TASK_LOCKS.md`

## Aktif Gorevler (Merkezi Koordinasyon)
1. `TASK-079` - Paket 01 owner service area / coverage write-path tamamlama `codex-a` ajanina devredildi

## Son Koordinator Kapanisi
1. `TASK-078` - `TASK-074` ve `TASK-075` icin ayrik merge task gerekmez karari merkezi kayda alindi
2. `TASK-077` - owner branch PR hazir / merge hazir standardi ve varsayilan sira modeli kayda alindi
3. `TASK-076` - task acilis recovery, aktif pano senkronu ve ajan teslimleri merkezi kapanisla tamamlandi

## Son Kapanis
1. `TASK-078` - `TASK-074` ve `TASK-075` icin ayrik merge task gerekmez karari merkezi kayda alindi
2. `TASK-077` - owner branch PR hazir / merge hazir standardi ve varsayilan sira modeli kayda alindi
3. `TASK-076` - task acilis recovery, aktif pano senkronu ve ajan teslimleri merkezi kapanisla tamamlandi

## Kapanis Kurali (Zorunlu)
1. Kapanis kaniti olmadan task `closed` edilemez.
2. Zorunlu kanit paketi:
   - `git branch --show-current`
   - `git branch -vv`
   - `git status --short`
   - `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick` sonucu `PASS`
3. Kanit yoksa task durumu `active` kalir.
4. Bu pano ile `docs/TASK_LOCKS.md` birebir senkron tutulur; paralel kapanis ve sira degisikligi yalniz koordinator tarafindan islenir.
