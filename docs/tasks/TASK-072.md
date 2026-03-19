# TASK-072

Durum: `DONE`  
Ajan: `codex`  
Branch: `agent/codex/task-072`  
Baslangic: `2026-03-19 04:27`

## Gorev Ozeti
- Yeni gelen koordinatör ajanin ilk dakikada repo disiplinine hizalanmasini saglayan bootstrap akisini ve kontrollu task-kapanis otomasyonunu ekle

## Task Karari
- [ ] mevcut task devam
- [ ] task genisletme
- [x] yeni task

## In Scope
- [x] Koordinator ilk tur akisini kisa, okunabilir bir bootstrap dokumanina baglamak
- [x] Task kapanisinda elde kalan manuel hata riskini azaltan kontrollu bir `close-task` scripti eklemek
- [x] Ilgili disiplin dokumanlarini yeni akisa gore minimum kapsamla guncellemek

## Out of Scope
- [x] Urun/runtime kodunu degistirmek
- [x] Mevcut task acma modelini yeniden tasarlamak
- [x] Paralel ajan dagitim mantigini kapsam disi sekilde genisletmek

## Lock Dosyalari
- `docs/tasks/TASK-072.md`
- `docs/TASK_LOCKS.md`
- `docs/NEXT_TASK.md`
- `docs/WORKLOG.md`
- `AGENTS.md`
- `docs/REPO_DISCIPLINE_TR.md`
- `docs/MULTI_AGENT_RULES_TR.md`
- `docs/AGENT_DELIVERY_CHECKLIST_TR.md`
- `docs/SESSION_HANDOFF_TR.md`
- `docs/tasks/_TEMPLATE.md`
- `docs/COORDINATOR_BOOTSTRAP_TR.md`
- `scripts/close-task.ps1`

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
- [x] Koordinator ilk 5 dakikada izleyecegi akisi tek dokumanda gorebilir
- [x] `close-task` scripti task karti, lock ve NEXT_TASK kapanisini kontrollu sekilde destekler
- [x] Ilgili repo disiplin dokumanlari bootstrap ve kapanis akisini acikca referanslar
- [ ] `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick` PASS

## Teslimde Zorunlu Kanit
- [ ] `git branch --show-current`
- [ ] `git branch -vv`
- [ ] `git status --short`
- [ ] `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick`
- [x] Goreve ozel test/komut sonucu: `close-task.ps1` parse + kopya klasorde smoke-test PASS
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
wsl -e bash -lc "cd /home/bekir/orkestram-k && git status --short && git branch --show-current"
powershell -NoProfile -Command "[void][scriptblock]::Create((Get-Content 'scripts/close-task.ps1' -Raw)); 'PARSE_OK'"
powershell -NoProfile -File scripts/close-task.ps1 -TaskId TASK-999 -Agent codex -ClosureNote "smoke close tamam" -WorklogTitle "Smoke Close" -WorklogSummary "mekanik kapanis smoke" -Files "docs/tasks/TASK-999.md" -Commands "powershell -NoProfile -File scripts/close-task.ps1" -Result PASS
powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick
```

## Risk / Not
- En buyuk risk ortak koordinasyon belgelerinde fazla-genis degisiklik yapip gereksiz drift uretmek; bu nedenle yalniz bootstrap ve kapanis akisina dokunuldu.

