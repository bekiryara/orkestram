# NEXT TASK (Koordinasyon Panosu)

Durum: `ACTIVE`  
Koordinator: `codex`  
Kaynak Gercek: `docs/TASK_LOCKS.md`

## Yuksek Etkili 3 Task
1. `TASK-020` - `codex` - Merge tren ve gate enforcement: kapanis kaniti olmayan PR merge edilmez, pre-pr zorunlu, lock hijyeni.
2. `TASK-021` - `codex-a` - Uctan uca smoke+acceptance toparlama: iki app parity ve kritik akislarda regressionsuz kapanis.
3. `TASK-022` - `codex-b` - Son entegrasyon paritesi: acik kalan UI/flow farklarini kapatip tek davranis standardi.

## Kapanis Kurali (Teknik Borc Sifir)
1. Her task kapanisinda zorunlu kanit:
   - `git branch --show-current`
   - `git status --short`
   - `powershell -ExecutionPolicy Bypass -File D:\orkestram\scripts\pre-pr.ps1 -Mode quick` PASS
2. Lock dosyasinda `closed` olmadan task tamam sayilmaz.
3. Ayni dosyada cakisacak iki aktif task acilmaz.

## Merge Sirasi
1. `TASK-021`
2. `TASK-022`
3. `TASK-020` (en son, entegrasyon kapanisi)
