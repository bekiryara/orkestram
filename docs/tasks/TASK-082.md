# TASK-082

Durum: `DOING`  
Ajan: `codex`  
Branch: `agent/codex/task-082`  
Baslangic: `2026-03-20 01:10`

## Gorev Ozeti
- Ortam guardrail kisa taski: runtime kontrati, readiness siniflari, shell/katman disiplini ve sandbox fallback kurallarini resmi hale getir

## Task Karari
- [ ] mevcut task devam
- [ ] task genisletme
- [x] yeni task

## In Scope
- [ ] `Edit Source`, `Mount Source`, `Runtime Source` ve `Preview Source` kontratini resmi kurala baglamak
- [ ] `validate` / `pre-pr` icin `ENV_BLOCKED`, `RUNTIME_BLOCKED`, `SANDBOX_BLOCKED` ve `CODE_FAIL` ayrimini netlestirmek
- [ ] Yanlis shell veya yanlis runtime katmaninda komut calistirma riskini kural ve script seviyesinde azaltmak
- [ ] Sandbox / `apply_patch` kirilmasinda tekrar deneme yerine durdurma ve fallback akisini resmi hale getirmek
- [ ] Urun tasklari sirasinda gereksiz `dev-up` veya runtime/source oynatma riskini koordinator kontrollu modele baglamak
- [ ] Yeni gelen ajanin ilk 5 dakikada sisteme hizalanmasi icin acik onboarding guardrail'leri eklemek
- [ ] PowerShell quoting ve Windows/WSL komut ayiraclarindan dogan yanlis komut riskini resmi kurala baglamak
- [ ] BOM / line-ending drift'in ne zaman icerik farki sayilacagi, ne zaman cleanup sinifi oldugu netlestirmek
- [ ] Upstream baglama sirasini task acilisi, commit ve `pre-pr` akisi icinde acik kurala baglamak
- [ ] WSL git credential/auth blokajini `read OK / write auth blocked` olarak ayristirip resmi ortamsal risk sinifina baglamak

## Out of Scope
- [ ] Yeni urun ozelligi gelistirmek
- [ ] Mevcut owner coverage parity akisini tekrar acmak
- [ ] UI preview/review veya urun ekrani degisikligi yapmak
- [ ] Runtime mount/source degisimi uygulamak

## Lock Dosyalari
- `docs/tasks/TASK-082.md`
- `docs/TASK_LOCKS.md`
- `docs/NEXT_TASK.md`
- `docs/SESSION_HANDOFF_TR.md`
- `AGENTS.md`
- `docs/REPO_DISCIPLINE_TR.md`
- `docs/MULTI_AGENT_RULES_TR.md`
- `docs/COORDINATOR_BOOTSTRAP_TR.md`
- `docs/AGENT_DELIVERY_CHECKLIST_TR.md`
- `docs/OPERATING_MODEL_TR.md`
- `docs/tasks/_TEMPLATE.md`
- `scripts/pre-pr.ps1`
- `scripts/validate.ps1`

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
- [ ] Task kartlari icin zorunlu runtime kontrati karari netlestirilir
- [ ] Koordinator ve ajanlar icin command execution layer matrisi resmi kurala baglanir
- [ ] `validate` ve `pre-pr` tarafinda ortam blokaji ile kod hatasi ayrimi netlestirilir
- [ ] Sandbox kirilmasinda tekrar deneme yerine durdurma/fallback akisi yazili hale gelir
- [ ] Yeni gelen ajanin ilk turda hizalanmasi icin onboarding guardrail'i resmi hale gelir
- [ ] PowerShell quoting, BOM/line-ending drift ve upstream baglama sirasi icin acik stop/fallback kurali yazilir
- [ ] WSL git credential/auth blokaji resmi readiness sinifi olarak tanimlanir
- [ ] `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick` PASS

## Teslimde Zorunlu Kanit
- [ ] `git branch --show-current`
- [ ] `git branch -vv`
- [ ] `git status --short`
- [ ] `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick`
- [ ] Goreve ozel test/komut sonucu
- [ ] `Edit Source == Mount Source` kaniti veya `n/a` gerekcesi
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
powershell -ExecutionPolicy Bypass -File scripts/close-task.ps1 -TaskId TASK-082 -Agent codex -ClosureNote "kisa kapanis ozeti" -WorklogTitle "baslik" -WorklogSummary "madde-1" -Files "dosya-1" -Commands "komut-1" -Result PASS
```

## Risk / Not
- Bu task urun gelistirme taski degildir; yalniz ortam guardrail standardizasyonu icindir.
- Koordinator bu turda yalniz task acilisi, lock ve devir planini yurutur; uygulama uygun ajan tarafinda tamamlanir.
- Varsayilan uygulayici ajan `codex-a` olarak planlanmistir; lock cakismasi veya worktree gorunurlugu degisirse koordinator owner kararini yeniden degerlendirir.
- Basit tasklarin bile bozuk/yarim cevre katmanlarina carpip uzadigi goruldugu icin bu task urun kodundan once ortam zincirini sertlestirmeyi hedefler.
