# TASK-093

Durum: `DOING`  
Ajan: `codex`  
Branch: `agent/codex/task-093`  
Baslangic: `2026-03-22 03:02`

## Gorev Ozeti
- SimplePricingV1 pricing_mode ve publish guard: listing pricing_mode zemini, simple publish kurallari ve yayin oncesi fiyat gecerlilik kontrolunu kapat

## Task Karari
- [ ] mevcut task devam
- [ ] task genisletme
- [x] yeni task

## In Scope
- [x] Listing seviyesinde resmi `pricing_mode` alanini veya esdeger zemin kararini kodda netlestirmek
- [x] Simple pricing kullanan ilanda publish oncesi zorunlu fiyat gecerlilik kontrolunu kapatmak
- [x] Simple ve structured modellerin ayni listingte birlikte aktif olmasini engelleyen guard yazmak
- [x] Admin ve owner akislarinda publish seviyesinde ayni pricing_mode/publish kuralini uygulamak
- [x] Orkestram tarafinda bu davranisi feature testlerle kilitlemek

## Out of Scope
- [x] Request-reservation aninda fiyat snapshot veya binding mantigini yazmak
- [x] StructuredPricingV1 editoru veya resolver omurgasini kurmak
- [x] Izmirorkestra parity tasimasi
- [x] Public filtre/siralama davranisini tekrar degistirmek
- [x] Veri migration/backfill veya canli veri temizligi yapmak

## Lock Dosyalari
- `docs/tasks/TASK-093.md`
- `docs/TASK_LOCKS.md`
- `docs/NEXT_TASK.md`
- `docs/WORKLOG.md`
- `docs/SESSION_HANDOFF_TR.md`
- `local-rebuild/apps/orkestram/app/Models/Listing.php`
- `local-rebuild/apps/orkestram/app/Http/Controllers/Admin/ListingController.php`
- `local-rebuild/apps/orkestram/app/Http/Controllers/Owner/OwnerDashboardController.php`
- `local-rebuild/apps/orkestram/resources/views/admin/listings/form.blade.php`
- `local-rebuild/apps/orkestram/resources/views/portal/owner/listings-create.blade.php`
- `local-rebuild/apps/orkestram/resources/views/portal/owner/listings-edit.blade.php`
- `local-rebuild/apps/orkestram/tests/Feature/AdminListingMediaFlowTest.php`
- `local-rebuild/apps/orkestram/tests/Feature/OwnerPanelActionsTest.php`

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
- App/Test Katmani: `container`
- Runtime Readiness: `ready`
- Upstream Durumu: `yok`
- Not: `Task 093, Task 092'de kilitlenen simple pricing write-path'ini publish seviyesine tasir. Ana risk, pricing_mode zemini kurulmadan publish davranisinin gri alanda kalmasi ve ikinci sistem geldiginde ayni listingte cift fiyat modelinin acilmasidir.`

## Uygulama Adimlari
- [x] Zorunlu dokumanlar okundu: `AGENTS.md`, `docs/REPO_DISCIPLINE_TR.md`, `docs/MULTI_AGENT_RULES_TR.md`
- [x] Branch dogrulandi: `agent/codex/task-093`
- [ ] Lock kapsam disina cikilmadi
- [ ] Mevcut admin-owner publish/status write-path'i ve simple pricing gecerlilik noktalarini satir bazli haritalamak
- [ ] `pricing_mode` zemini ve publish guard kararini model/controller seviyesinde uygulamak
- [ ] Formlarda simple modun resmi oldugunu aciklayip structured ile cift aktif olma yolunu kapatmak
- [ ] Publish kurallarini feature testlerle kilitlemek
- [ ] Gorev kapsamindaki degisiklikler tamamlandi
- [ ] Goreve ozel test/dogrulama calistirildi

## Kabul Kriterleri
- [ ] Sistemde simple pricing icin resmi bir `pricing_mode` zemini vardir
- [ ] Simple pricing kullanilan listingte publish olmadan once fiyat verisinin tutarliligi kontrol edilir
- [ ] Simple ve structured ayni listingte birlikte aktif olamaz; guard kodla sabitlenir
- [ ] Admin ve owner akislarinda publish seviyesinde ayni pricing_mode kural seti gecerli olur
- [ ] Bu task sonunda txt kararlar ile kod karari ayni yonde kalir
- [ ] Orkestram tarafindaki ilgili feature testleri PASS olur
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
wsl -e bash -lc "cd /home/bekir/orkestram-k && pwd && git rev-parse --show-toplevel && git branch --show-current && git status --short"
powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick
powershell -ExecutionPolicy Bypass -File scripts/close-task.ps1 -TaskId TASK-093 -Agent codex -ClosureNote "simple pricing v1 pricing_mode ve publish guard tamamlandi" -WorklogTitle "TASK-093 simple pricing v1 pricing_mode ve publish guard" -WorklogSummary "pricing_mode zemini ve publish guard tamamlandi" -Files "dosya-1" -Commands "komut-1" -Result PASS
```

## Risk / Not
- Bu task bitse bile SimplePricingV1 tam kapanmis sayilmaz; islem aninda fiyat gercegi icin `TASK-094` gereklidir.
- Geri donus ihtiyacinda ilk bakilacak alanlar publish akisina etki eden status guncelleme noktalari ve listing pricing helper'lari olacaktir.
