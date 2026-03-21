@php
    $cardAttributes = is_array($cardAttributes ?? null) ? $cardAttributes : [];
    $ctaText = $ctaText ?? ($siteMeta['listing_cta'] ?? 'Detaylari Incele');
    $featureOne = (string) ($cardAttributes[0]['value'] ?? '-');
    $featureTwo = (string) ($cardAttributes[1]['value'] ?? '-');
    $priceText = $item->displayPriceLabel();

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
        <img class="card-cover listing-card-cover" src="{{ \App\Support\MediaPath::listingUrl($item->cover_image_path) }}" alt="{{ $item->name }}">
    </a>

    <div class="listing-card-body">
        <div class="listing-card-row listing-card-row-title">
            <h3 class="listing-card-title">
                <a href="{{ route('listing.show', ['slug' => $item->slug]) }}">{{ $item->name }}</a>
            </h3>
            <span class="listing-card-rating" aria-label="Puan">★ {{ $ratingText }}</span>
        </div>

        <div class="listing-card-row">
            <span class="listing-card-feature">{{ $featureOne }}</span>
            <span class="listing-card-comments">{{ $commentCount }} Yorum</span>
        </div>

        <div class="listing-card-row listing-card-row-price">
            <span class="listing-card-feature">{{ $featureTwo }}</span>
            <span class="listing-card-price">{{ $priceText }}</span>
        </div>

        <a class="btn card-btn listing-card-cta" href="{{ route('listing.show', ['slug' => $item->slug]) }}">{{ $ctaText }}</a>
    </div>
</article>


