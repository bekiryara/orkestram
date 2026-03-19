# TASK-074

Durum: `DOING`  
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
- [ ] Design-preview lane merge sonrasi ne zaman main'e hizalanir kurali yazilacak
- [ ] Review URL ile final URL farkini tek akista aciklayan lifecycle maddeleri eklenecek
- [ ] Merge sonrasi runtime tazeleme ve kontrol checklisti resmi dokumanlara islenecek

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
- [ ] Zorunlu dokumanlar okundu: `AGENTS.md`, `docs/REPO_DISCIPLINE_TR.md`, `docs/MULTI_AGENT_RULES_TR.md`
- [ ] Branch dogrulandi: `agent/<ajan>/<task-id>`
- [ ] Lock kapsam disina cikilmadi
- [ ] Gorev kapsamindaki degisiklikler tamamlandi
- [ ] Goreve ozel test/dogrulama calistirildi

## Kabul Kriterleri
- [ ] Merge sonrasi design-preview lane durumu net kural olarak yazilir
- [ ] Main/design lane farki ve kullaniciya hangi URL'in ne amacla verilecegi tek akista anlatilir
- [ ] Merge sonrasi runtime tazeleme/checklisti eklenir
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
```

## Risk / Not
- Risk, merge sonrasi lane akisini gereksiz karmasik hale getirmek; hedef tek bakista anlasilan lifecycle yazmak.
