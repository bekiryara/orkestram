# TASK-028

Durum: `DONE`  
Ajan: `codex`  
Branch: `agent/codex/task-028`  
Baslangic: `2026-03-15 00:06`

## Ozet
- WSL tek-kaynak runtime stabilizasyonu: `dev-up` sync davranisinin guvenli varsayilana alinmasi, runtime mount/izin/env/vendor toparlama ve gate dogrulamalari.

## In Scope
- [x] `scripts/dev-up.ps1` sync davranisini guvenli varsayilana alma (`-SyncFromWindows` ile explicit sync)
- [x] Runtime WSL mount dogrulamasi (`/home/bekir/orkestram/...`)
- [x] `vendor` / `.env` / storage izin eksiklerinden kaynakli runtime 500 toparlama
- [x] `smoke-test` ve `pre-pr` quick PASS kaniti

## Out of Scope
- [x] `stack-hos-*` / diger compose projeleri
- [x] Uretim deploy islemleri

## Lock Dosyalari
- `scripts/dev-up.ps1`
- `docs/tasks/TASK-028.md`
- `docs/TASK_LOCKS.md`
- `docs/NEXT_TASK.md`
- `docs/WORKLOG.md`

## Kabul Kriteri
- [x] Web container mount kaynaklari WSL yolunu gostermeli.
- [x] `powershell -ExecutionPolicy Bypass -File D:\orkestram\scripts\smoke-test.ps1 -App both` => PASS
- [x] `powershell -ExecutionPolicy Bypass -File D:\orkestram\scripts\pre-pr.ps1 -Mode quick` => PASS

## Komutlar
```powershell
powershell -ExecutionPolicy Bypass -File D:\orkestram\scripts\dev-up.ps1 -App both
powershell -ExecutionPolicy Bypass -File D:\orkestram\scripts\smoke-test.ps1 -App both
powershell -ExecutionPolicy Bypass -File D:\orkestram\scripts\pre-pr.ps1 -Mode quick
```

## Notlar
- `git fetch --all --prune` ag erisimi nedeniyle bu turda fail olmustur.
- Teknik kabul kriterleri tamamlandi; resmi kapanis adimlari tamamlandi ve lock `closed` guncellendi.