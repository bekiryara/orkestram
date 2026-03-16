# NEXT TASK (Koordinasyon Panosu)

Durum: `ACTIVE`  
Koordinator: `codex`  
Kaynak Gercek: `docs/TASK_LOCKS.md`

## Aktif Gorevler (Tek Kaynak)
1. `TASK-052` - Profil/listing-card/listing-detail/galeri medya hattini kokten standardize etme gorevi aktif.

## Son Koordinator Kapanisi
1. `TASK-048` - Listing media/runtime hatti + detail karar hiyerarsisi + listing card parity entegrasyonu tamamlandi (pre-pr PASS).
2. `TASK-046` - `TASK-043` ana hatta merge edilip runtime hizasi dogrulandi.
3. `TASK-043` - Listing filtre UX toparlamasi paralel ajan teslimleriyle tamamlandi.

## Son Kapanis
1. `TASK-048` - Listing media/runtime hatti + detail karar hiyerarsisi + listing card parity entegrasyonu tamamlandi (pre-pr PASS).
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
