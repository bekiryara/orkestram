# NEXT TASK (Koordinasyon Panosu)

Durum: `ACTIVE`  
Koordinator: `codex`  
Kaynak Gercek: `docs/TASK_LOCKS.md`

## Aktif Gorevler (Merkezi Koordinasyon)
1. `TASK-092` - SimplePricingV1 validation ve UI sadelestirme: price_type bazli kurallar, owner-admin parity ve label_only temizligi
## Son Koordinator Kapanisi
1. `TASK-091` - 085-090 merge treni ana hatta tasindi
2. `TASK-085` - Smoke gate thumb fallback ve locations manifest/import stabilizasyonu tamamlandi
3. `TASK-083` - mekanik sertlestirme tamamlandi; task acma/kapatma akisi, koordinasyon locklari ve upstream zinciri repo disiplinine gore hizalandi
## Son Kapanis
1. `TASK-091` - 085-090 merge treni ana hatta tasindi
2. `TASK-085` - Smoke gate thumb fallback ve locations manifest/import stabilizasyonu tamamlandi
3. `TASK-083` - mekanik sertlestirme tamamlandi; task acma/kapatma akisi, koordinasyon locklari ve upstream zinciri repo disiplinine gore hizalandi
## Kapanis Kurali (Zorunlu)
1. Kapanis kaniti olmadan task `closed` edilemez.
2. Zorunlu kanit paketi:
   - `git branch --show-current`
   - `git branch -vv`
   - `git status --short`
   - `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick` sonucu `PASS`
3. Kanit yoksa task durumu `active` kalir.
4. Bu pano ile `docs/TASK_LOCKS.md` birebir senkron tutulur; paralel kapanis ve sira degisikligi yalniz koordinator tarafindan islenir.

