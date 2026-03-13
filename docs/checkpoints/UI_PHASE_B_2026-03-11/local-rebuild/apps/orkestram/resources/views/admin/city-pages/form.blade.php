@extends('admin.layout')

@section('content')
    <div class="card">
        <h2 class="title-reset">{{ $item->exists ? 'Sehir Sayfasi Duzenle' : 'Yeni Sehir Sayfasi' }}</h2>

        @if ($errors->any())
            <div class="form-error-box">
                @foreach ($errors->all() as $e)<div>{{ $e }}</div>@endforeach
            </div>
        @endif

        <form method="post" action="{{ $item->exists ? route('admin.city-pages.update', $item) : route('admin.city-pages.store') }}">
            @csrf
            @if($item->exists) @method('put') @endif

            <div class="row">
                <div>
                    <label>Site</label>
                    @php($siteVal = old('site', $item->site))
                    <select name="site">
                        <option value="orkestram.net" @selected($siteVal === 'orkestram.net')>orkestram.net</option>
                        <option value="izmirorkestra.net" @selected($siteVal === 'izmirorkestra.net')>izmirorkestra.net</option>
                    </select>
                </div>
                <div>
                    <label>Durum</label>
                    @php($statusVal = old('status', $item->status))
                    <select name="status">
                        <option value="draft" @selected($statusVal === 'draft')>draft</option>
                        <option value="published" @selected($statusVal === 'published')>published</option>
                        <option value="archived" @selected($statusVal === 'archived')>archived</option>
                    </select>
                </div>
            </div>

            <div class="row">
                <div><label>Baslik</label><input name="title" value="{{ old('title', $item->title) }}" required></div>
                <div><label>Slug</label><input name="slug" value="{{ old('slug', $item->slug) }}" required></div>
            </div>

            <div class="row">
                <div><label>Sehir</label><input name="city" value="{{ old('city', $item->city) }}" required></div>
                <div><label>Ilce</label><input name="district" value="{{ old('district', $item->district) }}"></div>
            </div>

            <div class="row">
                <div><label>Hizmet Slug</label><input name="service_slug" value="{{ old('service_slug', $item->service_slug) }}"></div>
                <div><label>Yayin Tarihi</label><input type="datetime-local" name="published_at" value="{{ old('published_at', optional($item->published_at)->format('Y-m-d\TH:i')) }}"></div>
            </div>

            <label>Icerik</label>
            <textarea name="content" rows="10">{{ old('content', $item->content) }}</textarea>

            <div class="row">
                <div><label>SEO Baslik</label><input name="seo_title" value="{{ old('seo_title', $item->seo_title) }}"></div>
                <div><label>SEO Aciklama</label><input name="seo_description" value="{{ old('seo_description', $item->seo_description) }}"></div>
            </div>

            <div class="actions">
                <button class="btn" type="submit">Kaydet</button>
                <a class="btn" href="{{ route('admin.city-pages.index', ['site' => old('site', $item->site ?: 'orkestram.net')]) }}">Iptal</a>
            </div>
        </form>
    </div>
@endsection
