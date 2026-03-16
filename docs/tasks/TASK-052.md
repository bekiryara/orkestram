# TASK-052

Durum: `DOING`  
Ajan: `codex`  
Branch: `agent/codex/task-052`  
Baslangic: `2026-03-16 21:05`
Kapanis: `-`

## Ozet
- Listing/profil/kart/galeri gorselleri icin tek ve kalici medya hattini standartlastirmak; yukleme, render, silme ve fallback akislarini kokten duzeltmek.

## In Scope
- [ ] Tek medya standardini belirlemek: fiziksel dosya + public URL + DB path formati.
- [ ] Listing, listing-card, profil ve galeri gorselleri icin ortak render/path helper stratejisini tanimlamak.
- [ ] Legacy `/uploads/...` ve yeni `/storage/uploads/...` karisikligini migration/fallback planiyla ele almak.
- [ ] Upload, replace, delete, reorder ve missing-file fallback akislarini resmi plana baglamak.
- [ ] Runtime symlink/permission/smoke/test gereksinimlerini kabul kriterine yazmak.

## Out of Scope
- [ ] Bu taskta dogrudan tum kod implementasyonunu bitirmek
- [ ] CTA veya listing layout polish'i

## Lock Dosyalari
- `docs/tasks/TASK-052.md`
- `docs/TASK_LOCKS.md`
- `docs/NEXT_TASK.md`
- `docs/WORKLOG.md`

## Kabul Kriteri
- [ ] Tek medya standardi acik secilir: disk yolu, DB path formati, public URL formati.
- [ ] Profil/listing-card/listing-detail/galeri icin ortak medya render kurali tanimlanir.
- [ ] Legacy path migration ve runtime recovery plani acik yazilir.
- [ ] Silme/yok-dosya/fallback davranisi netlestirilir.
- [ ] Test ve smoke matrisi resmi gorev kartinda tanimlanir.

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
