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
- [x] Design-preview review icin deterministic demo fixture standardi yazilacak
- [x] Demo slug, whitelist, idempotent update ve medya kaynagi kurallari netlestirilecek
- [x] Review demo verisi ile smoke/test verisinin ayrimi dokumana islenecek

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
- [x] Zorunlu dokumanlar okundu: `AGENTS.md`, `docs/REPO_DISCIPLINE_TR.md`, `docs/MULTI_AGENT_RULES_TR.md`
- [x] Branch dogrulandi: `agent/codex-c/task-075`
- [x] Lock kapsam disina cikilmadi
- [x] Gorev kapsamindaki degisiklikler tamamlandi
- [x] Goreve ozel test/dogrulama calistirildi
  Not: `git fetch --all --prune`, `git remote -v`, `git branch -vv` ve `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick`

## Kabul Kriterleri
- [x] Demo review icin deterministic fixture standardi tek dokumanda toplanir
- [x] Whitelist slug, medya kaynagi ve idempotent update kurali net yazilir
- [x] Smoke/test fixturelari ile preview demo fixturelarinin ayrimi aciklanir
- [x] `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick` PASS

## Teslimde Zorunlu Kanit
- [x] `git branch --show-current`
- [x] `git branch -vv`
- [x] `git status --short`
- [x] `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick`
- [x] Goreve ozel test/komut sonucu
- [ ] `Edit Source == Mount Source` kaniti
  Gerekce: bu gorev UI gorevi degil, preview kodu degistirmiyor.
- [ ] Commit hash
  Bloke: commit/push henuz alinmadi.

## Kapanis Adimlari
- [x] Task kartindaki checklistler gercek sonuca gore guncellendi
- [ ] `docs/WORKLOG.md` guncellendi
  Koordinator alani; bu gorevde dokunulmadi.
- [ ] `docs/TASK_LOCKS.md` kaydi `closed` yapildi
  Koordinator alani; bu gorevde dokunulmadi.
- [ ] `docs/NEXT_TASK.md` panosu guncellendi
  Koordinator alani; bu gorevde dokunulmadi.
- [ ] Branch pushlandi
  Bloke: commit ve push adimi henuz tamamlanmadi.

## Komutlar
```powershell
powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick
```

## Risk / Not
- Risk, demo fixture standardinin mevcut smoke/test fixturelarini etkileyecek sekilde genis yazilmasi; belge yalniz ayrik review hattini hedeflemeli.
- Cikti: `docs/DEMO_FIXTURE_STANDARD_TR.md` olusturuldu; standart yalniz ayrik review/demo fixture hattini tarif eder.

