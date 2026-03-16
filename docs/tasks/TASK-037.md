# TASK-037

Durum: `TODO`  
Ajan: `codex-a`  
Branch: `agent/codex-a/task-037`  
Baslangic: `2026-03-16 11:00`

## Ozet
- Ana sayfa hero bolumundeki CTA karmaşasını temizlemek ve public aksiyonlari tek hiyerarside toplamak.

## In Scope
- [ ] Hero bolumunde tek `primary` + tek `secondary` CTA duzeni.
- [ ] Public alanda yonetim tipi butonlari link/hidden duzenine cekmek.
- [ ] Iki appte parity (orkestram + izmirorkestra).

## Out of Scope
- [ ] CSS tasarim sistemi refactoru
- [ ] Ilan detay sayfasi duzeni

## Lock Dosyalari
- `local-rebuild/apps/orkestram/resources/views/frontend/home.blade.php`
- `local-rebuild/apps/izmirorkestra/resources/views/frontend/home.blade.php`
- `docs/tasks/TASK-037.md`

## Kabul Kriteri
- [ ] Hero CTA hiyerarsisi net (1 primary, 1 secondary).
- [ ] Buton metinleri ve sirasi iki appte ayni.
- [ ] `pre-pr` PASS.

## Komutlar
```powershell
powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick
```
