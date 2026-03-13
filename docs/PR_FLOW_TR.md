# PR Akisi (TR)

Amac: Main branch'e sadece kontrollu, testli ve izlenebilir degisim gitsin.

## Standart Akis
1. Gorev sec:
   - `docs/NEXT_TASK.md`
   - `docs/TASK_LOCKS.md` (lock sahipligini kontrol et)
2. Branch ac:
   - Ajan kurali: `agent/<agent-id>/task-xxx`
3. Degisiklik yap + commit
4. PR oncesi zorunlu:
   - `powershell -ExecutionPolicy Bypass -File D:\orkestram\scripts\pre-pr.ps1 -Mode quick`
   - Kapanis kaniti:
     - `git branch --show-current`
     - `git status --short`
5. Push:
   - `git push -u origin agent/<agent-id>/task-xxx`
6. GitHub PR ac:
   - base: `main`, compare: `agent/<agent-id>/task-xxx`
7. `ci-gate` PASS olmadan merge yok
8. Merge sonrasi:
   - `docs/WORKLOG.md` ve `docs/PROJECT_STATUS_TR.md` guncelle
9. Task kapatma:
   - `docs/TASK_LOCKS.md` satirinda `status=closed`, `updated_at`, kisa kapanis notu zorunlu

## Yasaklar
1. Main'e direkt push
2. Test calistirmadan PR acma
3. PR template'i bos gecme
4. Lock dosyasinda sahibi olmayan dosyaya yazma
