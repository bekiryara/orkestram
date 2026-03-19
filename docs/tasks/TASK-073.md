# TASK-073

Durum: `DONE`  
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
- [x] 3 ajan surekli calisma paketi ve paralel task secim modeli dokumanlastirilacak
- [x] `start-task.ps1` icine aktif lock overlap otomatik kontrolu eklenecek
- [x] Ilgili operasyon dokumanlari yeni orkestrasyon ve overlap kapisina gore hizalanacak

## Out of Scope
- [x] Urun/runtime kodunu degistirmek
- [x] Demo fixture standardini bu taskta tamamlamak
- [x] Merge sonrasi preview/runtime lifecycle maddelerini bu taskta kapatmak

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
- [x] Gorev kapsamindaki degisiklikler tamamlandi
- [x] Goreve ozel test/dogrulama calistirildi

## Kabul Kriterleri
- [x] 3 ajan surekli calisma orkestrasyonu, paralel task secimi ve kontrat bazli dagitim kurali resmi dokumana girer
- [x] `start-task.ps1`, aktif locklarla kesisen yeni dosya/wildcard taleplerini task acilmadan reddeder
- [x] Repo disiplini ve lock matrix overlap kapisini acikca referanslar
- [x] `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick` PASS

## Teslimde Zorunlu Kanit
- [x] `git branch --show-current`
- [x] `git branch -vv`
- [x] `git status --short`
- [x] `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick`
- [x] Goreve ozel test/komut sonucu: `start-task.ps1` parse PASS; kopya smoke senaryosunda overlap case `FAIL`, non-overlap case `OK`
- [x] `Edit Source == Mount Source` kaniti
- [x] Commit hash

## Kapanis Adimlari
- [x] Task kartindaki checklistler gercek sonuca gore guncellendi
- [x] `docs/WORKLOG.md` guncellendi
- [x] `docs/TASK_LOCKS.md` kaydi `closed` yapildi
- [x] `docs/NEXT_TASK.md` panosu guncellendi
- [x] Branch pushlandi

## Komutlar
```powershell
powershell -NoProfile -Command "[void][scriptblock]::Create((Get-Content 'scripts/start-task.ps1' -Raw)); 'PARSE_OK'"
powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick
```

## Risk / Not
- Overlap kapisi koordinasyon dosyalarini haric tutacak sekilde yazildi; aksi durumda paralel task acma akisi gereksiz sert blokaja girerdi.


