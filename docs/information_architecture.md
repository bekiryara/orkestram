# Information Architecture (Draft v1)

## Content Types
- `page`: kurumsal sabit sayfalar
- `service_page`: hizmet odakli landing sayfalar
- `city_page`: il/ilce odakli SEO sayfalari
- `listing`: grup/ilan/profil sayfalari
- `blog_post`: blog icerikleri

## URL Rules
- Mevcut degerli URL mumkunse korunur.
- Yeni standart:
  - `/hizmet/{slug}`
  - `/{sehir}/{hizmet-slug}`
  - `/ilan/{slug}`
  - `/blog/{slug}`

## Redirect Policy
- Hedef sayfa hazir olmadan redirect yok.
- Toplu anasayfaya/kategoriye redirect yok.
- 301 sadece niyet eslesmesi varsa.
