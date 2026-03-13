# Mesaj V1 (Deterministik Tek Omurga)

## Kapsam
- Tek mesaj sistemi, tek UI component omurgasi.
- Customer `Hesabim > Mesajlarim` ve Owner `Ilan Yonetimi > Ilan Mesajlari` ayni mesaj componentini kullanir.
- Fark sadece baglamdadir:
  - `personal`: customer/kisisel kutu
  - `listing`: owner/ilan kutusu

## Veri Modeli
- `message_conversations`
  - Konusma basligi (thread) kaydi
  - Eslesme: `site + listing_id + owner_user_id + customer_user_id`
- `message_conversation_messages`
  - Konusma altindaki tekil mesajlar
  - `conversation_id`, `sender_user_id`, `sender_role`, `body`

Bu model ile ayni kisi/ilan icin yeni baslik acilmaz, mesajlar ayni konusmada birikir.

## Ana Akis
1. Liste ekrani acilir (konusma basliklari)
2. Basliga tiklanir
3. Detay/thread acilir
4. Mesaj gonderilir
5. Mesaj ayni thread'e eklenir

## Endpointler
- `GET /messages`
  - Merkezi mesaj sayfasi
- `POST /messages/reply`
  - Mesaj gonderme/yanit
  - JSON isteklerde ajax yaniti doner
- `POST /messages/bulk`
  - Toplu islem (sil/engelle)
- `GET /messages/thread`
  - Acik konusma mesajlarini JSON dondurur (polling)

## UI Davranisi
- Tek ortak component: `portal/messages/center-content.blade.php`
- Liste ve detay ayni componentten render edilir
- Checkbox/toglu islem ayni componentte bulunur
- Mesaj gonderimi AJAX calisir (full page refresh yok)
- Acik thread 3 sn aralikla polling ile yenilenir
- Yeni mesaj gelirse otomatik alta kaydirma vardir

## Owner ve Customer Konumu
- Customer:
  - `Hesabim > Mesajlarim` sekmesi icinde calisir
- Owner:
  - `Ilan Yonetimi > Ilan Mesajlari` sekmesi icinde calisir

Ikisi de ayni componenti kullanir.

## Teknik Referans (Ana Dosyalar)
- `app/Services/Portal/MessageCenterService.php`
- `app/Http/Controllers/Portal/MessageCenterController.php`
- `resources/views/portal/messages/center-content.blade.php`
- `resources/views/portal/messages/index.blade.php`
- `resources/views/portal/account.blade.php`
- `resources/views/portal/owner/feedbacks.blade.php`
- `routes/web.php`

## Test Checklist
1. Ayni kisi/ilan icin tekrar mesaj gonder -> yeni baslik acilmamali
2. Mesaj detayinda gonder -> sayfa yenilenmeden mesaj thread'e dusmeli
3. Karsi taraf mesaji -> en gec birkac sn icinde polling ile gorunmeli
4. Yeni mesaj gelince thread otomatik en alta kaymali
5. Owner ve Customer ekraninda ayni component davranisi korunmali

## Not (V2 Onerileri)
- WebSocket (gercek zamanli push) ile polling kaldirilabilir
- Okundu/okunmadi metrikleri conversation seviyesine eklenebilir
- Dosya eki ve mesaj duzenleme/silme aksiyonlari eklenebilir
