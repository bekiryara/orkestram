# Etkilesim Merkezi Temizlik Plani (TR)

Tarih: 2026-03-12  
Durum: Aktif Uygulama Plani

## Hedef Mimari
1. Mesaj sistemi: `MESAJ_V1_TR.md` (ayri ve tek omurga).
2. Feedback sistemi: sadece `yorum + begeni`.
3. `soru` akisi tamamen kaldirilir.
4. Legacy "Etkilesim Merkezi" ekranlari aktif akistan cikarilir.

## Kapsam
1. Frontend listing CTA:
- Mesaj Gonder
- Yorum Yaz
- Begeni Birak
- `Soru Sor` kaldirilir.
2. Customer panel:
- `Sorularim` sekmesi kaldirilir.
- `Yorumlarim` korunur.
3. Owner panel:
- `Sorular` sekmesi kaldirilir.
- `Ilan Mesajlari` + `Yorumlar` korunur.
4. Admin/Support:
- Legacy engagements menuleri ve route'lari kaldirilir.
5. ACL:
- Kaldirilan route/ekranlara ait yetki tokenlari temizlenir.

## Uygulama Stratejisi
1. Davranisi degistir, veriyi koru:
- DB tablolari (`listing_feedback`, `listing_likes`) silinmez.
- `kind=question` yeni kayit olarak engellenir.
- Eski `question` kayitlari tarihsel veri olarak kalir.
2. UI/Route deterministic temizleme:
- Sadece kullanilan ekranlar menude gorunur.
- Yanlis yonlendirmeler (yorum -> mesaj gibi) kapatilir.
3. Iki app parity:
- `apps/orkestram` ve `apps/izmirorkestra` birebir ayni degisikligi alir.

## Riskler ve Onlem
1. Risk: Eski linkler 404 olur.
- Onlem: kritik eski yollar kontrollu redirect veya menuden kaldirma.
2. Risk: ACL drift.
- Onlem: route + middleware + config birlikte guncellenir.
3. Risk: Mesaj V1 regress.
- Onlem: mesaj endpoint ve componentlerine dokunmadan sadece baglantilar sadeletilir.

## Kabul Kriterleri
1. Listing sayfasinda `Soru Sor` yok.
2. Yorum ve begeni aksiyonlari dogru endpoint'e gider.
3. `/hesabim` icinde `Sorularim` sekmesi yok.
4. `/owner` icinde `Sorular` sekmesi yok.
5. Admin ve support panelinde legacy engagements girisi yok.
6. Mesaj V1 akislari aynen calisir.
