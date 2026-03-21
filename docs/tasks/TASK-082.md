# TASK-082

Durum: `DONE`  
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
- [x] `Edit Source`, `Mount Source`, `Runtime Source` ve `Preview Source` kontratini resmi kurala baglamak
- [x] `validate` / `pre-pr` icin `ENV_BLOCKED`, `RUNTIME_BLOCKED`, `SANDBOX_BLOCKED` ve `CODE_FAIL` ayrimini netlestirmek
- [x] Yanlis shell veya yanlis runtime katmaninda komut calistirma riskini kural ve script seviyesinde azaltmak
- [x] Sandbox / `apply_patch` kirilmasinda tekrar deneme yerine durdurma ve fallback akisini resmi hale getirmek
- [x] Urun tasklari sirasinda gereksiz `dev-up` veya runtime/source oynatma riskini koordinator kontrollu modele baglamak
- [x] Yeni gelen ajanin ilk 5 dakikada sisteme hizalanmasi icin acik onboarding guardrail'leri eklemek
- [x] PowerShell quoting ve Windows/WSL komut ayiraclarindan dogan yanlis komut riskini resmi kurala baglamak
- [x] BOM / line-ending drift'in ne zaman icerik farki sayilacagi, ne zaman cleanup sinifi oldugu netlestirmek
- [x] Upstream baglama sirasini task acilisi, commit ve `pre-pr` akisi icinde acik kurala baglamak
- [x] WSL git credential/auth blokajini `read OK / write auth blocked` olarak ayristirip resmi ortamsal risk sinifina baglamak

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

## Runtime Kontrati
- Runtime Source: `n/a`
- Preview Source: `n/a`
- Git Katmani: `WSL`
- Script Katmani: `PowerShell`
- App/Test Katmani: `container`
- Runtime Readiness: `ready`
- Upstream Durumu: `origin/agent/codex/task-082`
- Not: `Bu task urun ekranini degil, ortam/disiplin zincirini hedefledi.`

## Uygulama Adimlari
- [x] Zorunlu dokumanlar okundu: `AGENTS.md`, `docs/REPO_DISCIPLINE_TR.md`, `docs/MULTI_AGENT_RULES_TR.md`
- [x] Branch dogrulandi: `agent/<ajan>/<task-id>`
- [x] Lock kapsam disina cikilmadi
- [x] Gorev kapsamindaki degisiklikler tamamlandi
- [x] Goreve ozel test/dogrulama calistirildi

## Kabul Kriterleri
- [x] Task kartlari icin zorunlu runtime kontrati karari netlestirilir
- [x] Koordinator ve ajanlar icin command execution layer matrisi resmi kurala baglanir
- [x] `validate` ve `pre-pr` tarafinda ortam blokaji ile kod hatasi ayrimi netlestirilir
- [x] Sandbox kirilmasinda tekrar deneme yerine durdurma/fallback akisi yazili hale gelir
- [x] Yeni gelen ajanin ilk turda hizalanmasi icin onboarding guardrail'i resmi hale gelir
- [x] PowerShell quoting, BOM/line-ending drift ve upstream baglama sirasi icin acik stop/fallback kurali yazilir
- [x] WSL git credential/auth blokaji resmi readiness sinifi olarak tanimlanir
- [x] `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick` PASS

## Teslimde Zorunlu Kanit
- [x] `git branch --show-current`
- [x] `git branch -vv`
- [x] `git status --short`
- [x] `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick`
- [x] Goreve ozel test/komut sonucu
- [x] `Edit Source == Mount Source` kaniti veya `n/a` gerekcesi
- [x] Commit hash

## Kapanis Adimlari
- [x] Task kartindaki checklistler gercek sonuca gore guncellendi
- [x] `docs/WORKLOG.md` guncellendi
- [x] `docs/TASK_LOCKS.md` kaydi `closed` yapildi
- [x] `docs/NEXT_TASK.md` panosu guncellendi
- [x] Branch pushlandi

## Komutlar
```powershell
powershell -ExecutionPolicy Bypass -File scripts/start-task.ps1 -TaskId TASK-082 -Agent codex -Files "docs/SESSION_HANDOFF_TR.md,AGENTS.md,docs/REPO_DISCIPLINE_TR.md,docs/MULTI_AGENT_RULES_TR.md,docs/COORDINATOR_BOOTSTRAP_TR.md,docs/AGENT_DELIVERY_CHECKLIST_TR.md,docs/OPERATING_MODEL_TR.md,docs/tasks/_TEMPLATE.md,scripts/pre-pr.ps1,scripts/validate.ps1" -Note "Ortam guardrail kisa taski: runtime kontrati, readiness siniflari, shell/katman disiplini ve sandbox fallback kurallarini resmi hale getir"
wsl -e bash -lc "cd /home/bekir/orkestram-k && git checkout -b agent/codex/task-082"
git push -u origin agent/codex/task-082
powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick
```

## Risk / Not
- Bu task urun gelistirme taski degildir; yalniz ortam guardrail standardizasyonu icindir.
- `start-task.ps1` icin UNC branch acilisi problemi bu taskta resmi `PARTIAL_OPEN` / WSL fallback kuralina baglandi; scriptin kendisi bu taskta degistirilmedi.
- Teslim commit zinciri: `cbe48ec`, `da5b6d6`
