# PR Akisi (TR)

Amac: Main branch'e sadece kontrollu, testli ve izlenebilir degisim gitsin.

## Standart Akis
1. Gorev sec:
   - `docs/NEXT_TASK.md`
2. Branch ac:
   - `git checkout -b agent/<ajan>/<task-id>`
3. Remote hizasini dogrula:
   - `git remote -v`
   - `git branch -vv`
   - Beklenen: `origin = https://github.com/bekiryara/orkestram.git`
   - Worktree'de local WSL repo gerekiyorsa `canonical = /home/bekir/orkestram`
3. Degisiklik yap + commit
4. PR oncesi zorunlu:
   - `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick`
5. Push:
   - `git push -u origin agent/<ajan>/<task-id>`
6. GitHub PR ac:
   - base: `main`, compare: `agent/<ajan>/<task-id>`
7. `ci-gate` PASS olmadan merge yok
8. Merge sonrasi:
   - `docs/WORKLOG.md` ve `docs/PROJECT_STATUS_TR.md` guncelle

## Owner Branch PR / Merge Hazirlik Standardi
1. Owner ajan teslimi tek basina merge izni vermez; once koordinator teslim kanitini kontrol eder.
2. Bir owner branch ancak su kosullarda `PR hazir` sayilir:
   - branch `origin/agent/<ajan>/<task-id>` upstream'ine bagli
   - `git status --short` temiz
   - `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick` -> `PASS`
   - task kartinda scope ve kabul kriterleri gercek sonuca gore doldurulmus
3. Bir owner branch ancak su kosullarda `merge hazir` sayilir:
   - `PR hazir` durumu alinmis
   - koordinator owner branch commit hash'ini merkezi kayitlara islemis
   - ayni turda merge edilecek diger owner branch'lerle sira karari verilmis
4. `TASK-074` ve `TASK-075` gibi bagimsiz owner branch'ler icin varsayilan sira:
   - once `PR hazir`
   - sonra koordinator siralama karari
   - sonra kontrollu merge
5. Koordinator owner branch yerine commit, rebase veya icerik duzenlemesi yapmaz; yalniz hazirlik ve siralama kararini yazar.

## Koordinator Merge Siralama Checklisti
1. Hangi owner branch'in merge edilmeye aday oldugu task id ve commit hash ile yazilir.
2. Merge sirasina alinacak branch icin su kanit paketi tek yerde gorunur:
   - `git branch --show-current`
   - `git branch -vv`
   - `git status --short`
   - `pre-pr PASS`
   - commit hash
3. Ayni anda birden fazla owner branch hazirsa su sira kullanilir:
   - bagimlilik veya operasyonel blokaj olusturan once
   - bagimsiz belge/standart teslimleri sonra
4. Koordinator `docs/NEXT_TASK.md` ve `docs/SESSION_HANDOFF_TR.md` icinde owner branch durumunu su etiketlerden biriyle yazar:
   - `hazir degil`
   - `PR hazir`
   - `merge hazir`
   - `merge edildi`
5. Bu etiketlerden biri yoksa branch review/merge kuyruguna alinmis sayilmaz.

## Mevcut Merge Sirasi Ornegi (2026-03-19)
1. `TASK-074` / `agent/codex-b/task-074` durumu: `PR hazir`
   - commit: `4f95fa0`
   - gerekce: preview/runtime lifecycle standardi genel operasyon kuralidir; takip eden owner teslimleri once bu kurala dayanmalidir.
2. `TASK-075` / `agent/codex-c/task-075` durumu: `PR hazir`
   - commit: `8351cba`
   - gerekce: fixture standardi bagimsizdir ancak varsayilan merge sirasi olarak `TASK-074` sonrasina konur.
3. Bu ornekte iki branch de `merge hazir` degil, yalniz `PR hazir` durumundadir; merge karari ayrik koordinator turunde verilir.

## Merge Taski Acma Kurali
1. Merge taski varsayilan akış degildir; mumkunse uretim taski icinde PR ve merge kapanir.
2. Ayrı merge taski ancak su durumlarda acilir:
   - birden fazla owner branch icin sira karari gerekiyorsa
   - merge sonrasi runtime/preview etkisi ayri operasyon takibi gerektiriyorsa
   - merkezi kayit, onay veya rollback riski yuksekse
   - merge uygulamasi yeni kabul kriteri ve ayrik owner sorumlulugu doguruyorsa
3. Su durumlarda ayrı merge taski acilmaz:
   - tek owner branch var ve teslim kaniti tamamsa
   - baska branch ile sira bagimliligi yoksa
   - merge sonrasi ek operasyon riski olusmuyorsa
   - ayni task icinde PR, merge ve kapanis izlenebilir sekilde tamamlanabiliyorsa
4. Koordinator yeni merge taski acmadan once tek soruyu yanitlar:
   - `Bu merge yeni bir operasyon riski veya yeni kabul kriteri doguruyor mu?`
5. Cevap `hayir` ise merge ayrik task yerine mevcut taskin kapanis akisinda ele alinir.
6. Cevap `evet` ise merge icin yeni task acilir ve nedeni task kartinda acik yazilir.
## Pull/Fetch Kurali
1. Gunluk senkron:
   - `git fetch origin --prune`
2. Kendi branch'in guncellenecekse:
   - `git pull --ff-only origin agent/<ajan>/<task-id>`
3. `main` ile hizalama gerekiyorsa:
   - `git fetch origin --prune`
   - `git merge --ff-only origin/main` veya ekip kararina uygun kontrollu rebase
4. `windows-mirror` remote'u ile pull/push yapilmaz.

## Kapanis Kaniti Zorunlu (Yeni)
1. Task `closed` isaretlemeden once su 3 kanit zorunludur:
   - `git branch --show-current`
   - `git status --short`
   - `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick` -> `PASS`
2. Bu kanitlardan biri eksikse:
   - Task lock satiri `active` kalir.
   - PR acilsa bile merge edilmez.
3. Koordinator, `docs/TASK_LOCKS.md` ve `docs/NEXT_TASK.md` uyumsuzlugunu ayni turda duzeltir.

## Yasaklar
1. Main'e direkt push
2. Test calistirmadan PR acma
3. PR template'i bos gecme

