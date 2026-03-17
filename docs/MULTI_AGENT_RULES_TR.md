# Multi-Agent Kurallari (TR)

Amac: Ayni anda birden fazla ajan calisirken cakismaz, izlenebilir ve deterministic akis.

## Zorunlu Kurallar
1. `main` branch'e direkt push yasak.
2. Her ajan sadece kendi branch'inde calisir:
   - `agent/<ajan>/<task-id>`
3. Her ajan tek aktif gorev alir.
4. Gorev almadan once lock acilir:
   - `docs/TASK_LOCKS.md`
5. Ayni dosya iki aktif gorevde locklanamaz.
6. PR acmadan once zorunlu komut:
   - `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick`
7. Merge kosulu:
   - required checks PASS (`ci-gate`, `security-gate`)
   - gorev owner onayi

## Is Akisi
1. Gorev baslat:
   - `docs/tasks/TASK-0xx.md` dosyasini `_TEMPLATE.md` uzerinden olustur.
   - `docs/TASK_LOCKS.md` tablosuna `active` kaydi ekle.
   - `docs/NEXT_TASK.md` panosunu aktif goreve cek.
   - branch acilisini en son yap: `git checkout -b agent/<ajan>/task-0xx`
2. Kodla + commit:
   - `feat(task-0xx): ...` veya `fix(task-0xx): ...`
3. PR ac:
   - `agent/<ajan>/<task-id>` -> `main`
4. Merge sonrasi lock kapat:
   - `docs/TASK_LOCKS.md` kaydinda durumu `closed` yap.
5. Task karti doldur:
   - `Uygulama Adimlari`, `Kabul Kriterleri`, `Teslimde Zorunlu Kanit`, `Kapanis Adimlari` placeholder kalmaz.

## Not
1. Ayni makinede 3 ajan calisacaksa farkli klasor/worktree onerilir.
2. Lock/Task kaydi olmayan is "resmi is" sayilmaz.

## Yeni Gelen Ajan Kurali (Zorunlu)
1. Ilk adimda `AGENTS.md` okunur.
2. Branch disiplini zorunlu: `agent/<ajan>/<task-id>`.
3. Lock almadan dosya degistirmek yasak.
4. Is bitiminde lock `closed` yapilmadan gorev kapatilmaz.
5. `pre-pr` PASS olmayan is commit/push edilmez.
6. Task karti checklistleri doldurulmadan teslim kabul edilmez.

## Baslangic Guard (Zorunlu)
1. Ajan D:\orkestram'da acilsa bile gelistirmeden once WSL hizalama kaniti verir:
   - `wsl -e bash -lc "cd /home/bekir/orkestram-<slot> && pwd && git rev-parse --show-toplevel && git branch --show-current && git status --short"`
2. Kanit `/home/bekir/orkestram-a|b|c` degilse `REALIGN_REQUIRED` raporlanir.
3. `REALIGN_REQUIRED` halinde ajan yalniz hizalama adimini uygular; kod/doc degisikliklerine gecmez.
4. Koordinator, lock acmadan once bu kaniti istemekle yukumludur.

## Koordinator Karar Agaci
Koordinator yeni is geldiginde su sirayla karar verir:
1. `docs/NEXT_TASK.md` ve `docs/TASK_LOCKS.md` aktif isi kapsiyor mu?
   - Evet: mevcut task devam eder.
   - Hayir: yeni task acilmasi gerekir.
2. Dosya/alan cakismasi var mi?
   - Evet: ajan dagitimi durur, once lock cozulur.
   - Hayir: dagitim yapilabilir.
3. Is ortak belge veya ortak entegrasyon alani mi?
   - Evet: koordinator lock'u kendi elinde tutar.
   - Hayir: ayrik ajan lock'larina boler.

## Koordinator Dagitim Formati
Koordinator ajan gorevi verirken su formati kullanir:
1. ajan
2. branch
3. hedef dosyalar
4. yapacagi is
5. kapanis kaniti

Ornek alanlar:
- `agent/codex-a/<task-id>`
- hedef dosyalar
- kabul/kapanis kaniti:
  - `git branch --show-current`
  - `git branch -vv`
  - `git status --short`
  - `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick` -> `PASS`
  - task karti checklistleri ve `WORKLOG` kapanisi

## Koordinator Stop Kurallari
Asagidaki durumlardan biri varsa koordinator isi durdurur:
1. active lock cakismasi
2. yanlis branch
3. WSL hizalama kaniti yok
4. aktif task kapanmadan yeni kapsam acilmaya calisiliyor
5. task karti checklistleri veya kapanis kaniti eksik

## Koordinator Kapanis Sonrasi Davranisi
Koordinator is bitince tek mesajda su 3 seyi verir:
1. is kapandi mi, kaldi mi
2. siradaki uygun adim
3. senden gereken tek karar veya yeni gorev

## Task ID Tekrar Yasaki (Zorunlu)
1. Ayni `TASK-XXX` kimligi ikinci kez acilamaz.
2. Yeni is her zaman yeni task id ile acilir.
3. Koordinator lock tablosu ve task dosyalarinda tekrar id gorurse isi durdurur.

## Koordinator Cevap Sablonu (Sabit)
Koordinator karar cevabi yalniz su 4 satirla verilir:
1. `aktif branch: ...`
2. `aktif task durumu: ...`
3. `karar: mevcut task devam | yeni task ac`
4. `sonraki adim: ...`

## Remote/Upstream Dogrulamasi (Zorunlu)
1. Gorev basinda `git remote -v` ve `git branch -vv` zorunludur.
2. `origin` GitHub degilse is baslatilmaz.
3. Aktif branch upstream'i `origin/<branch>` degilse push/PR adimina gecilmez.

## Design Preview Lane (Zorunlu UI Review Kurali)
1. UI/tasarim gorevleri merge oncesi `design-preview` lane'inde gorulmeden kapatilmaz.
2. `design-preview` tek sabit lane'dir; ayni anda yalniz bir UI gorevi review edilir.
3. Task kartinda su alanlar zorunludur:
   - `Preview URL`
   - `Mount Source`
   - `Lane`
   - `UI Review Durumu`
4. `main preview` yalniz merge edilmis dunyadir; tasarim review araci olarak kullanilmaz.
5. UI gorev tesliminde ajan su kaniti verir:
   - worktree path
   - mount source
   - preview URL
   - manuel UI kontrol ozeti

## UI Revize ve Merge Kurali (Zorunlu)
1. Kapsam ayni kaldigi surece begenilmeyen UI duzeltmeleri icin yeni task acilmaz; ayni taskta revize devam eder.
2. Yeni task ancak kapsam, lock dosyalari veya hedef ekran anlamli sekilde degisirse acilir.
3. UI gorevinde merge sirasi sabittir:
   - `design-preview`da goster
   - kullanici preview onayi verirse finalize et
   - `pre-pr` PASS al
   - push et
   - merge et
4. Kullanici preview onayi vermeden koordinator merge adimina gecmez.
5. `main`e merge, UI review araci degil sadece onaylanmis sonucu entegre etme adimidir.
