# TASK-056

Durum: `DOING`  
Ajan: `codex-a`  
Branch: `agent/codex-a/task-056`  
Baslangic: `2026-03-17 00:37`

## Ozet
- Bozuk local git remote path temizligi ve fetch disiplini hizalamasi

## In Scope
- [ ] `.git/config` icindeki bozuk local remote path kayitlarini duzeltmek veya kaldirmak
- [ ] `git fetch --all --prune` komutunu temiz sonuc verecek hale getirmek
- [ ] Kapanista `docs/WORKLOG.md` icine kanit eklemek

## Out of Scope
- [ ] GitHub origin ayarlarini degistirmek
- [ ] Runtime/media koduna yeniden mudahale etmek

## Lock Dosyalari
- `docs/tasks/TASK-056.md`
- `docs/TASK_LOCKS.md`
- `docs/NEXT_TASK.md`
- `.git/config`
- `docs/WORKLOG.md`

## Kabul Kriteri
- [ ] `git fetch --all --prune` bozuk local remote path hatasi vermeden tamamlanir
- [ ] `git remote -v` ve `git branch -vv` ciktilari branch/upstream disiplinine uyar
- [ ] `pre-pr` PASS

## Komutlar
```powershell
powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick
```

## Notlar
- Bu gorev lokal git config temizligidir; repo kodu kapsami disinda operasyonel hijyen isidir
- Gerekirse eski local remote adlari silinip yeniden tanimlanabilir

