@extends('admin.layout')

@section('content')
    <div class="card">
        <h2 class="title-reset">{{ $item->exists ? 'Ilan Duzenle' : 'Yeni Ilan' }}</h2>

        @if ($errors->any())
            <div class="form-error-box">
                @foreach ($errors->all() as $e)<div>{{ $e }}</div>@endforeach
            </div>
        @endif

        <form method="post" enctype="multipart/form-data" action="{{ $item->exists ? route('admin.listings.update', $item) : route('admin.listings.store') }}">
            @csrf
            @if($item->exists) @method('put') @endif
            @php($categoriesByMain = $categoriesByMain ?? collect())

            <h3>1) Temel Bilgi</h3>
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
                    <label>Yayin Kapsami</label>
                    @php($scopeVal = old('site_scope', $item->site_scope ?: 'single'))
                    <select name="site_scope">
                        <option value="single" @selected($scopeVal === 'single')>Tek Site</option>
                        <option value="both" @selected($scopeVal === 'both')>Iki Site (both)</option>
                    </select>
                </div>
            </div>

            <div class="row">
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
                <div><label>Ad</label><input name="name" value="{{ old('name', $item->name) }}" required></div>
                <div><label>Slug</label><input name="slug" value="{{ old('slug', $item->slug) }}" required></div>
            </div>

            <label>Kategori</label>
            @php($selectedCategory = (int) old('category_id', $item->category_id))
            <select name="category_id">
                <option value="">Kategori sec (opsiyonel)</option>
                @foreach($categoriesByMain as $main)
                    <optgroup label="{{ $main->name }}">
                        @foreach($main->categories as $cat)
                            <option value="{{ $cat->id }}" @selected($selectedCategory === (int) $cat->id)>{{ $cat->name }}</option>
                        @endforeach
                    </optgroup>
                @endforeach
            </select>

            <div class="row">
                <div><label>Sehir *</label><input name="city" value="{{ old('city', $item->city) }}" required></div>
                <div><label>Ilce *</label><input name="district" value="{{ old('district', $item->district) }}" required></div>
            </div>

            <div class="row">
                <div><label>Hizmet Turu</label><input name="service_type" value="{{ old('service_type', $item->service_type) }}"></div>
                <div><label>Fiyat Etiketi *</label><input name="price_label" value="{{ old('price_label', $item->price_label) }}" required></div>
            </div>

            <label>Ozet * (30-500 karakter)</label>
            <textarea name="summary" rows="3" required>{{ old('summary', $item->summary) }}</textarea>
            <label>Icerik * (minimum 80 karakter)</label>
            <textarea name="content" rows="10" required>{{ old('content', $item->content) }}</textarea>

            <h3>2) Iletisim ve Ozellikler</h3>
            <div class="row">
                <div><label>WhatsApp</label><input name="whatsapp" value="{{ old('whatsapp', $item->whatsapp) }}" placeholder="+90532..."></div>
                <div><label>Telefon</label><input name="phone" value="{{ old('phone', $item->phone) }}" placeholder="+90232..."></div>
            </div>
            <label>Ozellikler (her satira bir ozellik)</label>
            <textarea name="features_text" rows="5" placeholder="Orn:\n6 kisilik ekip\nCanli davul + nefesli\n45 dakika performans">{{ old('features_text', is_array($item->features_json ?? null) ? implode("\n", $item->features_json) : '') }}</textarea>

            <h3>3) Gorseller</h3>
            @if(!empty($item->cover_image_path))
                <div class="card mb-12">
                    <strong>Mevcut Kapak:</strong><br>
                    <img src="/{{ $item->cover_image_path }}" alt="Kapak" class="img-preview mt-8">
                    <div class="mt-8">
                        <label><input type="checkbox" name="remove_cover_image" value="1"> Kapak gorselini kaldir</label>
                    </div>
                </div>
            @endif

            <label>Kapak Gorsel</label>
            <input type="file" name="cover_image" accept="image/*">

            @if(is_array($item->gallery_json) && count($item->gallery_json))
                <div class="card mb-12">
                    <strong>Mevcut Galeri (surukle-birak ile sirala):</strong>
                    <input type="hidden" name="gallery_order" id="gallery_order" value="">
                    <div id="gallery-sortable" class="gallery-grid">
                        @foreach($item->gallery_json as $img)
                            <div draggable="true" data-img="{{ $img }}" class="gallery-item">
                                <img src="/{{ $img }}" alt="Galeri">
                                <label class="gallery-item-note">
                                    <input type="checkbox" name="remove_gallery[]" value="{{ $img }}">
                                    Bu gorseli sil
                                </label>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-8">
                        <label><input type="checkbox" name="reset_gallery" value="1"> Galeriyi sifirla</label>
                    </div>
                </div>
            @endif

            <label>Galeri Gorselleri (coklu secim)</label>
            <input type="file" name="gallery_images[]" accept="image/*" multiple>

            <h3>4) SEO</h3>
            <div class="row">
                <div><label>SEO Baslik</label><input name="seo_title" value="{{ old('seo_title', $item->seo_title) }}"></div>
                <div><label>SEO Aciklama</label><input name="seo_description" value="{{ old('seo_description', $item->seo_description) }}"></div>
            </div>

            <div class="actions">
                <button class="btn" type="submit">Kaydet</button>
                <a class="btn" href="{{ route('admin.listings.index', ['site' => old('site', $item->site ?: 'orkestram.net')]) }}">Iptal</a>
            </div>
        </form>
    </div>

    <script>
        (function () {
            var container = document.getElementById('gallery-sortable');
            var orderInput = document.getElementById('gallery_order');
            if (!container || !orderInput) return;

            var dragged = null;

            function writeOrder() {
                var values = Array.from(container.querySelectorAll('[data-img]')).map(function (el) {
                    return el.getAttribute('data-img');
                });
                orderInput.value = JSON.stringify(values);
            }

            container.querySelectorAll('[draggable="true"]').forEach(function (el) {
                el.addEventListener('dragstart', function () { dragged = el; });
                el.addEventListener('dragover', function (e) { e.preventDefault(); });
                el.addEventListener('drop', function (e) {
                    e.preventDefault();
                    if (!dragged || dragged === el) return;
                    container.insertBefore(dragged, el);
                    writeOrder();
                });
            });

            writeOrder();
        })();
    </script>
@endsection
