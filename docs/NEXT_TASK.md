# NEXT TASK (Koordinasyon Panosu)

Durum: `IDLE`  
Koordinator: `codex`  
Kaynak Gercek: `docs/TASK_LOCKS.md`

## Aktif Gorevler (Tek Kaynak)
1. Aktif koordinator gorevi yok.

## Son Koordinator Kapanisi
1. `TASK-032` - WSL bazli paralel ajan koordinasyon standardi kapatildi.
   - Koordinator bakisi: `TASK-033` ciktilari `docs/WSL_RUNTIME_PLAYBOOK_TR.md` icinde toplandi.
   - Koordinator bakisi: `TASK-034` ciktilari `docs/AGENT_LOCK_MATRIX_TR.md` icinde toplandi.
   - Koordinator bakisi: `TASK-035` ciktilari `docs/AGENT_DELIVERY_CHECKLIST_TR.md` icinde toplandi.
   - Hard Guard ve repo-relative operasyon komutlari resmi kapanis kaydina alindi.

## Son Kapanis
1. `TASK-032` - Koordinator kapanisi tamamlandi; TASK-033/034/035 ciktilari entegre edildi (pre-pr PASS).
2. `TASK-031` - Admin/Owner listing gorsel hatti + admin500 deterministic model fix tamamlandi (pre-pr PASS).
3. `TASK-030` - Belge duzenleme disiplini resmi kurala baglandi (pre-pr PASS).

## Kapanis Kurali (Zorunlu)
1. Kapanis kaniti olmadan task `closed` edilemez.
2. Zorunlu kanit paketi:
   - `git branch --show-current`
   - `git status --short`
   - `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick` sonucu `PASS`
3. Kanit yoksa task durumu `active` kalir.
