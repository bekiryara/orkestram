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
   - `docs/tasks/TASK-001.md` dosyasini `_TEMPLATE.md` uzerinden olustur.
   - `docs/TASK_LOCKS.md` tablosuna `active` kaydi ekle.
   - `git checkout -b agent/codex-a/task-001` ile branch ac.
2. Kodla + commit:
   - `feat(task-001): ...` veya `fix(task-001): ...`
3. PR ac:
   - `agent/<ajan>/<task-id>` -> `main`
4. Merge sonrasi lock kapat:
   - `docs/TASK_LOCKS.md` kaydinda durumu `closed` yap.

## Not
1. Ayni makinede 3 ajan calisacaksa farkli klasor/worktree onerilir.
2. Lock/Task kaydi olmayan is "resmi is" sayilmaz.

## Yeni Gelen Ajan Kurali (Zorunlu)
1. Ilk adimda `AGENTS.md` okunur.
2. Branch disiplini zorunlu: `agent/<ajan>/<task-id>`.
3. Lock almadan dosya degistirmek yasak.
4. Is bitiminde lock `closed` yapilmadan gorev kapatilmaz.
5. `pre-pr` PASS olmayan is commit/push edilmez.

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
- gent/codex-a/<task-id>
- hedef dosyalar
- kabul/kapanis kaniti:
  - git branch --show-current
  - git status --short
  - powershell.exe -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick

## Koordinator Stop Kurallari
Asagidaki durumlardan biri varsa koordinator isi durdurur:
1. active lock cakismasi
2. yanlis branch
3. WSL hizalama kaniti yok
4. aktif task kapanmadan yeni kapsam acilmaya calisiliyor

## Koordinator Kapanis Sonrasi Davranisi
Koordinator is bitince tek mesajda su 3 seyi verir:
1. is kapandi mi, kaldi mi
2. siradaki uygun adim
3. senden gereken tek karar veya yeni gorev

