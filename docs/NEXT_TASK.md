# NEXT TASK (Tek Aktif Gorev)

Durum: `DONE`  
Sorumlu: `ajan`  
Baslangic: `2026-03-13 08:05`  
Hedef Bitis: `2026-03-13 07:43`

## Gorev Tanimi
- Portal label standardini uygulama katmaninda merkezilestir ve dokumanla kilitle.

## Kapsam (In)
- [x] `UI_LABEL_SOZLUGU_TR.md` ile kod etiketlerinin birebir uyumunu dogrula
- [x] `portal.*` label anahtarlarini tek kaynakta tut
- [x] parity kontrolunu (`orkestram` + `izmirorkestra`) tamamla

## Kapsam Disi (Out)
- [x] Frontend tema/icerik degisiklikleri (mevcut farklara dokunma)
- [x] Business logic degisikligi

## Kabul Kriteri
- [x] `/hesabim` ve `/owner` label seti sozlukle uyumlu
- [x] `Sorular/Sorularim` aktif menu setinde gecmiyor
- [x] iki app parity hash kontrolu PASS

## Zorunlu Komutlar
```powershell
Get-Content D:\orkestram\docs\UI_LABEL_SOZLUGU_TR.md
```

## Tamamlandiginda Isaretlenecekler
- [x] `docs/WORKLOG.md` kaydi eklendi
- [x] `docs/PROJECT_STATUS_TR.md` guncellendi
