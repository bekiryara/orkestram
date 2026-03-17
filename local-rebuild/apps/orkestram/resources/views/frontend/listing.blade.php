@extends('frontend.layout')

@php
    $metaTitle = $item->seo_title ?: $item->name;
    $metaDescription = $item->seo_description ?: ($item->summary ?: 'Ilan detayi');
    $priceLabelRaw = trim((string) ($item->price_label ?? ''));
    $parsedPrice = null;
    if (preg_match('/\d+(?:[.,]\d+)?/', $priceLabelRaw, $matches)) {
        $numeric = str_replace(',', '.', (string) $matches[0]);
        if (is_numeric($numeric)) {
            $parsedPrice = (float) $numeric;
        }
    }
    $offerPrice = number_format((float) ($parsedPrice ?? 0), 2, '.', '');
    $offerCurrency = strtoupper((string) data_get($item, 'meta_json.price_currency', 'TRY'));
    if ($offerCurrency === '') {
        $offerCurrency = 'TRY';
    }
    $categoryLabel = trim((string) ($item->service_type ?: 'Muzik Hizmeti'));
    $locationLabel = trim(collect([$item->city, $item->district])->filter()->implode(' / '));
    if ($locationLabel === '') {
        $locationLabel = 'Konum bilgisi yakinda guncellenecek';
    }
    $identityItems = array_values(array_filter([
        ['label' => 'Kategori', 'value' => $categoryLabel],
        ['label' => 'Sehir', 'value' => trim((string) ($item->city ?: 'Konum bekleniyor'))],
        ['label' => 'Bolge', 'value' => trim((string) ($item->district ?: 'Tum bolgeler'))],
    ], fn ($item) => trim((string) ($item['value'] ?? '')) !== ''));
    $mainImage = null;
    if (!empty($item->cover_image_path)) {
        $mainImage = (string) $item->cover_image_path;
    } elseif (is_array($item->gallery_json) && count($item->gallery_json)) {
        $mainImage = (string) $item->gallery_json[0];
    }
    $hasCustomMedia = $mainImage !== null;
    $mainImageUrl = $mainImage ? \App\Support\MediaPath::listingUrl($mainImage) : '/assets/listing-fallback.svg';
    $galleryItems = [];
    if (is_array($item->gallery_json) && count($item->gallery_json)) {
        foreach ($item->gallery_json as $img) {
            $galleryUrl = \App\Support\MediaPath::listingUrl($img);
            if ($galleryUrl !== $mainImageUrl) {
                $galleryItems[] = $galleryUrl;
            }
        }
    }
    $listingOfferJsonLd = [
        '@context' => 'https://schema.org',
        '@type' => 'Service',
        'name' => (string) $item->name,
        'description' => (string) ($item->summary ?: 'Ilan detayi'),
        'offers' => [
            '@type' => 'Offer',
            'price' => $offerPrice,
            'priceCurrency' => $offerCurrency,
            'availability' => 'https://schema.org/InStock',
            'url' => route('listing.show', ['slug' => $item->slug]),
        ],
    ];
@endphp

