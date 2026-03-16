# NEXT TASK (Koordinasyon Panosu)

Durum: `IDLE`  
Koordinator: `codex`  
Kaynak Gercek: `docs/TASK_LOCKS.md`

## Aktif Gorevler (Tek Kaynak)
1. Aktif koordinator gorevi yok.

## Son Koordinator Kapanisi
1. `TASK-041` - `TASK-040` ana hatta merge edilip koordinasyon kapanisi hizalandi.
2. `TASK-040` - Hero + listing CTA hiyerarsisi paralel koordinasyon ile tamamlandi.
3. `TASK-036` - Task-id tekrar yasagi + koordinator cevap sablonu + remote/upstream zorunlulugu tamamlandi.

## Son Kapanis
1. `TASK-041` - `TASK-040` merge/kapanis hizasi tamamlandi (pre-pr PASS).
2. `TASK-040` - Hero/CTA toparlama + listing detay CTA sadeleme tamamlandi (pre-pr PASS).
3. `TASK-036` - Disiplin sertlestirme tamamlandi (pre-pr PASS).

## Kapanis Kurali (Zorunlu)
1. Kapanis kaniti olmadan task `closed` edilemez.
2. Zorunlu kanit paketi:
   - `git branch --show-current`
   - `git status --short`
   - `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick` sonucu `PASS`
3. Kanit yoksa task durumu `active` kalir.
