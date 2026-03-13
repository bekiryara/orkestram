# Service Area Fallback Daraltma Plani (Operasyonel Checklist)

Tarih: 2026-03-13  
Durum: Uygulama Checklist'i

## Amac
- `listing_service_areas` akisinda string fallback bagimliligini kontrollu azaltmak.
- Hedef durum: yazma/okuma katmaninda `city_id` + `district_id` zorunlu ve deterministic model.

## Baslangic Kosullari (Preflight)
- [ ] Mevcut fallback hit metrikleri son 7 gun icin alinmis.
- [ ] ID eksik kayit raporu son haliyle uretilmis.
- [ ] Rollout/feature flag geri alma adimi net.
- [ ] Izleme panelinde su metrikler gorunur:
  - `fallback_hit_count`
  - `id_missing_write_attempt_count`
  - `coverage_mismatch_count`

## Faz A - Envanter ve Raporlama (Read-only)

### Adim A1 - Uyum raporunu uret
- Islem: ID eksik satirlari raporla (`app, listing_id, city, district, city_id, district_id, reason`).
- Risk: Eksik/yanlis rapor ile sonraki fazlarin yanlis karar vermesi.
- Rollback: Faz A disina cikma, rapor scriptini duzelt ve yeniden calistir.
- Dogrulama:
  - [ ] Rapor satir sayisi onceki baseline ile uyumlu.
  - [ ] `reason` dagilimi (null/format/eslesme yok) yuzde olarak cikarildi.

### Adim A2 - Kaynak neden siniflandirma
- Islem: ID eksikliginin kaynagini (yeni yazma, legacy veri, esleme hatasi) siniflandir.
- Risk: Kaynak neden yanlis etiketlenirse Faz B onlemleri etkisiz kalir.
- Rollback: Siniflandirmayi yeniden hesapla; Faz B gecisini beklet.
- Dogrulama:
  - [ ] Her kategori icin adet ve oran var.
  - [ ] "Aksiyon sahibi" (uygulama/veri/operasyon) atanmis.

### Faz A Gate
- [ ] Rapor PASS olmadan Faz B'ye gecis yok.
- [ ] `coverage_mismatch_count` artiyor ise Faz B ertelenir.

## Faz B - Uyarili Mod (Soft Enforcement)

### Adim B1 - Yazma tarafinda ID zorunlu warning/ret
- Islem: create/update akisinda ID yoksa logla; rollout flag'e gore kaydetmeyi engelle.
- Risk: Beklenmeyen is akisi kesintisi.
- Rollback: Flag'i "uyari modu"na al, reddi gecici kapat.
- Dogrulama:
  - [ ] `id_missing_write_attempt_count` metrikleniyor.
  - [ ] Uyari loglari beklenen endpointlerle sinirli.

### Adim B2 - Okuma tarafinda fallback izleme
- Islem: read akisinda fallback hit sayacini zorunlu izle.
- Risk: Fallback hit gorunurlugu olmazsa Faz C erken acilabilir.
- Rollback: Eski izleme/telemetri yoluna don.
- Dogrulama:
  - [ ] `fallback_hit_count` gunluk trend cizgisi mevcut.
  - [ ] Son 3 olcum periyodunda azalan trend var.

### Faz B Gate
- [ ] `fallback_hit_count` hedef esigin altina inmeden Faz C yok.
- [ ] Kritik endpointlerde coverage sapmasi yok.

## Faz C - Tam Zorunlu ID (Hard Enforcement)

### Adim C1 - String fallback'i kapat
- Islem: coverage ve filtre sorgularinda string fallback yolunu devre disi birak.
- Risk: Legacy kayitlar nedeniyle sonuc bosalmasi.
- Rollback: Faz bayragini B'ye al (C -> B), fallback'i kontrollu geri ac.
- Dogrulama:
  - [ ] Kritik smoke test PASS.
  - [ ] Hedef regression test PASS.
  - [ ] `coverage_mismatch_count` baseline ustune cikmadi.

### Adim C2 - Operasyonel izleme (24-48 saat)
- Islem: deploy sonrasi metrik ve hata oranlarini yakin izle.
- Risk: Gecikmeli veri anomalisi.
- Rollback: Faz B'ye don ve etkilenen listingleri backfill listesine al.
- Dogrulama:
  - [ ] `fallback_hit_count = 0` (hedef izleme penceresi boyunca).
  - [ ] 404/sonuc bosalma anomalisinde artis yok.

## Karar Kurallari (PASS/WARN/FAIL)
- PASS: `fallback_hit_count = 0` ve `coverage_mismatch_count` baseline icinde.
- WARN: fallback hit > 0 ama azalan trend; Faz C gecisi ertelenir.
- FAIL: fallback hit artisi veya coverage mismatch artisi; rollback tetiklenir.

## Rollback Runbook (Sirali)
1. Faz bayragini bir onceki guvenli moda cek (`C -> B`, gerekirse `B -> A`).
2. Son envanter raporunu tekrar uret; etkilenen listingleri isaretle.
3. Acil backfill listesiyle veri duzeltmesini calistir.
4. Kritik smoke/regression testlerini tekrar calistir.
5. PASS olmadan yeniden ileri faza gecme.

## Cikis Kriteri
- [ ] Faz A/B/C adimlari, risk ve rollback notlari tamam.
- [ ] Metrikler panelde gorunur ve karar kurallari uygulanir.
- [ ] Rollback runbook'u operasyon tarafinca uygulanabilir.
