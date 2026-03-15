# NEXT TASK (Koordinasyon Panosu)

Durum: `ACTIVE`  
Koordinator: `codex`  
Kaynak Gercek: `docs/TASK_LOCKS.md`

## Aktif Gorevler (Tek Kaynak)
1. `TASK-032` - WSL bazli paralel ajan koordinasyon standardi (koordinator taski).
   - Hard Guard: D baslangicinda zorunlu WSL hizalama kaniti aktif.

## Hazir Tasklar (Atama Bekliyor)
1. `TASK-033` (`codex-a`) - WSL runtime kanit ve startup playbook standardi.
2. `TASK-034` (`codex-b`) - Cakismaz lock matrisi ve paralel dagitim standardi.
3. `TASK-035` (`codex-c`) - Dogrulama pipeline + teslim check-list standardi.

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
