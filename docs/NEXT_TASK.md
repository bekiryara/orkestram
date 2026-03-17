# NEXT TASK (Koordinasyon Panosu)

Durum: `FROZEN`  
Koordinator: `codex`  
Kaynak Gercek: `docs/TASK_LOCKS.md`

## Aktif Gorevler (Tek Kaynak)
1. Aktif gorev yok.

## Son Koordinator Kapanisi
1. `TASK-062` - UI tasklarda preview onayi, ayni-task revize ve merge sirasi resmi kurala baglandi.
2. `TASK-061` - Design-preview runtime standardizasyonu tamamlandi; `main` ve `design` lane ayrimi ile merge-oncesi UI review akisi sabitlendi.
3. `TASK-057` - Ajan teslim disiplini ve task sablonu zorunlu checklist sertlestirmesi tamamlandi (pre-pr PASS).

## Son Kapanis
1. `TASK-062` - UI review onayi gelmeden merge yok; kapsam ayniysa revize ayni taskta donecek sekilde sabitlendi.
2. `TASK-061` - Design-preview runtime standardizasyonu tamamlandi; 8280/8281 sabit URL'leri ile tasarim review lane'i acildi.
3. `TASK-060` - Listing detail referans ekran yonune tekrar hizalandi; solda profil, sagda buyuk medya, altta galeri ve sade ikincil aksiyonlar parity ile guncellendi.

## Kapanis Kurali (Zorunlu)
1. Kapanis kaniti olmadan task `closed` edilemez.
2. Zorunlu kanit paketi:
   - `git branch --show-current`
   - `git branch -vv`
   - `git status --short`
   - `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick` sonucu `PASS`
3. Kanit yoksa task durumu `active` kalir.
