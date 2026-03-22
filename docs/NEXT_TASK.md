# NEXT TASK (Koordinasyon Panosu)

Durum: `ACTIVE`  
Koordinator: `codex`  
Kaynak Gercek: `docs/TASK_LOCKS.md`

## Aktif Gorevler (Merkezi Koordinasyon)
1. `TASK-093` - SimplePricingV1 pricing_mode ve publish guard: listing pricing_mode zemini, simple publish kurallari ve yayin oncesi fiyat gecerlilik kontrolunu kapat
## Son Koordinator Kapanisi
1. `TASK-092` - simple pricing v1 validation ve ui sadelestirme tamamlandi
2. `TASK-091` - 085-090 merge treni ana hatta tasindi
3. `TASK-085` - Smoke gate thumb fallback ve locations manifest/import stabilizasyonu tamamlandi
## Son Kapanis
1. `TASK-092` - simple pricing v1 validation ve ui sadelestirme tamamlandi
2. `TASK-091` - 085-090 merge treni ana hatta tasindi
3. `TASK-085` - Smoke gate thumb fallback ve locations manifest/import stabilizasyonu tamamlandi
## Kapanis Kurali (Zorunlu)
1. Kapanis kaniti olmadan task `closed` edilemez.
2. Zorunlu kanit paketi:
   - `git branch --show-current`
   - `git branch -vv`
   - `git status --short`
   - `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick` sonucu `PASS`
3. Kanit yoksa task durumu `active` kalir.
4. Bu pano ile `docs/TASK_LOCKS.md` birebir senkron tutulur; paralel kapanis ve sira degisikligi yalniz koordinator tarafindan islenir.


