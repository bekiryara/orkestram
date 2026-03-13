# Category Workspace (Izole Calisma Alani)

Bu alan gecici ve izoledir.

Kural:
1. Tum kategori analizleri, raporlar, csv ciktilari sadece bu klasorde tutulur.
2. Ana repo plan belgelerine bu asamada guncelleme yapilmaz.
3. Buradaki calisma kilitlenmeden import/deploy veya ana dokumana tasima yapilmaz.

Kaynak:
1. `D:\stack-data\catalog-dataset\csv\_imports\armut\categories_candidates_wave1_mapped.csv`

Mevcut hedef:
1. Armut setinden sitemize uyan tum kategorileri deterministik sekilde tespit etmek.
2. Disarda ihtiyac kategorisi birakmamak.
3. Sonucu iki sete indirmek:
   - Genis uygun havuz
   - Operasyona hazir cekirdek set

Bu klasor yapisi:
1. `01-source`: kaynak referanslari ve schema notlari
2. `02-rules`: deterministik filtre kurallari
3. `03-analysis`: ara analiz raporlari
4. `04-approved-tree`: onaylanan agac gorunumu
5. `05-db-ready`: DB'ye girecek final csv
6. `06-pending-queue`: bekleyecek kategoriler
7. `07-decisions`: karar logu
8. `exports`: script ciktilari
