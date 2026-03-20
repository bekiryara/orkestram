# TASK-085

Durum: `DONE`  
Ajan: `codex`  
Branch: `agent/codex/task-085`  
Baslangic: `2026-03-20 08:08`

## Gorev Ozeti
- Smoke gate thumb fallback hizasi ve locations manifest checksum/encoding stabilizasyonu

## Task Karari
- [ ] mevcut task devam
- [ ] task genisletme
- [x] yeni task

## In Scope
- [x] `scripts/smoke-test.ps1` admin listing thumb kontrolunu kapak-gorsel-opsiyonel davranisla hizalamak
- [x] `docs/category-catalog/ready/locations_v1/manifest_v1.json` checksum/encoding zincirini repo snapshot'i ile hizalamak
- [x] locations import ve resmi gate dogrulamasini tekrar gecirmek

## Out of Scope
- [ ] Yeni fixture otomasyonu yazmak
- [ ] Review/demo fixture standardini genisletmek
- [ ] Urun form/controller mantigini yeniden tasarlamak

## Lock Dosyalari
- `docs/tasks/TASK-085.md`
- `docs/TASK_LOCKS.md`
- `docs/NEXT_TASK.md`
- `docs/WORKLOG.md`
- `docs/SESSION_HANDOFF_TR.md`
- `scripts/smoke-test.ps1`
- `docs/category-catalog/ready/locations_v1/manifest_v1.json`

## Preview Kontrati
- Lane: `n/a`
- Preview URL: `n/a`
- Mount Source: `n/a`
- Edit Source: `n/a`
- UI review gerekir mi?: `no`
- UI Review Durumu: `n/a`
- Revize Notu: `n/a`

## Runtime Kontrati
- Runtime Source: `/home/bekir/orkestram-k/local-rebuild`
- Preview Source: `n/a`
- Git Katmani: `WSL`
- Script Katmani: `PowerShell`
- App/Test Katmani: `container`
- Runtime Readiness: `ready`
- Upstream Durumu: `yok`
- Not: `Smoke gate PASS; locations import deterministic snapshot + manifest checksum dogrulamasi ile calistirildi`

## Uygulama Adimlari
- [x] Zorunlu dokumanlar okundu: `AGENTS.md`, `docs/REPO_DISCIPLINE_TR.md`, `docs/MULTI_AGENT_RULES_TR.md`
- [x] Branch dogrulandi: `agent/<ajan>/<task-id>`
- [x] Lock kapsam disina cikilmadi
- [x] Gorev kapsamindaki degisiklikler tamamlandi
- [x] Goreve ozel test/dogrulama calistirildi

## Kabul Kriterleri
- [x] Admin listing smoke kontrolu, kapak varsa URL `200`, kapak yoksa optional fallback ile iki appte PASS verir
- [x] `locations_v1` manifest hash zinciri repo icindeki gercek snapshot dosyalariyla uyumludur
- [x] Lokasyon sozlugu importu `81 sehir / 973 ilce / 31855 mahalle` sonucunu verir
- [x] `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick` PASS

## Teslimde Zorunlu Kanit
- [x] `git branch --show-current`
- [x] `git branch -vv`
- [x] `git status --short`
- [x] `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick`
- [x] Goreve ozel test/komut sonucu
- [x] `Edit Source == Mount Source` kaniti
- [ ] Commit hash

## Kapanis Adimlari
- [x] Task kartindaki checklistler gercek sonuca gore guncellendi
- [x] `docs/WORKLOG.md` guncellendi
- [x] `docs/TASK_LOCKS.md` kaydi `closed` yapildi
- [x] `docs/NEXT_TASK.md` panosu guncellendi
- [ ] Branch pushlandi

## Komutlar
```powershell
powershell -ExecutionPolicy Bypass -File scripts/validate.ps1 -App both
powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick
wsl -e bash -lc "cd /home/bekir/orkestram-k/local-rebuild && docker compose exec -T orkestram-web php artisan locations:import --from=/tmp/locations_v1"
```

## Risk / Not
- Risk, smoke fixture ile review/demo fixture katmanlarinin halen ayni shared DB'yi kullanmasi; bu task yalniz mevcut gate ve locations snapshot stabilizasyonunu kapatti.
- `locations:import` varsayilan path'i container icinde repo docs klasorunu gormedigi icin snapshot `/tmp/locations_v1` altina kopyalanarak calistirildi.
- Importer hash guvencesi aktif oldugu icin bozuk manifest degil, gercek snapshot hash'leri esas alinmistir.

