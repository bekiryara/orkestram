# NEXT TASK (Tek Aktif Gorev)

Durum: `DONE`  
Sorumlu: `ajan`  
Baslangic: `2026-03-14 01:45`  
Hedef Bitis: `2026-03-14 02:20`

## Gorev Tanimi
- `ci-gate` yanina ikinci zorunlu kontrolu ekle: `security-gate`.

## Kapsam (In)
- [x] `scripts/security-gate.ps1` eklendi
- [x] `.github/workflows/security-gate.yml` eklendi
- [x] `scripts/pre-pr.ps1` icine security adimi baglandi

## Kapsam Disi (Out)
- [ ] Uygulama UI degisiklikleri
- [ ] Mesaj/yorum business logic degisikligi

## Kabul Kriteri
- [x] Push/PR tarafinda ikinci check workflow'u hazir
- [x] Local security taramasi PASS
- [x] `powershell -ExecutionPolicy Bypass -File D:\orkestram\scripts\pre-pr.ps1 -Mode quick` PASS

## Zorunlu Komutlar
```powershell
powershell -ExecutionPolicy Bypass -File D:\orkestram\scripts\security-gate.ps1
```

## Tamamlandiginda Isaretlenecekler
- [x] `docs/WORKLOG.md` kaydi eklendi
- [x] `docs/PROJECT_STATUS_TR.md` guncellendi
