# Agent Delivery Checklist (TR)

Amac: Ajan tesliminde dogrulama sirasi, paylasilacak kanit formati ve yari kesilen isin resume protokolunu tek yerde standardize etmek.

## 1. Goreve Baslamadan Once
1. `AGENTS.md`, `docs/REPO_DISCIPLINE_TR.md`, `docs/MULTI_AGENT_RULES_TR.md` ve `docs/SESSION_HANDOFF_TR.md` oku.
2. `git fetch --all --prune` calistir.
3. Sadece kendi branch'inle ilerle:
   - `agent/<ajan>/<task-id>`
4. `docs/TASK_LOCKS.md` icine tek bir `active` kayit ac.
5. Lock'a yazilmayan dosyalara dokunma.
6. `git remote -v` ile `origin`in GitHub oldugunu, local WSL referansi varsa `canonical` olarak ayrildigini dogrula.
7. Ayni kapsam revizesi gerekiyorsa yeni task acma; once mevcut taskin devam edip etmeyecegini kontrol et.
8. Hedef ayni kalip yeni dosya gerekiyorsa bunu `task genisletme` olarak task kartina ve lock listesine isle.

## 2. Runtime Kisa Hijyen Checklisti
Her teslim ve smoke turundan once su 4 kontrol yapilir:
1. Container up:
   - uygulama stack'i calisiyor olmalidir.
2. Mount source dogru:
   - aktif kaynak `WSL: /home/bekir/orkestram*` altindan gelmelidir.
3. Portlar cevap veriyor:
   - `8180`, `8181`, `8188`
4. Smoke PASS:
   - gorev kapsamindaki hizli smoke veya `validate/pre-pr` icindeki smoke adimi basarili olmalidir.

## 3. Validate ve Pre-PR Sirasi
Bu sira bozulmaz:

1. Gorev kapsamindaki degisiklikleri tamamla.
2. Varsa goreve ozel smoke/test/dokuman guncellemesini bitir.
3. `validate` calistir:
   - `powershell -ExecutionPolicy Bypass -File scripts/validate.ps1 -App both`
4. `validate` PASS ise `pre-pr` quick calistir:
   - `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick`
5. `pre-pr` PASS degilse teslim, commit ve push yok.

Not:
- `pre-pr`, `validate` yerine gecmez; `validate` once gelir.
- Ortamda `powershell` veya `pwsh` yoksa bu durum teslim notunda acikca yazilir.

## 4. Teslim Formati
Teslim mesaji kisa ve kanit odakli olur. Asgari format:

1. Yapilan degisiklik ozeti
2. Etkilenen dosya(lar)
3. Dogrulama sonucu
4. Zorunlu 4 kanit
5. Task kartindaki checklistlerin gercek sonuca gore guncellendigi notu
6. Kapanis adimlarinin tamamlandigi notu
7. worktree durumunun ve gerekiyorsa stale aday notunun paylasildigi ozet

Zorunlu 4 kanit aynen paylasilir:

```text
git branch --show-current
git branch -vv
git status --short
powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick
```

Beklenen yorumlama:
- Ilk cikti aktif branch'in `agent/<ajan>/<task-id>` oldugunu gostermeli.
- Ikinci cikti upstream'in `origin/<branch>` oldugunu gostermeli; `[gone]` gorunuyorsa `git push -u origin <branch>` ile GitHub branch'i olusturulmalidir.
- Ucuncu cikti sadece gorev kapsamindaki degisiklikleri gostermeli.
- Dorduncu cikti `PASS` vermeli; `powershell` veya `pwsh` yoksa "calistirilamadi" notu dusulmeli.

Koordinator kapanisi yapacaksa ajan lock'u kendi basina `closed` yapmaz; once kaniti verir ve devir notunu birakir.

## 4A. Task Karti Doldurma Zorunlulugu
Task sahibi teslimden once task kartinda su 4 bolumu gercek sonuca gore gunceller:
1. `Uygulama Adimlari`
2. `Kabul Kriterleri`
3. `Teslimde Zorunlu Kanit`
4. `Kapanis Adimlari`

Kural:
1. Yapilmayan madde isaretlenmez.
2. Kanitsiz madde isaretlenmez.
3. Task karti placeholder birakilmaz.
4. Bu alanlar doldurulmadan gorev teslim edilmis sayilmaz.
5. `Task Karari` bolumunde yalniz tek secim isaretlenir: `mevcut task devam`, `task genisletme` veya `yeni task`.

## 4B. UI / Tasarim Teslim Ek Kaniti
UI gorevlerinde zorunlu ek teslim paketi:
1. `Lane`
2. `Preview URL`
3. `Mount Source`
4. `worktree path`
5. manuel UI review ozeti
6. `UI Review Durumu` (`pending`, `revize`, `approved`)

Kural:
1. UI gorevi `design-preview` gorulmeden merge'e gitmez.
2. `main` preview tasarim review araci olarak kullanilmaz.
3. Preview URL gorev kartinda yazmiyorsa teslim eksik sayilir.
4. Kapsam ayni ise revize ayni taskta devam eder; yeni task acilmaz.
5. Kullanici `approved` demeden UI gorevinin merge teslimi tamamlanmis sayilmaz.

## 5. Resume Protokolu
Bir is yarida kaldiysa veya ajan yeniden baglandiysa:

1. Zorunlu dokumanlari tekrar oku:
   - `AGENTS.md`
   - `docs/REPO_DISCIPLINE_TR.md`
   - `docs/MULTI_AGENT_RULES_TR.md`
2. Su 3 kontrolu yap:
   - `git branch --show-current`
   - `git status --short`
   - `docs/TASK_LOCKS.md` icinde gorevin hala sana ait ve `active` oldugunu dogrula
3. Lock baska ajan uzerindeyse veya branch farkliysa calismayi durdur.
4. Kaldigin noktayi tek satir notla guncelle:
   - hedef
   - kalan is
   - blocker varsa blocker
5. Sonra yalniz lock'taki dosyalarda devam et.

## 6. Handoff Notu
Koordinator veya baska ajan devralacaksa son mesajda su net olur:

1. Tamamlanan kisim
2. Acik kalan kisim
3. Son calisan komut/dogrulama
4. Branch ve lock durumu
5. worktree status ozeti ve stale aday bilgisi

Bu bilgi yoksa is resume icin hazir sayilmaz.

## 6A. Koordinator Kabul Kurali
Koordinator asagidaki eksiklerden biri varsa teslimi reddeder:
1. Task karti checklistleri doldurulmamis
2. `docs/WORKLOG.md` guncellenmemis
3. `docs/TASK_LOCKS.md` kapanis durumu islenmemis
4. `docs/NEXT_TASK.md` guncellenmemis
5. Zorunlu kanit paketi eksik
6. `pre-pr` PASS yok

## 7. Koordinator Ilk Karar Mesaji (Sabit Sablon)
Koordinator yeni iste ilk karar mesajini su sabit formatla verir:
1. `aktif branch: ...`
2. `aktif task durumu: ...`
3. `karar: mevcut task devam | yeni task ac`
4. `sonraki adim: ...`

Not:
- Bu format disina cikilmaz.
- `aktif task durumu` satiri lock durumunu da icerir.

Kural (Edit Source Esitligi):
1. UI tesliminde `Edit Source` ile `Mount Source` ayni worktree/path degilse teslim reddedilir.
2. Ajan farkli source'ta kod degistirip baska source preview'u veremez.
