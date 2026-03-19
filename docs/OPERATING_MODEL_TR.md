# Operating Model (TR)

Tarih: 2026-03-19

Amac:
1. Koordinator ve ajanlar icin operasyon modelini tek merkezde toplamak.
2. Task acma, task genisletme, yeni task, handoff ve stale worktree gorunurlugunu tek akista netlestirmek.
3. Sohbet gecmisi yerine dosya tabanli operasyon hafizasi kurmak.

## 1) Ilk Okuma Sirasi

Koordinator veya ajan ise baslamadan once su sirayi izler:
1. `AGENTS.md`
2. `docs/REPO_DISCIPLINE_TR.md`
3. `docs/MULTI_AGENT_RULES_TR.md`
4. `docs/SESSION_HANDOFF_TR.md`
5. Gerekiyorsa `powershell -ExecutionPolicy Bypass -File scripts/agent-status.ps1`

## 2) Koordinator Operasyon Dongusu

1. Sistem okuma:
   - `docs/NEXT_TASK.md`
   - `docs/TASK_LOCKS.md`
   - `docs/SESSION_HANDOFF_TR.md`
   - `scripts/agent-status.ps1` raporu
2. Karar verme:
   - mevcut task devam
   - task genisletme
   - yeni task acma
   - isi ajanlara dagitma
3. Gorev acma:
   - task karti
   - lock
   - `NEXT_TASK`
   - branch
4. Yurutme:
   - yalniz lock icindeki dosyalar
   - minimal ve kontrollu degisiklik
5. Teslim:
   - task karti checklistleri
   - `WORKLOG`
   - `TASK_LOCKS`
   - `NEXT_TASK`
   - `pre-pr PASS`
   - commit/push

## 2A) 3 Ajan Surekli Calisma Orkestrasyonu

Varsayilan paket dagitimi:
1. codex-a -> UI / frontend / preview odakli ayrik ekran veya blade/css kapsamı
2. codex-b -> data-fixture / content / admin veri akisi / migration-disindan veri hazirlama
3. codex-c -> test-ops / servis-hardening / smoke-dogrulama / risk kapatma

Kurallar:
1. Bu dagitim sabit kisilik degil, varsayilan paket modelidir; koordinator ihtiyaca gore yer degistirebilir.
2. Ayni anda en fazla 3 aktif task acilir ve her paket yalniz ayrik lock alaniyla calisir.
3. Ortak koordinasyon dosyalari koordinator lock'unda kalir; ajanlara dagitilmaz.
4. Tek bir is ajanlara bolunecekse once kontratlar yazilir: owner, hedef dosya, kabul kaniti, kapanis kaniti.
5. UI + fixture + test-ops ayni hedefe hizmet ediyorsa once UI iskeleti, sonra fixture, sonra test-ops sirasi tercih edilir.

## 3) Task Karar Kurali

1. Hedef ve kabul kriteri ayniysa:
   - mevcut task devam eder.
2. Hedef ayni, yeni dosya veya yeni lock alani gerekiyorsa:
   - task genisletilir.
3. Yeni kabul kriteri, yeni risk sinifi, ayri owner veya bagimsiz teslim gerekiyorsa:
   - yeni task acilir.
4. Repo genelinde 3 aktif task siniri asilmiyorsa dagitim yapilir.
5. Ortak koordinasyon dosyalari gerekiyorsa koordinator sahipligi korunur.

## 4) Ajan Durum Panosu Standardi

Her ajan icin su alanlar tek raporda gorunur:
1. ajan adi
2. worktree path
3. aktif branch
4. aktif task
5. git status kisa ozeti
6. upstream durumu
7. stale aday durumu
8. preview/source eslesmesi varsa onun ozeti

Standart cikti kaynagi:
1. `powershell -ExecutionPolicy Bypass -File scripts/agent-status.ps1`

## 5) Session Handoff Standardi

`docs/SESSION_HANDOFF_TR.md` su alanlari tasir:
1. guncelleme zamani
2. aktif tasklar
3. aktif ajanlar ve branchleri
4. stale worktree ozetleri
5. preview/source eslesmeleri
6. bugun alinan kararlar
7. acik riskler
8. sonraki adim

Kural:
1. Koordinator yeni ise baslamadan once bu dosyayi gunceller veya dogrular.
2. Handoff dosyasi sohbet ozetinin yerine degil, resmi operasyon hafizasinin merkezi olarak kullanilir.

