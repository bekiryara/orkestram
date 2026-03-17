# NEXT TASK (Koordinasyon Panosu)

Durum: `ACTIVE`  
Koordinator: `codex`  
Kaynak Gercek: `docs/TASK_LOCKS.md`

## Aktif Gorevler (Tek Kaynak)
1. `TASK-065` - Listing detail UI v1 preview onayi alindi; deterministic demo fixture ve whitelist media kapsami devam ediyor.

## Son Koordinator Kapanisi
1. `TASK-063` - `:8281` design preview incident'i kapatildi; port eslesmesi iki appte duzeltildi ve `Edit Source == Mount Source` guard'i resmi kurala baglandi.
2. `TASK-062` - UI tasklarda preview onayi, ayni-task revize ve merge sirasi resmi kurala baglandi.
3. `TASK-061` - Design-preview runtime standardizasyonu tamamlandi; 8280/8281 design lane ve mount-source kontrati dogrulandi.

## Son Kapanis
1. `TASK-063` - `siteFromRequest()` iki appte `:8281` cozumler hale getirildi; preview/source mismatch root-cause'u belge ve template guard'lari ile kalici kurala baglandi.
2. `TASK-062` - UI review onayi gelmeden merge yok; kapsam ayniysa revize ayni taskta donecek sekilde sabitlendi.
3. `TASK-061` - Design-preview runtime standardizasyonu tamamlandi; 8280/8281 design lane ve mount-source kontrati dogrulandi.

## Kapanis Kurali (Zorunlu)
1. Kapanis kaniti olmadan task `closed` edilemez.
2. Zorunlu kanit paketi:
   - `git branch --show-current`
   - `git branch -vv`
   - `git status --short`
   - `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick` sonucu `PASS`
3. Kanit yoksa task durumu `active` kalir.

