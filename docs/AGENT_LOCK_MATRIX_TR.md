# Agent Lock Matrix (TR)

Tarih: 2026-03-16

Amac:
1. Ayni repo uzerinde paralel calisan ajanlar icin lock cakismasini sifira indirmek.
2. Lock alma sirasini ve karar agacini tek bir referansta toplamak.
3. Koordinator devralma kosullarini yoruma acik olmayacak sekilde netlestirmek.

## 1) Temel Ilkeler

1. Her gorev tam olarak bir `task_id` ile temsil edilir.
2. Her `task_id` icin `docs/TASK_LOCKS.md` tablosunda tam olarak bir satir bulunur.
3. Bir dosya veya wildcard deseni ayni anda sadece bir `active` satirda yer alabilir.
4. Lock acilmadan degisiklik yapilmaz; lock kapanmadan gorev tamamlandi sayilmaz.
5. Ajan sadece kendi branch'i ve kendi worktree klasoru icinde calisir.
6. Koordinator (`codex`) yalnizca operasyonel dokuman, stale lock temizligi veya resmi devralma icin dosya locklar.
7. `scripts/agent-status.ps1` salt-okuma gorunurluk araci oldugu icin lock gerektirmez.

## 2) Cakismasiz Lock Matrisi

| Durum | Ornek | Karar | Gerekce |
|---|---|---|---|
| Ayni dosya, iki aktif gorev | `docs/TASK_LOCKS.md` ikinci kez locklanmak isteniyor | Yasak | Tek dosyada eszamanli yazim deterministic degildir |
| Ayni klasor wildcard'i, alt dosya lock'u | `docs/**` aktifken `docs/AGENT_LOCK_MATRIX_TR.md` isteniyor | Yasak | Genis wildcard alt dosyalari kapsar |
| Iki wildcard biri digerini kapsiyor | `tests/**` ve `tests/Feature/**` | Yasak | Kesisim var, merge sirasinda sahiplik belirsiz |
| Ayrik dosyalar | `docs/A.md` ve `docs/B.md` | Serbest | Kesisim yok |
| Ayrik klasorler | `apps/orkestram/**` ve `apps/izmirorkestra/**` | Serbest | Fiziksel calisma alani ayrik |
| Salt okuma ihtiyaci | Ajan sadece inceleme yapiyor | Lock gerekmez | Yazma yoksa sahiplik yok |
| Ajan durum panosu raporu | `scripts/agent-status.ps1` calisiyor | Serbest | Salt okuma gorunurlugu merkezi raporlar |
| Ortak operasyon dosyasi | `docs/TASK_LOCKS.md` | Sadece aktif lock acan ajan veya koordinator yazar | Tablo tek kaynak kabul edilir |
| Kapanis kaniti eksik gorev | Kod tamam ama `pre-pr` kaniti yok | Lock `active` kalir | Gorev bitse bile resmi kapanis yok |

## 3) Lock Alma Karar Agaci

1. Degistirmek istedigin dosya veya wildcard desenlerini kesin listele.
2. `docs/TASK_LOCKS.md` icinde `status = active` satirlarini kontrol et.
3. Su sorulardan biri `evet` ise yeni lock acma:
   - Ayni dosya zaten aktif mi?
   - Talep ettigin desen mevcut aktif desenle kesisiyor mu?
   - Ortak operasyon dosyasina ayni anda ikinci yazici mi oluyorsun?
4. Kesisim yoksa kendi branch'inde yeni `active` satiri ac.
5. Gorev kapsami buyurse eski satiri sessizce genisletme:
   - Mevcut lock'u `closed` yap.
   - Yeni kapsam icin yeni `task_id` veya koordinator onayli yeni satir ac.
6. Gorev tamamlaninca kapanis kanitlarini topla.
7. Kanitlar tam ise ayni satiri `closed` yap ve not alanina ozet yaz.

## 4) Koordinator Devralma Kurallari

