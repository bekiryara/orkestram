# Agent Delivery Checklist (TR)

Amac:
1. Ajan teslimini tek formatta toplamak.
2. `validate` ve `pre-pr` kapilarini hangi adimda kosacagini netlestirmek.

## 1) Pipeline Sirasi

1. Baslangic kaniti al:
   - `pwd`
   - `git rev-parse --show-toplevel`
   - `git branch --show-current`
   - `git status --short`
2. Lock'u dogrula.
3. Degisiklikleri yap.
4. Gerekliyse `powershell -ExecutionPolicy Bypass -File scripts/validate.ps1 -App both`
5. Kapanis oncesi zorunlu:
   - `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick`

## 2) Hangi Durumda `validate`

`validate` zorunlu:
1. Uygulama kodu degistiyse
2. Testleri etkileyen script degisimi varsa
3. Runtime davranisini etkileyen degisim varsa

Yalniz belge/koordinasyon degisimi varsa:
1. `pre-pr -Mode quick` yeterlidir.

## 3) Teslim Formati

Koordinatora tek mesajda su format verilir:

```text
task: TASK-0xx
branch: agent/<ajan>/<task-id>
files:
- path/a
- path/b
checks:
- git branch --show-current
- git status --short
- pre-pr: PASS
risks:
- yok / kisa risk notu
```

## 4) Interrupted / Resume Protokolu

Terminal kapandiysa veya uzun ara verildiyse:
1. `pwd`
2. `git branch --show-current`
3. `git status --short`
4. `docs/TASK_LOCKS.md`
5. Sonra devam

Kural:
1. Bu kontrol yenilenmeden eski editore/degisiklige devam edilmez.

## 5) Koordinator Kapanis Paketi

Koordinator task kapatmadan once:
1. Ajanin dosya listesini alir.
2. Branch/status/pre-pr kanitini alir.
3. `TASK_LOCKS` ve `NEXT_TASK` uyumunu kontrol eder.
4. `WORKLOG` final satirini ekler.
