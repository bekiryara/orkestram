# REFERANS O MURGA KARARI (v1)

Tarih: 2026-03-09
Alan: docs/category-workspace

Karar:
1. Ana kategori omurgasi DUGUN.COM agacindan gelir.
2. DUGUNBUKETI.COM pazar dogrulama katmani olarak kullanilir.
3. ARMUT verisi sadece kapsam genisletme icin kullanilir; omurgayi bozmaz.

Kaynak Dosyalari:
1. 01-source/DUGUN_COM_TREE_2026-03-09.txt
2. 01-source/DUGUNBUKETI_COM_TREE_2026-03-09.txt
3. Armut kaynak CSV: D:\stack-data\catalog-dataset\csv\_imports\armut\categories_candidates_wave1_mapped.csv

Siradaki Teknik Adim:
1. DUGUN.COM kategorilerini canonical main set olarak kilitle.
2. DUGUNBUKETI ile kesisim tablosu cikar.
3. Armut satirlarini canonical sete map et.
4. Cikti:
   - full_scope_final.csv
   - core_seed.csv
   - pending_queue.csv
   - FINAL_TREE_CANONICAL.txt