@section('content')
    <script type="application/ld+json">{!! json_encode($listingOfferJsonLd, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
    @php($messageUrl = route('messages.index', ['box' => 'personal', 'listing' => $item->slug]))
    @php($messageLoginUrl = route('auth.login', ['next' => '/messages?'.http_build_query(['box' => 'personal', 'listing' => $item->slug, 'kind' => 'message'])]))
    @php($commentLoginUrl = route('auth.login', ['next' => '/ilan/' . $item->slug . '#yorumlar']))
    @php($likeLoginUrl = route('auth.login', ['next' => '/ilan/' . $item->slug]))

    <nav class="breadcrumbs">
        <a href="{{ route('home') }}">Ana Sayfa</a>
        <span>/</span>
        <a href="{{ route('listing.index') }}">Ilanlar</a>
        <span>/</span>
        <span>{{ $item->name }}</span>
    </nav>

    <article class="content listing-detail-page">
        <section class="listing-reference-hero">
            <aside class="listing-profile-panel">
                <div class="listing-profile-copy">
                    <p class="listing-eyebrow">{{ $categoryLabel }}</p>
                    <h1>{{ $item->name }}</h1>
                </div>

                <div class="listing-profile-meta" aria-label="Ilan kimligi">
                    @foreach($identityItems as $identity)
                        <div class="listing-profile-meta-row">
                            <span>{{ $identity['label'] }}</span>
                            <strong>{{ $identity['value'] }}</strong>
                        </div>
                    @endforeach
                </div>

                <div class="listing-price-band">
                    <span class="listing-price-label">Baslangic fiyati</span>
                    <strong class="listing-price-value">{{ $item->price_label ?: 'Bilgi icin iletisime gecin' }}</strong>
                    <p class="listing-price-note">Takvim ve ihtiyaca gore net teklif icin dogrudan iletisime gecin.</p>
                </div>

                <div class="listing-cta-panel-premium">
                    <div class="listing-cta-primary">
                        @if($item->phone)
                            <a class="btn btn-secondary" href="tel:{{ $item->phone }}">Ara</a>
                        @endif
                        @if($item->whatsapp)
                            <a class="btn btn-primary" target="_blank" rel="noopener" href="https://wa.me/{{ ltrim(preg_replace('/[^0-9]/', '', $item->whatsapp), '0') }}">WhatsApp Yaz</a>
                        @elseif($shellAuthenticated ?? false)
                            <a class="btn btn-primary" href="{{ $messageUrl }}">Mesaj Gonder</a>
                        @else
                            <a class="btn btn-primary" href="{{ $messageLoginUrl }}">Mesaj Gonder</a>
                        @endif
                    </div>

                    <div class="listing-cta-secondary">
                        @if(!$item->phone && !$item->whatsapp)
                            <a class="btn btn-outline-secondary" href="#detaylar">Aciklamayi Incele</a>
                        @endif
                        @if($shellAuthenticated ?? false)
                            <a class="btn btn-outline-secondary" href="{{ $messageUrl }}">Mesaj</a>
                            <form method="post" action="{{ route('customer.feedback.like') }}">
                                @csrf
                                <input type="hidden" name="listing_slug" value="{{ $item->slug }}">
                                <button type="submit" class="btn btn-outline-secondary">Begeni</button>
                            </form>
                        @else
                            <a class="btn btn-outline-secondary" href="{{ $messageLoginUrl }}">Mesaj</a>
                            <a class="btn btn-outline-secondary" href="{{ $likeLoginUrl }}">Begeni</a>
                        @endif
                        <a class="btn btn-outline-secondary" href="#yorumlar">Yorumlar</a>
                    </div>
                    @if(session('ok'))
                        <p class="meta mt-8">{{ session('ok') }}</p>
                    @endif
                </div>
            </aside>

            <div class="listing-media-panel">
                <div class="listing-showcase-frame{{ $hasCustomMedia ? '' : ' is-fallback' }}">
                    <button type="button" id="listing-open-lightbox" class="listing-main-button" data-img="{{ $mainImageUrl }}">
                        <img id="listing-main-image" class="listing-main-image{{ $hasCustomMedia ? '' : ' listing-main-image--fallback' }}" src="{{ $mainImageUrl }}" alt="{{ $item->name }}">
                    </button>
                </div>
            </div>
        </section>

        @if(count($galleryItems))
            <section class="listing-gallery-strip" aria-label="Galeri">
                <button type="button" class="thumb-btn is-active" data-img="{{ $mainImageUrl }}">
                    <img src="{{ $mainImageUrl }}" alt="{{ $item->name }} ana gorsel">
                </button>
                @foreach($galleryItems as $galleryImageUrl)
                    <button type="button" class="thumb-btn" data-img="{{ $galleryImageUrl }}">
                        <img src="{{ $galleryImageUrl }}" alt="{{ $item->name }} galeri">
                    </button>
                @endforeach
            </section>
        @endif

        <section id="detaylar" class="listing-block listing-story-block">
            @if($item->summary)
                <p class="listing-summary-lead">{{ $item->summary }}</p>
            @endif
            <div class="listing-richtext">{!! nl2br(e($item->content ?: 'Icerik girilmemis.')) !!}</div>
        </section>

        <section id="yorumlar" class="listing-block listing-comments-block">
            <div class="listing-section-head">
                <div>
                    <p class="listing-section-kicker">Yorumlar</p>
                    <h2>Musteri deneyimleri</h2>
                </div>
                <p class="meta">Bu alanda yalnizca onayli yorumlar gosterilir.</p>
            </div>

            @if($shellAuthenticated ?? false)
                <div id="yorum-formu" class="listing-comment-form">
                    <h3>Yorum Yaz</h3>
                    <form method="post" action="{{ route('customer.feedback.store') }}">
                        @csrf
                        <input type="hidden" name="listing_slug" value="{{ $item->slug }}">
                        <input type="hidden" name="kind" value="comment">
                        <input type="hidden" name="visibility" value="public">
                        <textarea class="form-control" name="content" rows="4" placeholder="Bu hizmetle ilgili deneyimini paylas..." required>{{ old('content') }}</textarea>
                        <div class="actions mt-8">
                            <button class="btn btn-primary" type="submit">Yorumu Gonder</button>
                        </div>
                    </form>
                </div>
            @endif

            <div class="listing-comments-list">
                @forelse(($publicComments ?? []) as $comment)
                    <div class="card listing-comment-card">
                        <p><strong>{{ $comment->user?->name ?? 'Musteri' }}</strong> <span class="meta">{{ optional($comment->created_at)->format('d.m.Y H:i') }}</span></p>
                        <p>{{ $comment->content }}</p>
                        @if(!empty($comment->owner_reply))
                            <p class="meta"><strong>Firma yaniti:</strong> {{ $comment->owner_reply }}</p>
                        @endif
                    </div>
                @empty
                    <p class="meta">Henuz onayli yorum yok.</p>
                @endforelse
            </div>
        </section>

        <section class="section listing-related-section">
            <div class="listing-section-head">
                <div>
                    <p class="listing-section-kicker">Benzer ilanlar</p>
                    <h2>Sonraki alternatifler</h2>
                </div>
                <p class="meta">Yorumlardan sonra karsilastirma yapabilmeniz icin en sonda listelenir.</p>
            </div>
            <div class="grid">
                @forelse($relatedListings as $related)
                    @php($relatedCover = $related->cover_image_path ? \App\Support\MediaPath::listingUrl($related->cover_image_path) : '/assets/listing-fallback.svg')
                    <article class="card listing-related-card">
                        <img class="card-cover" src="{{ $relatedCover }}" alt="{{ $related->name }}">
                        <h3><a href="{{ route('listing.show', ['slug' => $related->slug]) }}">{{ $related->name }}</a></h3>
                        <div class="meta">{{ $related->city }}{{ $related->district ? ' / ' . $related->district : '' }}</div>
                        <p>{{ $related->summary ?: 'Kisa tanitim metni girilmemis.' }}</p>
                        <a class="btn card-btn" href="{{ route('listing.show', ['slug' => $related->slug]) }}">{{ $siteMeta['listing_cta'] ?? 'Detaylari Incele' }}</a>
                    </article>
                @empty
                    <article class="empty-state">
                        <h3>Benzer ilan bulunamadi</h3>
                        <p>Yeni ilan eklendikce burada oneriler listelenecek.</p>
                    </article>
                @endforelse
            </div>
        </section>
    </article>

    <div id="listing-lightbox" class="lightbox" aria-hidden="true">
        <button type="button" id="listing-lightbox-prev" class="lightbox-nav prev" aria-label="Onceki gorsel">&lt;</button>
        <button type="button" id="listing-lightbox-next" class="lightbox-nav next" aria-label="Sonraki gorsel">&gt;</button>
        <button type="button" id="listing-lightbox-close" class="lightbox-close">Kapat</button>
        <img id="listing-lightbox-image" src="" alt="Galeri buyuk gorsel">
    </div>

    <script>
        (function () {
            var main = document.getElementById('listing-main-image');
            if (!main) return;
            var thumbs = document.querySelectorAll('.thumb-btn');
            var openBtn = document.getElementById('listing-open-lightbox');
            var lightbox = document.getElementById('listing-lightbox');
            var lightboxImage = document.getElementById('listing-lightbox-image');
            var lightboxClose = document.getElementById('listing-lightbox-close');
            var lightboxPrev = document.getElementById('listing-lightbox-prev');
            var lightboxNext = document.getElementById('listing-lightbox-next');
            var galleryImages = Array.from(thumbs).map(function (btn) {
                return btn.getAttribute('data-img');
            }).filter(Boolean);
            var currentIndex = 0;

            function activateThumbByImage(img) {
                thumbs.forEach(function (b, idx) {
                    if (b.getAttribute('data-img') === img) {
                        b.classList.add('is-active');
                        currentIndex = idx;
                    } else {
                        b.classList.remove('is-active');
                    }
                });
            }

            function showImage(img) {
                if (!img) return;
                main.setAttribute('src', img);
                if (openBtn) openBtn.setAttribute('data-img', img);
                activateThumbByImage(img);
            }

            thumbs.forEach(function (btn) {
                btn.addEventListener('click', function () {
                    showImage(btn.getAttribute('data-img'));
                });
            });

            function openLightbox(img) {
                if (!lightbox || !lightboxImage || !img) return;
                lightboxImage.setAttribute('src', img);
                lightbox.classList.add('is-open');
                lightbox.setAttribute('aria-hidden', 'false');
            }

            function closeLightbox() {
                if (!lightbox) return;
                lightbox.classList.remove('is-open');
                lightbox.setAttribute('aria-hidden', 'true');
            }

            function move(step) {
                if (!galleryImages.length) return;
                currentIndex = (currentIndex + step + galleryImages.length) % galleryImages.length;
                var img = galleryImages[currentIndex];
                showImage(img);
                if (lightbox && lightbox.classList.contains('is-open') && lightboxImage) {
                    lightboxImage.setAttribute('src', img);
                }
            }

            if (openBtn) {
                openBtn.addEventListener('click', function () {
                    openLightbox(openBtn.getAttribute('data-img'));
                });
            }

            if (lightboxClose) {
                lightboxClose.addEventListener('click', closeLightbox);
            }

            if (lightbox) {
                lightbox.addEventListener('click', function (e) {
                    if (e.target === lightbox) closeLightbox();
                });
            }

            if (lightboxPrev) {
                lightboxPrev.addEventListener('click', function () { move(-1); });
            }
            if (lightboxNext) {
                lightboxNext.addEventListener('click', function () { move(1); });
            }

            document.addEventListener('keydown', function (e) {
                if (!lightbox || !lightbox.classList.contains('is-open')) return;
                if (e.key === 'Escape') closeLightbox();
                if (e.key === 'ArrowLeft') move(-1);
                if (e.key === 'ArrowRight') move(1);
            });
        })();
    </script>
@endsection
