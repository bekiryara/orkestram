# Rol Sistemi Faz 2 Uygulama Plani (Repo Disiplini)

Tarih: 2026-03-10

## Kapsam
1. Teklif formu simdilik basit kalacak (customer request create akisi korunur).
2. Hesabim modulu profesyonel hale getirilecek.
3. Owner ve support panellerine gercek aksiyonlar eklenecek.
4. Kod tekrari azaltilacak, yetki kararinda if/else dagilimi engellenecek.
5. Iki app parity zorunlu.

## Hedef Cikti
1. `Hesabim`:
   - profil guncelleme (ad, username, email)
   - sifre degistirme (mevcut sifre dogrulama + confirm)
2. `Owner`:
   - kendi ilaninin alanlarini guncelleme
   - kendi ilan statusunu degistirme
   - kendi lead kaydinda status/not guncelleme
3. `Support`:
   - talep listesinde status/not guncelleme
4. `Policy/Ownership`:
   - owner sadece kendi kaydina etki eder (listing/request)
5. `UI`:
   - mobil uyumlu aksiyon butonlari, bos durum ve geri bildirim metinleri

## Teknik Prensip
1. Kural tek kaynak:
   - `config/admin_acl.php`
   - `config/navigation.php`
2. Ownership kontrolu controller icinde dagitilmaz:
   - ortak servis/guard katmani kullanilir.
3. Site tespiti tek fonksiyondan okunur (yinelenen host if bloklari azaltilir).
4. Her yeni aksiyon icin en az bir feature test eklenir.

## Uygulama Fazlari

### Faz A - Yetki Token Genisletme
1. `owner.leads.manage`
2. `support.requests.manage`
3. route korumalari ability middleware ile netlenir.

Kabul:
1. Yetkisiz rol `403` alir.

### Faz B - Hesabim Aksiyonlari
1. `/hesabim/profil` POST
2. `/hesabim/sifre` POST
3. DB user id olmayan hesaplarda (json/legacy) profil aksiyonu kapali mesaji.

Kabul:
1. Profil update ve sifre degistirme testle PASS.

### Faz C - Owner Aksiyonlari
1. Owner listing edit/update endpointleri.
2. Owner listing status update endpointi.
3. Owner lead status/not update endpointi.

Kabul:
1. Owner sadece kendi kaydini degistirebilir.
2. Diger owner kaydina erisim `403`.

### Faz D - Support Aksiyonlari
1. Support request status/not update endpointi.
2. Liste ekraninda filtre + hizli update.

Kabul:
1. Support rolunde update calisir, customer rolunde engellenir.

### Faz E - Parity ve Test Kapisi
1. Iki appte ayni patch set.
2. Yeni testler iki appte ayni sayida PASS.

Kabul:
1. Faz kapatma metrikleri:
   - role/auth paket testleri PASS
   - yeni owner/support/account testleri PASS

## Kapanis Notu
Bu faz bitince teklif formu basit kalmaya devam eder; bir sonraki fazda teklif akisi zenginlestirilir.

## Uygulama Durumu (2026-03-10)
1. Faz A/B/C/D/E tamamlandi.
2. Iki app parity dogrulandi (`orkestram` + `izmirorkestra`).
3. Dogrulama adimlari:
   - `php artisan about`
   - `php artisan migrate --force`
   - `php artisan test --testsuite=Feature --filter='AccountManagementTest|OwnerPanelActionsTest|SupportRequestManageTest'`
4. Sonuc: iki appte PASS.
