# NEXT TASK (Koordinasyon Panosu)

Durum: `ACTIVE`  
Koordinator: `codex`  
Kaynak Gercek: `docs/TASK_LOCKS.md`

## Yuksek Etkili 3 Task
1. `TASK-017` - `codex-b` - UI parity final: iki appte ayni frontend iskeleti + logo renk token standardi + responsive kirilim kapatma.
2. `TASK-019` - `codex-c` - Frontend shell migration final: ortak header/footer partiallari ve layout standardi, business logic degistirmeden.
3. `TASK-020` - `codex` - Merge tren ve gate enforcement: kapanis kaniti olmayan PR merge edilmez, pre-pr zorunlu, lock hijyeni.

## Kapanis Kurali (Teknik Borc Sifir)
1. Her task kapanisinda zorunlu kanit:
   - `git branch --show-current`
   - `git status --short`
   - `powershell -ExecutionPolicy Bypass -File D:\orkestram\scripts\pre-pr.ps1 -Mode quick` PASS
2. Lock dosyasinda `closed` olmadan task tamam sayilmaz.
3. Ayni dosyada cakisacak iki aktif task acilmaz.

## Merge Sirasi
1. `TASK-017`
2. `TASK-019`
3. `TASK-020` (en son, entegrasyon kapanisi)
