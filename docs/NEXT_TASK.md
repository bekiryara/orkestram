# NEXT TASK (Koordinasyon Panosu)

Durum: `ACTIVE`  
Koordinator: `codex`  
Kaynak Gercek: `docs/TASK_LOCKS.md`

## Aktif Gorevler (Merkezi Koordinasyon)
1. `TASK-090` - Structured fiyat modeli gecisi: public filtre/sort ve detail JSON-LD hattini price_label parse yerine price_min/price_max/currency/price_type kaynak modeline tasimak
## Son Koordinator Kapanisi
1. `TASK-089` - koordinator drift hijyeni tamamlandi
2. `TASK-088` - deterministic account fixture ve reset recovery akisi tamamlandi
3. `TASK-087` - Deterministic review demo medya hatti tamamlandi
## Son Kapanis
1. `TASK-089` - koordinator drift hijyeni tamamlandi
2. `TASK-088` - deterministic account fixture ve reset recovery akisi tamamlandi
3. `TASK-087` - Deterministic review demo medya hatti tamamlandi
## Kapanis Kurali (Zorunlu)
1. Kapanis kaniti olmadan task `closed` edilemez.
2. Zorunlu kanit paketi:
   - `git branch --show-current`
   - `git branch -vv`
   - `git status --short`
   - `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick` sonucu `PASS`
3. Kanit yoksa task durumu `active` kalir.
4. Bu pano ile `docs/TASK_LOCKS.md` birebir senkron tutulur; paralel kapanis ve sira degisikligi yalniz koordinator tarafindan islenir.






