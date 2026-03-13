@extends('admin.layout')

@section('content')
    <div class="card">
        <h2 class="title-reset">{{ $page->exists ? 'Sayfa Duzenle' : 'Yeni Sayfa' }}</h2>

        @if ($errors->any())
            <div class="form-error-box">
                @foreach ($errors->all() as $e)
                    <div>{{ $e }}</div>
                @endforeach
            </div>
        @endif

        <form method="post" action="{{ $page->exists ? route('admin.pages.update', $page) : route('admin.pages.store') }}">
            @csrf
            @if($page->exists)
                @method('put')
            @endif

            <div class="row">
                <div>
                    <label>Site</label>
                    <select name="site">
                        @php($siteVal = old('site', $page->site))
                        <option value="orkestram.net" @selected($siteVal === 'orkestram.net')>orkestram.net</option>
                        <option value="izmirorkestra.net" @selected($siteVal === 'izmirorkestra.net')>izmirorkestra.net</option>
                    </select>
                </div>
                <div>
                    <label>Durum</label>
                    @php($statusVal = old('status', $page->status))
                    <select name="status">
                        <option value="draft" @selected($statusVal === 'draft')>draft</option>
                        <option value="published" @selected($statusVal === 'published')>published</option>
                        <option value="archived" @selected($statusVal === 'archived')>archived</option>
                    </select>
                </div>
            </div>

            <div class="row">
                <div>
                    <label>Baslik</label>
                    <input name="title" value="{{ old('title', $page->title) }}" required>
                </div>
                <div>
                    <label>Slug</label>
                    <input name="slug" value="{{ old('slug', $page->slug) }}" required>
                </div>
            </div>

            <div class="row">
                <div>
                    <label>Sablon</label>
                    <input name="template" value="{{ old('template', $page->template ?: 'page') }}">
                </div>
                <div>
                    <label>Yayin Tarihi</label>
                    <input type="datetime-local" name="published_at" value="{{ old('published_at', optional($page->published_at)->format('Y-m-d\TH:i')) }}">
                </div>
            </div>

            <label>Ozet</label>
            <textarea name="excerpt" rows="3">{{ old('excerpt', $page->excerpt) }}</textarea>

            <label>Icerik</label>
            <textarea name="content" rows="10">{{ old('content', $page->content) }}</textarea>

            <div class="row">
                <div>
                    <label>SEO Baslik</label>
                    <input name="seo_title" value="{{ old('seo_title', $page->seo_title) }}">
                </div>
                <div>
                    <label>SEO Aciklama</label>
                    <input name="seo_description" value="{{ old('seo_description', $page->seo_description) }}">
                </div>
            </div>

            <label>Canonical URL</label>
            <input name="canonical_url" value="{{ old('canonical_url', $page->canonical_url) }}">

            <div class="actions">
                <button class="btn" type="submit">Kaydet</button>
                <a class="btn" href="{{ route('admin.pages.index', ['site' => old('site', $page->site ?: 'orkestram.net')]) }}">Iptal</a>
            </div>
        </form>
    </div>
@endsection
