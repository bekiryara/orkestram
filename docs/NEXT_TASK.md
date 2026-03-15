# NEXT TASK (Koordinasyon Panosu)

Durum: `ACTIVE`  
Koordinator: `codex`  
Kaynak Gercek: `docs/TASK_LOCKS.md`

## Aktif Gorevler (Tek Kaynak)
1. `TASK-032` - WSL bazli paralel ajan koordinasyon standardi (koordinator taski).

## Hazir Tasklar (Atama Bekliyor)
1. `TASK-032` altinda A/B/C alt gorevleri lock matrisi tamamlaninca acilacak.
2. Yeni bagimsiz task acma gecici olarak durduruldu.

## Son Kapanis
1. `TASK-031` - Admin/Owner listing gorsel hatti + admin500 deterministic model fix tamamlandi (pre-pr PASS).
2. `TASK-030` - Belge duzenleme disiplini resmi kurala baglandi (pre-pr PASS).
3. `TASK-029` - Dokuman drift hizalama tamamlandi (pre-pr PASS).

## Kapanis Kurali (Zorunlu)
1. Kapanis kaniti olmadan task `closed` edilemez.
2. Zorunlu kanit paketi:
   - `git branch --show-current`
   - `git status --short`
   - `powershell -ExecutionPolicy Bypass -File D:\orkestram\scripts\pre-pr.ps1 -Mode quick` sonucu `PASS`
3. Kanit yoksa task durumu `active` kalir.
