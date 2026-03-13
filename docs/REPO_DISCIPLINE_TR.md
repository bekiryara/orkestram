# Repo Disiplini ve Teknik Borc Kurallari (TR)

Tarih: 2026-03-09

Amac:
1. Teknik borcu biriktirmeden ilerlemek.
2. Dokuman-kod senkronunu korumak.
3. Local ve canli ortam farkindan dogan hatalari azaltmak.

## 1) Tek Kaynak Kurali

Bu projede calisan kaynak dizin:
1. `WSL: /home/bekir/orkestram`

Windows dizini:
1. `D:\orkestram` sadece edit/export noktasi olabilir.
2. Calisan container mount'u WSL yolundan takip edilir.

Kural:
1. "Kod degisti ama calismiyor" durumunda ilk kontrol mount kaynagi olmalidir.
2. WSL disinda calistirilan kod referans alinmaz.
3. Stack ayağa kaldirma komutu standarttir:
   - `powershell -ExecutionPolicy Bypass -File scripts/dev-up.ps1 -App both`
4. Dogrudan `docker compose up` ile manuel calistirma yapilmaz (yanlis mount riski).

## 2) Dizin ve Dosya Disiplini

Degisim alanlari:
1. `local-rebuild/apps/**` uygulama kodu
2. `docs/**` plan, rapor, checklist
3. `scripts/**` otomasyon

Kural:
1. Her kod degisimi icin ilgili dokumana en az bir satir guncelleme notu dusulur.
2. Dokuman degisimi kodu etkiliyorsa ilgili dosya referansi eklenir.
3. Gececi/hack kod canliya tasinmaz.
4. Controller'da is kurali birikirse service/refactor sprinti zorunlu acilir.

## 3) Definition of Done (DoD)

Bir is "tamam" sayilmasi icin:
1. Kod calisir (minimum smoke PASS).
2. Hata logu temizdir (kritik/seviye ERROR yok).
3. En az bir dogrulama yapilmistir (HTTP 200, form save, vb.).
4. Dokuman gunceldir (`PROJECT_STATUS_TR.md` + ilgili plan).
5. Deploy etkisi varsa not dusulmustur.

## 4) Teknik Borc Kapisi

Asagidaki durumlarda is kapatilmaz:
1. Sessiz 500 hatasi var.
2. Validasyon eksigi nedeniyle kirik veri yaziliyor.
3. Upload/izin/migration adimi dokumansiz.
4. UI degisikligi var ama fallback/yedek akis yok.

## 5) Migration ve Veri Guvenligi

Kural:
1. Her schema degisimi migration ile gelir.
2. `down()` metodu geri donus destekler.
3. Migration sonrasi:
   - `migrate --force`
   - temel endpoint testi

## 6) Upload ve Medya Kurallari

Kural:
1. Yukleme yolu standard:
   - `public/uploads/listings/{slug}/`
2. Yetersiz izin durumunda sessiz fail yok, acik hata mesaji verilir.
3. Fallback gorsel zorunludur.

## 7) Deploy Kilidi

Bu kosullar olmadan deploy yok:
1. `LOCAL_FINISH_PLAN_TR.md` Faz 1-6 kabul kriterleri PASS.
2. Kullanici acik onayi: `tamam, deploy et`.
3. Son smoke test PASS.

## 8) Calisma Protokolu (Her Gorevde)

1. Degisiklik oncesi: hedef dosya ve etki alani netlestir.
2. Degisiklik sonrasi: cache temizle + hizli smoke.
3. Sonra: dokuman/status satiri guncelle.
4. En son: sonraki adim net yaz.

## 9) Operasyonel Disiplin Dosyalari (Zorunlu)

1. `docs/NEXT_TASK.md`
   - Tek aktif gorev buraya yazilir.
   - Ayni anda birden fazla aktif gorev acilmaz.
2. `docs/WORKLOG.md`
   - Her ajan turu sonunda degisen dosya + komut + PASS/FAIL kaydi zorunlu.
3. `docs/ROLLBACK_POINTS.md`
   - Riskli degisimlerden once geri donus noktasi kaydi acilir.
4. `scripts/validate.ps1`
   - "Bitti" demeden once zorunlu dogrulama komutu.
   - Standart:
     - `powershell -ExecutionPolicy Bypass -File D:\orkestram\scripts\validate.ps1 -App both`
5. `scripts/pre-pr.ps1`
   - PR acmadan once zorunlu hizli kapidir.
   - Standart:
     - `powershell -ExecutionPolicy Bypass -File D:\orkestram\scripts\pre-pr.ps1 -Mode quick`
6. `.github/pull_request_template.md`
   - PR acilisinda ozet + test + dosya listesi zorunlu format.
7. `scripts/security-gate.ps1`
   - Potansiyel secret/key sizintilarini tarar.
   - Standart:
     - `powershell -ExecutionPolicy Bypass -File D:\orkestram\scripts\security-gate.ps1`
