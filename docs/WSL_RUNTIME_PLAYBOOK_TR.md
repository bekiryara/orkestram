# WSL Runtime Playbook (TR)

Amac:
1. Ajanin dogru WSL klasorunde, dogru branch'te ve dogru lock ile basladigini 2 dakika icinde kanitlamak.
2. Yanlis workdir veya yanlis branch durumunda kod/doc degisikligine gecmeden durdurmak.

## 1) Resmi Calisma Kokleri

Kural:
1. Canonical kaynak: `/home/bekir/orkestram`
2. Koordinator workdir: `/home/bekir/orkestram-k`
3. Ajan workdirleri:
   - `codex-a` -> `/home/bekir/orkestram-a`
   - `codex-b` -> `/home/bekir/orkestram-b`
   - `codex-c` -> `/home/bekir/orkestram-c`
4. `D:\orkestram` calisan kaynak degildir.

## 2) Zorunlu Baslangic Kaniti

Her ajan kendi WSL klasorunde su 4 komutu verir:

```bash
pwd
git rev-parse --show-toplevel
git branch --show-current
git status --short
```

Beklenen:
1. `pwd` kendi slot klasorunu gosterir.
2. `git rev-parse --show-toplevel` ilgili WSL repo kokunu gosterir.
3. Branch `agent/<ajan>/<task-id>` formatindadir.
4. `git status --short` bos veya beklenen degisiklikleri gosterir.

## 3) Hazir Baslatma Komutlari

Koordinator:

```bash
cd /home/bekir/orkestram-k
git branch --show-current
git status --short
```

Ajan A:

```bash
cd /home/bekir/orkestram-a
git checkout -b agent/codex-a/task-033
pwd
git rev-parse --show-toplevel
git branch --show-current
git status --short
```

Ajan B:

```bash
cd /home/bekir/orkestram-b
git checkout -b agent/codex-b/task-034
pwd
git rev-parse --show-toplevel
git branch --show-current
git status --short
```

Ajan C:

```bash
cd /home/bekir/orkestram-c
git checkout -b agent/codex-c/task-035
pwd
git rev-parse --show-toplevel
git branch --show-current
git status --short
```

## 4) Stop / Recover Protokolu

Asagidaki durumlardan biri varsa `REALIGN_REQUIRED`:
1. `pwd` WSL slot klasoru degilse
2. Branch `agent/<ajan>/<task-id>` degilse
3. Lock alinmadan degisiklik goruluyorsa
4. `git status --short` beklenmeyen dosyalar gosteriyorsa

Uygulanacak adim:
1. Kod/doc degisikligini durdur.
2. Koordinatora sadece kanit ciktisini ilet.
3. Dogru WSL klasorune gec.
4. Branch ve lock'u yeniden dogrula.
5. Ancak sonra devam et.

## 5) Hata Durumu Kontrolu

Kisa kontrol sirasi:
1. `pwd`
2. `git branch --show-current`
3. `git status --short`
4. `docs/TASK_LOCKS.md`
5. `docs/NEXT_TASK.md`

Kural:
1. Bu 5 kontrol temiz degilse `pre-pr` veya commit'e gecilmez.
