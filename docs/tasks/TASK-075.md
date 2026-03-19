# TASK-075

Durum: `DOING`  
Ajan: `codex-c`  
Branch: `agent/codex-c/task-075`  
Baslangic: `2026-03-19 06:07`

## Gorev Ozeti
- Deterministic demo fixture standardi ve review demo veri kurali yazilacak

## Task Karari
- [ ] mevcut task devam
- [ ] task genisletme
- [x] yeni task

## In Scope
- [ ] Design-preview review icin deterministic demo fixture standardi yazilacak
- [ ] Demo slug, whitelist, idempotent update ve medya kaynagi kurallari netlestirilecek
- [ ] Review demo verisi ile smoke/test verisinin ayrimi dokumana islenecek

## Out of Scope
- [ ] Seed/command implementasyonu yazmak
- [ ] Mevcut test/smoke fixturelarini degistirmek
- [ ] Merkezi kapanis dosyalarini tek basina finalize etmek

## Lock Dosyalari
- `docs/tasks/TASK-075.md`
- `docs/TASK_LOCKS.md`
- `docs/NEXT_TASK.md`
- `docs/DEMO_FIXTURE_STANDARD_TR.md`

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
- [ ] Demo review icin deterministic fixture standardi tek dokumanda toplanir
- [ ] Whitelist slug, medya kaynagi ve idempotent update kurali net yazilir
- [ ] Smoke/test fixturelari ile preview demo fixturelarinin ayrimi aciklanir
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
- Risk, demo fixture standardinin mevcut smoke/test fixturelarini etkileyecek sekilde genis yazilmasi; belge yalniz ayrik review hattini hedeflemeli.
