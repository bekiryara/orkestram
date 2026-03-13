# Feedback V1 (Yorum + Begeni)

Tarih: 2026-03-12  
Durum: Aktif Canonical

## Kapsam
1. Feedback sistemi sadece iki turden olusur:
- `comment` (yorum)
- `like` (begeni)
2. `question` turu aktif sistemde yoktur.
3. Mesaj sistemi bu dokumanin disindadir ve `MESAJ_V1_TR.md` ile yonetilir.

## Hedef Davranis
1. Listing detay CTA:
- `Mesaj Gonder` -> mesaj merkezi
- `Yorum Yaz` -> feedback/comment
- `Begeni Birak` -> feedback/like
2. Customer `Hesabim`:
- `Yorumlarim` gorunur
- `Sorularim` yok
3. Owner panel:
- `Ilan Mesajlari` + `Yorumlar` gorunur
- `Sorular` yok
4. Admin/Support:
- Legacy engagements moderasyon ekranlari aktif akista yok

## Veri Modeli
1. `listing_feedback`
- aktif `kind` degerleri: `comment`, `message`
- `question` kayitlari tarihsel veri olarak kalir, yeni olusmaz
2. `listing_likes`
- tekil begeni: `listing_id + actor_key` unique

## Endpoint Sozlesmesi
1. `POST /customer/feedbacks`
- `kind` yalniz `comment|message`
- `question` gonderimi validation hatasi verir
2. `POST /customer/feedbacks/like`
- idempotent begeni
3. `GET /customer/feedbacks`
- legacy uyumluluk icin redirect: `/hesabim?tab=comments`
4. `GET /owner/feedbacks?kind=comment|message`
- owner yorum/mesaj ekrani
- `kind=question` desteklenmez
5. Legacy not:
- `customer/engagements` ve `owner/engagements` yollari sistemden kaldirilmistir.
- Yalnizca `feedback` yollari gecerlidir.

## Yetki Tokenlari
1. Feedback:
- `customer.feedback.view`
- `customer.feedback.create`
- `owner.feedback.view`
- `owner.feedback.manage`
2. Mesaj:
- `customer.message.view`

## Deterministik Kurallar
1. Yorum aksiyonu mesaj ekranina dusmez.
2. Begeni aksiyonu yorum kaydi olusturmaz.
3. Mesaj aksiyonu feedback yorum akisini bozmaz.
4. Owner ve customer davranisi iki appte birebir aynidir.

## Kabul Kriterleri
1. UI'da `Soru Sor`, `Sorularim`, `Sorular` label'i yok.
2. Listing CTA'da yorum ve begeni ayrik calisir.
3. Legacy engagements admin/support route'lari aktif degil.
4. Mesaj V1 regression yok.

## Referanslar
1. `MESAJ_V1_TR.md`
2. `ETKILESIM_MERKEZI_TEMIZLIK_PLANI_TR.md`
