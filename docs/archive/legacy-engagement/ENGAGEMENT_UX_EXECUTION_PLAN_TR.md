# DEPRECATED

Bu dokuman legacy engagement modeline aittir. Aktif mesaj sistemi icin MESAJ_V1_TR.md kullanin.

# Engagement UX Uygulama Plani (TR)

Tarih: 2026-03-11  
Durum: Tamamlandi (iki app parity)

## Amac
1. Yorum / soru / mesaj / begeni akislarini kullanici icin daha az adimli hale getirmek.
2. Owner ve support moderasyon akislarini hizlandirmak.
3. Bootstrap 5.3 + mevcut `v1.css` disiplini disina cikmadan parity korumak.
4. Repo disiplinine uygun sekilde test ve dokuman kapisiyla teslim etmek.

## Kapsam (Bu Faz)
1. Listing detay CTA hiyerarsi sadeletme.
2. Customer engagements ekraninda slug elle yazma yerine secim odakli akis.
3. Owner engagements ekraninda hizli satir-ici guncelleme.
4. Alert / empty-state / status-badge dilini standardize etme.
5. Admin + support icin merkezi engagements moderasyon ekranlari.

## Fazli Uygulama

### Faz 1 - Listing CTA Hiyerarsi
1. Listing detayda etkilesim aksiyonlarini birincil/ikincil olarak ayir.
2. Guest kullanicida login `next` akisiyla hedef ekrana donus.
3. Ayni aksiyonu tekrarlayan dairesel linkleri azalt.

Kabul:
1. Listing detaydan 1 tikla etkilesim akisina gecis.
2. Guest login sonrasi dogrudan hedef etkilesim ekranina donus.

### Faz 2 - Customer Form Kullanilabilirligi
1. Slug serbest metin girisini secim odakli inputa indir.
2. Prefill listing/kind bilgisini form state'ine tasimaya devam et.
3. Basari/hata mesajlarini tek standarda al.

Kabul:
1. Kullanici slug yazmadan kayit acabilir.
2. Form validasyonunda net geri bildirim gorunur.

### Faz 3 - Owner Hizli Moderasyon
1. Owner engagements aksiyonunu satir-ici hizli modelle guncelle.
2. Status + reply alanlarini tek akisla kaydet.
3. Bos durumda net aksiyon mesaji ver.

Kabul:
1. Owner tek satirdan durum degistirip yanitlayabilir.
2. Kendi ilanlari disinda kayitta hala 403 korumasi calisir.

### Faz 4 - UI Standardizasyonu
1. `alert`, `badge`, `table` ve bos durum metinlerini ortak dilde toparla.
2. Buton hiyerarsisini (`btn-primary`, `btn-outline-secondary`) netlestir.
3. Mobilde tablo kirilmasini `table-responsive` ile koru.

Kabul:
1. Customer/owner/support/admin ekranlarinda ortak gorunum dili.
2. Mobilde kirilma olmadan okunabilir tablo/form.

### Faz 5 - Merkezi Moderasyon (Admin + Support)
1. Support icin `/support/engagements` liste + durum guncelleme.
2. Admin icin `/admin/engagements` merkezi liste + durum guncelleme.
3. Ability tokenlari ile route koruma:
   - `support.engagement.view`
   - `support.engagement.manage`
   - `engagements.manage` (admin)

Kabul:
1. Support kendi site kapsaminda engagements kayitlarini yonetebilir.
2. Admin merkezi ekrandan engagements kayitlarini yonetebilir.
3. Yetkisiz rolde 403 davranisi korunur.

## Test Kapisi
1. `ListingEngagementFlowTest`
2. `PortalSessionAuthTest` (`next` akisi dahil)
3. Yeni moderasyon testleri (support/admin engagements)
4. E2E: `EndToEndRoleJourneyTest`

## Repo Disiplini Kapanis
1. Iki app parity patch seti zorunlu.
2. WSL senkron + runtime mount dogrulama zorunlu.
3. `PROJECT_STATUS_TR.md` ve `NEXT_STEPS_TR.md` guncellenecek.

## Kapanis Kaniti
1. `PortalSessionAuthTest` PASS (next akisi dahil)
2. `ListingEngagementFlowTest` PASS
3. `EndToEndRoleJourneyTest` PASS
4. `SupportRequestManageTest` PASS
5. `EngagementModerationAccessTest` PASS

