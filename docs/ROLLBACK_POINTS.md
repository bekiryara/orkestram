# ROLLBACK POINTS

Amaç: Riskli degisimlerden once hizli geri donus noktasi birakmak.

## Kayit Formati

### [YYYY-MM-DD HH:mm] Etiket
- Kapsam:
  - Degisim alani 1
  - Degisim alani 2
- Dogrulama:
  - O an PASS olan test/smoke
- Geri Donus:
  - Geri alinacak dosya listesi
  - Geri alma komut/plani

---

### [2026-03-13 23:59] feedback-plan-locked
- Kapsam:
  - Tek omurga mesaj sistemi
  - Yorum/begeni akisi ve admin geri bildirim paneli
  - Dead code cleanup v2
- Dogrulama:
  - `FeedbackModerationAccessTest` PASS (iki app)
  - `ListingFeedbackFlowTest` PASS (iki app)
  - `smoke-test.ps1 -App both` PASS
- Geri Donus:
  - `docs/WORKLOG.md` kayitlarindaki dosya listelerine gore parca geri alim
  - Tumden geri alim yerine konu bazli rollback uygulanir

### [2026-03-14 00:25] release-validate-gate-v1
- Kapsam:
  - `release.ps1` ve `build-deploy-pack.ps1` zorunlu validate gate entegrasyonu
- Dogrulama:
  - `powershell -ExecutionPolicy Bypass -File D:\orkestram\scripts\validate.ps1 -App both` PASS
- Geri Donus:
  - Sadece gate baglantisi geri alinacaksa:
    - `scripts/release.ps1`
    - `scripts/build-deploy-pack.ps1`
