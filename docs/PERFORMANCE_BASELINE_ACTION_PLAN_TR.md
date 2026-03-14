# Performance Baseline Aksiyon Plani (TR)

Tarih: 2026-03-13  
Referans: `docs/PERFORMANCE_BASELINE_REPORT_TR.md` (TASK-004)

## Amac
1. Baseline raporunu "olcum" seviyesinden "karar" seviyesine cikarmak.
2. Her hafta ayni metodla olcum alip drift (gerileme/iyilesme) takibi yapmak.
3. Release oncesi performans riskini erken yakalamak.

## Kapsam
1. Endpoint bazli hedef esikler (cold/warm).
2. PASS/WARN/FAIL karar kurali.
3. Haftalik olcum rutini ve rapor guncelleme formati.

## Endpoint Esik Tablosu
Not: Esikler bu repodaki mevcut baseline'a gore pragmatik tanimlandi.

| Endpoint | Cold Hedef (ms) | Warm Hedef (ms) | Not |
|---|---:|---:|---|
| `/` | <= 500 | <= 150 | Ana landing, en kritik izleme |
| `/ilanlar` | <= 300 | <= 180 | Liste + filtre etkisine acik |
| `/robots.txt` | <= 100 | <= 100 | Statik ciktida stabil beklenti |
| `/sitemap.xml` | <= 300 | <= 260 | XML olusturma maliyeti degisken |

## Karar Kurali (PASS/WARN/FAIL)
1. PASS:
   - Tum endpointler 2xx/3xx.
   - Tum cold/warm degerleri hedef esiklerin icinde.
2. WARN:
   - Status basarili, ancak en az 1 metrik hedefi %10'a kadar asiyor.
   - Ornek: warm hedef 180ms, olcum 181-198ms.
3. FAIL:
   - Herhangi bir endpoint 4xx/5xx (beklenen 404 haric).
   - Herhangi bir metrik hedefin %10'dan fazla ustunde.
   - Ornek: warm hedef 180ms, olcum >= 199ms.

## Drift Yorumu
1. Ust uste 2 hafta WARN gelen ayni endpoint:
   - Teknik borc/backlog'a "P1 performans" olarak acilir.
2. Tek olcumde FAIL:
   - Release oncesi zorunlu inceleme.
   - Koken sebep notu olmadan gorev kapatilmaz.
3. Iyilesme:
   - 3 hafta ust uste PASS gelen endpointte esik daraltma degerlendirilir.

## Olcum Rutini (Haftalik)
1. Senkron:
   - `git fetch --all --prune`
2. Stack hazir:
   - `powershell -ExecutionPolicy Bypass -File D:\orkestram\scripts\dev-up.ps1 -App both`
3. Olcum:
   - `powershell -ExecutionPolicy Bypass -File D:\orkestram\scripts\perf-baseline.ps1 -App both -WarmRuns 3 -OutFile D:\orkestram\docs\PERFORMANCE_BASELINE_REPORT_TR.md`
4. Disiplin kapisi:
   - `powershell -ExecutionPolicy Bypass -File D:\orkestram\scripts\pre-pr.ps1 -Mode quick`

## Rapor Guncelleme Formati
1. `PERFORMANCE_BASELINE_REPORT_TR.md` dosyasina son olcum yazilir.
2. `WORKLOG.md` icine tek satir performans ozeti eklenir:
   - `cold/warm kritik endpoint`, `PASS/WARN/FAIL`.
3. FAIL veya tekrar eden WARN varsa `NEXT_TASK.md`e net aksiyon yazilir.

## Bu Turdan Sonraki Somut Is (TASK-005 cikisi)
1. Bu aksiyon plani kabul edildikten sonra `perf-baseline.ps1` icine opsiyonel threshold kontrol ciktilari eklenebilir (ayri task).
2. Haftalik rapor takibi icin sorumlu ajan/rol netlestirilir.
