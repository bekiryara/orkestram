# NEXT TASK (Koordinasyon Panosu)

Durum: `ACTIVE`  
Koordinator: `codex`  
Kaynak Gercek: `docs/TASK_LOCKS.md`

## Aktif Gorevler (Tek Kaynak)
1. `TASK-048` - Listing media/runtime hattini netlestirme ve detail karar hiyerarsisi koordinasyonu aktif.

## Son Koordinator Kapanisi
1. `TASK-048` - Listing media/runtime hattini ve detail karar hiyerarsisini koordine et.
2. `TASK-046` - `TASK-043` ana hatta merge edilip runtime hizasi dogrulandi.
3. `TASK-043` - Listing filtre UX toparlamasi paralel ajan teslimleriyle tamamlandi.

## Son Kapanis
1. `TASK-047` - Listing sonuc ozeti sadelestirme + izmir test parity duzeltmesi tamamlandi (pre-pr PASS).
2. `TASK-046` - Listing filtre UX ana hata tasindi (pre-pr PASS).
3. `TASK-043` - Listing filtre UX toparlamasi tamamlandi (pre-pr PASS).

## Kapanis Kurali (Zorunlu)
1. Kapanis kaniti olmadan task `closed` edilemez.
2. Zorunlu kanit paketi:
   - `git branch --show-current`
   - `git branch -vv`
   - `git status --short`
   - `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick` sonucu `PASS`
3. Kanit yoksa task durumu `active` kalir.
