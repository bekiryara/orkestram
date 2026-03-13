# TASK-008

Durum: `DONE`  
Ajan: `codex-a`  
Branch: `agent/codex-a/task-008`  
Baslangic: `2026-03-13 23:58`

## Ozet
- Koordinator talebiyle yeni gorev acildi. Kapsam netlestiginde bu dosya guncellenecek.

## In Scope
- [x] Gorev kapsamini netlestir.
- [x] Lock dosyalariyla uyumlu ilerle.
- [x] Gorev sonunda `pre-pr` PASS al.

## Out of Scope
- [ ] Lock disi dosyalara mudahale.
- [ ] Diger ajan task dosyalarina degisiklik.

## Lock Dosyalari
- `docs/tasks/TASK-008.md`

## Kabul Kriteri
- [x] Kapsam net ve yazili.
- [x] Kapanis kanitlari hazir (`branch/status/pre-pr`).

## Komutlar
```powershell
powershell -ExecutionPolicy Bypass -File D:\orkestram\scripts\pre-pr.ps1 -Mode quick
```

## Notlar
- TASK-003 lock'u kapanip TASK-008 lock'u acildi.
- Kapanis kaniti:
  - `pre-pr -Mode quick` => PASS
