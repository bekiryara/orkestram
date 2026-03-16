# TASK-033

Durum: `DONE`  
Ajan: `codex-a`  
Branch: `agent/codex-a/task-033`  
Baslangic: `2026-03-16 08:14`

## Ozet
- WSL runtime calisma akisini tek dokumanda netlestir.

## In Scope
- [x] Baslangic kaniti adimlarini tanimla.
- [x] WSL workdir kurallarini netlestir.
- [x] Stop/recover ve startup checklistini yaz.

## Out of Scope
- [x] Kod veya script davranisi degistirmek yok.
- [x] Hedef dokuman disinda operasyonel akisi genisletmek yok.

## Lock Dosyalari
- `docs/tasks/TASK-033.md`
- `docs/TASK_LOCKS.md`
- `docs/WSL_RUNTIME_PLAYBOOK_TR.md`

## Kabul Kriteri
- [x] Baslangic kaniti acik komutlarla yazili.
- [x] WSL workdir ve tek kaynak yolu net.
- [x] Stop/recover ve startup checklisti uygulanabilir.
- [ ] `pre-pr` PASS

## Komutlar
```powershell
powershell -ExecutionPolicy Bypass -File D:\orkestram\scripts\pre-pr.ps1 -Mode quick
```

## Notlar
- Dokuman bu task ile ilk kez olusturuluyor.
- `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick` bu ortamda `powershell: command not found` ile bloke oldu.
