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
3. Stack ayaga kaldirma komutu standarttir:
   - `powershell -ExecutionPolicy Bypass -File scripts/dev-up.ps1 -App both`
4. Dogrudan `docker compose up` ile manuel calistirma yapilmaz (yanlis mount riski).

## 1.1) Git Remote Kaynak Modeli

Kural:
1. Operasyonel `origin` her zaman GitHub reposudur:
   - `https://github.com/bekiryara/orkestram.git`
2. Canonical WSL repo:
   - `/home/bekir/orkestram`
3. Koordinator/ajan workdir'lerinde local WSL referansi gerekiyorsa ayri remote adi kullanilir:
   - `canonical` -> `/home/bekir/orkestram`
4. `windows-mirror` varsa sadece export/aynalama veya gecis yardimcisi roldedir; gunluk `fetch/pull/push` akisinin parcasi degildir.

Kontrol:
1. `git remote -v` icinde `origin` GitHub degilse ise baslanmaz.
2. `git branch -vv` icinde aktif branch `origin/<branch>` takip etmiyorsa push/pull oncesi hizalanir.

## 2) Dizin ve Dosya Disiplini

Degisim alanlari:
1. `local-rebuild/apps/**` uygulama kodu
2. `docs/**` plan, rapor, checklist
3. `scripts/**` otomasyon

Kural:
1. Her kod degisimi icin ilgili dokumana en az bir satir guncelleme notu dusulur.
2. Dokuman degisimi kodu etkiliyorsa ilgili dosya referansi eklenir.
3. Gecici/hack kod canliya tasinmaz.
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

## 8) Runtime Kisa Hijyen Checklisti

Her gorevde dogrulama oncesi su 4 kontrol yapilir:
1. Container up:
   - uygulama stack'i ayakta olmalidir.
2. Mount source dogru:
   - aktif kaynak `WSL: /home/bekir/orkestram*` altindan gelmelidir.
3. Portlar cevap veriyor:
   - `8180`, `8181`, `8188`
4. Smoke PASS:
   - hizli smoke veya `validate/pre-pr` icindeki smoke adimi basarili olmalidir.

## 9) Calisma Protokolu (Her Gorevde)

1. Degisiklik oncesi: hedef dosya ve etki alani netlestir.
2. Degisiklik sonrasi: cache temizle + hizli smoke.
3. Sonra: dokuman/status satiri guncelle.
4. En son: sonraki adim net yaz.
5. Git akisi:
   - `git fetch origin --prune`
   - gerekirse `git pull --ff-only origin <branch>`
   - yeni branch upstream'i yoksa ilk baglama adimi: `git push -u origin agent/<ajan>/<task-id>`
   - upstream kurulduktan sonra `pre-pr PASS` olmadan yeni push yok

## 9A) Komut Katmani ve Ortam Readiness Siniflari

Komut katmani:
1. `git`, branch acilisi, upstream ve worktree status dogrulamasi:
   - varsayilan katman `WSL`
2. `scripts/*.ps1`:
   - varsayilan katman `PowerShell`
3. `php artisan test` ve uygulama ici komutlar:
   - varsayilan katman `container`
4. Runtime mount/source veya preview kaniti:
   - `WSL + ilgili script`

Ortam/readiness siniflari:
1. `ENV_BLOCKED`
   - upstream yok, credential/auth blokaji, yanlis shell, quoting hatasi, arac eksigi
2. `RUNTIME_BLOCKED`
   - container ayakta degil, mount source yanlis, `vendor/autoload.php` eksik, runtime izin sorunu var
3. `SANDBOX_BLOCKED`
   - sandbox refresh veya arac katmani (`apply_patch`, dosya okuma) kirildi
4. `CODE_FAIL`
   - test/smoke/validate dogrudan koddan fail verdi

Kural:
1. `ENV_BLOCKED`, `RUNTIME_BLOCKED` ve `SANDBOX_BLOCKED` durumlari urun hatasi diye kapanmaz.
2. Blokaj sinifi gorulurse ayni kirik komut tekrar edilmez; dogru fallback katmanina gecilir.
3. PowerShell icinde `&&` kullanilip yarim komut zinciri uretilmez.
4. WSL credential/helper sorunu varsa `read OK / write auth blocked` olarak kayda alinir.

## 10) Operasyonel Disiplin Dosyalari (Zorunlu)

1. `docs/NEXT_TASK.md`
   - En fazla 3 aktif gorev buraya yazilir.
   - docs/TASK_LOCKS.md ile birebir senkron tutulur.
   - YOK yalniz hic aktif task yoksa kullanilir.
2. `docs/WORKLOG.md`
   - Her ajan turu sonunda degisen dosya + komut + PASS/FAIL kaydi zorunlu.
3. `docs/SESSION_HANDOFF_TR.md`
   - Aktif tasklar, worktree durumlari, stale adaylar ve sonraki adim icin merkezi operasyon hafizasidir.
