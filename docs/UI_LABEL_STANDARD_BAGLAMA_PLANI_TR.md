# UI Label Standarda Baglama Plani (TR)

Tarih: 2026-03-13  
Durum: Plan Kilidi (Uygulama Oncesi)

## 1) Amac
- UI label sozlugunu kod tabanina deterministik sekilde baglamak.
- `apps/orkestram` ve `apps/izmirorkestra` parity zorunlulugunu korumak.
- `/hesabim`, `/owner`, `/customer` ekranlarinda tek terminoloji kullanmak.

## 2) Karar Hiyerarsisi (Cakismazlik Kurali)
1. Operasyonel aktif hedef: `docs/NEXT_STEPS_TR.md`
2. Label sozlugu: `docs/UI_LABEL_SOZLUGU_TR.md`
3. Kod gercegi: `local-rebuild/apps/*/resources/views/**`

Kural:
- `NEXT_STEPS_TR.md` ile `UI_LABEL_SOZLUGU_TR.md` cakisiyorsa once aktif operasyonel hedef korunur.
- Sonra sozluk o hedefe gore guncellenir.

## 3) Kapsam
- In:
  - Label envanteri cikarma
  - Sapma listesi olusturma
  - Standart label map kilidi
  - Uygulama fazlari + test kapisi tanimi
- Out:
  - Business logic degisikligi
  - Route/controller isim degisikligi
  - SEO/redirect calismalari

## 4) Mevcut Sapma Envanteri (Ilk Tarama)
1. Sorular sistemi kapsam disi oldugu halde bazi aktif dokumanlarda `Sorularim/Sorular` beklentisi geciyor.
   - Karar: `Sorular` akisi geri getirilmeyecek; dokumanlar kod gercegine hizalanacak.
2. Terminoloji cakismasi:
   - Sozlukte: `Sahiplik ve Yetki` (`docs/UI_LABEL_SOZLUGU_TR.md:50`)
   - Aktif hedefte: `Sahiplik ve Yetki Ayarlari` (`docs/NEXT_STEPS_TR.md:21`)
   - Kod: `Sahiplik ve Yetki Ayarlari` (`local-rebuild/apps/orkestram/resources/views/portal/owner/partials/menu.blade.php:9`)

Not:
- Yukaridaki dosyalarin iki app parity dogrulamasi PASS (eslesen dosyalar birebir ayni).

## 5) Fazli Uygulama Plani

### Faz A - Sozluk Kilidi
- `UI_LABEL_SOZLUGU_TR.md` icin cakismazlik karari yazilir.
- Standart menu sira kurali sabitlenir:
  - `/hesabim`: `Genel Bakis, Taleplerim, Mesajlarim, Yorumlarim, Profilim, Guvenlik`
  - `/owner`: `Genel Bakis, Ilanlarim, Isler / Talepler, Ilan Mesajlari, Yorumlar, Sahiplik ve Yetki Ayarlari`

### Faz B - Label Map Sozlesmesi
- Tek tablo halinde `label_key -> standart_metin -> ekran` listesi uretilir.
- Yasakli varyantlar bu tabloya "reject" notu ile baglanir.

### Faz C - Kod Uygulama
- Once `apps/orkestram`, sonra ayni patch mantigiyla `apps/izmirorkestra`.
- UI iskeleti korunur; sadece label/menu duzeyi degisir.
- Gerekirse ortak partial/lang kullanimi ile hardcoded dagilim azaltilir.

### Faz D - Dogrulama Kapisi
- Label sapma listesi sifirlanir.
- Komutlar:
  - `powershell -ExecutionPolicy Bypass -File D:\orkestram\scripts\pre-pr.ps1 -Mode quick`
  - `powershell -ExecutionPolicy Bypass -File D:\orkestram\scripts\validate.ps1 -App both -Mode quick`
- Manuel smoke:
  - `/hesabim`
  - `/owner`
  - `/customer/requests`

### Faz E - Dokuman Kapanisi
- `docs/PROJECT_STATUS_TR.md` ve `docs/WORKLOG.md` guncellenir.
- `docs/NEXT_TASK.md` durumu `DONE` yapilir.

## 6) Kabul Kriteri
1. Iki appte menu label setleri birebir ayni.
2. `Sorularim` ve `Sorular` label'lari aktif menu setlerinde yer almaz.
3. `Sahiplik ve Yetki Ayarlari` terimi tek standart olarak kullanilir.
4. `pre-pr` ve `validate` quick PASS.
