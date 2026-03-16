@extends('frontend.layout')

@php
    $metaTitle = 'Ilanlar | ' . ($siteMeta['name'] ?? 'Orkestram');
    $metaDescription = 'Sehir, ilce ve kategoriye gore filtrelenebilir ilan listesi.';
    $activeFilters = [];

    if ($city !== '') {
        $activeFilters[] = ['label' => 'Sehir', 'value' => $city];
    }
    if ($district !== '') {
        $activeFilters[] = ['label' => 'Ilce', 'value' => $district];
    }
    if ($categorySlug !== '') {
        foreach ($categoryGroups as $group) {
            foreach ($group as $option) {
                if ($categorySlug === $option->slug) {
                    $activeFilters[] = ['label' => 'Kategori', 'value' => $option->name];
                    break 2;
                }
            }
        }
    }
    if ($search !== '') {
        $activeFilters[] = ['label' => 'Arama', 'value' => $search];
    }
    if ($priceMin !== null && $priceMin !== '') {
        $activeFilters[] = ['label' => 'Fiyat Min', 'value' => $priceMin];
    }
    if ($priceMax !== null && $priceMax !== '') {
        $activeFilters[] = ['label' => 'Fiyat Max', 'value' => $priceMax];
    }
    if ($sort !== '' && $sort !== 'recommended') {
        $sortLabels = [
            'newest' => 'En Yeni',
            'oldest' => 'En Eski',
            'name_asc' => 'A-Z',
            'name_desc' => 'Z-A',
            'price_asc' => 'Fiyat Artan',
            'price_desc' => 'Fiyat Azalan',
        ];
        $activeFilters[] = ['label' => 'Sirala', 'value' => $sortLabels[$sort] ?? $sort];
    }

    foreach (($dynamicFilters ?? []) as $filter) {
        $filterLabel = (string) ($filter['label'] ?? 'Filtre');
        $fieldType = (string) ($filter['field_type'] ?? '');
        $filterMode = (string) ($filter['filter_mode'] ?? 'exact');
        $filterValue = trim((string) ($filter['value'] ?? ''));
        $filterValues = is_array($filter['values'] ?? null) ? array_values(array_filter($filter['values'], static fn ($value) => trim((string) $value) !== '')) : [];
        $filterMin = trim((string) ($filter['min'] ?? ''));
        $filterMax = trim((string) ($filter['max'] ?? ''));

        if ($filterMode === 'range' && $fieldType === 'number' && ($filterMin !== '' || $filterMax !== '')) {
            $rangeText = trim(($filterMin !== '' ? 'Min ' . $filterMin : '') . ($filterMin !== '' && $filterMax !== '' ? ' - ' : '') . ($filterMax !== '' ? 'Max ' . $filterMax : ''));
            $activeFilters[] = ['label' => $filterLabel, 'value' => $rangeText];
            continue;
        }

        if ($fieldType === 'multiselect' && $filterValues !== []) {
            $activeFilters[] = ['label' => $filterLabel, 'value' => implode(', ', array_map(static fn ($value) => (string) $value, $filterValues))];
            continue;
        }

        if ($filterValue !== '') {
            if ($fieldType === 'boolean') {
                $filterValue = $filterValue === '1' ? 'Evet' : ($filterValue === '0' ? 'Hayir' : $filterValue);
            }
            $activeFilters[] = ['label' => $filterLabel, 'value' => $filterValue];
        }
    }
@endphp

