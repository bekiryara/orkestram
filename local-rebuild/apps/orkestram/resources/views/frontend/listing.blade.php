@extends('frontend.layout')

@php
    $metaTitle = $item->seo_title ?: $item->name;
    $metaDescription = $item->seo_description ?: ($item->summary ?: 'Ilan detayi');
    $displayPrice = $item->displayPriceLabel('Bilgi icin iletisime gecin');
    $offerPrice = number_format((float) ($item->publicPriceValue() ?? 0), 2, '.', '');
    $offerCurrency = $item->publicPriceCurrency();
    $categoryLabel = trim((string) ($item->service_type ?: 'Muzik Hizmeti'));
    $identityLine = collect([$categoryLabel, trim((string) $item->city), trim((string) $item->district)])->filter()->implode(' / ');
    $commentCount = count($publicComments ?? []);
    $likeCount = isset($item->like_count) ? (int) ($item->like_count ?? 0) : (int) $item->likes()->count();
    $ratingLabel = '4.9';
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
    @php($quoteRequestUrl = route('customer.dashboard', ['listing' => $item->slug]))
    @php($quoteRequestLoginUrl = route('auth.login', ['next' => '/customer?'.http_build_query(['listing' => $item->slug])]))

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
                    @if($identityLine !== '')
                        <p class="listing-identity-line">{{ $identityLine }}</p>
                    @endif
                    <h1>{{ $item->name }}</h1>
                </div>

                <div class="listing-price-band">
                    <span class="listing-price-label">Baslangic fiyati</span>
                    <strong class="listing-price-value">{{ $displayPrice }}</strong>
                    <div class="listing-proof-row" aria-label="Sosyal kanit">
                        <span>{{ $commentCount }} yorum</span>
                        <span>{{ $likeCount }} begeni</span>
                        <strong><span aria-hidden="true">&#9733;</span> {{ $ratingLabel }}</strong>
                    </div>
                    <p class="listing-price-note">Takvim ve ihtiyaca gore net teklif icin dogrudan iletisime gecin.</p>
                </div>

                <div class="listing-cta-panel-premium">
                    <div class="listing-cta-primary">
                        @if($item->phone)
                            <a class="btn btn-secondary" href="tel:{{ $item->phone }}">Ara</a>
                        @endif
                        @if($item->whatsapp)
                            <a class="btn btn-primary" target="_blank" rel="noopener" href="https://wa.me/{{ ltrim(preg_replace('/[^0-9]/', '', $item->whatsapp), '0') }}">WhatsApp Yaz</a>
                        @endif
                    </div>
                    @if($shellAuthenticated ?? false)
                        <a class="listing-cta-link" href="{{ $quoteRequestUrl }}">Teklif Al</a>
                    @else
                        <a class="listing-cta-link" href="{{ $quoteRequestLoginUrl }}">Teklif Al</a>
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


        @if((is_array($detailAttributes ?? null) && count($detailAttributes)) || (is_array($item->features_json) && count($item->features_json)))
            <section class="listing-block listing-features-block">
                <div class="listing-inline-features">
                    @if(is_array($detailAttributes ?? null) && count($detailAttributes))
                        <div class="listing-inline-feature-group">
                            <h2 class="listing-inline-title">Kategori ozellikleri</h2>
                            <div class="listing-inline-feature-list">
                                @foreach($detailAttributes as $row)
                                    <div class="listing-inline-feature-item">
                                        <span>{{ $row['label'] }}</span>
                                        <strong>{{ $row['value'] }}</strong>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if(is_array($item->features_json) && count($item->features_json))
                        <div class="listing-inline-feature-group">
                            <h2 class="listing-inline-title">Ek avantajlar</h2>
                            <div class="listing-feature-list">
                                @foreach($item->features_json as $feature)
                                    <span>{{ $feature }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </section>
        @endif

        @if(count($galleryItems))
            <section class="listing-block listing-gallery-block" aria-label="Galeri">
                <div class="listing-gallery-head">
                    <h2 class="listing-inline-title">Galeri</h2>
                    <div class="listing-gallery-nav" aria-label="Galeri gezinme">
                        <button type="button" class="listing-gallery-arrow" data-gallery-nav="prev" aria-label="Onceki gorsel">&larr;</button>
                        <button type="button" class="listing-gallery-arrow" data-gallery-nav="next" aria-label="Sonraki gorsel">&rarr;</button>
                    </div>
                </div>
                <div class="listing-gallery-strip" data-gallery-track>
                    <button type="button" class="thumb-btn is-active" data-img="{{ $mainImageUrl }}">
                        <img src="{{ $mainImageUrl }}" alt="{{ $item->name }} ana gorsel">
                    </button>
                    @foreach($galleryItems as $galleryImageUrl)
                        <button type="button" class="thumb-btn" data-img="{{ $galleryImageUrl }}">
                            <img src="{{ $galleryImageUrl }}" alt="{{ $item->name }} galeri">
                        </button>
                    @endforeach
                </div>
            </section>
        @endif

        @if($item->summary || $item->content)
            <section id="detaylar" class="listing-block listing-about-block">
                <h2 class="listing-inline-title">Hakkinda</h2>
                <div class="listing-story-block">
                    @if($item->summary)
                        <p class="listing-summary-lead">{{ $item->summary }}</p>
                    @endif
                    @if($item->content)
                        <div class="listing-richtext">{!! nl2br(e($item->content)) !!}</div>
                    @endif
                </div>
            </section>
        @endif

        <section id="yorumlar" class="listing-block listing-comments-block">
            <div class="listing-section-head">
                <div>
                    <h2>Musteri deneyimleri</h2>
                </div>
                <p class="meta">Bu alanda yalnizca onayli yorumlar gosterilir.</p>
            </div>

            @if(session('ok'))
                <div class="listing-status-note" role="status">{{ session('ok') }}</div>
            @endif

            <div class="listing-comment-actions" aria-label="Musteri deneyimleri aksiyonlari">
                @if($shellAuthenticated ?? false)
                    <a class="listing-comment-action" href="{{ $messageUrl }}">
                        <span aria-hidden="true">&#9993;</span>
                        <span>Mesaj</span>
                    </a>
                    <form method="post" action="{{ route('customer.feedback.like') }}" class="listing-comment-like-form{{ $likeCount > 0 ? ' is-active' : '' }}">
                        @csrf
                        <input type="hidden" name="listing_slug" value="{{ $item->slug }}">
                        <button type="submit" class="listing-comment-action{{ $likeCount > 0 ? ' is-active' : '' }}">
                            <span aria-hidden="true">&#9829;</span>
                            <span>Begeni</span>
                        </button>
                    </form>
                    <button type="button" class="listing-comment-action listing-comment-action--soft{{ $commentCount > 0 ? ' is-active' : '' }}" data-comment-open>
                        <span aria-hidden="true">&#9998;</span>
                        <span>Yorum Yap</span>
                    </button>
                @else
                    <a class="listing-comment-action" href="{{ $messageLoginUrl }}">
                        <span aria-hidden="true">&#9993;</span>
                        <span>Mesaj</span>
                    </a>
                    <a class="listing-comment-action{{ $likeCount > 0 ? ' is-active' : '' }}" href="{{ $likeLoginUrl }}">
                        <span aria-hidden="true">&#9829;</span>
                        <span>Begeni</span>
                    </a>
                    <a class="listing-comment-action listing-comment-action--soft{{ $commentCount > 0 ? ' is-active' : '' }}" href="{{ $commentLoginUrl }}">
                        <span aria-hidden="true">&#9998;</span>
                        <span>Yorum Yap</span>
                    </a>
                @endif
            </div>

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
                    <h2>Benzer ilanlar</h2>
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

    @if($shellAuthenticated ?? false)
        <div id="listing-comment-modal" class="listing-modal" aria-hidden="true">
            <div class="listing-modal-backdrop" data-comment-close></div>
            <div class="listing-modal-card" role="dialog" aria-modal="true" aria-labelledby="listing-comment-modal-title">
                <button type="button" class="listing-modal-close" data-comment-close aria-label="Yorum formunu kapat">&times;</button>
                <h3 id="listing-comment-modal-title">Yorum Yap</h3>
                <form method="post" action="{{ route('customer.feedback.store') }}" class="listing-comment-form">
                    @csrf
                    <input type="hidden" name="listing_slug" value="{{ $item->slug }}">
                    <input type="hidden" name="kind" value="comment">
                    <input type="hidden" name="visibility" value="public">
                    <textarea class="form-control" name="content" rows="5" placeholder="Bu hizmetle ilgili deneyimini paylas..." required>{{ old('content') }}</textarea>
                    <div class="actions mt-8">
                        <button class="btn btn-primary" type="submit">Yorumu Gonder</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

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
            var galleryTrack = document.querySelector('[data-gallery-track]');
            var galleryPrev = document.querySelector('[data-gallery-nav="prev"]');
            var galleryNext = document.querySelector('[data-gallery-nav="next"]');
            var openBtn = document.getElementById('listing-open-lightbox');
            var commentModal = document.getElementById('listing-comment-modal');
            var commentOpen = document.querySelector('[data-comment-open]');
            var commentCloses = document.querySelectorAll('[data-comment-close]');
            var lightbox = document.getElementById('listing-lightbox');
            var lightboxImage = document.getElementById('listing-lightbox-image');
            var lightboxClose = document.getElementById('listing-lightbox-close');
            var lightboxPrev = document.getElementById('listing-lightbox-prev');
            var lightboxNext = document.getElementById('listing-lightbox-next');
            var galleryImages = Array.from(thumbs).map(function (btn) {
                return btn.getAttribute('data-img');
            }).filter(Boolean);
            var currentIndex = 0;

            function scrollGalleryTo(index) {
                if (!galleryTrack || !thumbs[index]) return;
                thumbs[index].scrollIntoView({ behavior: 'smooth', inline: 'start', block: 'nearest' });
            }

            function activateThumbByImage(img) {
                thumbs.forEach(function (b, idx) {
                    if (b.getAttribute('data-img') === img) {
                        b.classList.add('is-active');
                        currentIndex = idx;
                    } else {
                        b.classList.remove('is-active');
                    }
                });
                scrollGalleryTo(currentIndex);
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

            function moveGallery(step) {
                if (!galleryImages.length) return;
                currentIndex = (currentIndex + step + galleryImages.length) % galleryImages.length;
                var img = galleryImages[currentIndex];
                showImage(img);
            }

            if (galleryPrev) {
                galleryPrev.addEventListener('click', function () {
                    moveGallery(-1);
                });
            }

            if (galleryNext) {
                galleryNext.addEventListener('click', function () {
                    moveGallery(1);
                });
            }

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

            function openCommentModal() {
                if (!commentModal) return;
                commentModal.classList.add('is-open');
                commentModal.setAttribute('aria-hidden', 'false');
            }

            function closeCommentModal() {
                if (!commentModal) return;
                commentModal.classList.remove('is-open');
                commentModal.setAttribute('aria-hidden', 'true');
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

            if (commentOpen) {
                commentOpen.addEventListener('click', openCommentModal);
            }

            if (commentCloses.length) {
                commentCloses.forEach(function (btn) {
                    btn.addEventListener('click', closeCommentModal);
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
                if (e.key === 'Escape') {
                    closeLightbox();
                    closeCommentModal();
                }
                if (!lightbox || !lightbox.classList.contains('is-open')) return;
                if (e.key === 'ArrowLeft') move(-1);
                if (e.key === 'ArrowRight') move(1);
            });

            @if($shellAuthenticated ?? false)
                @if($errors->any() || old('content'))
                    openCommentModal();
                @endif
            @endif
        })();
    </script>
@endsection






