# Session Handoff (TR)

Guncelleme Zamani: $nowShort
Koordinator Branch: gent/codex/task-067
Koordinator Task: yok

## Aktif Tasklar
1. YOK - TASK-067 operasyon modeli, session handoff ve ajan durum panosu standardi ile kapatildi.

## Ajan / Worktree Durumu
1. codex
   - Worktree: /home/bekir/orkestram-k
   - Branch: gent/codex/task-067
   - Aktif task: yok
   - Status ozeti: u branch kapanis commitinden sonra temizlenmelidir
   - Not: Koordinator worktree bu taski kapattiktan sonra temiz duruma donecek.
2. codex-a
   - Worktree: /home/bekir/orkestram-a
   - Branch: gent/codex-a/task-056
   - Aktif task: yok
   - Status ozeti: 32 kirli dosya
   - Not: Aktif task kaydi olmadan kirli worktree; stale aday.
3. codex-b
   - Worktree: /home/bekir/orkestram-b
   - Branch: main
   - Aktif task: yok
   - Status ozeti: 40 kirli dosya
   - Not: main uzerinde kirli durum; stale aday.
4. codex-c
   - Worktree: /home/bekir/orkestram-c
   - Branch: main
   - Aktif task: yok
   - Status ozeti: 34 kirli dosya
   - Not: main uzerinde kirli durum; stale aday.

## Preview / Source Durumu
1. Bu oturum stale worktree gorunurlugu odaklidir; preview/source eslesmesi degistirilmemistir.
2. UI review kararlari icin mevcut Edit Source == Mount Source kurali gecerlidir.

## Bugun Alinan Kararlar
1. Stale worktree temizligi yeni urun tasklari icine gomulmeyecek.
2. Once gorunurluk, handoff ve ajan durum panosu standardi kurulacak.
3. Temizlik veya devralma gerekiyorsa sonraki taskta resmi karar ile ilerlenilecek.

## Acik Riskler
1. orkestram-b ve orkestram-c worktree'leri main uzerinde kirli gorunuyor.
2. orkestram-a aktif task olmadan kirli branch tasiyor.
3. WSL status sayimi ile Windows shell status gorunumu farkli olabilir; karar kaynagi olarak koordinator raporu standardize edilmelidir.

## Sonraki Adim
1. Stale worktree temizligi ve devralma standardi icin ayri task ac.
2. scripts/agent-status.ps1 ciktisini yeni task acma oncesi zorunlu okuma haline getir.
3. Gerekirse stale worktree temizligi oncesi karar kayitlarini docs/SESSION_HANDOFF_TR.md icinde guncelle.
