# TASK-062

Durum: `DONE`  
Ajan: `codex`  
Branch: `agent/codex/task-062`  
Baslangic: `2026-03-17 14:22`

## Gorev Ozeti
- UI tasarim review akisi preview-onayi, revize ve merge sirasi ile resmi kurala baglandi.

## In Scope
- [x] UI gorevlerinde `design-preview` zorunlulugu ve `main`in review araci olmayacagi netlestirildi
- [x] Kapsam degismiyorsa ayni UI taskta revize donecegi kurali sabitlendi
- [x] `begenildi -> pre-pr PASS -> merge` akis sirasý dokumanlara eklendi
- [x] Task template'e UI review durumu ve revize notu alanlari eklendi
- [x] Ajan teslim checklistine preview onayi ve ayni-task revize davranisi eklendi

## Out of Scope
- [x] Yeni runtime/script kodu yazmak
- [x] Tasarim tasklarinin kendisini uygulamak
- [x] Ayrik preview lane sayisini arttirmak
- [x] Kod/backend task akislarini degistirmek

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
- [x] Zorunlu dokumanlar okundu: `AGENTS.md`, `docs/REPO_DISCIPLINE_TR.md`, `docs/MULTI_AGENT_RULES_TR.md`
- [x] Branch dogrulandi: `agent/<ajan>/<task-id>`
- [x] Lock kapsam disina cikilmadi
- [x] UI review / revize / merge akis kurallari yazili hale getirildi
- [x] Task template UI review alanlariyla guncellendi
- [x] Gorev kapsamindaki degisiklikler tamamlandi
- [x] Goreve ozel test/dogrulama calistirildi

## Kabul Kriterleri
- [x] UI gorevleri icin `design-preview` review zorunlulugu acik yazilidir
- [x] Kapsam degismiyorsa begenilmeyen UI duzeltmeleri ayni taskta donecek sekilde netlestirilmistir
- [x] Merge sirasi `preview onayi -> pre-pr PASS -> merge` olarak belgelenmistir
- [x] Task template `UI Review Durumu` ve revize notu alanlarini tasir
- [x] `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick` PASS

## Teslimde Zorunlu Kanit
- [x] `git branch --show-current`
- [x] `git branch -vv`
- [x] `git status --short`
- [x] `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick`
- [x] Goreve ozel test/komut sonucu
- [x] Commit hash

## Kapanis Adimlari
- [x] Task kartindaki checklistler gercek sonuca gore guncellendi
- [x] `docs/WORKLOG.md` guncellendi
- [x] `docs/TASK_LOCKS.md` kaydi `closed` yapildi
- [x] `docs/NEXT_TASK.md` panosu guncellendi
- [x] Branch pushlandi

## Komutlar
```powershell
powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick
```

## Risk / Not
- UI gorevlerinde ayni taskta revize donmek yalnýzca kapsam degismiyorsa gecerlidir; yeni ozellik veya yeni dosya seti gerekiyorsa yeni task acilir.