## 6) Stale Worktree Kurali

Asagidaki durumlardan biri varsa worktree `stale aday` olarak raporlanir:
1. aktif task yokken kirli status var
2. branch `main` iken yerel degisiklik var
3. branch ile `TASK_LOCKS` aktif kaydi uyusmuyor
4. task kapali oldugu halde worktree kirli kalmis

Kural:
1. Stale worktree temizligi ayni taskta sessizce yapilmaz.
2. Once gorunurluk ve handoff dosyasina islenir.
3. Temizlik veya devralma gerekiyorsa ayri task veya koordinator karari ile ilerlenir.
4. Yikici komut (`reset`, `clean`, `checkout --`, toplu `restore`) uygulanmadan once karar sinifi yazili hale getirilir.

## 6A) Stale Karar Siniflari

Her stale aday su siniflardan tam biri ile etiketlenir:
1. `koru`
   - Worktree icinde potansiyel deger tasiyan ama aktif task kaydi olmayan is vardir.
   - Once owner, branch ve kapsam handoff kaydina yazilir; sessiz cleanup yoktur.
2. `devral`
   - Koordinator, bloke edici veya sahipsiz stale kapsam icin resmi task acar.
   - Eski durum handoff ve lock kaydinda korunur; devralinan kapsam minimum tutulur.
3. `temizle`
   - Degisimler task-disi, tekrar uretilebilir veya kanitli sekilde satir-sonu/encoding drift'idir.
   - Cleanup ancak resmi kayit ve kanit sonrasi uygulanir.

## 6B) Stale Karar Akisi

Koordinator stale aday gordugunde su sirayi izler:
1. `scripts/agent-status.ps1 -Detailed` raporunu alir.
2. Su kanit paketini toplar:
   - worktree path
   - branch ve upstream
   - `git status --short` veya status sayisi
   - temsilci dosya/path listesi
   - aktif task uyusmazligi varsa onun notu
3. Adayi `koru | devral | temizle` olarak siniflar.
4. Karari `docs/SESSION_HANDOFF_TR.md` icine yazar.
5. Yalniz bundan sonra yeni task acar veya cleanup/devralma karari uygular.

## 6C) Destructive Cleanup Guvenceleri

1. Baska ajanin worktree'sinde yikici temizlik, ayri task veya acik koordinator karari olmadan uygulanmaz.
2. `main` uzerinde kirli stale worktree gorulurse varsayilan karar `temizle` degil, once `koru` veya `devral` degerlendirmesidir.
3. Icerik farki yok, yalniz satir-sonu/encoding drift'i varsa koordinator bunu kanitlayip kendi worktree'sinde temizleyebilir.
4. `git diff` icerik farki tasiyan stale worktree'de cleanup karari owner/handoff karari olmadan verilmez.
5. Cleanup sonrasi ayni task icinde tekrar `agent-status` raporu alinip handoff dosyasina sonuc yazilir.

## 7) Stop Kurallari

1. `SESSION_HANDOFF_TR.md` guncel degil ve ajan durum panosu okunmadiysa koordinator dagitim kararini bekletir.
2. Stale aday worktree yeni gorev alacaksa once rapor, sonra karar alinir.
3. Ortak operasyon dosyalarinda paralel yazim yoktur.
4. Stale worktree icin karar sinifi yazilmadan cleanup/devralma uygulanmaz.

## 7A) Paralel Task Secim Kurali

Koordinator yeni isi ajanlara bolmeden once su sirayla filtreler:
1. Hedef tek owner ile bitebiliyorsa dagitmaz, tek taskta tutar.
2. Ayri lock alanlari varsa paketlere ayirir.
3. Ortak entegrasyon dosyalari varsa ayrik ajanlara vermez; koordinator tutar.
4. Her alt is teslimi kendi kaniti ile gelir; entegrasyon kaniti ayrica koordinator tarafinda uretilir.
5. Lock cakismasi veya overlap sinyali varsa task acma durur.

## 8) Kapanis

Bir operasyon gorevi ancak su durumda kapanir:
1. task karti gercek sonuca gore dolu
2. `SESSION_HANDOFF_TR.md` ve gerekiyorsa `agent-status` raporu guncel
3. `WORKLOG`, `TASK_LOCKS`, `NEXT_TASK` guncel
4. `pre-pr PASS`
5. commit/push tamam

