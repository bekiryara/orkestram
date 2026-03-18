# TASK-063

Durum: `DONE`  
Ajan: `codex`  
Branch: `agent/codex/task-063`  
Baslangic: `2026-03-17 14:43`

## Gorev Ozeti
- Design preview `8281` incident'i kapsaminda hem `:8281` port eslesmesi duzeltildi hem de kok neden olan `edit source == preview source` guard'i resmi kurala baglandi.

## In Scope
- [x] `PublicController` icinde `:8281` port eslesmesini dogrulamak ve iki appte eklemek
- [x] `design-preview` review akisi icin `edit source == preview source` kuralini belgelemek
- [x] Task template, teslim checklisti ve ajan kurallarina `Edit Source` kanitini eklemek
- [x] Kok neden olarak `preview source != edit source` durumunun review'i gecersiz kildigini netlestirmek

## Out of Scope
- [x] `orkestram-b` worktree'sindeki ayri kopyalara manual patch gecmemek
- [x] Yeni runtime/container lane'i olusturmamak
- [x] Baska ekranlar icin UI implementasyonu yapmamak

## Lock Dosyalari
- `docs/tasks/TASK-063.md`
- `docs/TASK_LOCKS.md`
- `docs/NEXT_TASK.md`
- `local-rebuild/apps/orkestram/app/Http/Controllers/PublicController.php`
- `local-rebuild/apps/izmirorkestra/app/Http/Controllers/PublicController.php`
- `AGENTS.md`
- `docs/REPO_DISCIPLINE_TR.md`
- `docs/MULTI_AGENT_RULES_TR.md`
- `docs/AGENT_DELIVERY_CHECKLIST_TR.md`
- `docs/tasks/_TEMPLATE.md`
- `docs/WORKLOG.md`

## Preview Kontrati
- Lane: `design`
- Preview URL: `http://127.0.0.1:8281`
- Mount Source: `/home/bekir/orkestram-b/local-rebuild/apps/izmirorkestra` (`incident diagnosis`)
- Edit Source: `/home/bekir/orkestram-k/local-rebuild/apps/izmirorkestra`
- UI review gerekir mi?: `no`
- UI Review Durumu: `blocked by source mismatch`
- Revize Notu: `Bu task UI revizesi degil; incident kokeni olarak Mount Source != Edit Source durumu tespit edilip resmi kurala baglandi.`

## Uygulama Adimlari
- [x] Zorunlu dokumanlar okundu: `AGENTS.md`, `docs/REPO_DISCIPLINE_TR.md`, `docs/MULTI_AGENT_RULES_TR.md`
- [x] Branch dogrulandi: `agent/<ajan>/<task-id>`
- [x] Lock kapsam disina cikilmadi
- [x] Gorev kapsamindaki degisiklikler tamamlandi
- [x] Goreve ozel test/dogrulama calistirildi

## Kabul Kriterleri
- [x] Iki appte de `siteFromRequest()` `:8281` icin `izmirorkestra.net` dondurur
- [x] UI review oncesi `Edit Source`, `Mount Source`, `Preview URL` ucunun birlikte istenecegi belgelenmistir
- [x] `Edit Source != Mount Source` ise review ve merge'in duracagi kurala baglanmistir
- [x] `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick` PASS

## Teslimde Zorunlu Kanit
- [x] `git branch --show-current`
- [x] `git branch -vv`
- [x] `git status --short`
- [x] `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick`
- [x] Goreve ozel test/komut sonucu
- [x] `Edit Source != Mount Source` incident kaniti ve blokaj kurali
- [x] Commit hash

## Kapanis Adimlari
- [x] Task kartindaki checklistler gercek sonuca gore guncellendi
- [x] `docs/WORKLOG.md` guncellendi
- [x] `docs/TASK_LOCKS.md` kaydi `closed` yapildi
- [x] `docs/NEXT_TASK.md` panosu guncellendi
- [x] Branch pushlandi

## Komutlar
```powershell
powershell -ExecutionPolicy Bypass -File scripts/smoke-test.ps1 -App izmirorkestra -Lane design
powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick
```

## Risk / Not
- Incident kapanisi icin kod fix ve operasyon kurali birlikte ele alindi; design preview review'lari bundan sonra `Edit Source == Mount Source` kaniti olmadan gecerli sayilmayacak.
