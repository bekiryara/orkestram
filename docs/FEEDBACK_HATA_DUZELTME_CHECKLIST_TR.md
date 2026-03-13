# Feedback Hata Duzeltme Checklist (TR)

Tarih: 2026-03-13  
Durum: Tamamlandi

## Hedef
Owner / Customer / Admin / Public listing ekranlarindaki yorum-begeni akis tutarsizliklarini kapatmak.

## Checklist
- [x] 1. Listing detayda onayli yorumlarin gorunmesi
  - [x] `PublicController@listing` yorum sorgusu eklendi
  - [x] listing detay blade'de yorum listesi render edildi
- [x] 2. Owner yorum ekraninda gonderen kimligi
  - [x] owner feedback query `user` relation ile genisletildi
  - [x] owner yorum tablosuna `Gonderen` kolonu eklendi
- [x] 3. Owner yorum ekrani sadeleme
  - [x] comment modunda `Benim Gonderdiklerim` bolumu kaldirildi
- [x] 4. Admin feedback moderasyon ekrani
  - [x] `/admin/feedbacks` route/controller/view eklendi
  - [x] admin menuye `Geri Bildirimler` eklendi
- [x] 5. Durum kurali gorunurlugu
  - [x] listing yorum bolumune "yalniz onayli yorumlar" notu eklendi
- [x] 6. Parity + dogrulama
  - [x] tum degisiklikler `izmirorkestra` app'ine aynalandi
  - [x] `smoke-test.ps1 -App both` PASS alindi
- [x] 7. Test kilidi
  - [x] feature testlere listing yorum gorunurlugu ve owner gonderen kimligi senaryolari eklendi
  - [x] admin feedback panel erisimi + yorum durum guncelleme senaryosu eklendi
  - [x] smoke script icerik kontrolleri eklendi (`Yorumlar`, `Geri Bildirimler`)
- [x] 8. Dead code cleanup v2
  - [x] `/messages` tek yuzeye alinip `Hesabim > Mesajlarim` tabina yonlendirildi
  - [x] standalone `portal/messages/index.blade.php` dosyasi kaldirildi (iki app)
  - [x] owner dashboard legacy degisken adi `engagementCount` -> `feedbackCount` normalize edildi
  - [x] kalan legacy metinler (`Etkilesim`) yeni terminolojiye cekildi
  - [x] feature test + smoke (`-App both`) tekrar PASS alindi

## Not
- Bu checklist tamamlandiginda aktif referanslar:
  - `FEEDBACK_V1_TR.md`
  - `MESAJ_V1_TR.md`
