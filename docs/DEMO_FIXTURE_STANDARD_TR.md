# Demo Fixture Standardi (TR)

## Amac
1. `design-preview` review hattinda kullanilan demo verinin her calistirmada ayni sonucu vermesini saglamak.
2. Demo fixture'lari mevcut smoke/test verisinden kesin olarak ayirmak.
3. Demo verinin mevcut seed zincirini, kullanici verisini veya test fixture'larini kirletmesini engellemek.

## Kapsam
1. Bu standart yalniz review/demo verisini kapsar.
2. Production seed, smoke fixture, test factory ve kullanici verisi bu standardin disindadir.
3. Demo fixture hatti ayriktir; genel `DatabaseSeeder` veya `InitialContentSeeder` zincirine eklenmez.
4. Hedef lane `design-preview` olsa da kural seti UI'dan bagimsizdir; review icin gereken demo veri kontratini tarif eder.

## Whitelist Slug
1. Demo kayitlari yalniz acik bir whitelist slug listesi ile yonetilir.
2. Esleme anahtari dar kapsamda `site + slug` olur.
3. Whitelist disindaki hicbir listing, profil veya medya kaydina dokunulmaz.
4. Demo slug degisikligi gerekiyorsa mevcut slug mutate edilmez; yeni whitelist karari acilir.
5. Wildcard veya toplu hedefleme kullanilmaz.

## Medya Kaynagi
1. Demo medya kaynagi repo icinde sabit, versiyonlanan ve deterministic bir klasorden gelmelidir.
2. Medya kaynagi masaustu dosyalari, gecici download'lar, manuel kopyalama gecmisi veya belirsiz container dosyalarina dayanmaz.
3. Kapak, galeri ve profil medyasi icin kaynak dosya listesi sabit tutulur.
4. Medya eslemesi slug bazli yapilir; calisma sirasina veya rasgele secime baglanmaz.
5. Medya bulunamazsa davranis acik secilir: fail etmek veya onceden tanimli placeholder kullanmak. Sessiz fallback yasaktir.
6. `Repo ici medya kaynagi` kurali, fixture komutunun yalniz repoda izlenen dosyalardan okuma yapmasi demektir; `storage/app/public` icindeki elde kalma dosyalar source-of-truth sayilmaz.

## Idempotent Update
1. Demo fixture tekrar calistiginda ayni whitelist kaydini bulur ve yalniz o kaydi gunceller.
2. Guncelleme dar kosullu olur; `site + slug` disinda toplu tarama yapilmaz.
3. Whitelist kaydi yoksa olusturulur; varsa ayni demo kimligi korunarak guncellenir.
4. Whitelist disi kayitlar okunmaz, tasinmaz, rename edilmez, overwrite edilmez.
5. Ayni girdilerle ayni cikti uretilmesi zorunludur.
6. Idempotent davranis, ikinci calistirmada duplicate satir, ek medya kopyasi veya slug drift uretmemelidir.

## Smoke/Test Ayrimi
1. Smoke ve test fixture'lari kendi seed ve assertion ihtiyaclari ile ayrik kalir.
2. Demo fixture smoke testlerini yesile boyamak icin mevcut test verisini degistirmez.
3. Test slug'lari ile review/demo slug'lari ayni havuzda tutulmaz.
4. Review demo verisi insan incelemesi icindir; otomatik testin tek gercek veri kaynagi haline getirilmez.
5. Smoke/test tarafinda deterministic veri ihtiyaci olsa bile ayni slug whitelist'i paylasilmaz; review lane ile test lane veri kontratlari ayri tutulur.

## Yasaklar
1. `DatabaseSeeder` veya `InitialContentSeeder` icine dogrudan demo review verisi eklemek yasaktir.
2. Whitelist disi mevcut slug'lari mutate etmek yasaktir.
3. Smoke/test fixture'larini demo review ihtiyaci icin degistirmek yasaktir.
4. Repo disi veya gecici medya kaynagiga baglanmak yasaktir.
5. Sessiz fallback veya toplu overwrite davranisi yasaktir.
