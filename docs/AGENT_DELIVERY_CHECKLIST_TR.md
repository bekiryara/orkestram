# Agent Delivery Checklist (TR)

Amac: Ajan tesliminde dogrulama sirasi, paylasilacak kanit formati ve yari kesilen isin resume protokolunu tek yerde standardize etmek.

## 1. Goreve Baslamadan Once
1. `AGENTS.md`, `docs/REPO_DISCIPLINE_TR.md` ve `docs/MULTI_AGENT_RULES_TR.md` oku.
2. `git fetch --all --prune` calistir.
3. Sadece kendi branch'inle ilerle:
   - `agent/<ajan>/<task-id>`
4. `docs/TASK_LOCKS.md` icine tek bir `active` kayit ac.
5. Lock'a yazilmayan dosyalara dokunma.

## 2. Validate ve Pre-PR Sirasi
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

## 3. Teslim Formati
Teslim mesaji kisa ve kanit odakli olur. Asgari format:

1. Yapilan degisiklik ozeti
2. Etkilenen dosya(lar)
3. Dogrulama sonucu
4. Zorunlu 3 kanit

Zorunlu 3 kanit aynen paylasilir:

```text
git branch --show-current
git status --short
powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick
```

Beklenen yorumlama:
- Ilk cikti aktif branch'in `agent/<ajan>/<task-id>` oldugunu gostermeli.
- Ikinci cikti sadece gorev kapsamindaki degisiklikleri gostermeli.
- Ucuncu cikti `PASS` vermeli; `powershell` veya `pwsh` yoksa "calistirilamadi" notu dusulmeli.

Koordinator kapanisi yapacaksa ajan lock'u kendi basina `closed` yapmaz; once kaniti verir ve devir notunu birakir.

## 4. Resume Protokolu
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

## 5. Handoff Notu
Koordinator veya baska ajan devralacaksa son mesajda su net olur:

1. Tamamlanan kisim
2. Acik kalan kisim
3. Son calisan komut/dogrulama
4. Branch ve lock durumu

Bu bilgi yoksa is resume icin hazir sayilmaz.
