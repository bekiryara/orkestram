@extends('frontend.layout')

@php
    $metaTitle = ($heroPage?->seo_title ?: $heroPage?->title ?: 'Orkestram');
    $metaDescription = ($heroPage?->seo_description ?: 'Dugun, nisan, kurumsal etkinlikler icin canli muzik ve bando ekipleri.');
@endphp

@section('content')
    <section class="hero">
        <h1>{{ $heroPage?->title ?: ($siteMeta['tagline'] ?? 'Etkinligine Uygun Muzik Ekibini Bul') }}</h1>
        <p class="page-subtitle">{{ $heroPage?->excerpt ?: ($siteMeta['lead'] ?? 'Yeni sistemde sade ve hizli bir yapida, sehir ve hizmet bazli sayfalarla daha net teklif akisi kuruyoruz.') }}</p>
        <div class="cta">
            <a class="btn btn-primary" href="{{ route('listing.index') }}">Ilanlari Incele</a>
            <a class="btn btn-primary" href="/admin/listings">Ilanlari Yonet</a>
            <a class="btn btn-secondary" href="/admin/city-pages">Sehir Sayfalari</a>
            <a class="btn" href="/admin/pages">Icerik Sayfalari</a>
        </div>
    </section>

    <section class="section">
        <h2>{{ $siteMeta['listing_label'] ?? 'One Cikan Ilanlar' }}</h2>
        <p class="lead">{{ $siteMeta['listing_lead'] ?? 'Yayin durumundaki kayitlardan otomatik listelenir.' }}</p>
        <div class="grid">
            @forelse($featuredListings as $item)
                <article class="card">
                    <img class="card-cover" src="/{{ $item->cover_image_path ?: 'assets/listing-fallback.svg' }}" alt="{{ $item->name }}">
                    <h3><a href="{{ route('listing.show', ['slug' => $item->slug]) }}">{{ $item->name }}</a></h3>
                    <div class="meta">{{ $item->city }}{{ $item->district ? ' / '.$item->district : '' }}</div>
                    <p>{{ $item->summary ?: 'Kisa tanitim metni girilmemis.' }}</p>
                    <p><strong>Fiyat:</strong> {{ $item->price_label ?: 'Iletisim ile netlesir' }}</p>
                    @php($cardAttrs = $cardAttributesByListing[$item->id] ?? [])
                    @if(is_array($cardAttrs) && count($cardAttrs))
                        <div class="meta">
                            @foreach($cardAttrs as $row)
                                <div><strong>{{ $row['label'] }}:</strong> {{ $row['value'] }}</div>
                            @endforeach
                        </div>
                    @endif
                    <a class="btn card-btn" href="{{ route('listing.show', ['slug' => $item->slug]) }}">{{ $siteMeta['listing_cta'] ?? 'Detaylari Incele' }}</a>
                </article>
            @empty
                <article class="card"><h3>Henuz ilan yok</h3><p>Adminden yayinlanmis ilan ekleyince burada gorunecek.</p></article>
            @endforelse
        </div>
    </section>

    <section class="section">
        <h2>Sehir Sayfalari</h2>
        <p class="lead">SEO landing yapisi icin hazirlanan sehir/ilce sayfalari.</p>
        <div class="grid">
            @forelse($featuredCities as $city)
                <article class="card">
                    <h3><a href="{{ route('city-page.show', ['slug' => $city->slug]) }}">{{ $city->title }}</a></h3>
                    <div class="meta">{{ $city->city }}{{ $city->district ? ' / '.$city->district : '' }}</div>
                    <p>{{ $city->seo_description ?: 'Sehir sayfasi aciklamasi girilmemis.' }}</p>
                </article>
            @empty
                <article class="card"><h3>Henuz sehir sayfasi yok</h3><p>Adminden yayinlanmis sehir sayfasi eklenince burada gorunecek.</p></article>
            @endforelse
        </div>
    </section>
@endsection

