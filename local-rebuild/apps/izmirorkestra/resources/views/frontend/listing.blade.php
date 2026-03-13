@extends('frontend.layout')

@php
    $metaTitle = $item->seo_title ?: $item->name;
    $metaDescription = $item->seo_description ?: ($item->summary ?: 'Ilan detayi');
@endphp

@section('content')
    <nav class="breadcrumbs">
        <a href="{{ route('home') }}">Ana Sayfa</a>
        <span>/</span>
        <a href="{{ route('listing.index') }}">Ilanlar</a>
        <span>/</span>
        <span>{{ $item->name }}</span>
    </nav>

    <article class="content">
        <section class="listing-hero">
            <div>
                <h1>{{ $item->name }}</h1>
                <p class="page-subtitle">{{ $item->city }}{{ $item->district ? ' / '.$item->district : '' }} | {{ $item->service_type ?: 'Muzik Hizmeti' }}</p>
                <p class="listing-price"><strong>Baslangic Fiyati:</strong> {{ $item->price_label ?: 'Bilgi icin iletisime gecin' }}</p>
                @if($item->summary)
                    <p class="listing-summary">{{ $item->summary }}</p>
                @endif

                <div class="pill-row">
                    @if($item->city)<span class="pill">{{ $item->city }}</span>@endif
                    @if($item->district)<span class="pill">{{ $item->district }}</span>@endif
                    @if($item->service_type)<span class="pill">{{ $item->service_type }}</span>@endif
                </div>
            </div>

            <aside class="listing-cta-panel">
                @php($messageUrl = route('messages.index', ['box' => 'personal', 'listing' => $item->slug]))
                @php($messageLoginUrl = route('auth.login', ['next' => '/messages?'.http_build_query(['box' => 'personal', 'listing' => $item->slug, 'kind' => 'message'])]))
                @php($commentLoginUrl = route('auth.login', ['next' => '/ilan/' . $item->slug . '#yorum-formu']))
                @php($likeLoginUrl = route('auth.login', ['next' => '/ilan/' . $item->slug]))
                <h3>{{ $siteMeta['contact_heading'] ?? 'Hemen Iletisime Gec' }}</h3>
                <p class="meta">{{ $siteMeta['contact_lead'] ?? 'Etkinlik detaylarini paylas, hizli geri donus al.' }}</p>
                <div class="sticky-cta">
                    @if($item->whatsapp)
                        <a class="btn btn-primary" target="_blank" rel="noopener" href="https://wa.me/{{ ltrim(preg_replace('/[^0-9]/', '', $item->whatsapp), '0') }}">WhatsApp ile Ulas</a>
                    @endif
                    @if($item->phone)
                        <a class="btn btn-secondary" href="tel:{{ $item->phone }}">Telefon Et</a>
                    @endif
                    @if(!$item->whatsapp && !$item->phone)
                        <a class="btn btn-primary" href="#">Teklif Al</a>
                    @endif
                </div>
                <hr>
                <h3 class="mt-8">Iletisim ve Geri Bildirim</h3>
                <p class="meta">Bu ilan icin mesaj gonderebilir, yorum yazabilir ve begeni birakabilirsin.</p>
                <div class="sticky-cta">
                    @if($shellAuthenticated ?? false)
                        <a class="btn btn-primary" href="{{ $messageUrl }}">Mesaj Gonder</a>
                        <a class="btn btn-outline-secondary" href="#yorum-formu">Yorum Yaz</a>
                        <form method="post" action="{{ route('customer.feedback.like') }}">
                            @csrf
                            <input type="hidden" name="listing_slug" value="{{ $item->slug }}">
                            <button type="submit" class="btn btn-outline-secondary">Begeni Birak</button>
                        </form>
                    @else
                        <a class="btn btn-primary" href="{{ $messageLoginUrl }}">Mesaj Gonder</a>
                        <a class="btn btn-outline-secondary" href="{{ $commentLoginUrl }}">Yorum Yaz</a>
                        <a class="btn btn-outline-secondary" href="{{ $likeLoginUrl }}">Begeni Birak</a>
                    @endif
                </div>
                @if(session('ok'))
                    <p class="meta mt-8">{{ session('ok') }}</p>
                @endif
            </aside>
        </section>

        @if($shellAuthenticated ?? false)
            <section id="yorum-formu" class="listing-block">
                <h2>Yorum Yaz</h2>
                <form method="post" action="{{ route('customer.feedback.store') }}">
                    @csrf
                    <input type="hidden" name="listing_slug" value="{{ $item->slug }}">
                    <input type="hidden" name="kind" value="comment">
                    <input type="hidden" name="visibility" value="public">
                    <textarea class="form-control" name="content" rows="4" placeholder="Yorumunuzu yazin..." required>{{ old('content') }}</textarea>
                    <div class="actions mt-8">
                        <button class="btn btn-primary" type="submit">Yorumu Gonder</button>
                    </div>
                </form>
            </section>
        @endif

        <section class="listing-block">
            <h2>Yorumlar</h2>
            <p class="meta">Bu alanda yalnizca onayli yorumlar gosterilir.</p>
            @forelse(($publicComments ?? []) as $comment)
                <div class="card">
                    <p><strong>{{ $comment->user?->name ?? 'Musteri' }}</strong> <span class="meta">{{ optional($comment->created_at)->format('d.m.Y H:i') }}</span></p>
                    <p>{{ $comment->content }}</p>
                    @if(!empty($comment->owner_reply))
                        <p class="meta"><strong>Firma yaniti:</strong> {{ $comment->owner_reply }}</p>
                    @endif
                </div>
            @empty
                <p class="meta">Henuz onayli yorum yok.</p>
            @endforelse
        </section>

        @if($item->cover_image_path || (is_array($item->gallery_json) && count($item->gallery_json)))
            <section class="listing-gallery">
                @php($mainImage = $item->cover_image_path ?: $item->gallery_json[0])
                <button type="button" id="listing-open-lightbox" class="listing-main-button" data-img="/{{ $mainImage }}">
                    <img id="listing-main-image" class="listing-main-image" src="/{{ $mainImage }}" alt="{{ $item->name }}">
                </button>
                @if(is_array($item->gallery_json) && count($item->gallery_json))
                    <div class="listing-thumbs">
                        <button type="button" class="thumb-btn is-active" data-img="/{{ $mainImage }}">
                            <img src="/{{ $mainImage }}" alt="{{ $item->name }} ana gorsel">
                        </button>
                        @foreach($item->gallery_json as $img)
                            <button type="button" class="thumb-btn" data-img="/{{ $img }}">
                                <img src="/{{ $img }}" alt="{{ $item->name }} galeri">
                            </button>
                        @endforeach
                    </div>
                @endif
            </section>
        @endif

        <section class="listing-block">
            <h2>Detaylar</h2>
            <div>{!! nl2br(e($item->content ?: 'Icerik girilmemis.')) !!}</div>
        </section>

        @if(is_array($detailAttributes ?? null) && count($detailAttributes))
            <section class="listing-block">
                <h2>Kategori Ozellikleri</h2>
                <div class="feature-grid">
                    @foreach($detailAttributes as $row)
                        <div class="feature-card"><strong>{{ $row['label'] }}:</strong> {{ $row['value'] }}</div>
                    @endforeach
                </div>
            </section>
        @endif

        @if(is_array($item->features_json) && count($item->features_json))
            <section class="listing-block">
                <h2>Ozellikler</h2>
                <div class="feature-grid">
                    @foreach($item->features_json as $feature)
                        <div class="feature-card">{{ $feature }}</div>
                    @endforeach
                </div>
            </section>
        @endif
    </article>

    <section class="section">
        <h2>Benzer Ilanlar</h2>
        <div class="grid">
            @forelse($relatedListings as $related)
                <article class="card">
                    <img class="card-cover" src="/{{ $related->cover_image_path ?: 'assets/listing-fallback.svg' }}" alt="{{ $related->name }}">
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

