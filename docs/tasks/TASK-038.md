# TASK-038

Durum: `TODO`  
Ajan: `codex-b`  
Branch: `agent/codex-b/task-038`  
Baslangic: `2026-03-16 11:00`

## Ozet
- Buton/CTA gorunum sistemini profesyonel ve tutarli bir tasarim standardina cekmek.

## In Scope
- [ ] `primary / secondary / ghost` buton siniflarini netlestirmek.
- [ ] Boyut, padding, border, hover, focus state standardini uygulamak.
- [ ] Mobilde stack, desktopta kontrollu yatay duzen.

## Out of Scope
- [ ] Blade icerik metinlerinin degistirilmesi
- [ ] Ilan detay form akisi

## Lock Dosyalari
- `local-rebuild/apps/orkestram/public/assets/v1.css`
- `local-rebuild/apps/izmirorkestra/public/assets/v1.css`
- `docs/tasks/TASK-038.md`

## Kabul Kriteri
- [ ] Hero ve kart CTA gorunumu tek tasarim dilinde.
- [ ] Fokus/hover durumlari erisilebilir.
- [ ] `pre-pr` PASS.

## Komutlar
```powershell
powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick
```
