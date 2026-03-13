@extends('admin.layout')

@section('content')
    <div class="card">
        <h2 class="title-reset">{{ $item->exists ? 'Kategori Duzenle' : 'Yeni Kategori' }}</h2>

        @if ($errors->any())
            <div class="form-error-box">
                @foreach ($errors->all() as $e)<div>{{ $e }}</div>@endforeach
            </div>
        @endif

        <form method="post" enctype="multipart/form-data" action="{{ $item->exists ? route('admin.categories.update', $item) : route('admin.categories.store') }}">
            @csrf
            @if($item->exists) @method('put') @endif

            <div class="row">
                <div>
                    <label>Ana Kategori</label>
                    <select name="main_category_id" required>
                        @foreach($mainCategories as $main)
                            <option value="{{ $main->id }}" @selected((int) old('main_category_id', $item->main_category_id) === (int) $main->id)>{{ $main->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label>Sira</label>
                    <input type="number" name="sort_order" min="0" value="{{ old('sort_order', $item->sort_order ?? 100) }}">
                </div>
            </div>

            <div class="row">
                <div><label>Ad</label><input name="name" value="{{ old('name', $item->name) }}" required></div>
                <div><label>Slug</label><input name="slug" value="{{ old('slug', $item->slug) }}" required></div>
            </div>

            <label>Kisa Aciklama</label>
            <input name="short_description" value="{{ old('short_description', $item->short_description) }}">

            <label>Detay Aciklama</label>
            <textarea name="description" rows="6">{{ old('description', $item->description) }}</textarea>

            @if(!empty($item->cover_image_path))
                <div class="card mb-12">
                    <strong>Mevcut Kapak:</strong><br>
                    <img src="/{{ $item->cover_image_path }}" alt="Kapak" class="img-preview mt-8">
                    <div class="mt-8">
                        <label><input type="checkbox" name="remove_cover_image" value="1"> Kapak gorselini kaldir</label>
                    </div>
                </div>
            @endif

            <div class="row">
                <div><label>Kapak Gorsel Yolu (opsiyonel manuel)</label><input name="cover_image_path" value="{{ old('cover_image_path', $item->cover_image_path) }}" placeholder="uploads/categories/ornek.webp"></div>
                <div><label>SEO Baslik</label><input name="seo_title" value="{{ old('seo_title', $item->seo_title) }}"></div>
            </div>
            <label>Kapak Gorsel Yukle</label>
            <input type="file" name="cover_image" accept="image/*">
            <label>SEO Aciklama</label>
            <input name="seo_description" value="{{ old('seo_description', $item->seo_description) }}">

            <div class="row">
                <div>
                    <label>Aktiflik</label>
                    <select name="is_active">
                        <option value="1" @selected((string) old('is_active', (int) $item->is_active) === '1')>active</option>
                        <option value="0" @selected((string) old('is_active', (int) $item->is_active) === '0')>passive</option>
                    </select>
                </div>
                <div>
                    <label>Index Durumu</label>
                    <select name="is_indexable">
                        <option value="1" @selected((string) old('is_indexable', (int) $item->is_indexable) === '1')>indexable</option>
                        <option value="0" @selected((string) old('is_indexable', (int) $item->is_indexable) === '0')>noindex</option>
                    </select>
                </div>
            </div>

            <div class="actions">
                <button class="btn" type="submit">Kaydet</button>
                <a class="btn" href="{{ route('admin.categories.index') }}">Iptal</a>
            </div>
        </form>
    </div>
@endsection
