# AGENTS.md

Bu repo'ya gelen her ajan bu dosyayi ve asagidaki iki dokumani okumadan ise baslamaz:
- `docs/REPO_DISCIPLINE_TR.md`
- `docs/MULTI_AGENT_RULES_TR.md`

## Zorunlu WSL Hizalama (Hard Guard)
Ajan terminali `D:\orkestram` altinda acilsa bile kod degisikligine gecmeden once su adimlar zorunludur:
1. `git branch --show-current` ile kendi gorev branch'i dogrulanir (`agent/<ajan>/<task-id>`).
2. `docs/TASK_LOCKS.md` icinde gorev lock'u `active` ve dosya listesi dogrulanir.
3. WSL kaynak kaniti alinmadan kod/doc degisikligine gecilmez:
   - `wsl -e bash -lc "cd /home/bekir/orkestram-<slot> && pwd && git rev-parse --show-toplevel && git branch --show-current && git status --short"`
4. Kanit sonucu `/home/bekir/orkestram-<slot>` degilse durum `REALIGN_REQUIRED` kabul edilir; calisma durdurulur ve WSL workdir ile yeniden baslatilir.

## Zorunlu Baslangic Protokolu
1. `git fetch --all --prune`
2. Sadece kendi branch'inde calis:
   - `agent/<ajan>/<task-id>`
3. Gorev almadan lock ac:
   - `docs/TASK_LOCKS.md` icine tek `active` kayit
4. Sadece lock'ta yazan dosyalara dokun.
5. Is bitince lock'u `closed` yap.

## Zorunlu Dogrulama
Commit/push oncesi zorunlu:
```powershell
powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick
```
PASS olmadan commit/push yasak.

## Kanit Zorunlulugu
Her gorev sonunda su 3 ciktinin paylasilmasi zorunlu:
1. `git branch --show-current`
2. `git status --short`
3. `pre-pr` sonucu (PASS/FAIL)

## Ihlal Durumu
Kural ihlalinde calisma durdurulur, once su duzeltilir:
1. branch
2. lock
3. dogrulama
4. WSL hizalama kaniti

