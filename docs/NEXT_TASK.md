# NEXT TASK (Koordinasyon Panosu)

Durum: `READY`  
Koordinator: `codex`  
Kaynak Gercek: `docs/TASK_LOCKS.md`

## Aktif Gorevler (Merkezi Koordinasyon)
1. `YOK` - `TASK-066` kontrollu paralel task ve task-genisletme kurallari pre-pr PASS ile kapatildi.

## Son Koordinator Kapanisi
1. `TASK-066` - Repo genelinde en fazla 3 aktif task, ayni-task revize, task genisletme ve merkezi koordinasyon dosyasi kurallari AGENTS, disiplin, multi-agent, checklist, task template ve start-task akisi seviyesinde resmi hale getirildi.
2. `TASK-065` - Listing detail UI v1 iki appte parity ile tamamlandi; design-preview demo verisi read-only audit ile dogrulandi ve ayri fixture otomasyon task'i gerektigi netlesti.
3. `TASK-063` - `:8281` design preview incident'i kapatildi; port eslesmesi iki appte duzeltildi ve `Edit Source == Mount Source` guard'i resmi kurala baglandi.

## Son Kapanis
1. `TASK-066` - Tek active task siniri kaldirildi; repo-geneli 3 aktif task siniri, tek-ajan tek-task, task genisletme ve merkezi koordinasyon kurali pre-pr PASS ile sabitlendi.
2. `TASK-065` - UI v1 onayli sonuc korundu; mevcut demo listingler db uzerinden read-only audit ile dogrulandi ve whitelist/idempotent fixture otomasyonu yeni taska ayrildi.
3. `TASK-063` - `siteFromRequest()` iki appte `:8281` cozumler hale getirildi; preview/source mismatch root-cause'u belge ve template guard'lari ile kalici kurala baglandi.

## Kapanis Kurali (Zorunlu)
1. Kapanis kaniti olmadan task `closed` edilemez.
2. Zorunlu kanit paketi:
   - `git branch --show-current`
   - `git branch -vv`
   - `git status --short`
   - `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick` sonucu `PASS`
3. Kanit yoksa task durumu `active` kalir.
4. Bu pano ile `docs/TASK_LOCKS.md` birebir senkron tutulur; paralel kapanis ve sira degisikligi yalniz koordinator tarafindan islenir.
