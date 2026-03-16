@extends('frontend.layout')

@php
    $metaTitle = $item->seo_title ?: $item->title;
    $metaDescription = $item->seo_description ?: ($item->city . ' hizmet sayfasi');
@endphp

@section('content')
    <nav class="breadcrumbs">
        <a href="{{ route('home') }}">Ana Sayfa</a>
        <span>/</span>
        <a href="{{ route('listing.index') }}">Ilanlar</a>
        <span>/</span>
        <span>{{ $item->title }}</span>
    </nav>

    <article class="content">
        <h1>{{ $item->title }}</h1>
        <p class="meta">{{ $item->city }}{{ $item->district ? ' / '.$item->district : '' }} | {{ $item->service_slug }}</p>
        <div>{!! nl2br(e($item->content ?: 'Icerik girilmemis.')) !!}</div>
        <p>
            <a class="btn btn-primary" href="{{ route('listing.index', ['city' => $item->city, 'district' => $item->district]) }}">Bu Bolgedeki Ilanlari Listele</a>
        </p>
    </article>

    <section class="section">
        <h2>Bolgedeki Ilanlar</h2>
        <div class="grid">
            @forelse($cityListings as $listing)
                <article class="card">
                    <img class="card-cover" src="{{ \App\Support\MediaPath::listingUrl($listing->cover_image_path) }}" alt="{{ $listing->name }}">
                    <h3><a href="{{ route('listing.show', ['slug' => $listing->slug]) }}">{{ $listing->name }}</a></h3>
                    <div class="meta">{{ $listing->city }}{{ $listing->district ? ' / ' . $listing->district : '' }}</div>
                    <p>{{ $listing->summary ?: 'Kisa tanitim metni girilmemis.' }}</p>
                    <a class="btn card-btn" href="{{ route('listing.show', ['slug' => $listing->slug]) }}">Detaylari Incele</a>
                </article>
            @empty
                <article class="empty-state">
                    <h3>Bu bolge icin ilan bulunamadi</h3>
                    <p>Filtreleri genisletip tum ilanlari inceleyebilirsin.</p>
                    <a class="btn card-btn" href="{{ route('listing.index') }}">Tum Ilanlar</a>
                </article>
            @endforelse
        </div>
    </section>
@endsection


