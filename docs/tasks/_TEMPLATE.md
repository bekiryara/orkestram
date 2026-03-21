# TASK-XXX

Durum: `TODO | DOING | DONE`  
Ajan: `agent-name`  
Branch: `agent/agent-name/task-xxx`  
Baslangic: `YYYY-MM-DD HH:mm`

## Gorev Ozeti
- Bu gorevin amaci

## Task Karari
- [ ] mevcut task devam
- [ ] task genisletme
- [ ] yeni task

## In Scope
- [ ] Degisiklik 1
- [ ] Degisiklik 2

## Out of Scope
- [ ] Konu disi 1
- [ ] Konu disi 2

## Lock Dosyalari
- `path/one`
- `path/two`

## Preview Kontrati
- Lane: `main | design-preview | n/a`
- Preview URL: `n/a`
- Mount Source: `n/a`
- Edit Source: `n/a`
- UI review gerekir mi?: `yes | no`
- UI Review Durumu: `pending | revize | approved | n/a`
- Revize Notu: `n/a`

## Runtime Kontrati
- Runtime Source: `n/a`
- Preview Source: `n/a`
- Git Katmani: `WSL | Windows | n/a`
- Script Katmani: `PowerShell | WSL | n/a`
- App/Test Katmani: `container | WSL | n/a`
- Runtime Readiness: `ready | ENV_BLOCKED | RUNTIME_BLOCKED | SANDBOX_BLOCKED | CODE_FAIL | n/a`
- Upstream Durumu: `origin/<branch> | yok | n/a`
- Not: `n/a`

## Uygulama Adimlari
- [ ] Zorunlu dokumanlar okundu: `AGENTS.md`, `docs/REPO_DISCIPLINE_TR.md`, `docs/MULTI_AGENT_RULES_TR.md`
- [ ] Branch dogrulandi: `agent/<ajan>/<task-id>`
- [ ] Lock kapsam disina cikilmadi
- [ ] Gorev kapsamindaki degisiklikler tamamlandi
- [ ] Goreve ozel test/dogrulama calistirildi

## Kabul Kriterleri
- [ ] Beklenen davranis 1
- [ ] Beklenen davranis 2
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
powershell -ExecutionPolicy Bypass -File scripts/close-task.ps1 -TaskId TASK-XXX -Agent agent-name -ClosureNote "kisa kapanis ozeti" -WorklogTitle "baslik" -WorklogSummary "madde-1" -Files "dosya-1" -Commands "komut-1" -Result PASS
```

## Risk / Not
- Riskler ve geri donus notu

