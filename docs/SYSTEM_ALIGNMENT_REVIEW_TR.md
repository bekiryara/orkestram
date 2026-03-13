# Sistem Uyum ve Risk Degerlendirmesi (TR)

Tarih: 2026-03-09

Kapsam:
1. Plan/dokuman uyumu
2. Deterministik calisma akisi
3. Iki uygulama (orkestram / izmirorkestra) teknik hizasi
4. Refactor kaynakli regresyon riski

## Ozet Sonuc

1. Genel yon dogru ve planlar buyuk oranda sistemle uyumlu.
2. Deterministik akis isliyor; smoke kapisi aktif.
3. Kritik uyum acigi var: iki app tam senkron degil (test katmani).
4. Bu acik kapatilmazsa ileri refactorlarda regresyon riski yuksek.

## Bulgular

## 1) Plan Uyum Durumu

1. `DETERMINISTIC_DELIVERY_FLOW_TR.md` ile sprint sirasi net ve dogru.
2. `LOCAL_FINISH_PLAN_TR.md` ile "icerik/URL en son" stratejisi dogru.
3. `CATEGORY_TAXONOMY_PLAN_TR.md` mevcut ihtiyacla uyumlu (fazli kurulum dogru secim).
4. Tutarlilik acigi:
   - `DETERMINISTIC_DELIVERY_FLOW_TR.md` icinde gecen `MASTER_PLAN_TR.md` dosyasi repo'da yok.

Etkisi:
1. Dokuman zincirinde tek kaynak referansi eksik.

## 2) Deterministik Akis Durumu

Pozitif:
1. `scripts/release.ps1` akisi net: `dev-up -> smoke -> build-pack`.
2. `scripts/smoke-test.ps1` iki app icin public + admin auth kontrolleri yapiyor.
3. `deploy-guard` policy tabanli ve profilli calisiyor.

Risk:
1. `release.ps1` timeout nedeniyle bu turda uctan uca dogrulanamadi.
2. Guard policy'de `xml` extension su an `warn_only`; cPanelde zorunlu olursa surpriz dogabilir.

## 3) Iki App Teknik Hiza Durumu

Pozitif:
1. Route/middleware parity buyuk oranda ayni.
2. Smoke iki appte PASS (8180/8181).

Kritik acik:
1. `izmirorkestra` feature testte kirilim var:
   - `docker exec izmirorkestra-local-web php artisan test --testsuite=Feature`
   - `ExampleTest` FAIL: `no such table: redirects` (500)
2. Dosya farki:
   - `orkestram` app'te `AdminListingMediaFlowTest` ve `RedirectRulesTest` var.
   - `izmirorkestra` app'te bu testler yok.

Etkisi:
1. Iki app "davranis olarak hizali" gorunse de "test guvencesi olarak hizali degil".
2. Refactor sonrasi bir appte bug yakalanip digerinde kacabilir.

## 4) Refactor Riski ve Beklenti Karsilama

Refactor yarari:
1. Controller sadeleme ve servis katmani dogru.
2. QA kapisi guclendirildigi icin geri donus riski azaldi.

Kalan risk:
1. Test parity eksikligi.
2. Localde iki app'in ayni DB adini kullanmasi (`orkestram_local`) veri izolasyonunda yan etki riski dogurabilir.
3. Windows <-> WSL iki kopya ile calisma operasyonel sapma riski tasiyor (senkron unutulursa fark acilir).

## Sonuc (Karar)

1. Beklentileri karsilar mi?
   - Evet, fakat once test-parity acigi kapatilirsa.
2. Deterministik olur mu?
   - Evet, release/smoke kapisi var; ancak iki app test hizasi tamamlanmali.
3. Iki app hizali mi?
   - Kismen. Smoke seviyesinde evet, test seviyesinde hayir.

## Zorunlu Aksiyonlar (Plan Guncelleme)

1. P0: `izmirorkestra` test katmanini `orkestram` ile hizala:
   - `ExampleTest` DB/migration guvencesi
   - `RedirectRulesTest` parity
   - `AdminListingMediaFlowTest` parity
2. P0: Iki app icin ortak test tabani ve parity checklist dokumani.
3. P1: `MASTER_PLAN_TR.md` referansini duzelt (dosyayi ekle veya referansi kaldir).
4. P1: Release scripti timeout oluşturmadan adim adim dry-run ile tekrar dogrula.

## Uygulanan Kalici Duzeltme (2026-03-09)

1. Runtime tekrar WSL ext4'e kilitlendi:
   - mount source: `/home/bekir/orkestram/local-rebuild/apps/...`
2. `scripts/dev-up.ps1` kalici olarak guncellendi:
   - WSL-first compose
   - windows->WSL rsync senkron adimi
   - mount source guard (yanlis kaynakta fail-fast)
3. `scripts/wsl-migrate-project.ps1` ayni standartla hizalandi.
4. Duzeltme sonrasi dogrulama:
   - smoke PASS (iki app)
   - `route:list` sureleri belirgin iyilesme: ~1-2 sn bandi
