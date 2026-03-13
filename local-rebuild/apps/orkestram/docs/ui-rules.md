# UI Rules (Zorunlu)

Bu repoda yeni ekran/alan geliştirmelerinde temel UI kuralı:

1. UI library standardı: `Bootstrap 5.3`
2. Yeni geliştirmelerde öncelik sırası:
   - Bootstrap component class'ları
   - Mevcut `v1.css` tema sınıfları
   - Zorunluysa küçük, sayfaya özel scoped CSS
3. Yasaklar:
   - Rastgele yeni global renk/font/spacing sistemi
   - Aynı işi yapan ikinci buton/kart/input class'ı üretmek
   - Inline style ile kalıcı çözüm
4. Rol farklılığı yalnızca içerik/izin seviyesinde yapılır; tema farklılığı yapılmaz.
5. `portal`, `frontend`, `auth` ekranları aynı header/düğme/form dilini korumalıdır.

## PR Kontrolü

Her UI değişikliği için kontrol edilmesi gerekenler:

1. Bootstrap komponenti kullanıldı mı?
2. Yeni CSS eklendiyse gerçekten gerekli mi?
3. Mobil görünümde kırılım var mı?
4. Aynı aksiyon ekranda iki kez görünmüyor mu?
