# NEXT TASK (Koordinasyon Panosu)

Durum: `ACTIVE`  
Koordinator: `codex`  
Kaynak Gercek: `docs/TASK_LOCKS.md`

## Aktif Gorevler (Tek Kaynak)
1. `TASK-024` - `codex-b` - Listing card v2 yerlesim sozlesmesi (baslik/puan/yorum/ozellik/fiyat konumlari sabit, iki app parity).

## Hazir Tasklar (Atama Bekliyor)
1. Yeni task acilacaksa once `TASK_LOCKS.md` aktif lock cakismazligi kontrol edilir.

## Son Kapanis
1. `TASK-023` - Servis bolgesi il/ilce coklu secim (admin+owner) tamamlandi ve lock kapatildi.

## Kapanis Kurali (Zorunlu)
1. Kapanis kaniti olmadan task `closed` edilemez.
2. Zorunlu kanit paketi:
   - `git branch --show-current`
   - `git status --short`
   - `powershell -ExecutionPolicy Bypass -File D:\orkestram\scripts\pre-pr.ps1 -Mode quick` sonucu `PASS`
3. Kanit yoksa task durumu `active` kalir.
