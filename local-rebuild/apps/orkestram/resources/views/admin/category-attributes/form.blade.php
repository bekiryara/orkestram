@extends('admin.layout')

@section('content')
    <div class="card">
        <h2 class="title-reset">{{ $item->exists ? 'Ozellik Duzenle' : 'Yeni Ozellik' }}</h2>
        <p class="muted">{{ $category->name }} kategorisi icin alan tanimi.</p>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="post" action="{{ $item->exists ? route('admin.category-attributes.update', [$category, $item]) : route('admin.category-attributes.store', $category) }}">
            @csrf
            @if($item->exists)
                @method('put')
            @endif

            <div class="form-grid">
                <div>
                    <label>Anahtar (`a-z0-9_`)</label>
                    <input name="key" value="{{ old('key', $item->key) }}" required>
                </div>
                <div>
                    <label>Etiket</label>
                    <input name="label" value="{{ old('label', $item->label) }}" required>
                </div>
                <div>
                    <label>Alan Tipi</label>
                    @php($fieldType = old('field_type', $item->field_type ?: 'text'))
                    <select name="field_type" required>
                        @foreach(['text', 'number', 'select', 'multiselect', 'boolean'] as $type)
                            <option value="{{ $type }}" @selected($fieldType === $type)>{{ $type }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label>Filtre Modu</label>
                    @php($filterMode = old('filter_mode', $item->filter_mode ?: 'exact'))
                    <select name="filter_mode" required>
                        <option value="exact" @selected($filterMode === 'exact')>Exact</option>
                        <option value="range" @selected($filterMode === 'range')>Range (number)</option>
                    </select>
                </div>
                <div>
                    <label>Sira</label>
                    <input type="number" name="sort_order" min="0" max="100000" value="{{ old('sort_order', $item->sort_order ?? 100) }}">
                </div>
            </div>

            @php($optionsText = old('options_text', collect($item->options_json ?? [])->implode("\n")))
            <div>
                <label>Secenekler (satir satir, sadece select/multiselect icin)</label>
                <textarea name="options_text" rows="6">{{ $optionsText }}</textarea>
            </div>

            <div class="form-grid">
                @php($isRequired = old('is_required', $item->is_required ? '1' : '0'))
                @php($isFilterable = old('is_filterable', $item->is_filterable ? '1' : '0'))
                @php($isVisibleInCard = old('is_visible_in_card', $item->is_visible_in_card ? '1' : '0'))
                @php($isVisibleInDetail = old('is_visible_in_detail', $item->exists ? ($item->is_visible_in_detail ? '1' : '0') : '1'))
                @php($isActive = old('is_active', $item->exists ? ($item->is_active ? '1' : '0') : '1'))
                <div>
                    <label>Zorunlu</label>
                    <select name="is_required">
                        <option value="0" @selected($isRequired === '0')>Hayir</option>
                        <option value="1" @selected($isRequired === '1')>Evet</option>
                    </select>
                </div>
                <div>
                    <label>Filtrede Goster</label>
                    <select name="is_filterable">
                        <option value="0" @selected($isFilterable === '0')>Hayir</option>
                        <option value="1" @selected($isFilterable === '1')>Evet</option>
                    </select>
                </div>
                <div>
                    <label>Kartta Goster</label>
                    <select name="is_visible_in_card">
                        <option value="0" @selected($isVisibleInCard === '0')>Hayir</option>
                        <option value="1" @selected($isVisibleInCard === '1')>Evet</option>
                    </select>
                </div>
                <div>
                    <label>Detayda Goster</label>
                    <select name="is_visible_in_detail">
                        <option value="0" @selected($isVisibleInDetail === '0')>Hayir</option>
                        <option value="1" @selected($isVisibleInDetail === '1')>Evet</option>
                    </select>
                </div>
                <div>
                    <label>Durum</label>
                    <select name="is_active">
                        <option value="1" @selected($isActive === '1')>Aktif</option>
                        <option value="0" @selected($isActive === '0')>Pasif</option>
                    </select>
                </div>
            </div>

            <div class="form-actions">
                <button class="btn" type="submit">Kaydet</button>
                <a class="btn" href="{{ route('admin.category-attributes.index', $category) }}">Iptal</a>
            </div>
        </form>
    </div>
@endsection
