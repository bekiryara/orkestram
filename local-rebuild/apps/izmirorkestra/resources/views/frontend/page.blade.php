@extends('frontend.layout')

@php
    $metaTitle = $item->seo_title ?: $item->title;
    $metaDescription = $item->seo_description ?: ($item->excerpt ?: 'Sayfa icerigi');
@endphp

@section('content')
    <nav class="breadcrumbs">
        <a href="{{ route('home') }}">Ana Sayfa</a>
        <span>/</span>
        <span>{{ $item->title }}</span>
    </nav>

    <section class="section page-hero">
        <h1>{{ $item->title }}</h1>
        @if($item->excerpt)
            <p class="page-subtitle">{{ $item->excerpt }}</p>
        @endif
    </section>

    <article class="content section section-tight">
        <div>{!! nl2br(e($item->content ?: 'Icerik girilmemis.')) !!}</div>
    </article>
@endsection
