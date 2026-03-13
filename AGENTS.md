# AGENTS.md

Bu repo'ya gelen her ajan bu dosyayi ve asagidaki iki dokumani okumadan ise baslamaz:
- `docs/REPO_DISCIPLINE_TR.md`
- `docs/MULTI_AGENT_RULES_TR.md`

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
powershell -ExecutionPolicy Bypass -File D:\orkestram\scripts\pre-pr.ps1 -Mode quick
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