4. `scripts/agent-status.ps1`
   - Ajan/worktree branch, status, upstream ve stale aday durumunu salt-okuma raporlar.
5. `docs/ROLLBACK_POINTS.md`
   - Riskli degisimlerden once geri donus noktasi kaydi acilir.
6. `scripts/validate.ps1`
   - "Bitti" demeden once zorunlu dogrulama komutu.
   - Standart:
     - `powershell -ExecutionPolicy Bypass -File scripts/validate.ps1 -App both`
7. `scripts/pre-pr.ps1`
   - PR acmadan once zorunlu hizli kapidir.
   - Standart:
     - `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick`
8. `.github/pull_request_template.md`
   - PR acilisinda ozet + test + dosya listesi zorunlu format.
9. `scripts/security-gate.ps1`
   - Potansiyel secret/key sizintilarini tarar.
   - Standart:
     - `powershell -ExecutionPolicy Bypass -File scripts/security-gate.ps1`
10. `docs/TASK_LOCKS.md` + `docs/tasks/_TEMPLATE.md`
   - Gorev lock ve task kaydi script bagimsiz (manual) acilir.
   - Standart task acma sirasi:
     1. `docs/tasks/TASK-0xx.md`
     2. `docs/TASK_LOCKS.md`
     3. `docs/NEXT_TASK.md`
     4. branch acilisi
   - Mekanik komut:
     - `powershell -ExecutionPolicy Bypass -File scripts/start-task.ps1 -TaskId TASK-0xx -Agent codex -Files "path/one,path/two" -Note "kisa ozet"`
   - Script aktif lock overlap tespit ederse task acilmaz.
   - UNC path uzerinde branch acilisi kirilirse durum `PARTIAL_OPEN` sayilir:
     1. task karti + lock + `NEXT_TASK` yazildiysa silinmez
     2. branch acilisi WSL icinde tamamlanir
     3. durum `docs/SESSION_HANDOFF_TR.md` icine islenir
11. `docs/COORDINATOR_BOOTSTRAP_TR.md`
   - Yeni gelen koordinatorun ilk 5 dakika akisi icin tek referans dokumandir.
12. `scripts/close-task.ps1`
   - Task karti + `TASK_LOCKS` + `NEXT_TASK` + `WORKLOG` mekanik kapanis yardimcisidir.
   - Standart:
     - `powershell -ExecutionPolicy Bypass -File scripts/close-task.ps1 -TaskId TASK-0xx -Agent codex -ClosureNote "kisa kapanis ozeti" -WorklogTitle "baslik" -WorklogSummary "madde-1" -Files "dosya-1" -Commands "komut-1" -Result PASS`

Kurallar:
1. Repo genelinde ayni anda en fazla 3 `active` task olabilir.
2. Ayni kapsamda revize, polish veya kapanis eksigi icin yeni task acilmaz; mevcut task devam eder.
3. Hedef ayni kalip yeni dosya gerekiyorsa task genisletilir; lock listesi ve task karti guncellenir.
4. Yeni task ancak yeni kabul kriteri, yeni risk sinifi, yeni lock alani veya ayrik owner gerektiriyorsa acilir.
5. `docs/NEXT_TASK.md`, `docs/TASK_LOCKS.md`, `docs/WORKLOG.md` ve `docs/SESSION_HANDOFF_TR.md` merkezi koordinasyon alanidir; bu dosyalarda paralel kapanis gerekiyorsa koordinat?r kontrollu entegrasyon uygulanir.
6. Koordinator yeni is veya dagitim oncesi `scripts/agent-status.ps1` raporunu okuyarak stale worktree adaylarini kontrol eder.
7. Surekli 3 ajan orkestrasyonunda varsayilan paketler `UI | data-fixture | test-ops` olarak dusunulur; lock cakismasi varsa dagitim iptal edilir.

## 10A) Merge Taski Istisna Standardi

Kural:
1. Her teslim icin otomatik ikinci bir `merge taski` acilmaz.
2. Varsayilan model su olur:
   - owner task teslim + PR + merge + kapanis ayni taskta tamamlanir
3. Ayrı merge taski yalniz su durumda acilir:
   - merge yeni operasyon riski doguruyorsa
   - birden fazla owner branch icin sira karari gerekiyorsa
   - merge sonrasi runtime/preview etkisi ayri takip gerektiriyorsa
   - merkezi koordinasyon kayitlari mevcut task kapsamindan cikiyorsa
4. Tek owner branch, temiz teslim kaniti ve dusuk operasyon riski varsa yeni merge taski acmak yerine mevcut task kapanisinda ilerlenir.
5. Koordinator merge taski aciyorsa gerekceyi task kartinda acik yazar; `varsayilan degil, istisna` oldugu belirtilir.
## 11) Yeni Gelen Ajan Onboarding (Zorunlu)

