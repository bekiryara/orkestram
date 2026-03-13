# TASK-017

Durum: `DOING`  
Ajan: `codex-b`  
Branch: `agent/codex-b/task-017`  
Baslangic: `2026-03-13 13:20`

## Ozet
- Iki appte frontend parity ve UX standardizasyonunu finalize et.

## In Scope
- [ ] Home/listing/detail sayfalarinda ayni spacing, tipografi ve kart hiyerarsisi.
- [ ] `v1.css` tokenlari parity: ana renk/logo rengi tum shell buton/badge elemanlarinda tutarli.
- [ ] Mobil kirilimlarda tasma/ust uste binme sifir.
- [ ] `docs/UX_STANDARD_DUGUN_PARITY_TR.md` final checklist guncelle.
- [ ] `pre-pr -Mode quick` PASS.

## Out of Scope
- [ ] Controller/model/business logic degisikligi.
- [ ] Veritabani migration.

## Lock Dosyalari
- `docs/tasks/TASK-017.md`
- `local-rebuild/apps/orkestram/resources/views/frontend/**`
- `local-rebuild/apps/izmirorkestra/resources/views/frontend/**`
- `local-rebuild/apps/orkestram/public/assets/v1.css`
- `local-rebuild/apps/izmirorkestra/public/assets/v1.css`
- `docs/UX_STANDARD_DUGUN_PARITY_TR.md`

## Kabul Kriteri
- [ ] Iki app ayni UI iskeletiyle calisir (parity kaniti).
- [ ] Kritik viewportlarda tasma yok (mobile/tablet/desktop).
- [ ] `pre-pr` PASS.
