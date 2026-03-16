# TASK-033

Durum: `TODO`  
Ajan: `codex-a`  
Branch: `agent/codex-a/task-033`  
Baslangic: `2026-03-15 07:10`

## Ozet
- WSL tek-kaynak calisma kanit protokolunu ve ajan baslangic check-listini kalici dokuman haline getirmek.

## In Scope
- [ ] Ajan baslangicinda zorunlu kanit setini dokumante etmek (`pwd`, `git rev-parse --show-toplevel`, `git branch --show-current`).
- [ ] WSL calisma kokleri icin net komut ornekleri yazmak (`/home/bekir/orkestram-a`, `-b`, `-c`).
- [ ] Yanlis klasorde calisma tespit adimlarini yazmak (stop/recover proseduru).
- [ ] Hata durumunda tek adim rollback/check proseduru eklemek.

## Out of Scope
- [ ] Uygulama kod degisikligi
- [ ] DB/migration
- [ ] Deploy

## Lock Dosyalari
- `docs/tasks/TASK-033.md`
- `docs/REPO_DISCIPLINE_TR.md`
- `docs/WSL_RUNTIME_PLAYBOOK_TR.md`

## Kabul Kriteri
- [ ] WSL tek-kaynak kanit protokolu tek sayfada, adim adim ve test edilebilir olur.
- [ ] Ajan baslangic check-listi net ve kisa olur.
- [ ] Recover adimlari yazilir ve belirsizlik kalmaz.
- [ ] `pre-pr` PASS

## Komutlar
```powershell
powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick
```

## Notlar
- Hedef: yeni ajan geldiginde ilk 2 dakikada dogru klasor/branch/lock durumunu kanitlayabilmek.

