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
3. Stack ayaÃ„Å¸a kaldirma komutu standarttir:
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
     - `powershell -ExecutionPolicy Bypass -File scripts/validate.ps1 -App both`
5. `scripts/pre-pr.ps1`
   - PR acmadan once zorunlu hizli kapidir.
   - Standart:
     - `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick`
6. `.github/pull_request_template.md`
   - PR acilisinda ozet + test + dosya listesi zorunlu format.
7. `scripts/security-gate.ps1`
   - Potansiyel secret/key sizintilarini tarar.
   - Standart:
     - `powershell -ExecutionPolicy Bypass -File scripts/security-gate.ps1`
8. `docs/TASK_LOCKS.md` + `docs/tasks/_TEMPLATE.md`
   - Gorev lock ve task kaydi script bagimsiz (manual) acilir.
   - Standart:
     - `docs/tasks/TASK-001.md` olustur
     - `docs/TASK_LOCKS.md` icine `active` satiri ekle
     - `git checkout -b agent/codex-a/task-001` ile branch ac

## 10) Yeni Gelen Ajan Onboarding (Zorunlu)

Her yeni ajan ilk turda su sirayi uygular:
1. `AGENTS.md` dosyasini okur.
2. `docs/REPO_DISCIPLINE_TR.md` ve `docs/MULTI_AGENT_RULES_TR.md` okur.
3. `git fetch --all --prune` yapar.
4. Sadece `agent/<ajan>/<task-id>` branch'i ile ilerler.
5. `docs/TASK_LOCKS.md` icinde lock almadan kod degisikligi yapmaz.
6. `pre-pr` PASS olmadan commit/push yapmaz.

## 11) WSL Tek Kaynak + 3 Ajan Klasoru Standardi

Kural:
1. Tek calisan kaynak `WSL: /home/bekir/orkestram` olmalidir.
2. Ajanlar ayni klasorde paralel calismaz.
3. Her ajan su klasorlerde calisir:
   - `/home/bekir/orkestram-a`
   - `/home/bekir/orkestram-b`
   - `/home/bekir/orkestram-c`

Kurulum komutu:
1. `powershell -ExecutionPolicy Bypass -File D:\orkestram\scripts\wsl-migrate-project.ps1 -LinuxUser bekir -SetupAgentWorkspaces`

Dogrulama:
1. Her klasorde `git branch --show-current` => `main`
2. Her klasorde `git status --short` bos olmalidir.

## 12) Dokuman Duzenleme Disiplini (Koordinator)

Kural:
1. Dokumanlarda toplu regex/replace ile coklu alan degisimi yapilmaz.
2. Degisimler minimum kapsamda, satir bazli ve hedef odakli yapilir.
3. Her dosya guncellemesinden hemen sonra dosya yeniden okunup kontrol edilir.
4. Task dosyalari `docs/tasks/_TEMPLATE.md` baslik sirasina birebir uyar.
5. Belge guncellemelerinde `main` yerine `agent/<ajan>/<task-id>` branch disiplini zorunludur.
6. Kapanis oncesi zorunlu kapi: `pre-pr -Mode quick` PASS.

## 13) Ajan Baslangic Hard Guard (D:\orkestram -> WSL Hizalama)

Kural:
1. Ajan `D:\orkestram` altinda acilsa bile kod degisikligine gecmeden WSL hizalama kaniti zorunludur.
2. Zorunlu kanit komutu:
   - `wsl -e bash -lc "cd /home/bekir/orkestram-<slot> && pwd && git rev-parse --show-toplevel && git branch --show-current && git status --short"`
3. Cikti `/home/bekir/orkestram-<slot>` degilse durum `REALIGN_REQUIRED` kabul edilir.
4. `REALIGN_REQUIRED` durumunda ajan calismasi durdurulur; dogru WSL workdir ile yeniden baslatilmadan degisiklik yapilmaz.
5. Hard Guard kaniti alinmadan lock acik olsa bile kod/dokuman degisikligi yapilmaz.



