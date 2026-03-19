# TASK-073

Durum: `DOING`  
Ajan: `codex`  
Branch: `agent/codex/task-073`  
Baslangic: `2026-03-19 05:41`

## Gorev Ozeti
- 3 ajan surekli calisma orkestrasyonu ve lock overlap otomatik kontrolu sertlestirilecek

## Task Karari
- [ ] mevcut task devam
- [ ] task genisletme
- [x] yeni task

## In Scope
- [ ] 3 ajan surekli calisma paketi ve paralel task secim modeli dokumanlastirilacak
- [ ] `start-task.ps1` icine aktif lock overlap otomatik kontrolu eklenecek
- [ ] Ilgili operasyon dokumanlari yeni orkestrasyon ve overlap kapisina gore hizalanacak

## Out of Scope
- [ ] Urun/runtime kodunu degistirmek
- [ ] Demo fixture standardini bu taskta tamamlamak
- [ ] Merge sonrasi preview/runtime lifecycle maddelerini bu taskta kapatmak

## Lock Dosyalari
- `docs/tasks/TASK-073.md`
- `docs/TASK_LOCKS.md`
- `docs/NEXT_TASK.md`
- `docs/OPERATING_MODEL_TR.md`
- `docs/MULTI_AGENT_RULES_TR.md`
- `docs/REPO_DISCIPLINE_TR.md`
- `docs/AGENT_LOCK_MATRIX_TR.md`
- `docs/SESSION_HANDOFF_TR.md`
- `scripts/start-task.ps1`

## Preview Kontrati
- Lane: `n/a`
- Preview URL: `n/a`
- Mount Source: `n/a`
- Edit Source: `n/a`
- UI review gerekir mi?: `no`
- UI Review Durumu: `n/a`
- Revize Notu: `n/a`

## Uygulama Adimlari
- [x] Zorunlu dokumanlar okundu: `AGENTS.md`, `docs/REPO_DISCIPLINE_TR.md`, `docs/MULTI_AGENT_RULES_TR.md`
- [x] Branch dogrulandi: `agent/<ajan>/<task-id>`
- [x] Lock kapsam disina cikilmadi
- [ ] Gorev kapsamindaki degisiklikler tamamlandi
- [ ] Goreve ozel test/dogrulama calistirildi

## Kabul Kriterleri
- [ ] 3 ajan surekli calisma orkestrasyonu, paralel task secimi ve kontrat bazli dagitim kurali resmi dokumana girer
- [ ] `start-task.ps1`, aktif locklarla kesisen yeni dosya/wildcard taleplerini task acilmadan reddeder
- [ ] Repo disiplini ve lock matrix overlap kapisini acikca referanslar
- [ ] `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick` PASS

## Teslimde Zorunlu Kanit
- [ ] `git branch --show-current`
- [ ] `git branch -vv`
- [ ] `git status --short`
- [ ] `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick`
- [ ] Goreve ozel test/komut sonucu
- [ ] `Edit Source == Mount Source` kaniti
- [ ] Commit hash

## Kapanis Adimlari
- [ ] Task kartindaki checklistler gercek sonuca gore guncellendi
- [ ] `docs/WORKLOG.md` guncellendi
- [ ] `docs/TASK_LOCKS.md` kaydi `closed` yapildi
- [ ] `docs/NEXT_TASK.md` panosu guncellendi
- [ ] Branch pushlandi

## Komutlar
```powershell
powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick
powershell -ExecutionPolicy Bypass -File scripts/close-task.ps1 -TaskId TASK-073 -Agent codex -ClosureNote "kisa kapanis ozeti" -WorklogTitle "baslik" -WorklogSummary "madde-1" -Files "dosya-1" -Commands "komut-1" -Result PASS
```

## Risk / Not
- En buyuk risk ortak koordinasyon dosyalarinda gereksiz sert blokaj uretmek; overlap kapisi koordinasyon dosyalarini degil gercek hedef lock alanlarini esas alacak.
