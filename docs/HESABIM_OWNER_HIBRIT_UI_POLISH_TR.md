# Hesabim + Owner Hibrit UI Polish (TR)

Tarih: 2026-03-13  
Durum: Faz 3 tamamlandi

## Hedef
- `/hesabim` ve `/owner` akislarinda mobil kirilim (360px-480px) regressionsuz, parity korunmus UI.

## Faz 3 - Mobil Edge-case Duzeltmeleri

### 1) Mesaj listesi ve kart tasma onlemi
- `message-list-row` mobilde tek kolona alindi.
- Checkbox alani solda ve sabit hizaya cekildi.
- Kart ic bosluklari 360-480 kirilimda optimize edildi.

### 2) Thread scroll davranisi
- `message-thread` icin mobilde `max-height` dinamik sinirlandi.
- `overscroll-behavior: contain` eklenerek sayfa-thread scroll cakisimi azaltildi.

### 3) Sticky bulk toolbar
- Toplu islem formu (`#owner-message-bulk-form`) sticky yapildi.
- Uzun listede aksiyonlara geri donus hizlandirildi.

### 4) Mesaj detay header kirilimi
- Kucuk ekranda `message-detail-head` dikey akisa cekildi.
- Badge ve baslik bloklarinin ust uste binmesi engellendi.

## Parity Notu
- Ayni CSS duzeltmeleri hem `orkestram` hem `izmirorkestra` uygulamalarina birebir uygulandi.

## Dokunulan Dosyalar
- `local-rebuild/apps/orkestram/public/assets/v1.css`
- `local-rebuild/apps/izmirorkestra/public/assets/v1.css`
- `docs/tasks/TASK-015.md`
- `docs/HESABIM_OWNER_HIBRIT_UI_POLISH_TR.md`

## Dogrulama
- [x] `pre-pr -Mode quick` (PASS)
