# Koordinator Bootstrap (TR)

Tarih: 2026-03-19

Amac:
1. Yeni gelen koordinatorun ilk 5 dakikada repo disiplinine hizalanmasini saglamak.
2. Ilk cevap, task karari, WSL kaniti ve kapanis akislarini tek yerde toplamak.
3. Ortak koordinasyon belgelerinde gereksiz drift uretmeden calismayi sabitlemek.

## 1) Ilk 5 Dakika Akisi

1. Ilk cevapta yalniz sabit 4 satir kullan:
   - `aktif branch: ...`
   - `aktif task durumu: ...`
   - `karar: mevcut task devam | yeni task ac`
   - `sonraki adim: ...`
2. Sonra su dosyalari bu sirayla oku:
   - `AGENTS.md`
   - `docs/REPO_DISCIPLINE_TR.md`
   - `docs/MULTI_AGENT_RULES_TR.md`
   - `docs/SESSION_HANDOFF_TR.md`
   - `docs/NEXT_TASK.md`
   - `docs/TASK_LOCKS.md`
   - bu dokuman
3. `powershell -ExecutionPolicy Bypass -File scripts/agent-status.ps1 -Detailed` ile worktree gorunurlugunu al.
4. WSL hizalama kanitini al:
   - `wsl -e bash -lc "cd /home/bekir/orkestram-k && pwd && git rev-parse --show-toplevel && git branch --show-current && git status --short"`
5. `git remote -v` ve `git branch -vv` ciktilarini dogrula.`r`n6. Komut katmanini dogrula:`r`n   - `git` icin WSL`r`n   - `scripts/*.ps1` icin PowerShell`r`n   - uygulama ici test icin container

## 2) Ilk Karar Kurali

1. Aktif task ayni hedef ve kabul kriteri icinde ise:
   - `mevcut task devam`
2. Hedef ayni, yalniz yeni dosya veya lock alani gerekiyorsa:
   - task genislet
3. Yeni kabul kriteri, yeni risk sinifi, ayri owner veya bagimsiz teslim gerekiyorsa:
   - yeni task ac
4. `agent-status` raporunda stale aday worktree varsa:
   - once `docs/SESSION_HANDOFF_TR.md` icine karar sinifini yaz
   - sonra yeni task veya dagitim kararina gec

## 3) Yeni Task Acma Sirasi

1. Repo genelinde 3 aktif task sinirini kontrol et.
2. Koordinatorun baska aktif taski olmadigini dogrula.
3. Task kartini ac.
4. `docs/TASK_LOCKS.md` kaydini `active` olarak ekle.
5. `docs/NEXT_TASK.md` panosunu senkronla.
6. Branch'i en son ac.
7. Sadece lock dosyalarinda calis.`r`n`r`nNot:`r`n1. `start-task.ps1` task/lock/pano yazip branch acilisinda UNC nedeniyle kirilirse durum `PARTIAL_OPEN` kabul edilir.`r`n2. Bu durumda kayitlar korunur, branch WSL icinde acilir ve handoff dosyasina not dusulur.

Standart komut:
```powershell
powershell -ExecutionPolicy Bypass -File scripts/start-task.ps1 -TaskId TASK-0xx -Agent codex -Files "path/one,path/two" -Note "kisa ozet"
```

## 4) Kapanis Sirasi

1. Task kartindaki gercek checklistleri doldur.
2. `docs/WORKLOG.md` icin tek kayit hazirla.
3. Mekanik kapanisi `scripts/close-task.ps1` ile uygula.
4. Gerekliyse `docs/SESSION_HANDOFF_TR.md` kaydini guncelle.
5. `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick` calistir.
6. Push kanitini topla.

Standart komut:
```powershell
powershell -ExecutionPolicy Bypass -File scripts/close-task.ps1 -TaskId TASK-0xx -Agent codex -ClosureNote "kisa kapanis ozeti" -WorklogTitle "baslik" -WorklogSummary "madde-1","madde-2" -Files "dosya-1","dosya-2" -Commands "komut-1" -Result PASS
```

## 5) WSL ve Git Notu

1. Git operasyonlari mumkun oldugunca WSL icinde yurutulur.
2. UNC path uzerinde Windows `git` yanlis remote veya sahte kirli status uretirse is durdurulur.
3. Bu durumda ayni kontrol `wsl -e bash -lc` icinde tekrar kosulur.
4. WSL kaniti temiz degilse yeni task acilmaz, kapanis da yapilmaz.`r`n5. Upstream olmayan yeni branch icin once `git push -u origin <branch>` ile baglama adimi tamamlanir, sonra `pre-pr` kosulur.

## 6) Stop Kurallari

1. 4 satirlik ilk cevap formati bozulmaz.
2. Lock yoksa degisiklik yok.
3. WSL kaniti yoksa dagitim yok.
4. `Edit Source != Mount Source` ise UI review yok.
5. `pre-pr` PASS yoksa kapanis yok.`r`n6. `ENV_BLOCKED`, `RUNTIME_BLOCKED` veya `SANDBOX_BLOCKED` sinifi varsa ayni komut tekrar edilmez; blocker resmi kayda yazilir.

