@php
    $cardAttributes = is_array($cardAttributes ?? null) ? $cardAttributes : [];
    $ctaText = $ctaText ?? ($siteMeta['listing_cta'] ?? 'Detaylari Incele');
    $featureOne = (string) ($cardAttributes[0]['value'] ?? '-');
    $featureTwo = (string) ($cardAttributes[1]['value'] ?? '-');
    $priceText = trim((string) ($item->price_label ?? '')) !== '' ? (string) $item->price_label : 'Iletisim ile netlesir';

    $ratingRaw = $item->rating_avg ?? $item->rating ?? data_get($item, 'meta_json.rating');
    $ratingText = is_numeric($ratingRaw) ? number_format((float) $ratingRaw, 1) : '0.0';

    $commentCount = isset($item->comments_count) ? (int) $item->comments_count : \App\Models\ListingFeedback::query()
        ->where('site', (string) $item->site)
        ->where('listing_id', (int) $item->id)
        ->where('kind', 'comment')
        ->where('visibility', 'public')
        ->where('status', 'approved')
        ->count();
@endphp

<article class="card listing-card">
    <a href="{{ route('listing.show', ['slug' => $item->slug]) }}" class="listing-card-cover-link" aria-label="{{ $item->name }}">
        <img class="card-cover listing-card-cover" src="/{{ $item->cover_image_path ?: 'assets/listing-fallback.svg' }}" alt="{{ $item->name }}">
    </a>

    <div class="listing-card-body">
        <div class="listing-card-header">
            <div class="listing-card-row listing-card-row-title">
                <h3 class="listing-card-title">
                    <a href="{{ route('listing.show', ['slug' => $item->slug]) }}">{{ $item->name }}</a>
                </h3>
                <span class="listing-card-rating" aria-label="Puan">★ {{ $ratingText }}</span>
            </div>

            <div class="listing-card-meta" aria-label="Kart ozet bilgileri">
                <span class="listing-card-feature-chip">{{ $featureOne }}</span>
                <span class="listing-card-comments">{{ $commentCount }} Yorum</span>
            </div>
        </div>

        <div class="listing-card-footer">
            <div class="listing-card-price-block">
                <span class="listing-card-price-label">Baslangic</span>
                <span class="listing-card-price">{{ $priceText }}</span>
            </div>
            <span class="listing-card-feature listing-card-feature-secondary">{{ $featureTwo }}</span>
        </div>

        <a class="btn card-btn listing-card-cta" href="{{ route('listing.show', ['slug' => $item->slug]) }}">{{ $ctaText }}</a>
    </div>
</article>
