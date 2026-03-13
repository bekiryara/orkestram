# Deterministik Gelistirme Akisi (TR)

Tarih: 2026-03-09

Amac:
1. Her adimda olculebilir ilerleme.
2. Teknik borc birikmeden sprint kapatma.
3. Belge duzeni bozulmadan tutarli teslim.

## Sprint Sirasi (Kilitli)

1. Sprint 1: Listing Domain Refactor (Tamam)
2. Sprint 2: Listing Detay Final Polish (Tamam)
3. Sprint 3: Izmir Theme Split (Tamam)
4. Sprint 4: QA Kapisi + release sertlestirme
5. Sprint 5: Gercek 5-6 ilan + son asama sayfa/URL operasyonu

## Her Sprintte Zorunlu Adim

1. Plan guncelle:
   - `LOCAL_FINISH_PLAN_TR.md`
   - ilgili sprint plani
2. Kod uygula (kucuk ve atomik adimlar).
3. Dogrula:
   - feature/smoke test
   - temel endpoint kontrolu
4. Durum guncelle:
   - `PROJECT_STATUS_TR.md`
5. Sonraki adimi netlestir.

## Belge Duzeni Kurali

1. Planlar:
   - `MASTER_PLAN_TR.md`
   - `LOCAL_FINISH_PLAN_TR.md`
   - `CLEAN_ARCHITECTURE_SPRINT_PLAN_TR.md`
   - `DETERMINISTIC_DELIVERY_FLOW_TR.md`
2. Durum:
   - `PROJECT_STATUS_TR.md`
3. Operasyon:
   - `RELEASE_PIPELINE_TR.md`
   - `CPANEL_DEPLOY_PLAYBOOK_TR.md`
4. Disiplin:
   - `REPO_DISCIPLINE_TR.md`

## Deploy Kilidi

1. Kullanici acik onayi olmadan deploy yok.
2. Son smoke PASS olmadan deploy yok.
3. Dokuman guncel degilse sprint kapanmaz.
