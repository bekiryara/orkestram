# NEXT TASK (Koordinasyon Panosu)

Durum: `ACTIVE`  
Koordinator: `codex`  
Kaynak Gercek: `docs/TASK_LOCKS.md`

## Aktif Gorevler (Tek Kaynak)
1. `TASK-040` - Ana sayfa + ilan detay CTA/dugme hiyerarsisi paralel ajan koordinasyonu.

## Paralel Alt Gorevler (Hazir)
1. `TASK-037` (`codex-a`) - Home hero CTA sadeleme
2. `TASK-038` (`codex-b`) - CTA tasarim sistemi (CSS)
3. `TASK-039` (`codex-c`) - Listing detay CTA hiyerarsisi

## Son Koordinator Kapanisi
1. `TASK-036` - Task-id tekrar yasagi + koordinator cevap sablonu + remote/upstream zorunlulugu tamamlandi.
2. `TASK-032` - Remote/upstream hizasi tamamlandi.

## Son Kapanis
1. `TASK-036` - Disiplin sertlestirme tamamlandi (pre-pr PASS).
2. `TASK-032` - Canonical WSL repo + GitHub remote/upstream modeli tamamlandi (pre-pr PASS).
3. `TASK-031` - Admin/Owner listing hatti + admin500 deterministic model fix tamamlandi (pre-pr PASS).

## Kapanis Kurali (Zorunlu)
1. Kapanis kaniti olmadan task `closed` edilemez.
2. Zorunlu kanit paketi:
   - `git branch --show-current`
   - `git status --short`
   - `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick` sonucu `PASS`
3. Kanit yoksa task durumu `active` kalir.
