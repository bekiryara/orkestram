# NEXT TASK (Tek Aktif Gorev)

Durum: `DONE`  
Sorumlu: `ajan`  
Baslangic: `2026-03-13 23:59`  
Hedef Bitis: `2026-03-14 02:00`

## Gorev Tanimi
- `validate` kapisini release akisina bagla; test/smoke PASS olmadan yayin adimi calismasin.

## Kapsam (In)
- [x] `scripts/release.ps1` icinde `validate.ps1` zorunlu pre-check adimi eklendi
- [x] `scripts/build-deploy-pack.ps1` icinde ayni gate kurali uygulandi
- [x] FAIL durumunda net hata mesaji ile akisin durmasi saglandi

## Kapsam Disi (Out)
- [ ] Uygulama UI degisiklikleri
- [ ] Mesaj/yorum business logic degisiklikleri

## Kabul Kriteri
- [x] `validate` FAIL iken release/build akisi duracak sekilde gate baglandi
- [x] `validate` PASS iken release akisi devam edebilecek sekilde baglandi
- [x] `powershell -ExecutionPolicy Bypass -File D:\orkestram\scripts\validate.ps1 -App both` PASS

## Zorunlu Komutlar
```powershell
powershell -ExecutionPolicy Bypass -File D:\orkestram\scripts\validate.ps1 -App both
```

## Tamamlandiginda Isaretlenecekler
- [x] `docs/WORKLOG.md` kaydi eklendi
- [x] `docs/PROJECT_STATUS_TR.md` guncellendi
- [x] Gerekliyse `docs/ROLLBACK_POINTS.md` kaydi eklendi