Koordinatorun devralma yapabilecegi durumlar:
1. Ajan 24 saatten uzun suredir `active` kalmis ve guncel kanit birakmamis.
2. Ajan branch'i yok, yanlis branch'te veya yanlis worktree'de calismis.
3. Gorev dosyasi kayip ama lock satiri aktif kalmis.
4. Cakisan iki lock acilmis ve sahiplik tek turda duzeltilmek zorunda.
5. Merge veya release'i bloke eden operasyonel dokuman tutarsizligi var.

Koordinator devralma akisi:
1. Mevcut lock satirini silmez; not alaninda devralma nedenini yazar.
2. Eski satiri `closed` yapar ve `updated_at` alanini gunceller.
3. Gerekirse yeni bir koordinator satiri acar:
   - `agent/codex/<task-id>`
4. Devralinan kapsam disina tasmaz; sadece bloke eden dosyalari locklar.
5. Is bitince kapanis kanitini veya neden kanit alinamadigini not eder.

## 4A) Stale Cleanup / Koruma / Devralma Matrisi

| Durum | Karar | Owner | Zorunlu Kanit |
|---|---|---|---|
| Aktif task yok, kirli branch var, icerik degeri belirsiz | `koru` | Koordinator kaydi acar, owner daha sonra netlesir | worktree path, branch, upstream, status sayisi, temsilci dosyalar |
| `main` uzerinde kirli worktree | `koru` veya `devral` | Koordinator | handoff kaydi, neden `main`de kaldigi, yeni task gerekip gerekmedigi |
| Task kapali, yalniz satir-sonu/encoding drift'i var | `temizle` | Koordinator veya ayni owner | `git diff` kaniti, drift sinifi, cleanup sonrasi temiz status |
| Sahipsiz stale kapsam release/merge blokaji uretiyor | `devral` | Koordinator | bloke nedeni, eski owner/task notu, yeni task kaydi |
| Aktif ajan hala duzenli ilerleme notu veriyor | cleanup/devralma yok | Mevcut owner | mevcut aktif lock ve ilerleme kaniti |

Kurallar:
1. `temizle` karari resmi kayit olmadan uygulanmaz.
2. `koru` sinifi potansiyel is kaybini onleme sinifidir; varsayilan guvenli karar budur.
3. `devral` yalniz bloke kaldiran minimum kapsama iner; stale worktree'deki tum dosyalar otomatik koordinator lock'una alinmaz.

## 4B) Destructive Cleanup Kisitlari

1. `git reset --hard`, `git clean`, `git checkout --`, toplu `git restore` gibi komutlar yalniz resmi stale cleanup karari uzerinden uygulanir.
2. Baska ajan worktree'sinde destructive cleanup oncesi `SESSION_HANDOFF_TR.md` kaydi zorunludur.
3. Cleanup komutu uygulanacaksa once temsilci diff kaniti alinmis olmalidir.
4. Cleanup sonrasi `git status --short` ve gerekiyorsa `scripts/agent-status.ps1 -Detailed` yeniden kayda alinir.
5. Kanit paketi yoksa lock kapanmaz ve cleanup tamamlandi sayilmaz.

## 5) Koordinatorun Devralamayacagi Durumlar

1. Aktif ajan duzenli ilerleme notu veriyor ve lock cakismasi yoksa.
2. Sadece hiz kazanmak icin baska ajanin kapsamindaki dosyaya girmek isteniyorsa.
3. Kullanici ayni isi baska ajana resmen devretmemisse.
4. `pre-pr` FAIL iken gorevi kapatmak icin kanit atlamak isteniyorsa.

## 6) Not Yazim Standardi

`note` alani su uc siniftan birine girmelidir:
1. Isin kapsami: ne yapiliyor.
2. Durum: neden hala `active`.
3. Kapanis kaniti: hangi commit, hangi gate, hangi devralma.

Belirsiz notlar kullanilmaz:
1. `bakiliyor`
2. `duzeltildi`
3. `sonra bak`

## 7) Hizli Uygulama Ozet Kurali

1. Lock acmadan once kapsam sabitlenir.
2. Kesisim varsa ajan durur; lock sahibini veya koordinatoru bekler.
3. Kapanis kaniti yoksa lock kapanmaz.
4. Devralma varsa tek kaynak `docs/TASK_LOCKS.md` not alanidir.
