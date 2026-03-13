# NEXT TASK (Koordinasyon Panosu)

Durum: `ACTIVE`  
Koordinator: `codex`  
Kaynak Gercek: `docs/TASK_LOCKS.md`

## Aktif Gorevler
1. `TASK-011` - `codex-a` - Runtime izin sertlestirme (startup + preflight).
2. `TASK-013` - `codex-b` - Hesabim/Owner hibrit akis Faz 2 UI polish.

## Koordinator Notu
1. Lock/branch sahipligi sadece `TASK_LOCKS.md` uzerinden takip edilir.
2. Her kapanista zorunlu kanit:
   - `git branch --show-current`
   - `git status --short`
   - `pre-pr -Mode quick` PASS
3. Kapanis almayan gorev `closed` yapilmaz.
