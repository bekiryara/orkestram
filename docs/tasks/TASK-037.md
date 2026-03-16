# TASK-037

Durum: `DONE`  
Ajan: `codex-a`  
Branch: `agent/codex-a/task-037`  
Baslangic: `2026-03-16 11:00`
Kapanis: `2026-03-16 12:16`

## Ozet
- Ana sayfa hero bolumundeki CTA karmasasi temizlendi ve public aksiyonlar sade hiyerarsiye cekildi.

## In Scope
- [x] Hero bolumunde tek `primary` + tek `secondary` CTA duzeni.
- [x] Public alanda yonetim tipi butonlari link/hidden duzenine cekmek.
- [x] Iki appte parity (orkestram + izmirorkestra).

## Out of Scope
- [ ] CSS tasarim sistemi refactoru
- [ ] Ilan detay sayfasi duzeni

## Lock Dosyalari
- `local-rebuild/apps/orkestram/resources/views/frontend/home.blade.php`
- `local-rebuild/apps/izmirorkestra/resources/views/frontend/home.blade.php`
- `docs/tasks/TASK-037.md`

## Kabul Kriteri
- [x] Hero CTA hiyerarsisi net (1 primary, 1 secondary).
- [x] Buton metinleri ve sirasi iki appte ayni.
- [x] `pre-pr` PASS.

## Komutlar
```powershell
powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick
```

## Notlar
- Ajan teslim commiti: `4099e57`.
