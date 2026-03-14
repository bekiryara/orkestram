# TASK-028

## Ozet
WSL tek-kaynak runtime stabilizasyonu: `dev-up` varsayilan sync kapatma, runtime mount/izin/env/vendor toparlama ve gate dogrulamalari.

## In Scope
- `scripts/dev-up.ps1` sync davranisini guvenli varsayilana alma (`-SyncFromWindows` ile explicit sync)
- Runtime WSL mount dogrulamasi (`/home/bekir/orkestram/...`)
- `vendor` / `.env` / storage izin eksiklerinden kaynakli runtime 500 toparlama
- `smoke-test` ve `pre-pr` quick PASS kaniti

## Out of Scope
- `stack-hos-*` / diger compose projeleri
- Uretim deploy islemleri

## Lock Dosyalari
- `scripts/dev-up.ps1`
- `docs/tasks/TASK-028.md`
- `docs/TASK_LOCKS.md`
- `docs/NEXT_TASK.md`
- `docs/WORKLOG.md`

## Kabul Kriteri
1. Web container mount kaynaklari WSL yolunu gostermeli.
2. `powershell -ExecutionPolicy Bypass -File D:\orkestram\scripts\smoke-test.ps1 -App both` => PASS
3. `powershell -ExecutionPolicy Bypass -File D:\orkestram\scripts\pre-pr.ps1 -Mode quick` => PASS

## Komutlar
- `powershell -ExecutionPolicy Bypass -File D:\orkestram\scripts\dev-up.ps1 -App both`
- `powershell -ExecutionPolicy Bypass -File D:\orkestram\scripts\smoke-test.ps1 -App both`
- `powershell -ExecutionPolicy Bypass -File D:\orkestram\scripts\pre-pr.ps1 -Mode quick`

## Notlar
- `git fetch --all --prune` ag erisimi nedeniyle bu turda fail olmustur.
