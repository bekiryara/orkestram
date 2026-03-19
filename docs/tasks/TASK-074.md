# TASK-074

Durum: `DONE`  
Ajan: `codex-b`  
Branch: `agent/codex-b/task-074`  
Baslangic: `2026-03-19 06:06`

## Gorev Ozeti
- Merge sonrasi preview/runtime lifecycle ve design lane yasam dongusu standardi yazilacak

## Task Karari
- [ ] mevcut task devam
- [ ] task genisletme
- [x] yeni task

## In Scope
- [x] Design-preview lane merge sonrasi ne zaman main'e hizalanir kurali yazilacak
- [x] Review URL ile final URL farkini tek akista aciklayan lifecycle maddeleri eklenecek
- [x] Merge sonrasi runtime tazeleme ve kontrol checklisti resmi dokumanlara islenecek

## Out of Scope
- [ ] Demo fixture standardini yazmak
- [ ] Urun kodu veya preview container konfigunu degistirmek
- [ ] Merkezi kapanis dosyalarini tek basina finalize etmek

## Lock Dosyalari
- `docs/tasks/TASK-074.md`
- `docs/TASK_LOCKS.md`
- `docs/NEXT_TASK.md`
- `docs/OPERATING_MODEL_TR.md`
- `docs/MULTI_AGENT_RULES_TR.md`
- `docs/REPO_DISCIPLINE_TR.md`
- `docs/AGENT_DELIVERY_CHECKLIST_TR.md`
- `docs/SESSION_HANDOFF_TR.md`

## Preview Kontrati
- Lane: `design-preview`
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
- [x] Merge sonrasi design-preview lane durumu net kural olarak yazilir
- [x] Main/design lane farki ve kullaniciya hangi URL'in ne amacla verilecegi tek akista anlatilir
- [x] Merge sonrasi runtime tazeleme/checklisti eklenir
- [x] `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick` PASS

## Teslimde Zorunlu Kanit
- [x] `git branch --show-current`
- [x] `git branch -vv`
- [x] `git status --short`
- [x] `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick`
- [x] Goreve ozel test/komut sonucu
- [x] `Edit Source == Mount Source` kaniti `n/a` (UI review yok; Preview Kontrati `no`)
- [x] Commit hash `4f95fa0`

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
- Risk, merge sonrasi lane akisini gereksiz karmasik hale getirmek; hedef tek bakista anlasilan lifecycle yazmak.
- Goreve ozel komut dogrulamasi rg aramasi ile alindi: Merge Sonrasi Preview / Runtime | Review URL | Final URL | Runtime refresh | design-preview.
- Bu gorev dokuman/lifecycle kapsamindadir; UI review calismadigi icin Edit Source == Mount Source kaniti n/a olarak kaydedildi.


