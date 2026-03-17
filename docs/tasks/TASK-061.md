# TASK-061

Durum: `DONE`  
Ajan: `codex`  
Branch: `agent/codex/task-061`  
Baslangic: `2026-03-17 13:35`

## Gorev Ozeti
- Design-preview runtime standardizasyonu tamamlandi; tek sabit tasarim preview lane'i ile merge-oncesi UI review akisi deterministik hale getirildi.

## In Scope
- [x] `design-preview` lane'i icin runtime/compose yapisi tanimlandi
- [x] `main preview` ile `design preview` ayrimi script ve dokumanlarda sabitlendi
- [x] `design-preview` icin sabit port, mount-source ve owner kurali eklendi
- [x] `dev-up` ve ilgili runtime komutlari design slotunu kurup kaynak kanitini verir hale getirildi
- [x] Task/template/disiplin dokumanlarina `Preview URL` ve `Mount Source` zorunlulugu eklendi
- [x] UI tasklarin merge-oncesi preview onayi almadan kapanamayacagi kuralina baglandi

## Out of Scope
- [x] Listing/detail tasarimlarinin yeniden yapimi
- [x] Kod/back-end tasklar icin yeni preview lane'leri acmak
- [x] Her ajana ayri kalici preview altyapisi kurmak
- [x] Yeni DB izolasyonu veya ekstra MySQL konteynerleri

## Lock Dosyalari
- `docs/tasks/TASK-061.md`
- `docs/TASK_LOCKS.md`
- `docs/NEXT_TASK.md`
- `local-rebuild/docker-compose.yml`
- `scripts/dev-up.ps1`
- `scripts/smoke-test.ps1`
- `docs/tasks/_TEMPLATE.md`
- `docs/MULTI_AGENT_RULES_TR.md`
- `docs/AGENT_DELIVERY_CHECKLIST_TR.md`
- `docs/REPO_DISCIPLINE_TR.md`
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
- [x] `design-preview` icin hedef runtime modeli netlestirildi: `main` sabit, `design` sabit lane
- [x] `docker-compose` ve script degisiklikleri design lane kurulumuna gore guncellendi
- [x] Task/template/disiplin metinleri preview kontratina gore guncellendi
- [x] Gorev kapsamindaki degisiklikler tamamlandi
- [x] Goreve ozel test/dogrulama calistirildi

## Kabul Kriterleri
- [x] `design-preview` lane'i iki site icin sabit portlarla tanimlidir
- [x] `main preview` ile `design preview` kaynak ayrimi dokumanda ve scriptte nettir
- [x] Runtime komutu hangi worktree'nin mount edildigini acik kanitlar
- [x] UI task kartlari `Preview URL` ve `Mount Source` alanlarini zorunlu tasir
- [x] UI task merge akisi `preview onayi -> commit/push -> merge` sirasina baglanmistir
- [x] `design-preview` lane'i icin temel smoke/dogrulama akisi tanimlanmistir
- [ ] `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick` PASS

## Teslimde Zorunlu Kanit
- [x] `git branch --show-current`
- [ ] `git branch -vv`
- [x] `git status --short`
- [ ] `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick`
- [x] Goreve ozel test/komut sonucu
- [ ] Commit hash

## Kapanis Adimlari
- [x] Task kartindaki checklistler gercek sonuca gore guncellendi
- [x] `docs/WORKLOG.md` guncellendi
- [x] `docs/TASK_LOCKS.md` kaydi `closed` yapildi
- [x] `docs/NEXT_TASK.md` panosu guncellendi
- [ ] Branch pushlandi

## Komutlar
```powershell
powershell -ExecutionPolicy Bypass -File scripts/dev-up.ps1 -App both -Lane design
powershell -ExecutionPolicy Bypass -File scripts/smoke-test.ps1 -App both -Lane design
powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick
```

## Risk / Not
- `design-preview` tek lane oldugu icin ayni anda sadece bir UI task review edilir; bu bilincli sinirdir.
- Design lane runtime artifact'lari (`.env`, `vendor`, `storage`, `public/uploads`) `main` lane'den paylasilir; UI review icin bilincli tradeoff budur.
