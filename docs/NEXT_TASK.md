# NEXT TASK (Koordinasyon Panosu)

Durum: `ACTIVE`  
Koordinator: `codex`  
Kaynak Gercek: `docs/TASK_LOCKS.md`

## Aktif Gorevler (Merkezi Koordinasyon)
1. `TASK-083` - Mekanik sertlestirme: task acma/kapatma akisi, upstream baglama sirasi ve WSL/credential cukurlarini script seviyesinde azalt

## Son Koordinator Kapanisi
1. `TASK-082` - ortam guardrail standardi runtime/source/readiness/sandbox/upstream/auth kurallariyla resmi hale getirildi
2. `TASK-081` - kanitli satir-sonu/encoding drift temizlendi; `codex-b` ve `codex-c` stale gorunurlugu kapatildi
3. `TASK-080` - owner coverage parity iki appte tamamlandi; branch merge hazir duruma getirildi

## Son Kapanis
1. `TASK-082` - task template, ajan/koordinator disiplin dokumanlari ve `pre-pr`/`validate` scriptleri ortam blokaj siniflariyla guncellendi; `pre-pr PASS`
2. `TASK-081` - koordinator, `codex-b` ve `codex-c` worktree'lerinde yalniz drift sinifindaki dosyalar restore edilip temiz status alindi
3. `TASK-080` - iki app owner coverage write-path parity tamamlandi; `OwnerPanelActionsTest` iki appte PASS ve branch merge hazir

## Kapanis Kurali (Zorunlu)
1. Kapanis kaniti olmadan task `closed` edilemez.
2. Zorunlu kanit paketi:
   - `git branch --show-current`
   - `git branch -vv`
   - `git status --short`
   - `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick` sonucu `PASS`
3. Kanit yoksa task durumu `active` kalir.
4. Bu pano ile `docs/TASK_LOCKS.md` birebir senkron tutulur; paralel kapanis ve sira degisikligi yalniz koordinator tarafindan islenir.

