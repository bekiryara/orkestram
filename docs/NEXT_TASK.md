# NEXT TASK (Koordinasyon Panosu)

Durum: `ACTIVE`  
Koordinator: `codex`  
Kaynak Gercek: `docs/TASK_LOCKS.md`

## Aktif Gorevler (Merkezi Koordinasyon)
1. `TASK-091` - Merge treni: TASK-085, TASK-086, TASK-087, TASK-088 ve TASK-090 zincirini kontrollu sekilde ana hatta tasiyip runtime dogrulamasini tamamla

## Son Koordinator Kapanisi
1. `TASK-085` - Smoke gate thumb fallback ve locations manifest/import stabilizasyonu tamamlandi
2. `TASK-083` - mekanik sertlestirme tamamlandi; task acma/kapatma akisi, koordinasyon locklari ve upstream zinciri repo disiplinine gore hizalandi
3. `TASK-082` - ortam guardrail standardi runtime/source/readiness/sandbox/upstream/auth kurallariyla resmi hale getirildi

## Son Kapanis
1. `TASK-085` - Smoke gate thumb fallback ve locations manifest/import stabilizasyonu tamamlandi
2. `TASK-083` - mekanik sertlestirme tamamlandi; task acma/kapatma akisi, koordinasyon locklari ve upstream zinciri repo disiplinine gore hizalandi
3. `TASK-082` - task template, ajan/koordinator disiplin dokumanlari ve `pre-pr`/`validate` scriptleri ortam blokaj siniflariyla guncellendi; `pre-pr PASS`

## Kapanis Kurali (Zorunlu)
1. Kapanis kaniti olmadan task `closed` edilemez.
2. Zorunlu kanit paketi:
   - `git branch --show-current`
   - `git branch -vv`
   - `git status --short`
   - `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick` sonucu `PASS`
3. Kanit yoksa task durumu `active` kalir.
4. Bu pano ile `docs/TASK_LOCKS.md` birebir senkron tutulur; paralel kapanis ve sira degisikligi yalniz koordinator tarafindan islenir.
