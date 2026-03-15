@extends('portal.layout')

@section('content')
    <div class="card shadow-sm">
        <div class="account-header">
            <div>
                <h2>Ilan Duzenle</h2>
                <p class="muted">Ilan bilgilerini guncelle ve kaydet.</p>
            </div>
        </div>
    </div>

    <div class="account-shell">
        @include('portal.owner.partials.menu', ['ownerTab' => 'listings'])

        <section class="card shadow-sm p-3 p-md-4">
            @php($locationOptions = $locationOptions ?? ['cities' => [], 'district_map' => [], 'neighborhood_map' => []])
            @php($selectedCityId = (int) old('city_id', $item->city_id))
            @php($selectedDistrictId = (int) old('district_id', $item->district_id))
            @php($selectedNeighborhoodId = (int) old('neighborhood_id', $item->neighborhood_id))
            @php($serviceAreasText = old('service_areas_text', $item->serviceAreasText()))
            @php($selectedServiceAreaCityIds = old('service_area_city_ids', []))
            @php($selectedServiceAreaDistrictIds = old('service_area_district_ids', []))
            @php($selectedServiceAreaCityIds = is_array($selectedServiceAreaCityIds) ? array_values(array_filter($selectedServiceAreaCityIds, static fn($v) => (string)$v !== '')) : [])
            @php($selectedServiceAreaDistrictIds = is_array($selectedServiceAreaDistrictIds) ? array_values(array_filter($selectedServiceAreaDistrictIds, static fn($v) => (string)$v !== '')) : [])

            @if($errors->any())
                <div class="alert alert-danger" role="alert">
                    <div class="fw-semibold mb-1">Form hatasi var</div>
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="post" enctype="multipart/form-data" action="{{ route('owner.listings.update', ['listing' => $item->id]) }}" class="vstack gap-3">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Baslik</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $item->name) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Kategori</label>
                        @php($categoriesByMain = $categoriesByMain ?? collect())
                        @php($selectedCategory = (int) old('category_id', $item->category_id))
                        <select name="category_id" class="form-select">
                            <option value="">Kategori sec (opsiyonel)</option>
                            @foreach($categoriesByMain as $main)
                                <optgroup label="{{ $main->name }}">
                                    @foreach($main->categories as $cat)
                                        <option value="{{ $cat->id }}" @selected($selectedCategory === (int) $cat->id)>{{ $cat->name }}</option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Sehir</label>
                        <select name="city_id" id="owner_city_select" class="form-select" required data-selected="{{ $selectedCityId }}">
                            <option value="">Sehir sec</option>
                            @foreach(($locationOptions['cities'] ?? []) as $cityRow)
                                <option value="{{ $cityRow['id'] }}" @selected($selectedCityId === (int) $cityRow['id'])>{{ $cityRow['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Ilce</label>
                        <select name="district_id" id="owner_district_select" class="form-select" required data-selected="{{ $selectedDistrictId }}">
                            <option value="">Ilce sec</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Mahalle</label>
                        <select name="neighborhood_id" id="owner_neighborhood_select" class="form-select" required data-selected="{{ $selectedNeighborhoodId }}">
                            <option value="">Mahalle sec</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Cadde</label>
                        <input type="text" name="avenue_name" class="form-control" value="{{ old('avenue_name', $item->avenue_name) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Sokak</label>
                        <input type="text" name="street_name" class="form-control" value="{{ old('street_name', $item->street_name) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Dis Kapi No</label>
                        <input type="text" name="building_no" class="form-control" value="{{ old('building_no', $item->building_no) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Ic Kapi No</label>
                        <input type="text" name="unit_no" class="form-control" value="{{ old('unit_no', $item->unit_no) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Adres Notu (opsiyonel)</label>
                        <input type="text" name="address_note" class="form-control" value="{{ old('address_note', $item->address_note) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Servis Tipi</label>
                        <input type="text" name="service_type" class="form-control" value="{{ old('service_type', $item->service_type) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Fiyat Etiketi</label>
                        <input type="text" name="price_label" class="form-control" value="{{ old('price_label', $item->price_label) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Fiyat Min</label>
                        <input type="number" step="0.01" min="0" name="price_min" class="form-control" value="{{ old('price_min', $item->price_min) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Fiyat Max</label>
                        <input type="number" step="0.01" min="0" name="price_max" class="form-control" value="{{ old('price_max', $item->price_max) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Para Birimi</label>
                        @php($currencyVal = strtoupper((string) old('currency', $item->currency ?: 'TRY')))
                        <select name="currency" class="form-select">
                            <option value="">Seciniz</option>
                            <option value="TRY" @selected($currencyVal === 'TRY')>TRY</option>
                            <option value="USD" @selected($currencyVal === 'USD')>USD</option>
                            <option value="EUR" @selected($currencyVal === 'EUR')>EUR</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Fiyat Tipi</label>
                        @php($priceTypeVal = old('price_type', $item->price_type))
                        <select name="price_type" class="form-select">
                            <option value="">Seciniz</option>
                            <option value="fixed" @selected($priceTypeVal === 'fixed')>Sabit</option>
                            <option value="starting_from" @selected($priceTypeVal === 'starting_from')>Baslangic Fiyati</option>
                            <option value="range" @selected($priceTypeVal === 'range')>Aralik</option>
                            <option value="hourly" @selected($priceTypeVal === 'hourly')>Saatlik</option>
                            <option value="daily" @selected($priceTypeVal === 'daily')>Gunluk</option>
                            <option value="label_only" @selected($priceTypeVal === 'label_only')>Sadece Etiket</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Servis Sehri (coklu)</label>
                        <select
                            name="service_area_city_ids[]"
                            id="owner_service_area_city_ids"
                            class="form-select"
                            multiple
                            size="6"
                            data-selected='@json($selectedServiceAreaCityIds)'
                        >
                            @foreach(($locationOptions['cities'] ?? []) as $cityRow)
                                <option value="{{ $cityRow['id'] }}">{{ $cityRow['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Servis Ilcesi (coklu)</label>
                        <select
                            name="service_area_district_ids[]"
                            id="owner_service_area_district_ids"
                            class="form-select"
                            multiple
                            size="6"
                            data-selected='@json($selectedServiceAreaDistrictIds)'
                        >
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Servis Alanlari (otomatik / geriye uyumlu)</label>
                        <textarea id="owner_service_areas_preview" class="form-control" rows="4" readonly></textarea>
                        <textarea id="owner_service_areas_text" name="service_areas_text" class="d-none">{{ $serviceAreasText }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Telefon</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $item->phone) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">WhatsApp</label>
                        <input type="text" name="whatsapp" class="form-control" value="{{ old('whatsapp', $item->whatsapp) }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Ozet</label>
                        <textarea name="summary" class="form-control" rows="3">{{ old('summary', $item->summary) }}</textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Icerik</label>
                        <textarea name="content" class="form-control" rows="6">{{ old('content', $item->content) }}</textarea>
                    </div>
                    <div class="col-12">
                        <h3 class="h6 mb-2 mt-2">Gorseller</h3>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Kapak Gorseli</label>
                        @if(!empty($item->cover_image_path))
                            <div class="mb-2">
                                <img src="/{{ $item->cover_image_path }}" alt="Kapak" class="img-preview mb-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remove_cover_image" value="1" id="owner_remove_cover_image">
                                    <label class="form-check-label" for="owner_remove_cover_image">Kapak gorselini kaldir</label>
                                </div>
                            </div>
                        @endif
                        <input class="form-control" type="file" name="cover_image" accept="image/*">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Galeri Gorselleri</label>
                        <input type="hidden" name="gallery_order" value="">
                        @if(is_array($item->gallery_json) && count($item->gallery_json))
                            <div class="d-flex flex-wrap gap-2 mb-2">
                                @foreach($item->gallery_json as $img)
                                    <div class="gallery-item border rounded p-2">
                                        <img src="/{{ $img }}" alt="Galeri" class="img-preview mb-2">
                                        <label class="form-check-label d-flex align-items-center gap-2">
                                            <input type="checkbox" name="remove_gallery[]" value="{{ $img }}">
                                            <span>Kaldir</span>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                        <input class="form-control" type="file" name="gallery_images[]" accept="image/*" multiple>
                    </div>
                    @php($dynamicAttributes = $dynamicAttributes ?? [])
                    @if(count($dynamicAttributes))
                        <div class="col-12">
                            <h3 class="h6 mb-2 mt-2">Kategoriye Ozel Alanlar</h3>
                        </div>
                        @foreach($dynamicAttributes as $attr)
                            @php($inputName = (string) $attr['input_name'])
                            @php($fieldType = (string) $attr['field_type'])
                            @php($label = (string) $attr['label'])
                            @php($isRequired = (bool) ($attr['is_required'] ?? false))
                            <div class="col-md-6">
                                <label class="form-label">{{ $label }} @if($isRequired)*@endif</label>
                                @if($fieldType === 'multiselect')
                                    @php($selectedValues = old($inputName, $attr['values'] ?? []))
                                    @php($selectedValues = is_array($selectedValues) ? $selectedValues : (trim((string)$selectedValues) === '' ? [] : [(string)$selectedValues]))
                                    @php($selectedValuesNorm = array_map(static fn ($v) => mb_strtolower(trim((string)$v)), $selectedValues))
                                    <div class="d-flex flex-column gap-1">
                                        @foreach(($attr['options'] ?? []) as $option)
                                            @php($optionValue = (string) $option)
                                            <label class="d-flex align-items-center gap-2">
                                                <input
                                                    type="checkbox"
                                                    name="{{ $inputName }}[]"
                                                    value="{{ $optionValue }}"
                                                    @checked(in_array(mb_strtolower($optionValue), $selectedValuesNorm, true))
                                                >
                                                <span>{{ $optionValue }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                @elseif($fieldType === 'select')
                                    @php($selectedValue = (string) old($inputName, (string) ($attr['value'] ?? '')))
                                    <select name="{{ $inputName }}" class="form-select" @if($isRequired) required @endif>
                                        <option value="">Seciniz</option>
                                        @foreach(($attr['options'] ?? []) as $option)
                                            @php($optionValue = (string) $option)
                                            <option value="{{ $optionValue }}" @selected($selectedValue === $optionValue)>{{ $optionValue }}</option>
                                        @endforeach
                                    </select>
                                @elseif($fieldType === 'boolean')
                                    @php($selectedValue = (string) old($inputName, (string) ($attr['value'] ?? '')))
                                    <select name="{{ $inputName }}" class="form-select" @if($isRequired) required @endif>
                                        <option value="">Seciniz</option>
                                        <option value="1" @selected($selectedValue === '1')>Evet</option>
                                        <option value="0" @selected($selectedValue === '0')>Hayir</option>
                                    </select>
                                @elseif($fieldType === 'number')
                                    <input
                                        type="number"
                                        step="any"
                                        name="{{ $inputName }}"
                                        class="form-control"
                                        value="{{ old($inputName, $attr['value'] ?? '') }}"
                                        @if($isRequired) required @endif
                                    >
                                @else
                                    <input
                                        type="text"
                                        name="{{ $inputName }}"
                                        class="form-control"
                                        value="{{ old($inputName, $attr['value'] ?? '') }}"
                                        @if($isRequired) required @endif
                                    >
                                @endif
                            </div>
                        @endforeach
                    @endif
                </div>

                <div class="d-flex flex-wrap gap-2 pt-1">
                    <button class="btn btn-primary" type="submit">Kaydet</button>
                    <a class="btn btn-outline-secondary" href="{{ route('owner.listings.index') }}">Geri</a>
                </div>
            </form>
        </section>
    </div>

    <script>
        (function () {
            var districtMap = @json($locationOptions['district_map'] ?? []);
            var neighborhoodMap = @json($locationOptions['neighborhood_map'] ?? []);
            var cityRows = @json($locationOptions['cities'] ?? []);
            var citySelect = document.getElementById('owner_city_select');
            var districtSelect = document.getElementById('owner_district_select');
            var neighborhoodSelect = document.getElementById('owner_neighborhood_select');
            var serviceAreaCitySelect = document.getElementById('owner_service_area_city_ids');
            var serviceAreaDistrictSelect = document.getElementById('owner_service_area_district_ids');
            var serviceAreasText = document.getElementById('owner_service_areas_text');
            var serviceAreasPreview = document.getElementById('owner_service_areas_preview');
            if (!citySelect || !districtSelect || !neighborhoodSelect) return;

            function normalizeText(value) {
                return String(value || '').trim().toLocaleLowerCase('tr-TR');
            }

            var cityNameById = {};
            var cityIdByName = {};
            cityRows.forEach(function (row) {
                var id = String(row.id);
                var name = String(row.name || '');
                cityNameById[id] = name;
                cityIdByName[normalizeText(name)] = id;
            });

            var districtNameById = {};
            var districtIdByName = {};
            var districtCityIdById = {};
            Object.keys(districtMap || {}).forEach(function (cityId) {
                (districtMap[cityId] || []).forEach(function (row) {
                    var districtId = String(row.id);
                    var districtName = String(row.name || '');
                    districtNameById[districtId] = districtName;
                    districtCityIdById[districtId] = String(cityId);
                    districtIdByName[String(cityId) + '|' + normalizeText(districtName)] = districtId;
                });
            });

            function getSelectValues(select) {
                if (!select) return [];
                return Array.from(select.selectedOptions || []).map(function (opt) { return String(opt.value); });
            }

            function ensureSelected(select, values) {
                if (!select) return;
                var selectedSet = {};
                (values || []).forEach(function (v) { selectedSet[String(v)] = true; });
                Array.from(select.options || []).forEach(function (opt) {
                    opt.selected = !!selectedSet[String(opt.value)];
                });
            }

            function parseLegacyAreas(raw) {
                var lines = String(raw || '').split(/\r\n|\r|\n/);
                var cityIds = {};
                var districtIds = {};

                lines.forEach(function (line) {
                    var normalized = String(line || '').trim().replace(/\s+/g, ' ');
                    if (!normalized) return;

                    var parts = null;
                    ['|', '/', '>'].forEach(function (delimiter) {
                        if (parts) return;
                        if (normalized.indexOf(delimiter) !== -1) {
                            parts = normalized.split(delimiter, 2);
                        }
                    });

                    var cityName = normalized;
                    var districtName = '';
                    if (parts) {
                        cityName = String(parts[0] || '').trim();
                        districtName = String(parts[1] || '').trim();
                    }

                    var cityId = cityIdByName[normalizeText(cityName)] || '';
                    if (!cityId) return;
                    cityIds[cityId] = true;

                    if (districtName) {
                        var districtId = districtIdByName[String(cityId) + '|' + normalizeText(districtName)] || '';
                        if (districtId) {
                            districtIds[districtId] = true;
                        }
                    }
                });

                return {
                    cityIds: Object.keys(cityIds),
                    districtIds: Object.keys(districtIds)
                };
            }

            function renderServiceAreaDistrictOptions() {
                if (!serviceAreaCitySelect || !serviceAreaDistrictSelect) return;

                var selectedCityIds = getSelectValues(serviceAreaCitySelect);
                var selectedDistrictIds = getSelectValues(serviceAreaDistrictSelect);
                var districtSelectedSet = {};
                selectedDistrictIds.forEach(function (id) { districtSelectedSet[id] = true; });

                serviceAreaDistrictSelect.innerHTML = '';
                selectedCityIds.forEach(function (cityId) {
                    var cityName = cityNameById[String(cityId)] || '';
                    (districtMap[String(cityId)] || []).forEach(function (row) {
                        var districtId = String(row.id);
                        var option = document.createElement('option');
                        option.value = districtId;
                        option.textContent = cityName ? (cityName + ' / ' + row.name) : row.name;
                        if (districtSelectedSet[districtId]) {
                            option.selected = true;
                        }
                        serviceAreaDistrictSelect.appendChild(option);
                    });
                });
            }

            function syncServiceAreasText() {
                if (!serviceAreaCitySelect || !serviceAreaDistrictSelect || !serviceAreasText || !serviceAreasPreview) return;

                var selectedCityIds = getSelectValues(serviceAreaCitySelect);
                var selectedDistrictIds = getSelectValues(serviceAreaDistrictSelect);
                var lines = [];
                var seen = {};

                selectedCityIds.forEach(function (cityId) {
                    var cityName = cityNameById[String(cityId)] || '';
                    if (!cityName) return;

                    var hasDistrict = false;
                    selectedDistrictIds.forEach(function (districtId) {
                        if (String(districtCityIdById[String(districtId)] || '') !== String(cityId)) return;
                        var districtName = districtNameById[String(districtId)] || '';
                        if (!districtName) return;
                        hasDistrict = true;
                        var line = cityName + ' | ' + districtName;
                        var key = normalizeText(line);
                        if (seen[key]) return;
                        seen[key] = true;
                        lines.push(line);
                    });

                    if (!hasDistrict) {
                        var cityKey = normalizeText(cityName);
                        if (!seen[cityKey]) {
                            seen[cityKey] = true;
                            lines.push(cityName);
                        }
                    }
                });

                var text = lines.join("\n");
                serviceAreasText.value = text;
                serviceAreasPreview.value = text;
            }

            function renderNeighborhoods(resetSelected) {
                var districtId = districtSelect.value || '';
                var selectedNeighborhood = resetSelected ? '' : (neighborhoodSelect.getAttribute('data-selected') || neighborhoodSelect.value || '');
                var neighborhoods = neighborhoodMap[districtId] || [];

                neighborhoodSelect.innerHTML = '';
                var neighborhoodEmptyOpt = document.createElement('option');
                neighborhoodEmptyOpt.value = '';
                neighborhoodEmptyOpt.textContent = 'Mahalle sec';
                neighborhoodSelect.appendChild(neighborhoodEmptyOpt);

                neighborhoods.forEach(function (row) {
                    var value = String(row.id);
                    var option = document.createElement('option');
                    option.value = value;
                    option.textContent = row.name;
                    if (value === String(selectedNeighborhood)) {
                        option.selected = true;
                    }
                    neighborhoodSelect.appendChild(option);
                });
            }

            function renderDistricts(resetSelected) {
                var selectedCityId = citySelect.value || '';
                var selectedDistrict = resetSelected ? '' : (districtSelect.getAttribute('data-selected') || districtSelect.value || '');
                var districts = districtMap[selectedCityId] || [];

                districtSelect.innerHTML = '';
                var emptyOpt = document.createElement('option');
                emptyOpt.value = '';
                emptyOpt.textContent = 'Ilce sec';
                districtSelect.appendChild(emptyOpt);

                districts.forEach(function (row) {
                    var value = String(row.id);
                    var option = document.createElement('option');
                    option.value = value;
                    option.textContent = row.name;
                    if (value === String(selectedDistrict)) {
                        option.selected = true;
                    }
                    districtSelect.appendChild(option);
                });

                renderNeighborhoods(resetSelected);
            }

            citySelect.addEventListener('change', function () {
                districtSelect.setAttribute('data-selected', '');
                neighborhoodSelect.setAttribute('data-selected', '');
                renderDistricts(true);
            });
            districtSelect.addEventListener('change', function () {
                neighborhoodSelect.setAttribute('data-selected', '');
                renderNeighborhoods(true);
            });

            renderDistricts(false);

            if (serviceAreaCitySelect && serviceAreaDistrictSelect && serviceAreasText && serviceAreasPreview) {
                var selectedCityIdsRaw = [];
                var selectedDistrictIdsRaw = [];
                try { selectedCityIdsRaw = JSON.parse(serviceAreaCitySelect.getAttribute('data-selected') || '[]') || []; } catch (e) { selectedCityIdsRaw = []; }
                try { selectedDistrictIdsRaw = JSON.parse(serviceAreaDistrictSelect.getAttribute('data-selected') || '[]') || []; } catch (e) { selectedDistrictIdsRaw = []; }

                if ((!selectedCityIdsRaw || selectedCityIdsRaw.length === 0) && serviceAreasText.value.trim() !== '') {
                    var parsed = parseLegacyAreas(serviceAreasText.value);
                    selectedCityIdsRaw = parsed.cityIds;
                    selectedDistrictIdsRaw = parsed.districtIds;
                }

                ensureSelected(serviceAreaCitySelect, selectedCityIdsRaw);
                renderServiceAreaDistrictOptions();
                ensureSelected(serviceAreaDistrictSelect, selectedDistrictIdsRaw);
                syncServiceAreasText();

                serviceAreaCitySelect.addEventListener('change', function () {
                    renderServiceAreaDistrictOptions();
                    syncServiceAreasText();
                });
                serviceAreaDistrictSelect.addEventListener('change', syncServiceAreasText);

                var listingForm = serviceAreaCitySelect.closest('form');
                if (listingForm) {
                    listingForm.addEventListener('submit', syncServiceAreasText);
                }
            }
        })();
    </script>
@endsection

