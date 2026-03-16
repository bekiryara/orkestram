# TASK-048

Durum: `DOING`  
Ajan: `codex`  
Branch: `agent/codex/task-048`  
Baslangic: `2026-03-16 17:45`
Kapanis: `-`

## Ozet
- Iki bagli problemi resmi olarak koordine etmek: once listing gorsel/runtime dosya hatti, sonra listing detail bilgi ve aksiyon hiyerarsisi.

## In Scope
- [x] Teknik gorev ve urun gorevi icin koordinator taskini acmak.
- [x] Isi cakismasiz alt gorevlere dagitmak.
- [~] Ajan teslimlerini entegre etmek.
- [ ] `pre-pr` PASS ile resmi kapanis vermek.

## Out of Scope
- [ ] Merge/distribution disi unrelated runtime bakimi
- [ ] Yeni feature butonlari eklemek

## Lock Dosyalari
- `docs/tasks/TASK-048.md`
- `docs/tasks/TASK-049.md`
- `docs/tasks/TASK-050.md`
- `docs/tasks/TASK-051.md`
- `docs/TASK_LOCKS.md`
- `docs/NEXT_TASK.md`
- `docs/WORKLOG.md`

## Kabul Kriteri
- [x] Teknik dosya hatti gorevi ayri lock ile acilir.
- [x] Listing detail UX gorevi ayri lock alanlariyla dagitilir.
- [~] Runtime kaniti + detail hiyerarsi entegrasyonu tamamlanir.
- [ ] `pre-pr` PASS

## Komutlar
```powershell
powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick
```

## Notlar
- Teknik problem urun polish oncesi cozulmelidir.
- `codex-c` bu turda runtime medya hatti ve admin upload akislarini tarayan teknik ajan olarak kullanilacaktir.
- `TASK-051` (media/runtime) ve `TASK-049` (detail hierarchy) koordinator branch'ine entegre edildi.
- `TASK-050` teslim notu kabul edilmis olsa da `origin/agent/codex-b/task-050` beklenen kart/CSS commitini icermiyor; remote stale oldugu icin entegrasyon bloke edildi.
