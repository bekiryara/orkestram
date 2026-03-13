# NEXT TASK (Tek Aktif Gorev)

Durum: `DONE`  
Sorumlu: `ajan`  
Baslangic: `2026-03-14 01:05`  
Hedef Bitis: `2026-03-14 01:40`

## Gorev Tanimi
- PR akisini standartlastir: PR template + pre-pr gate + dokuman baglantisi.

## Kapsam (In)
- [x] `.github/pull_request_template.md` eklendi
- [x] `scripts/pre-pr.ps1` eklendi
- [x] `docs/PR_FLOW_TR.md` eklendi ve disiplin dokumanina baglandi

## Kapsam Disi (Out)
- [ ] Uygulama UI degisiklikleri
- [ ] Mesaj/yorum business logic degisiklikleri

## Kabul Kriteri
- [x] PR acilisinda zorunlu checklist formati hazir
- [x] PR oncesi tek komut hizli gate calisiyor
- [x] `powershell -ExecutionPolicy Bypass -File D:\orkestram\scripts\pre-pr.ps1 -Mode quick` PASS

## Zorunlu Komutlar
```powershell
powershell -ExecutionPolicy Bypass -File D:\orkestram\scripts\pre-pr.ps1 -Mode quick
```

## Tamamlandiginda Isaretlenecekler
- [x] `docs/WORKLOG.md` kaydi eklendi
- [x] `docs/PROJECT_STATUS_TR.md` guncellendi
- [x] Gerekliyse `docs/ROLLBACK_POINTS.md` kaydi eklendi
