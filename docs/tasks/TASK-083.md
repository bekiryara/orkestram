# TASK-083

Durum: `DOING`  
Ajan: `codex`  
Branch: `agent/codex/task-083`  
Baslangic: `2026-03-20 02:08`

## Gorev Ozeti
- Mekanik sertlestirme: task acma/kapatma akisi, upstream baglama sirasi ve WSL/credential cukurlarini script seviyesinde azalt

## Task Karari
- [ ] mevcut task devam
- [ ] task genisletme
- [x] yeni task

## In Scope
- [ ] `start-task.ps1` icinde UNC path uzerinde branch acilamama durumunu WSL fallback ile mekanik olarak toparlamak
- [ ] `close-task.ps1` icindeki hardcoded kapanis davranisini generic ve tekrar kullanilabilir hale getirmek
- [ ] `start-task.ps1` icinde zorunlu koordinasyon dosyalarini (`docs/WORKLOG.md`, koordinator icin `docs/SESSION_HANDOFF_TR.md`) otomatik lock kapsaminda acmak
- [ ] Repo-local kirik credential helper kaydini temizleyip Windows git push akisini gereksiz hata mesajindan arindirmak
- [ ] Task acma/kapatma zincirinde upstream ve branch durumunun daha deterministik ilerlemesini saglamak

## Out of Scope
- [ ] Yeni urun ozelligi gelistirmek
- [ ] Mevcut runtime/disiplin dokumanlarini tekrar buyuk refaktore etmek
- [ ] WSL global credential kurulumunu repo disinda kalici makine ayari olarak degistirmek
- [ ] UI preview/review akisini degistirmek

## Lock Dosyalari
- `docs/tasks/TASK-083.md`
- `docs/TASK_LOCKS.md`
- `docs/NEXT_TASK.md`
- `docs/WORKLOG.md`
- `docs/SESSION_HANDOFF_TR.md`
- `scripts/start-task.ps1`
- `scripts/close-task.ps1`
- `scripts/pre-pr.ps1`
- `AGENTS.md`
- `docs/REPO_DISCIPLINE_TR.md`
- `docs/MULTI_AGENT_RULES_TR.md`
- `docs/COORDINATOR_BOOTSTRAP_TR.md`
- `docs/AGENT_DELIVERY_CHECKLIST_TR.md`
- `docs/OPERATING_MODEL_TR.md`

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
- Git Katmani: `WSL + Windows`
- Script Katmani: `PowerShell`
- App/Test Katmani: `n/a`
- Runtime Readiness: `ready`
- Upstream Durumu: `yok`
- Not: `Bu task mekanik acilis/kapanis scriptlerini sertlestirir; urun runtime davranisini degistirmez.`

## Uygulama Adimlari
- [x] Zorunlu dokumanlar okundu: `AGENTS.md`, `docs/REPO_DISCIPLINE_TR.md`, `docs/MULTI_AGENT_RULES_TR.md`
- [x] Branch dogrulandi: `agent/<ajan>/<task-id>`
- [x] Lock kapsam disina cikilmadi
- [ ] Gorev kapsamindaki degisiklikler tamamlandi
- [ ] Goreve ozel test/dogrulama calistirildi

## Kabul Kriterleri
- [ ] `start-task.ps1` UNC uzerinde task karti/lock/pano yazdikten sonra branch acilisini WSL fallback ile tamamlayabilir
- [ ] `close-task.ps1` aktif taski generic olarak kapatip kalan aktif tasklari koruyarak `NEXT_TASK` listelerini hardcoded icerik olmadan guncelleyebilir
- [ ] `start-task.ps1` zorunlu koordinasyon dosyalarini otomatik lock listesine ekler
- [ ] Repo-local kirik credential helper kaydi temizlenir
- [ ] `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick` PASS

## Teslimde Zorunlu Kanit
- [ ] `git branch --show-current`
- [ ] `git branch -vv`
- [ ] `git status --short`
- [ ] `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick`
- [ ] Goreve ozel test/komut sonucu
- [ ] `Edit Source == Mount Source` kaniti veya `n/a` gerekcesi
- [ ] Commit hash

## Kapanis Adimlari
- [ ] Task kartindaki checklistler gercek sonuca gore guncellendi
- [ ] `docs/WORKLOG.md` guncellendi
- [ ] `docs/TASK_LOCKS.md` kaydi `closed` yapildi
- [ ] `docs/NEXT_TASK.md` panosu guncellendi
- [ ] Branch pushlandi

## Komutlar
```powershell
powershell -ExecutionPolicy Bypass -File scripts/start-task.ps1 -TaskId TASK-083 -Agent codex -Files "docs/SESSION_HANDOFF_TR.md,scripts/start-task.ps1,scripts/close-task.ps1,scripts/pre-pr.ps1,AGENTS.md,docs/REPO_DISCIPLINE_TR.md,docs/MULTI_AGENT_RULES_TR.md,docs/COORDINATOR_BOOTSTRAP_TR.md,docs/AGENT_DELIVERY_CHECKLIST_TR.md,docs/OPERATING_MODEL_TR.md" -Note "Mekanik sertlestirme: task acma/kapatma akisi, upstream baglama sirasi ve WSL/credential cukurlarini script seviyesinde azalt"
wsl -e bash -lc "cd /home/bekir/orkestram-k && git checkout -b agent/codex/task-083"
```

## Risk / Not
- Bu task ortam/disiplin scriptlerini hedefler; urun kodu degistirmez.
- `start-task.ps1` mevcut haliyle cukür urettigi icin bu task acilisi bizzat WSL fallback ile toparlandi; duzeltme dogrudan bu taskin kabul kriteridir.
- Kapanis zincirinde `docs/WORKLOG.md` zorunlu oldugu icin lock kapsami mevcut task icinde genisletildi; bu yeni task nedeni degildir.

