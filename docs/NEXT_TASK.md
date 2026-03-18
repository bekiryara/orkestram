# NEXT TASK (Koordinasyon Panosu)

Durum: `READY`  
Koordinator: `codex`  
Kaynak Gercek: `docs/TASK_LOCKS.md`

## Aktif Gorevler (Tek Kaynak)
1. `YOK` - `TASK-065` listing detail UI v1 ve mevcut demo veri audit'i ile kapatildi; whitelist/idempotent fixture otomasyonu icin yeni task acilacak.

## Son Koordinator Kapanisi
1. `TASK-065` - Listing detail UI v1 iki appte parity ile tamamlandi; design-preview demo verisi read-only audit ile dogrulandi ve ayri fixture otomasyon task'i gerektigi netlesti.
2. `TASK-063` - `:8281` design preview incident'i kapatildi; port eslesmesi iki appte duzeltildi ve `Edit Source == Mount Source` guard'i resmi kurala baglandi.
3. `TASK-062` - UI tasklarda preview onayi, ayni-task revize ve merge sirasi resmi kurala baglandi.

## Son Kapanis
1. `TASK-065` - UI v1 onayli sonuc korundu; mevcut demo listingler db uzerinden read-only audit ile dogrulandi ve whitelist/idempotent fixture otomasyonu yeni taska ayrildi.
2. `TASK-063` - `siteFromRequest()` iki appte `:8281` cozumler hale getirildi; preview/source mismatch root-cause'u belge ve template guard'lari ile kalici kurala baglandi.
3. `TASK-062` - UI review onayi gelmeden merge yok; kapsam ayniysa revize ayni taskta donecek sekilde sabitlendi.

## Kapanis Kurali (Zorunlu)
1. Kapanis kaniti olmadan task `closed` edilemez.
2. Zorunlu kanit paketi:
   - `git branch --show-current`
   - `git branch -vv`
   - `git status --short`
   - `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick` sonucu `PASS`
3. Kanit yoksa task durumu `active` kalir.
