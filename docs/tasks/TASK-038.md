# TASK-038

Durum: `DONE`  
Ajan: `codex-b`  
Branch: `agent/codex-b/task-038`  
Baslangic: `2026-03-16 11:00`
Kapanis: `2026-03-16 12:16`

## Ozet
- Buton/CTA gorunum sistemi profesyonel ve tutarli bir standarda cekildi.

## In Scope
- [x] `primary / secondary / ghost` buton siniflarini netlestirmek.
- [x] Boyut, padding, border, hover, focus state standardini uygulamak.
- [x] Mobilde stack, desktopta kontrollu yatay duzen.

## Out of Scope
- [ ] Blade icerik metinlerinin degistirilmesi
- [ ] Ilan detay form akisi

## Lock Dosyalari
- `local-rebuild/apps/orkestram/public/assets/v1.css`
- `local-rebuild/apps/izmirorkestra/public/assets/v1.css`
- `docs/tasks/TASK-038.md`

## Kabul Kriteri
- [x] Hero ve kart CTA gorunumu tek tasarim dilinde.
- [x] Fokus/hover durumlari erisilebilir.
- [x] `pre-pr` PASS.

## Komutlar
```powershell
powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick
```

## Notlar
- `origin/agent/codex-b/task-038` branch'inde kod commit farki olmadigi icin kapsam `task-040` uzerinden koordinator tarafinda tamamlandi.
