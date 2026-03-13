@php
    $cardAttributes = is_array($cardAttributes ?? null) ? $cardAttributes : [];
    $ctaText = $ctaText ?? ($siteMeta['listing_cta'] ?? 'Detaylari Incele');
@endphp

<article class="card">
    <img class="card-cover" src="/{{ $item->cover_image_path ?: 'assets/listing-fallback.svg' }}" alt="{{ $item->name }}">
    <h3><a href="{{ route('listing.show', ['slug' => $item->slug]) }}">{{ $item->name }}</a></h3>
    @if($item->category)
        <div class="meta">{{ $item->category->name }}</div>
    @endif
    <div class="meta">{{ $item->city }}{{ $item->district ? ' / '.$item->district : '' }}</div>
    <p>{{ $item->summary ?: 'Kisa tanitim metni girilmemis.' }}</p>
    <p><strong>Baslangic:</strong> {{ $item->price_label ?: 'Iletisim ile netlesir' }}</p>
    @if(count($cardAttributes))
        <div class="meta">
            @foreach($cardAttributes as $row)
                <div><strong>{{ $row['label'] }}:</strong> {{ $row['value'] }}</div>
            @endforeach
        </div>
    @endif
    <a class="btn card-btn" href="{{ route('listing.show', ['slug' => $item->slug]) }}">{{ $ctaText }}</a>
</article>
