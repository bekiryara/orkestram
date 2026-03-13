# Rol Sistemi Deterministik Uygulama Plani (TR)

Tarih: 2026-03-10

Amac:
1. Admin rolleri + muster/ilan sahibi rollerini tek bir yetki omurgasinda toplamak.
2. Route/ekran yetkilerini `if/else` dagitmadan merkezi ve testlenebilir hale getirmek.
3. Iki app (`orkestram` / `izmirorkestra`) parity'yi bozmadan ilerlemek.

## 0) Durum Ozeti (2026-03-10)

Tamamlananlar:
1. Faz A tamam: `ability` middleware + merkezi rol matrisi aktif.
2. Faz B tamam: DB role modeli (`roles`, `role_user`, `users.username`, `users.is_active`) ve resolver ile auth kaynagi siralamasi aktif.
3. Faz C temel tamam: `/owner`, `/customer`, `/support` route gruplari ve panel iskeletleri aktif.
4. Faz C genisleme tamam: owner panelden ilan olusturma (`GET /owner/listings/create`, `POST /owner/listings`) aktif.
5. Faz D kritik test kapisi aktif: allow/deny + DB role + owner/customer akis testleri iki appte parity PASS.
6. Faz F tamam: session login/logout + `hesabim` + role bazli redirect aktif.

Acik kalanlar:
1. Ownership policy (model/policy katmani) ile "kendi kaydi" kuralini controller disina tasimak.
2. Profile detaylari (sifre degistirme, hesap guncelleme) eklemek.

## 1) Tasarim Prensipleri (Kilit)

1. Yetki kurali tek kaynak:
   - `config/admin_acl.php` (admin panel)
   - `config/customer_acl.php` (musteri/ilan sahibi paneli)
2. Role -> ability mapping data-driven olacak (hard-coded if/else yok).
3. Her route ability middleware ile korunacak:
   - `ability:<token>`
4. Role kontrolu middleware katmaninda kalacak, controller business logic'e karismayacak.
5. Tum degisiklikler iki appte ayni patch set ile uygulanacak.

## 2) Hedef Rol Matrisi (v1)

Admin tarafi:
1. `super_admin` -> `*`
2. `admin` -> `pages.manage`, `listings.manage`, `city_pages.manage`, `users.manage`
3. `content_editor` -> `pages.manage`, `listings.manage`
4. `listing_editor` -> `listings.manage`
5. `viewer` -> sadece panel goruntuleme

Musteri tarafi:
1. `listing_owner`:
   - kendi ilanini gor/guncelle
   - gelen talep/lead gor
2. `customer`:
   - profil yonetimi
   - teklif/rezervasyon talebi olusturma ve takip
3. `support_agent`:
   - destek/talep goruntuleme (kisitli)

## 3) Fazli Uygulama

## Faz A - Yetki Omurgasi Sertlestirme (Admin)
Sure: 0.5 gun

Yapilacaklar:
1. `admin.basic` + `ability` akisini tek standarda sabitle.
2. Ability token adlandirma standardi:
   - `<domain>.<action>` (orn: `listings.manage`)
3. Rol konfigunu dokumanla senkronla.

Kabul:
1. Tum admin route'lar `ability:*` ile korunuyor olacak.
2. `super_admin` disi rol hatali endpointte `403` donecek.

## Faz B - Kullanici Kimlik Modeli (DB tabanli)
Sure: 1 gun

Yapilacaklar:
1. `users` tablosuna rol baglama yapisi:
   - `roles` ve `role_user` (veya tekil `users.role`)
2. Basic auth fallback korunur, ancak asama asama DB auth'a gecis acilir.
3. Seed ile minimum roller olusturulur.

Kabul:
1. DB tabanli rol atama calisir.
2. Basic auth fallback devre disi birakilmadan gecis mumkun olur.

## Faz C - Musteri / Ilan Sahibi Paneli
Sure: 1.5 - 2 gun

Yapilacaklar:
1. Yeni route gruplari:
   - `/owner/*`
   - `/customer/*`
2. `owner` icin "kendi kaydi" siniri (resource ownership policy).
3. `customer` icin talep/rezervasyon akisi iskeleti.

Kabul:
1. `listing_owner` baska kullanicinin ilanini goremez/guncelleyemez.
2. `customer` admin endpointlerine erisemez.
3. `support_agent` sadece izinli ekranlara erisir.

## Faz D - Test ve QA Kapisi
Sure: 1 gun

Yapilacaklar:
1. Role matrix feature testleri:
   - allow/deny senaryolari
   - ownership senaryolari
2. Smoke teste rol tabanli endpoint kontrolleri eklenir.
3. Iki app parity checklisti zorunlu hale getirilir.

Kabul:
1. Her rolde beklenen `200/403/401` davranislari testle kilitlenmis olur.
2. `orkestram` ve `izmirorkestra` test sayisi/paritesi ayni olur.

## Faz E - Operasyon ve Gecis Kilidi
Sure: 0.5 gun

Yapilacaklar:
1. `.env` rol konfig anahtarlari dokumante edilir.
2. Rollback adimi netlenir (auth config fallback).
3. Release notlarina role-change impact eklenir.

Kabul:
1. Deploy oncesi role smoke PASS zorunlu olur.
2. Geri donus adimi tek komutla uygulanabilir olur.

## Faz F - Login/Hesabim (Faz 1)
Sure: 1 gun

Yapilacaklar:
1. Session tabanli giris cikis endpointleri:
   - `GET /giris`
   - `POST /giris`
   - `POST /cikis`
2. Rol bazli deterministic redirect:
   - `listing_owner -> /owner`
   - `customer -> /customer`
   - `support_agent -> /support`
   - admin/editor/viewer -> `/admin/pages`
3. `GET /hesabim` ekrani:
   - aktif kullanici/rol/auth source ozetini goster.
4. `admin.basic` middleware:
   - once session identity kontrolu
   - session yoksa mevcut basic resolver fallback
5. Test:
   - login basarili/basarisiz
   - role redirect
   - logout + korumali endpointe erisim engeli

Kabul:
1. Owner/customer/support/admin panellerine basic header olmadan session login ile giris mumkun olacak.
2. Basic Auth fallback bozulmayacak.
3. Iki appte ayni test seti PASS olacak.

Durum:
1. Tamamlandi (2026-03-10).

## 4) If/Else Cehenneminden Kacinma Kurallari

1. Controller'da `if ($role === ...)` kullanimi yasak.
2. Yetki karari middleware/policy/ability map uzerinden verilir.
3. Ownership kurali policy class'ta tanimlanir.
4. Yeni rol eklendiginde sadece:
   - config map
   - test matrix
   guncellenir.

## 5) Deterministik Teslim Kriterleri

1. Plan dokumani guncel olacak:
   - `PROJECT_STATUS_TR.md`
   - bu plan dosyasi
2. Her faz sonunda en az bir test kaniti bulunacak.
3. Iki appte ayni commit degisikligi yoksa faz kapanmayacak.
4. Kullanici acik onayi olmadan deploy adimi acilmayacak.

## 6) Kisa Uygulama Sirasi (Pratik)

1. Faz A (admin omurga) -> hizli kazanım
2. Faz D'nin admin kismi (test kilidi)
3. Faz B (DB role modeli)
4. Faz C (owner/customer)
5. Faz D tamam + Faz E