Her yeni ajan ilk turda su sirayi uygular:
1. `AGENTS.md` dosyasini okur.
2. `docs/REPO_DISCIPLINE_TR.md` ve `docs/MULTI_AGENT_RULES_TR.md` okur.
3. `git fetch origin --prune` yapar.
4. Sadece `agent/<ajan>/<task-id>` branch'i ile ilerler.
5. `docs/TASK_LOCKS.md` icinde lock almadan kod degisikligi yapmaz.
6. `pre-pr` PASS olmadan commit/push yapmaz.
7. `git remote -v` ile `origin`in GitHub oldugunu dogrular.
8. Koordinator ise `docs/COORDINATOR_BOOTSTRAP_TR.md` akisini da uygular.

## 12) WSL Tek Kaynak + 3 Ajan Klasoru Standardi

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

## 13) Dokuman Duzenleme Disiplini (Koordinator)

Kural:
1. Dokumanlarda toplu regex/replace ile coklu alan degisimi yapilmaz.
2. Degisimler minimum kapsamda, satir bazli ve hedef odakli yapilir.
3. Her dosya guncellemesinden hemen sonra dosya yeniden okunup kontrol edilir.
4. Task dosyalari `docs/tasks/_TEMPLATE.md` baslik sirasina birebir uyar.
5. Belge guncellemelerinde `main` yerine `agent/<ajan>/<task-id>` branch disiplini zorunludur.
6. Kapanis oncesi zorunlu kapi: `pre-pr -Mode quick` PASS.

## 14) Ajan Baslangic Hard Guard (D:\orkestram -> WSL Hizalama)

Kural:
1. Ajan `D:\orkestram` altinda acilsa bile kod degisikligine gecmeden WSL hizalama kaniti zorunludur.
2. Zorunlu kanit komutu:
   - `wsl -e bash -lc "cd /home/bekir/orkestram-<slot> && pwd && git rev-parse --show-toplevel && git branch --show-current && git status --short"`
3. Cikti `/home/bekir/orkestram-<slot>` degilse durum `REALIGN_REQUIRED` kabul edilir.
4. `REALIGN_REQUIRED` durumunda ajan calismasi durdurulur; dogru WSL workdir ile yeniden baslatilmadan degisiklik yapilmaz.
5. Hard Guard kaniti alinmadan lock acik olsa bile kod/dokuman degisikligi yapilmaz.

## 15) Task ID Tekrar Yasaki + Koordinator Cevap Sablonu + Remote/Upstream Zorunlulugu

Kural:
1. `TASK-XXX` kimligi tekrar kullanilamaz; her yeni is benzersiz task id ile acilir.
2. Koordinator ilk karar cevabini su sabit formatla verir:
   1. `aktif branch: ...`
   2. `aktif task durumu: ...`
   3. `karar: mevcut task devam | yeni task ac`
   4. `sonraki adim: ...`
3. `git remote -v` ve `git branch -vv` dogrulamasi gorev baslangici ve `pre-pr` oncesi zorunludur.
4. `origin` GitHub degilse veya aktif branch upstream'i `origin/<branch>` degilse commit/push akisi durdurulur.

## 16) Preview Lane Standardi

Kural:
1. `main preview` merge edilmis dunya icindir ve koordinator worktree'sinden servis verir.
2. `design preview` tasarim gorevleri icindir ve sabit tasarim ajaninin worktree'sinden servis verir.
3. Tasarim review icin merge yapilmaz; once `design preview` gorulur, sonra onayli is merge edilir.
4. `design preview` URL'leri sabittir:
   - `http://127.0.0.1:8280` -> orkestram design
   - `http://127.0.0.1:8281` -> izmirorkestra design
5. Runtime komutu mount source kaniti vermeden UI gorevi review'e cikmaz.

## 17) UI Review ve Merge Sirasi

Kural:
1. UI tasklarda begenilmeyen duzeltmeler, kapsam ayni kaldigi surece ayni taskta revize edilir.
2. Yeni task ancak yeni ozellik, yeni lock dosyalari veya yeni ekran kapsami aciliyorsa olusur.
3. Koordinator UI islerinde merge'i su sirayla ilerletir:
   - `design-preview`da goster
   - kullanici onayi al
   - `pre-pr` PASS al
   - push/teslim kanitini topla
   - merge et
4. Kullanici onayi olmadan UI isleri `main`e alinmaz.
5. `main`e bakmak review degil, onayli sonucu gormektir.

## 18) Edit Source = Preview Source Standardi

Kural:
1. UI gorevlerinde kodun degistirildigi worktree/path ile preview'un mount ettigi worktree/path ayni olmak zorundadir.
2. `Edit Source != Mount Source` ise review gecersizdir; merge karari verilmez.
3. Koordinator UI review oncesi `Edit Source`, `Mount Source` ve `Preview URL` ucunu birlikte dogrular.
4. Farkli worktree'de patch yazip baska worktree preview'u gostermek operasyonel ihlaldir.
5. Bu esitlik saglanmiyorsa once kaynak hizasi duzeltilir, sonra UI review baslar.






