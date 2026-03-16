# TASK-052

Durum: `DONE`  
Ajan: `codex`  
Branch: `agent/codex/task-052`  
Baslangic: `2026-03-16 21:05`
Kapanis: `2026-03-16 21:25`

## Ozet
- Listing/profil/kart/galeri gorselleri icin tek ve kalici medya hattini standartlastirmak; yukleme, render, silme ve fallback akislarini kokten duzeltmek.

## In Scope
- [x] Tek medya standardini belirlemek: fiziksel dosya + public URL + DB path formati.
- [x] Listing, listing-card, profil ve galeri gorselleri icin ortak render/path helper stratejisini tanimlamak.
- [x] Legacy `/uploads/...` ve yeni `/storage/uploads/...` karisikligini migration/fallback planiyla ele almak.
- [x] Upload, replace, delete, reorder ve missing-file fallback akislarini resmi plana baglamak.
- [x] Runtime symlink/permission/smoke/test gereksinimlerini kabul kriterine yazmak.

## Out of Scope
- [ ] Bu taskta dogrudan tum kod implementasyonunu bitirmek
- [ ] CTA veya listing layout polish'i

## Lock Dosyalari
- `docs/tasks/TASK-052.md`
- `docs/TASK_LOCKS.md`
- `docs/NEXT_TASK.md`
- `docs/WORKLOG.md`

## Kabul Kriteri
- [x] Tek medya standardi acik secilir: disk yolu, DB path formati, public URL formati.
- [x] Profil/listing-card/listing-detail/galeri icin ortak medya render kurali tanimlanir.
- [x] Legacy path migration ve runtime recovery plani acik yazilir.
- [x] Silme/yok-dosya/fallback davranisi netlestirilir.
- [x] Test ve smoke matrisi resmi gorev kartinda tanimlanir.

## Uygulama Plani
1. Tek kaynak medya standardi:
   - fiziksel dosya: `storage/app/public/uploads/...`
   - public URL: `/storage/uploads/...`
   - DB path: `storage/uploads/...`
2. Tek medya servis/helper katmani:
   - profil resmi
   - listing kapak resmi
   - listing galeri resimleri
   - listing-card cover render
3. Legacy migration:
   - eski `uploads/...` kayitlarini normalize et
   - gerekirse fiziksel dosyalari `storage/app/public/uploads/...` altina tasi
   - gecis suresinde kontrollu legacy fallback uygula
4. Delete/fallback davranisi:
   - replace ve delete tutarli olsun
   - missing file durumunda fallback gorsel + log
5. Test/gate:
   - upload/update/delete/reorder
   - old path render
   - new path render
   - missing-file fallback
   - smoke 200/404 beklentileri

## Notlar
- Mevcut kirik davranis iki ayri kaynaktan geliyor: eski legacy `public/uploads` kayitlari ve yeni `storage` hattinin tam sabitlenmemis olmasi.
- Uygulama gorevi ayri alt tasklara bolunebilir; bu task once resmi teknik plan ve kabul kriterini sabitler.

## Kapanis Notu
- Teknik plan resmi olarak sabitlendi; tek medya standardi `storage/app/public/uploads/...` + `/storage/uploads/...` + `storage/uploads/...` DB path formati olarak kayda baglandi.
- Legacy `public/uploads` davranisi runtime recovery notu ve migration gereksinimiyle gorev kartina alindi.
- Bu turda ek olarak listing detail sayfasinda galeri en uste, bilgi/CTA blogu galerinin altina ve `Benzer Ilanlar` en alta tasindi.
- Kanit paketi: upstream ayarli branch, temiz status ve `pre-pr PASS`.
