# NEXT TASK (Koordinasyon Panosu)

Durum: `ACTIVE`  
Koordinator: `codex`  
Kaynak Gercek: `docs/TASK_LOCKS.md`

## Aktif Gorevler (Tek Kaynak)
1. `TASK-026` - `codex-b` - Public liste/hizmet kategori fiyat filtre + siralama.
2. `TASK-027` - `codex-c` - Listing detail structured data Offer (SEO).

## Hazir Tasklar (Atama Bekliyor)
1. Yeni task acilacaksa once `TASK_LOCKS.md` aktif lock cakismazligi kontrol edilir.

## Son Kapanis
1. `TASK-025` - Fiyat veri modeli + admin/owner parity tamamlandi (commit `3ff02ff`, pre-pr PASS).

## Kapanis Kurali (Zorunlu)
1. Kapanis kaniti olmadan task `closed` edilemez.
2. Zorunlu kanit paketi:
   - `git branch --show-current`
   - `git status --short`
   - `powershell -ExecutionPolicy Bypass -File D:\orkestram\scripts\pre-pr.ps1 -Mode quick` sonucu `PASS`
3. Kanit yoksa task durumu `active` kalir.
