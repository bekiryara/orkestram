# TASK-036

Durum: `DONE`  
Ajan: `codex`  
Branch: `agent/codex/task-036`  
Baslangic: `2026-03-16 09:51`
Kapanis: `2026-03-16 10:20`

## Ozet
- Repo disiplinine 3 zorunlu kuralin eklenmesi:
  - Task ID tekrar kullaniminin yasaklanmasi
  - Koordinator cevap sablonunun sabitlenmesi
  - Remote/upstream dogrulamasinin zorunlu hale getirilmesi

## In Scope
- [x] `TASK-036` resmi lock kaydini `active` acmak.
- [x] Koordinator cevap sablonunu dokumanlarda sabitlemek.
- [x] Task ID tekrar kullanimini dokuman ve script seviyesinde engellemek.
- [x] Remote/upstream dogrulamasini `pre-pr` kapisinda zorunlu hale getirmek.
- [x] `pre-pr -Mode quick` calistirip sonucu gorev kaydina islemek.
- [x] Gorev kapanisinda lock'u `closed` durumuna cekmek.

## Out of Scope
- [ ] Uygulama is kurali degisikligi.
- [ ] Frontend/UX degisikligi.
- [ ] Deploy veya production ayari.

## Lock Dosyalari
- `docs/tasks/TASK-036.md`
- `docs/TASK_LOCKS.md`
- `docs/NEXT_TASK.md`
- `docs/WORKLOG.md`
- `docs/REPO_DISCIPLINE_TR.md`
- `docs/MULTI_AGENT_RULES_TR.md`
- `docs/AGENT_DELIVERY_CHECKLIST_TR.md`
- `scripts/pre-pr.ps1`
- `scripts/start-task.ps1`

## Kabul Kriteri
- [x] Task ID tekrar kullanimi "yasak" olarak resmi kurala baglanir.
- [x] Koordinator cevap sablonu sabit formatla dokumanlasir.
- [x] `pre-pr` remote/upstream dogrulamasini fail-fast sekilde uygular.
- [x] `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick` => PASS

## Komutlar
```powershell
powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick
```

## Notlar
- `git fetch --all --prune` bu ortamda `a/b/c/canonical` local remote tanimlari nedeniyle fail verebiliyor; bu durum WORKLOG'da notlanir.
