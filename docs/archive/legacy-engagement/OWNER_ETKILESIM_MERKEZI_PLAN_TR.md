# DEPRECATED

Bu dokuman legacy engagement modeline aittir. Aktif mesaj sistemi icin MESAJ_V1_TR.md kullanin.

# Owner Etkilesim Merkezi Uygulama Plani (TR)

Tarih: 2026-03-11  
Durum: Tamamlandi (iki app parity)

## Hedef
1. Owner kullanicinin hesap ekraninda yasadigi panel dongusunu bitirmek.
2. Owner is akislarini tek bir "Etkilesim Merkezi" uzerinden minimum surtunmeyle yonetmek.
3. Musteri deneyimi ve owner deneyimini birbirinden net ayirmak.
4. Bootstrap 5.3 ve mevcut gorunur dil disina cikmadan sade bir akis sunmak.

## Kapsam
1. Hesabim ekrani sadeleme (owner icin sadece Profil + Guvenlik).
2. Header navigasyonda role gore "Hesabim" hedefini ayirma (owner -> `/owner`).
3. `/panel` yonlendirmesinde owner ana akisini `/owner` yapma.
4. `/owner/engagements` sayfasini "Etkilesim Merkezi" modeline gecirme:
   - Ustte: ilana gelen etkilesimler (gelen kutusu)
   - Altta: owner kullanicinin kendi gonderdigi etkilesimler (giden kutusu)
   - Satirdan "Detay" popup + hizli yanit/guncelleme
5. Blade ve test parity (iki app ayni dosya seti).

## Uygulama Adimlari
1. Role-aware menu altyapisi:
   - `config/navigation.php` icine rol bazli hedef map eklenir.
   - `NavigationFactory` bunu kullanarak `href` degerini role gore cozer.
2. Auth panel yonlendirme:
   - `PortalAuthController::panel()` sabit `/hesabim?tab=overview` yerine `homeByRole()` ile role bazli yonlendirir.
3. Hesabim sadeleme:
   - `portal/account.blade.php` owner rolunde sadece `profile` ve `security` tablarini gosterir.
   - Owner icin varsayilan tab `profile` olur.
4. Owner etkileşim merkezi:
   - `Owner\EngagementController@index` icinde iki dataset uret:
     - `incomingRows`: own listing feedback
     - `outgoingRows`: owner kullanicinin gonderdigi feedback
   - `portal/owner/engagements.blade.php`:
     - Ust tablo: gelen kayitlar + hizli guncelleme
     - Alt tablo: giden kayitlar (sadece okuma)
     - Satir bazli Bootstrap modal: icerik tam metin ve yanit alani
5. Test guncelleme:
   - `PortalSessionAuthTest`: owner `/panel` beklentileri `/owner` olarak guncellenir.
   - Owner hesabim sadeleme ve owner etkileşim merkezi metin kontrolleri eklenir.

## Kabul Kriterleri
1. Owner login sonrasi `/panel` -> `/owner`.
2. Owner Hesabim ekraninda operasyon tablari yoktur, sadece Profil + Guvenlik vardir.
3. Owner etkileşim ekraninda gelen ve giden akislari ayri gorur.
4. Gelen kayitlarda popup ile detay + hizli yanit islemi yapabilir.
5. Tum ilgili feature testleri PASS olur.

## Kapanis Kaniti
1. `PortalSessionAuthTest` PASS:
   - `orkestram-local-web`
   - `izmirorkestra-local-web`
2. `ListingEngagementFlowTest` PASS:
   - `orkestram-local-web`
   - `izmirorkestra-local-web`

