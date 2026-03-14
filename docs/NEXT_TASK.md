# NEXT TASK (Koordinasyon Panosu)

Durum: `FROZEN`  
Koordinator: `codex`  
Kaynak Gercek: `docs/TASK_LOCKS.md`

## Aktif Gorevler (Tek Kaynak)
1. Aktif gorev yok.

## Hazir Tasklar (Atama Bekliyor)
1. Yeni task acma gecici olarak durduruldu (stabilizasyon penceresi).
2. Sonraki tasklar yalnizca koordinator onayi ile acilacak.

## Son Kapanis
1. `TASK-030` - Belge duzenleme disiplini resmi kurala baglandi (pre-pr PASS).
2. `TASK-029` - Dokuman drift hizalama tamamlandi (pre-pr PASS).
3. `TASK-028` - WSL tek-kaynak runtime stabilizasyonu tamamlandi (commit `f592646`, pre-pr PASS).

## Kapanis Kurali (Zorunlu)
1. Kapanis kaniti olmadan task `closed` edilemez.
2. Zorunlu kanit paketi:
   - `git branch --show-current`
   - `git status --short`
   - `powershell -ExecutionPolicy Bypass -File D:\orkestram\scripts\pre-pr.ps1 -Mode quick` sonucu `PASS`
3. Kanit yoksa task durumu `active` kalir.