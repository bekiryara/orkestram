# TASK-062

Durum: `DOING`  
Ajan: `codex`  
Branch: `agent/codex/task-062`  
Baslangic: `2026-03-17 14:22`

## Gorev Ozeti
- UI tasarim review akisini preview-onayi, revize ve merge sirasi ile kurala baglamak

## In Scope
- [ ] UI gorevlerinde `design-preview` zorunlulugunu ve `main`in review araci olmayacagini netlestirmek
- [ ] Kapsam degismiyorsa ayni UI taskta revize donecegi kuralini sabitlemek
- [ ] `begenildi -> pre-pr PASS -> merge` akis sirasini dokumanlara eklemek
- [ ] Task template'e UI review durumu ve revize notu alanlarini eklemek
- [ ] Ajan teslim checklistine preview onayi ve ayni-task revize davranisini eklemek

## Out of Scope
- [ ] Yeni runtime/script kodu yazmak
- [ ] Tasarim tasklarinin kendisini uygulamak
- [ ] Ayrik preview lane sayisini arttirmak
- [ ] Kod/backend task akislarini degistirmek

## Lock Dosyalari
- `docs/tasks/TASK-062.md`
- `docs/TASK_LOCKS.md`
- `docs/NEXT_TASK.md`
- `docs/REPO_DISCIPLINE_TR.md`
- `docs/MULTI_AGENT_RULES_TR.md`
- `docs/AGENT_DELIVERY_CHECKLIST_TR.md`
- `docs/tasks/_TEMPLATE.md`
- `AGENTS.md`
- `docs/WORKLOG.md`

## Preview Kontrati
- Lane: `design`
- Preview URL: `http://127.0.0.1:8280`, `http://127.0.0.1:8281`
- Mount Source: `/home/bekir/orkestram-b/local-rebuild/apps/{orkestram|izmirorkestra}`
- UI review gerekir mi?: `yes`

## Uygulama Adimlari
- [ ] Zorunlu dokumanlar okundu: `AGENTS.md`, `docs/REPO_DISCIPLINE_TR.md`, `docs/MULTI_AGENT_RULES_TR.md`
- [ ] Branch dogrulandi: `agent/<ajan>/<task-id>`
- [ ] Lock kapsam disina cikilmadi
- [ ] UI review / revize / merge akis kurallari yazili hale getirildi
- [ ] Task template UI review alanlariyla guncellendi
- [ ] Gorev kapsamindaki degisiklikler tamamlandi
- [ ] Goreve ozel test/dogrulama calistirildi

## Kabul Kriterleri
- [ ] UI gorevleri icin `design-preview` review zorunlulugu acik yazilidir
- [ ] Kapsam degismiyorsa begenilmeyen UI duzeltmeleri ayni taskta donecek sekilde netlestirilmistir
- [ ] Merge sirasi `preview onayi -> pre-pr PASS -> merge` olarak belgelenmistir
- [ ] Task template `UI Review Durumu` ve revize notu alanlarini tasir
- [ ] `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick` PASS

## Teslimde Zorunlu Kanit
- [ ] `git branch --show-current`
- [ ] `git branch -vv`
- [ ] `git status --short`
- [ ] `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick`
- [ ] Goreve ozel test/komut sonucu
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
```

## Risk / Not
- UI gorevlerinde ayni taskta revize donmek yalnýzca kapsam degismiyorsa gecerlidir; yeni ozellik veya yeni dosya seti gerekiyorsa yeni task acilir.