@section('content')
    <nav class="breadcrumbs">
        <a href="{{ route('home') }}">Ana Sayfa</a>
        <span>/</span>
        <span>Ilanlar</span>
    </nav>

    <section class="section">
        <h1>Ilanlar</h1>
        <p class="lead">Filtreleyip en uygun ekipleri kisa surede bulabilirsin.</p>
    </section>

    <section class="listing-layout section">
        <aside class="listing-sidebar">
            <form method="GET" action="{{ route('listing.index') }}" class="filters filters-panel">
                <div class="filter-summary">
                    <h2>Filtreler</h2>
                    <p class="meta">
                        @if($activeFilters !== [])
                            {{ count($activeFilters) }} aktif filtre ile sonucu daraltiyorsun.
                        @else
                            Sonucu sehir, kategori, fiyat ve ozelliklere gore daraltabilirsin.
                        @endif
                    </p>
                </div>

                @if($activeFilters !== [])
                    <div class="pill-row">
                        @foreach($activeFilters as $filter)
                            <span class="pill">{{ $filter['label'] }}: {{ $filter['value'] }}</span>
                        @endforeach
                    </div>
                @endif

                <div class="filter-grid sidebar-grid">
                    <label>
                        <span>Sehir</span>
                        <select name="city" id="listing_city_select">
                            <option value="">Tum Sehirler</option>
                            @foreach($cities as $option)
                                @php($optionName = (string) ($option['name'] ?? ''))
                                <option value="{{ $optionName }}" @selected($city === $optionName)>{{ $optionName }}</option>
                            @endforeach
                        </select>
                    </label>

                    <label>
                        <span>Ilce</span>
                        <select name="district" id="listing_district_select" data-selected="{{ $district }}">
                            <option value="">Tum Ilceler</option>
                            @foreach($districts as $option)
                                @php($optionName = (string) ($option['name'] ?? ''))
                                <option value="{{ $optionName }}" @selected($district === $optionName)>{{ $optionName }}</option>
                            @endforeach
                        </select>
                    </label>

                    <label>
                        <span>Kategori</span>
                        <select name="category">
                            <option value="">Tum Kategoriler</option>
                            @foreach($categoryGroups as $mainName => $group)
                                <optgroup label="{{ $mainName }}">
                                    @foreach($group as $option)
                                        <option value="{{ $option->slug }}" @selected($categorySlug === $option->slug)>{{ $option->name }}</option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </label>

                    @foreach(($dynamicFilters ?? []) as $filter)
                        <label>
                            <span>{{ $filter['label'] }}</span>
                            @if(($filter['filter_mode'] ?? 'exact') === 'range' && ($filter['field_type'] ?? '') === 'number')
                                <div class="filter-range">
                                    <input
                                        type="number"
                                        name="{{ $filter['query_key'] }}_min"
                                        value="{{ $filter['min'] ?? '' }}"
                                        placeholder="Min"
                                    >
                                    <input
                                        type="number"
                                        name="{{ $filter['query_key'] }}_max"
                                        value="{{ $filter['max'] ?? '' }}"
                                        placeholder="Max"
                                    >
                                </div>
                            @elseif($filter['field_type'] === 'multiselect')
                                <div class="filter-multiselect">
                                    @php($selectedValues = is_array($filter['values'] ?? null) ? $filter['values'] : [])
                                    @foreach(($filter['options'] ?? []) as $option)
                                        @php($optionValue = (string) $option)
                                        <label class="checkbox-line">
                                            <input
                                                type="checkbox"
                                                name="{{ $filter['query_key'] }}[]"
                                                value="{{ $optionValue }}"
                                                @checked(in_array($optionValue, $selectedValues, true))
                                            >
                                            <span>{{ $optionValue }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            @elseif($filter['field_type'] === 'select')
                                <select name="{{ $filter['query_key'] }}">
                                    <option value="">Tum Secenekler</option>
                                    @foreach(($filter['options'] ?? []) as $option)
                                        @php($optionValue = (string) $option)
                                        <option value="{{ $optionValue }}" @selected((string) ($filter['value'] ?? '') === $optionValue)>{{ $optionValue }}</option>
                                    @endforeach
                                </select>
                            @elseif($filter['field_type'] === 'boolean')
                                <select name="{{ $filter['query_key'] }}">
                                    <option value="">Hepsi</option>
                                    <option value="1" @selected((string) ($filter['value'] ?? '') === '1')>Evet</option>
                                    <option value="0" @selected((string) ($filter['value'] ?? '') === '0')>Hayir</option>
                                </select>
                            @else
                                <input
                                    type="{{ $filter['field_type'] === 'number' ? 'number' : 'text' }}"
                                    name="{{ $filter['query_key'] }}"
                                    value="{{ $filter['value'] ?? '' }}"
                                    placeholder="{{ $filter['label'] }}"
                                >
                            @endif
                        </label>
                    @endforeach

                    <label>
                        <span>Arama</span>
                        <input type="text" name="q" value="{{ $search }}" placeholder="Orn: dugun orkestrasi">
                    </label>

                    <label>
                        <span>Fiyat Min</span>
                        <input type="number" step="0.01" min="0" name="price_min" value="{{ $priceMin ?? '' }}" placeholder="Orn: 1000">
                    </label>

                    <label>
                        <span>Fiyat Max</span>
                        <input type="number" step="0.01" min="0" name="price_max" value="{{ $priceMax ?? '' }}" placeholder="Orn: 5000">
                    </label>

                    <label>
                        <span>Sirala</span>
                        <select name="sort">
                            <option value="recommended" @selected($sort === 'recommended')>Onerilen</option>
                            <option value="newest" @selected($sort === 'newest')>En Yeni</option>
                            <option value="oldest" @selected($sort === 'oldest')>En Eski</option>
                            <option value="name_asc" @selected($sort === 'name_asc')>A-Z</option>
                            <option value="name_desc" @selected($sort === 'name_desc')>Z-A</option>
                            <option value="price_asc" @selected($sort === 'price_asc')>Fiyat Artan</option>
                            <option value="price_desc" @selected($sort === 'price_desc')>Fiyat Azalan</option>
                        </select>
                    </label>
                </div>

                <div class="filter-actions sidebar-actions">
                    <button class="btn btn-primary" type="submit">Filtreleri Uygula</button>
                    <a class="btn" href="{{ route('listing.index') }}">Tum Filtreleri Temizle</a>
                </div>
            </form>
        </aside>

        <div class="listing-results">
            <p class="lead listing-count">
                <strong>{{ $items->total() }}</strong> ilan bulundu.
                @if($activeFilters !== [])
                    Sonuclar secili filtrelere gore gosteriliyor.
                @endif
            </p>

            @if($activeFilters !== [])
                <section class="listing-block section-tight">
                    <h2>Aktif Filtreler</h2>
                    <div class="pill-row">
                        @foreach($activeFilters as $filter)
                            <span class="pill">{{ $filter['label'] }}: {{ $filter['value'] }}</span>
                        @endforeach
                    </div>
                    <div class="filter-actions">
                        <a class="btn" href="{{ route('listing.index') }}">Filtreleri Sifirla</a>
                    </div>
                </section>
            @endif

            <div class="grid listing-grid">
            @forelse($items as $item)
                @include('frontend.partials.listing-card', [
                    'item' => $item,
                    'siteMeta' => $siteMeta,
                    'cardAttributes' => $cardAttributesByListing[$item->id] ?? [],
                ])
            @empty
                <article class="card">
                    <h3>Sonuc bulunamadi</h3>
                    <p>Filtreleri temizleyip tekrar deneyin.</p>
                </article>
            @endforelse
            </div>

            @if($items->hasPages())
                <div class="pagination-wrap">
                    {{ $items->links() }}
                </div>
            @endif
        </div>
    </section>
    <script>
        (function () {
            var districtMap = @json($districtMap ?? []);
            var citySelect = document.getElementById('listing_city_select');
            var districtSelect = document.getElementById('listing_district_select');
            if (!citySelect || !districtSelect) return;

            function renderDistricts(resetSelected) {
                var selectedCity = citySelect.value || '';
                var selectedDistrict = resetSelected ? '' : (districtSelect.getAttribute('data-selected') || districtSelect.value || '');
                var rows = districtMap[selectedCity] || [];

                districtSelect.innerHTML = '';
                var emptyOpt = document.createElement('option');
                emptyOpt.value = '';
                emptyOpt.textContent = 'Tum Ilceler';
                districtSelect.appendChild(emptyOpt);

                rows.forEach(function (row) {
                    var value = String(row.name || '');
                    var option = document.createElement('option');
                    option.value = value;
                    option.textContent = row.name || '';
                    if (value !== '' && value === String(selectedDistrict)) {
                        option.selected = true;
                    }
                    districtSelect.appendChild(option);
                });
            }

            citySelect.addEventListener('change', function () {
                districtSelect.setAttribute('data-selected', '');
                renderDistricts(true);
            });

            renderDistricts(false);
        })();
    </script>
@endsection
