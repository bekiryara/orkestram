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

## 7) Stop Kurallari

1. `SESSION_HANDOFF_TR.md` guncel degil ve ajan durum panosu okunmadiysa koordinator dagitim kararini bekletir.
2. Stale aday worktree yeni gorev alacaksa once rapor, sonra karar alinir.
3. Ortak operasyon dosyalarinda paralel yazim yoktur.

## 8) Kapanis

Bir operasyon gorevi ancak su durumda kapanir:
1. task karti gercek sonuca gore dolu
2. `SESSION_HANDOFF_TR.md` ve gerekiyorsa `agent-status` raporu guncel
3. `WORKLOG`, `TASK_LOCKS`, `NEXT_TASK` guncel
4. `pre-pr PASS`
5. commit/push tamam
