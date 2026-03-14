@extends('frontend.layout')

@php
    $metaTitle = ($category->seo_title ?: $category->name) . ' | ' . ($siteMeta['name'] ?? 'Orkestram');
    $metaDescription = $category->seo_description ?: ($category->short_description ?: $category->name . ' kategorisindeki ilanlar.');
@endphp

@section('content')
    <nav class="breadcrumbs">
        <a href="{{ route('home') }}">Ana Sayfa</a>
        <span>/</span>
        <a href="{{ route('listing.index') }}">Ilanlar</a>
        <span>/</span>
        <span>{{ $category->name }}</span>
    </nav>

    <section class="section page-hero">
        <h1>{{ $category->name }}</h1>
        <p class="page-subtitle">{{ $category->short_description ?: 'Bu kategorideki ilanlari sehir ve ilceye gore inceleyebilirsin.' }}</p>
    </section>

    @if(!empty($category->cover_image_path))
        <section class="section section-tight">
            <img class="cover-banner" src="/{{ $category->cover_image_path }}" alt="{{ $category->name }}">
        </section>
    @endif

    @if($isEmpty)
        <section class="section">
            <article class="empty-state">
                <h3>Bu kategoride henuz aktif ilan yok</h3>
                <p>Yeni ilan eklendiginde burada otomatik listelenecek.</p>
                <a class="btn card-btn" href="{{ route('listing.index') }}">Tum Ilanlara Don</a>
            </article>
        </section>
    @else
        <section class="section section-tight">
            <form method="GET" action="{{ request()->url() }}" class="filters">
                @foreach(request()->except(['price_min', 'price_max', 'page']) as $key => $value)
                    @if(is_array($value))
                        @foreach($value as $row)
                            <input type="hidden" name="{{ $key }}[]" value="{{ $row }}">
                        @endforeach
                    @else
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endif
                @endforeach
                <div class="filter-grid">
                    <label>
                        <span>Fiyat Min</span>
                        <input type="number" step="0.01" min="0" name="price_min" value="{{ $priceMin ?? '' }}" placeholder="Orn: 1000">
                    </label>
                    <label>
                        <span>Fiyat Max</span>
                        <input type="number" step="0.01" min="0" name="price_max" value="{{ $priceMax ?? '' }}" placeholder="Orn: 5000">
                    </label>
                </div>
                <div class="filter-actions">
                    <button class="btn btn-primary" type="submit">Filtrele</button>
                    <a class="btn" href="{{ request()->url() }}">Temizle</a>
                </div>
            </form>
        </section>

        <section class="section">
            <p class="lead"><strong>{{ $items->total() }}</strong> ilan bulundu.</p>
            <div class="grid">
                @foreach($items as $item)
                    @include('frontend.partials.listing-card', [
                        'item' => $item,
                        'siteMeta' => $siteMeta,
                        'cardAttributes' => $cardAttributesByListing[$item->id] ?? [],
                    ])
                @endforeach
            </div>

            @if($items->hasPages())
                <div class="pagination-wrap">
                    {{ $items->links() }}
                </div>
            @endif
        </section>
    @endif

    @if(!empty($category->description))
        <section class="section">
            <article class="content">
                {!! nl2br(e($category->description)) !!}
            </article>
        </section>
    @endif
@endsection
