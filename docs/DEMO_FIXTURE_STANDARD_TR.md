# Demo Fixture Standardi (TR)

Tarih: 2026-03-20

Amac:
1. `baseline seed`, `smoke/test fixture` ve `review/demo fixture` katmanlarini karistirmadan tanimlamak.
2. Yeni ajan task aldiginda hangi komutun hangi veri sinifina dokundugunu tek dokumanda gostermek.
3. Shared DB icinde yanlis overwrite, yanlis smoke beklentisi ve preview drift riskini azaltmak.

## 1) Katmanlar

1. `baseline`
   - Kaynak: `DatabaseSeeder` -> `RoleSeeder`, `CategoryTaxonomySeeder`, `InitialContentSeeder`
   - Amac: sistemin temel kategori, icerik ve referans verisiyle ayaga kalkmasi
   - Kural: smoke veya review/demo whitelist listingleri bu katmana baglanmaz
2. `smoke`
   - Kaynak komutlar:
     - `smoke:prepare-range-fixture`
     - `smoke:prepare-bando-fixture`
   - Amac: filtre, listeleme ve kritik akislar icin deterministik test listingleri uretmek
   - Kural: slug seti yalniz smoke whitelist ile sinirlidir
3. `review_demo`
   - Kaynak komut:
     - `demo:prepare-bando-review-fixture`
   - Amac: design-preview ve parity review icin whitelist demo listinglerini tekrar kurulabilir sekilde hazirlamak
   - Kural: smoke sluglari ile karismaz; review odakli whitelist listingleri gunceller

## 2) Whitelist Sozlesmesi

1. `baseline`
   - `beste-muzik`
   - `grup-moda`
   - `gelin-alma-bandosu`
   - `izmir-bandosu`
   - `sunnet-organizasyon-ekibi`
   - `nisan-organizasyon-grubu`
2. `smoke`
   - `smoke-range-ilan-a`
   - `smoke-range-ilan-b`
   - `test-bando-a`
   - `test-bando-b`
3. `review_demo`
   - `demo-bando-sahil-seremonisi`
   - `demo-bando-kordon-alayi`

Kural:
1. Bir katman yalniz kendi whitelist slug setine dokunur.
2. `review_demo` komutu `test-bando-*` veya `smoke-range-*` sluglarini mutate etmez.
3. `smoke` komutlari `demo-bando-*` sluglarini mutate etmez.

## 3) Meta Isaretleme

Kural:
1. Fixture komutlari yazdiklari listinglerde `meta_json.fixture_layer` alanini doldurur.
2. Beklenen degerler:
   - `smoke`
   - `review_demo`
3. Bu isaret yeni ajan icin listing kaynaginin hizli kanitidir.

## 4) Medya Kaynagi Kurali

1. `review_demo` listingleri repo ici `storage/uploads/listings/<slug>/...` pathleri kullanir.
2. Harici masaustu klasoru, manuel kopya veya kullanici makinesine bagimli path kullanilmaz.
3. Medya dosyasi eksikse smoke bug'i gibi yorumlanmaz; eksik repo fixture olarak ayrik kayda alinir.

## 5) Operasyon Kurali

1. Yeni ajan review/demo gorunumu icin once `demo:prepare-bando-review-fixture` calistirir.
2. Smoke kapisi veya filtre dogrulamasi icin yalniz `smoke:*` komutlari kullanilir.
3. `migrate:fresh --force` ortak DB'de koordinatorsuz kullanilmaz.
4. Shared DB sarsildiysa geri donus sirasi:
   - baseline seed
   - gerekiyorsa `locations:import`
   - smoke fixture
   - review demo fixture

## 6) Komut Ozetleri

```powershell
# smoke katmani
php artisan smoke:prepare-range-fixture --site=orkestram.net
php artisan smoke:prepare-bando-fixture --site=orkestram.net

# review demo katmani
php artisan demo:prepare-bando-review-fixture --site=orkestram.net
php artisan demo:prepare-bando-review-fixture --site=izmirorkestra.net
```

## 7) Kanit Beklentisi

1. Task tesliminde ilgili komut ciktilari yazilir.
2. Listing kanitinda en az su alanlar gorulur:
   - `site`
   - `slug`
   - `cover_image_path`
   - `meta_json.fixture_layer`
3. `pre-pr PASS` olmadan fixture standardi degisimi kapatilmaz.
