# NEXT TASK (Koordinasyon Panosu)

Durum: `READY`  
Koordinator: `codex`  
Kaynak Gercek: `docs/TASK_LOCKS.md`

## Aktif Gorevler (Merkezi Koordinasyon)
1. `YOK` - aktif koordinasyon gorevi bulunmuyor
## Son Koordinator Kapanisi
1. `TASK-094` - simple pricing v1 request binding tamamlandi
2. `TASK-093` - simple pricing v1 pricing_mode ve publish guard tamamlandi
3. `TASK-092` - simple pricing v1 validation ve ui sadelestirme tamamlandi
## Son Kapanis
1. `TASK-094` - simple pricing v1 request binding tamamlandi
2. `TASK-093` - simple pricing v1 pricing_mode ve publish guard tamamlandi
3. `TASK-092` - simple pricing v1 validation ve ui sadelestirme tamamlandi
## Kapanis Kurali (Zorunlu)
1. Kapanis kaniti olmadan task `closed` edilemez.
2. Zorunlu kanit paketi:
   - `git branch --show-current`
   - `git branch -vv`
   - `git status --short`
   - `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick` sonucu `PASS`
3. Kanit yoksa task durumu `active` kalir.
4. Bu pano ile `docs/TASK_LOCKS.md` birebir senkron tutulur; paralel kapanis ve sira degisikligi yalniz koordinator tarafindan islenir.



