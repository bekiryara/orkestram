# TASK-068

Durum: `DOING`  
Ajan: `codex`  
Branch: `agent/codex/task-068`  
Baslangic: `2026-03-19 02:28`

## Gorev Ozeti
- stale worktree temizligi, koruma ve koordinator devralma standardini resmi operasyon akisina bagla

## Task Karari
- [ ] mevcut task devam
- [ ] task genisletme
- [x] yeni task

## In Scope
- [x] Stale worktree icin `koru | devral | temizle` karar siniflarini netlestirmek
- [x] Koordinatorun hangi durumda yalniz raporlayacagini, hangi durumda resmi devralma yapacagini yazmak
- [x] Sessiz/destructive cleanup yasagini ve zorunlu kanit paketini resmi kurala baglamak
- [x] `orkestram-a`, `orkestram-b`, `orkestram-c` icin mevcut stale aday durumunu handoff kaydinda siniflandirmak
- [x] Merkezi koordinasyon kayitlarini TASK-068 acilisi ile senkron tutmak

## Out of Scope
- [x] Diger worktree'lerde bu task kapsaminda `reset`, `clean`, `checkout --`, `restore` veya benzeri yikici temizlik yapmak
- [x] Urun kodu, UI veya runtime davranisini degistirmek
- [x] Demo fixture, seed veya medya otomasyonu yapmak
- [x] Ajan branch'lerini bu task icinde merge etmek veya kapatmak

## Lock Dosyalari
- `docs/tasks/TASK-068.md`
- `docs/TASK_LOCKS.md`
- `docs/NEXT_TASK.md`
- `docs/WORKLOG.md`
- `docs/SESSION_HANDOFF_TR.md`
- `docs/OPERATING_MODEL_TR.md`
- `docs/AGENT_LOCK_MATRIX_TR.md`

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
- [ ] Lock kapsam disina cikilmadi
- [ ] Gorev kapsamindaki degisiklikler tamamlandi
- [ ] Goreve ozel test/dogrulama calistirildi

## Kabul Kriterleri
- [ ] `OPERATING_MODEL_TR.md` stale worktree icin acik karar akisi ve stop kurallari icerir
- [ ] `AGENT_LOCK_MATRIX_TR.md` destructive cleanup/devralma kosullarini ve kanit paketini netlestirir
- [ ] `SESSION_HANDOFF_TR.md` mevcut stale adaylari `koru | devral | temizle` siniflariyla kaydeder
- [ ] `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick` PASS

## Teslimde Zorunlu Kanit
- [ ] `git branch --show-current`
- [ ] `git branch -vv`
- [ ] `git status --short`
- [ ] `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick`
- [ ] Goreve ozel test/komut sonucu: `powershell -ExecutionPolicy Bypass -File scripts/agent-status.ps1 -Detailed`
- [x] `Edit Source == Mount Source` kaniti
- [ ] Commit hash

## Kapanis Adimlari
- [ ] Task kartindaki checklistler gercek sonuca gore guncellendi
- [ ] `docs/WORKLOG.md` guncellendi
- [ ] `docs/TASK_LOCKS.md` kaydi `closed` yapildi
- [ ] `docs/NEXT_TASK.md` panosu guncellendi
- [ ] Branch pushlandi

## Komutlar
```powershell
powershell -ExecutionPolicy Bypass -File scripts/agent-status.ps1 -Detailed
powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick
```

## Risk / Not
- Bu task karar standardini kurar; stale worktree'lerde yikici temizlik ayri kanit/izin olmadan uygulanmaz.
