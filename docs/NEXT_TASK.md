# NEXT TASK (Koordinasyon Panosu)

Durum: `FROZEN`  
Koordinator: `codex`  
Kaynak Gercek: `docs/TASK_LOCKS.md`

## Aktif Gorevler (Tek Kaynak)
1. Aktif gorev yok.

## Hazir Tasklar (Atama Bekliyor)
1. Yeni task acma gecici olarak durduruldu (stabilizasyon penceresi).
2. Sonraki tasklar yalnizca stabilizasyon commitinden sonra acilacak.

## Son Kapanis
1. `TASK-027` - Listing detail Offer schema tamamlandi (commit `a8e818c`, pre-pr PASS).
2. `TASK-026` - Public fiyat filtre/siralama tamamlandi (commit `06af8de`, pre-pr PASS).
3. `TASK-025` - Fiyat veri modeli + admin/owner parity tamamlandi (commit `3ff02ff`, pre-pr PASS).

## Kapanis Kurali (Zorunlu)
1. Kapanis kaniti olmadan task `closed` edilemez.
2. Zorunlu kanit paketi:
   - `git branch --show-current`
   - `git status --short`
   - `powershell -ExecutionPolicy Bypass -File D:\orkestram\scripts\pre-pr.ps1 -Mode quick` sonucu `PASS`
3. Kanit yoksa task durumu `active` kalir.
