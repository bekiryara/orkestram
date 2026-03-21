# TASK-089

Durum: `DONE`
Ajan: `codex`
Branch: `agent/codex/task-089`
Baslangic: `2026-03-20 20:33`

## Gorev Ozeti
- Koordinator drift hijyeni: TASK-087 closure kaydini resmi hale getir, manifest ve task dosyalarindaki satir-sonu/encoding farklarini normalize et.

## Task Karari
- [x] mevcut task devam
- [ ] task genisletme
- [ ] yeni task

## In Scope
- [x] `docs/tasks/TASK-087.md` icindeki kapanis driftini resmi closure gercegine gore normalize etmek
- [x] `docs/demo-media/bando-review/manifest.json`, `docs/tasks/TASK-085.md` ve `docs/tasks/TASK-086.md` dosyalarindaki satir-sonu/encoding driftini temizlemek
- [x] Merkezi koordinasyon kayitlarinda hijyen taskini ayri closure ile kayda almak

## Out of Scope
- [x] Yeni urun/runtime davranisi degistirmek
- [x] Demo medya fixture veri setini genisletmek
- [x] TASK-088 veya daha eski tasklarin urun koduna tekrar dokunmak

## Lock Dosyalari
- `docs/tasks/TASK-089.md`
- `docs/TASK_LOCKS.md`
- `docs/NEXT_TASK.md`
- `docs/WORKLOG.md`
- `docs/SESSION_HANDOFF_TR.md`
- `docs/tasks/TASK-087.md`
- `docs/demo-media/bando-review/manifest.json`
- `docs/tasks/TASK-085.md`
- `docs/tasks/TASK-086.md`

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
- App/Test Katmani: `n/a`
- Runtime Readiness: `ready`
- Upstream Durumu: `origin/agent/codex/task-089`
- Not: `Bu task urun runtime davranisini degistirmez; yalniz koordinator drift hijyenini temizler.`

## Uygulama Adimlari
- [x] Zorunlu dokumanlar okundu: `AGENTS.md`, `docs/REPO_DISCIPLINE_TR.md`, `docs/MULTI_AGENT_RULES_TR.md`
- [x] Branch dogrulandi: `agent/codex/task-089`
- [x] Lock kapsam disina cikilmadi
- [x] Saf satir-sonu/encoding drift dosyalari HEAD ile hizalandi
- [x] `TASK-087` closure kaydi resmi sonuca gore normalize edildi
- [x] Goreve ozel test/dogrulama calistirildi

## Kabul Kriterleri
- [x] `TASK-087` closure kaydi gercek kapanis durumunu yansitir
- [x] Manifest ve eski task kartlarindaki satir-sonu/encoding driftleri temizlenir
- [x] `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick` PASS

## Teslimde Zorunlu Kanit
- [x] `git branch --show-current`
- [x] `git branch -vv`
- [x] `git status --short`
- [x] `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick`
- [x] Goreve ozel test/komut sonucu
- [x] `Edit Source == Mount Source` kaniti `n/a`
- [ ] Commit hash (commit sonrasi eklenecek)

## Kapanis Adimlari
- [x] Task kartindaki checklistler gercek sonuca gore guncellendi
- [x] `docs/WORKLOG.md` guncellendi
- [x] `docs/TASK_LOCKS.md` kaydi `closed` yapildi
- [x] `docs/NEXT_TASK.md` panosu guncellendi
- [x] Branch pushlandi

## Komutlar
```powershell
wsl -e bash -lc "cd /home/bekir/orkestram-k && git diff -- docs/demo-media/bando-review/manifest.json docs/tasks/TASK-085.md docs/tasks/TASK-086.md docs/tasks/TASK-087.md"
powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick
powershell -ExecutionPolicy Bypass -File scripts/close-task.ps1 -TaskId TASK-089 -Agent codex -ClosureNote "koordinator drift hijyeni tamamlandi" -WorklogTitle "TASK-089 coordinator drift hygiene" -WorklogSummary "TASK-087 closure drift normalize edildi" -Files "docs/tasks/TASK-087.md" -Commands "pre-pr quick" -Result PASS
```

## Risk / Not
- Bu task yalniz koordinator kaynakli closure/hijyen driftini temizler; urun runtime davranisina dokunmaz.
- Benzer drift tekrar ederse nedeni task karti kapanisinin commitlenmeden worktree'de kalmasidir; bu task o artakalanı kapatir.
- docs/demo-media/bando-review/manifest.json, docs/tasks/TASK-085.md ve docs/tasks/TASK-086.md icin bekleyen fark icerik degisimi degil; satir-sonu/encoding hizasidir.
- Goreve ozel kanit zinciri: git diff --numstat ile TASK-087 closure duzeltmesi dogrulandi, local account fixture PASS ve admin login kaniti runtime tarafinin saglikli oldugunu gosterdi, pre-pr -Mode quick PASS alindi.

