# NEXT TASK (Koordinasyon Panosu)

Durum: `IDLE`  
Koordinator: `codex`  
Kaynak Gercek: `docs/TASK_LOCKS.md`

## Aktif Gorevler (Tek Kaynak)
1. Aktif koordinator gorevi yok.

## Son Koordinator Kapanisi
1. `TASK-032` - Remote/upstream hizasi tamamlandi.
   - `origin` artik canonical WSL repo ve koordinator workdir icin GitHub remote.
   - `windows-mirror` operasyonel push/pull akisindan cikarilip export-only roluyle sinirlandi.
   - Koordinator workdir icinde `canonical = /home/bekir/orkestram` remote modeli dokumante edildi.
   - `agent/codex/task-032` branch'i GitHub'da olusturulup upstream'e baglandi.

## Son Kapanis
1. `TASK-032` - Canonical WSL repo + GitHub remote/upstream modeli tamamlandi (pre-pr PASS).
2. `TASK-031` - Admin/Owner listing gorsel hatti + admin500 deterministic model fix tamamlandi (pre-pr PASS).
3. `TASK-030` - Belge duzenleme disiplini resmi kurala baglandi (pre-pr PASS).

## Kapanis Kurali (Zorunlu)
1. Kapanis kaniti olmadan task `closed` edilemez.
2. Zorunlu kanit paketi:
   - `git branch --show-current`
   - `git status --short`
   - `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick` sonucu `PASS`
3. Kanit yoksa task durumu `active` kalir.
