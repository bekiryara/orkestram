# Hesabim + Owner Hibrit UI Polish (TR)

Tarih: 2026-03-13  
Gorev: TASK-013  
Ajan: codex-b

## Kapsam Ozeti
- `/hesabim` ve `/owner` akislarinda menu ve mesaj merkezi deneyimi iki uygulamada (orkestram + izmirorkestra) parity olacak sekilde hizalandi.
- Mobil kirilimlarda tasma/yerlesim bozulmasi azaltildi.

## Yapilan UI Kararlari

### 1) Owner menu parity + mobil sekme davransi
- Owner menu tek bir tab listesi (`$ownerTabs`) uzerinden uretiliyor.
- Ayni tab listesi hem mobil (`tabs-mobile`) hem masaustu (`account-nav`) bloklarina basiliyor.
- Sonuc: `/owner` ekranlari, `/hesabim` sekme davranisiyla tutarli hale geldi.

### 2) Mesaj karti spacing ve kirilim iyilestirmesi
- Mesaj karti satiri mobilde dikey, masaustunde yatay akisa alindi (`flex-column` -> `flex-md-row`).
- Ana icerik alani `min-w-0` + `text-break` ile uzun metinlerde tasma riskini azaltiyor.
- Tarih alani `text-nowrap` ile tek satirda, baslik alaniyla hizali.
- Durum badge'i mobilde sola/yukariya tasinmadan okunur konumda.

### 3) Buton hiyerarsisi ve mobil eylem rahatligi
- Konusma detayinda eylem butonlari dikey/yatay uyarlanabilir (`flex-column flex-sm-row`).
- Toplu islem ve yeni mesaj kartlarinda ic bosluk (`p-3`) standartlandi.
- Mobilde ana aksiyon butonlari `d-grid` ile tam-genislikte tiklanabilir yapildi.

## Dokunulan Dosyalar
- `local-rebuild/apps/orkestram/resources/views/portal/owner/partials/menu.blade.php`
- `local-rebuild/apps/izmirorkestra/resources/views/portal/owner/partials/menu.blade.php`
- `local-rebuild/apps/orkestram/resources/views/portal/partials/message-list-item.blade.php`
- `local-rebuild/apps/izmirorkestra/resources/views/portal/partials/message-list-item.blade.php`
- `local-rebuild/apps/orkestram/resources/views/portal/messages/center-content.blade.php`
- `local-rebuild/apps/izmirorkestra/resources/views/portal/messages/center-content.blade.php`

## Dogrulama
- [ ] pre-pr -Mode quick
- [ ] /hesabim messages ve /owner messages ekranlarinda mobil gorunum kontrolu
- [ ] Iki app arasinda gorunur fark kalmadi kontrolu
