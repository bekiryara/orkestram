# Agent Lock Matrix (TR)

Amac:
1. Paralel ajanlari hizlandirirken dosya cakismasini engellemek.
2. Koordinatorun lock dagitim kararini deterministic hale getirmek.

## 1) Temel Kural

1. Ayni dosya veya ayni glob deseni iki aktif taskta locklanamaz.
2. Lock olmadan degisiklik yapilan is resmi is sayilmaz.
3. Ortak belge dosyalarini sadece koordinator degistirir.

## 2) Varsayilan Dagitim Matrisi

| rol | izinli alan | not |
|---|---|---|
| `codex` | `docs/NEXT_TASK.md`, `docs/TASK_LOCKS.md`, `docs/WORKLOG.md`, `docs/tasks/**`, `AGENTS.md`, koordinasyon dokumanlari | lock/pano/kapanis tek elde kalir |
| `codex-a` | runtime/startup/playbook dokumanlari | WSL baslangic kaniti ve startup standardi |
| `codex-b` | lock matrix ve paralel dagitim dokumanlari | cakismaz file-set ve karar agaci |
| `codex-c` | teslim checklisti, PR/dogrulama akisi | kanit paketi ve resume/kapanis |

## 3) Ortak Alanlar

Asagidaki alanlar koordinator onayi olmadan paylasilmaz:
1. `AGENTS.md`
2. `docs/REPO_DISCIPLINE_TR.md`
3. `docs/MULTI_AGENT_RULES_TR.md`
4. `docs/PR_FLOW_TR.md`
5. `docs/NEXT_TASK.md`
6. `docs/TASK_LOCKS.md`
7. `docs/WORKLOG.md`

Kural:
1. Ortak alan degisimi gerekiyorsa ya sadece koordinator yapar ya da koordinator lock'u gecici devralir.

## 4) Cakisma Karar Agaci

1. Dosya zaten baska aktif taskta lock'lu mu?
   - Evet: yeni task baslamaz, koordinator karar verir.
   - Hayir: lock acilir.
2. Degisim ortak belgeye mi tasiyor?
   - Evet: koordinatora devir zorunlu.
   - Hayir: ajan kendi lock alaninda devam eder.
3. Cakisma sonradan mi cikti?
   - Evet: her iki ajan durur, koordinator tek yeni dagitim notu yazar.

## 5) Lock Closure

Task kapanisinda zorunlu:
1. `git branch --show-current`
2. `git status --short`
3. `pre-pr PASS`
4. `docs/TASK_LOCKS.md` satirini guncelle
5. Gerekirse `docs/NEXT_TASK.md` durumunu koordinator duzeltir
