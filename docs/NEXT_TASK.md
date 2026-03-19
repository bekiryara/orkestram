# NEXT TASK (Koordinasyon Panosu)

Durum: `READY`  
Koordinator: `codex`  
Kaynak Gercek: `docs/TASK_LOCKS.md`

## Aktif Gorevler (Merkezi Koordinasyon)
1. `YOK` - aktif koordinasyon gorevi bulunmuyor

## Son Koordinator Kapanisi
1. `TASK-083` - mekanik sertlestirme tamamlandi; task acma/kapatma akisi, koordinasyon locklari ve upstream zinciri repo disiplinine gore hizalandi
2. `TASK-082` - ortam guardrail standardi runtime/source/readiness/sandbox/upstream/auth kurallariyla resmi hale getirildi
3. `TASK-081` - kanitli satir-sonu/encoding drift temizlendi; `codex-b` ve `codex-c` stale gorunurlugu kapatildi

## Son Kapanis
1. `TASK-083` - mekanik sertlestirme tamamlandi; task acma/kapatma akisi, koordinasyon locklari ve upstream zinciri repo disiplinine gore hizalandi
2. `TASK-082` - task template, ajan/koordinator disiplin dokumanlari ve `pre-pr`/`validate` scriptleri ortam blokaj siniflariyla guncellendi; `pre-pr PASS`
3. `TASK-081` - koordinator, `codex-b` ve `codex-c` worktree'lerinde yalniz drift sinifindaki dosyalar restore edilip temiz status alindi

## Kapanis Kurali (Zorunlu)
1. Kapanis kaniti olmadan task `closed` edilemez.
2. Zorunlu kanit paketi:
   - `git branch --show-current`
   - `git branch -vv`
   - `git status --short`
   - `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick` sonucu `PASS`
3. Kanit yoksa task durumu `active` kalir.
4. Bu pano ile `docs/TASK_LOCKS.md` birebir senkron tutulur; paralel kapanis ve sira degisikligi yalniz koordinator tarafindan islenir.
