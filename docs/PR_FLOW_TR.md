# PR Akisi (TR)

Amac: Main branch'e sadece kontrollu, testli ve izlenebilir degisim gitsin.

## Standart Akis
1. Gorev sec:
   - `docs/NEXT_TASK.md`
2. Branch ac:
   - `git checkout -b feat/<kisa-ad>`
3. Degisiklik yap + commit
4. PR oncesi zorunlu:
   - `powershell -ExecutionPolicy Bypass -File D:\orkestram\scripts\pre-pr.ps1 -Mode quick`
5. Push:
   - `git push -u origin feat/<kisa-ad>`
6. GitHub PR ac:
   - base: `main`, compare: `feat/<kisa-ad>`
7. `ci-gate` PASS olmadan merge yok
8. Merge sonrasi:
   - `docs/WORKLOG.md` ve `docs/PROJECT_STATUS_TR.md` guncelle

## Kapanis Kaniti Zorunlu (Yeni)
1. Task `closed` isaretlemeden once su 3 kanit zorunludur:
   - `git branch --show-current`
   - `git status --short`
   - `powershell -ExecutionPolicy Bypass -File D:\orkestram\scripts\pre-pr.ps1 -Mode quick` -> `PASS`
2. Bu kanitlardan biri eksikse:
   - Task lock satiri `active` kalir.
   - PR acilsa bile merge edilmez.
3. Koordinator, `docs/TASK_LOCKS.md` ve `docs/NEXT_TASK.md` uyumsuzlugunu ayni turda duzeltir.

## Yasaklar
1. Main'e direkt push
2. Test calistirmadan PR acma
3. PR template'i bos gecme
