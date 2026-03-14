@extends('admin.layout')

@section('content')
    <div class="card shadow-sm p-3 p-md-4">
        <h2 class="h4 mb-3">{{ $item->exists ? 'Ilan Duzenle' : 'Yeni Ilan' }}</h2>

        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                <div class="fw-semibold mb-1">Form hatasi var</div>
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="post" enctype="multipart/form-data" action="{{ $item->exists ? route('admin.listings.update', $item) : route('admin.listings.store') }}" class="vstack gap-4">
            @csrf
            @if($item->exists) @method('put') @endif
            @php($categoriesByMain = $categoriesByMain ?? collect())

            <section>
                <h3 class="h6 mb-3">1) Temel Bilgi</h3>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Site</label>
                        @php($siteVal = old('site', $item->site))
                        <select name="site" class="form-select">
                            <option value="orkestram.net" @selected($siteVal === 'orkestram.net')>orkestram.net</option>
                            <option value="izmirorkestra.net" @selected($siteVal === 'izmirorkestra.net')>izmirorkestra.net</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Yayin Kapsami</label>
                        @php($scopeVal = old('site_scope', $item->site_scope ?: 'single'))
                        <select name="site_scope" class="form-select">
                            <option value="single" @selected($scopeVal === 'single')>Tek Site</option>
                            <option value="both" @selected($scopeVal === 'both')>Iki Site (both)</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Durum</label>
                        @php($statusVal = old('status', $item->status))
                        <select name="status" class="form-select">
                            <option value="draft" @selected($statusVal === 'draft')>draft</option>
                            <option value="published" @selected($statusVal === 'published')>published</option>
                            <option value="archived" @selected($statusVal === 'archived')>archived</option>
                        </select>
                    </div>
                </div>

                <div class="row g-3 mt-1">
                    <div class="col-md-6">
                        <label class="form-label">Ad</label>
                        <input name="name" class="form-control" value="{{ old('name', $item->name) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Slug</label>
                        <input name="slug" class="form-control" value="{{ old('slug', $item->slug) }}" required>
                    </div>
                </div>

                <div class="mt-3">
                    <label class="form-label">Kategori</label>
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

                @php($locationOptions = $locationOptions ?? ['cities' => [], 'district_map' => []])
                @php($selectedCityId = (int) old('city_id', $item->city_id))
                @php($selectedDistrictId = (int) old('district_id', $item->district_id))
                @php($selectedNeighborhoodId = (int) old('neighborhood_id', $item->neighborhood_id))

                <div class="row g-3 mt-1">
                    <div class="col-md-4">
                        <label class="form-label">Sehir *</label>
                        <select name="city_id" id="city_select" class="form-select" required data-selected="{{ $selectedCityId }}">
                            <option value="">Sehir sec</option>
                            @foreach(($locationOptions['cities'] ?? []) as $cityRow)
                                <option value="{{ $cityRow['id'] }}" @selected($selectedCityId === (int) $cityRow['id'])>{{ $cityRow['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Ilce *</label>
                        <select name="district_id" id="district_select" class="form-select" required data-selected="{{ $selectedDistrictId }}">
                            <option value="">Ilce sec</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Mahalle *</label>
                        <select name="neighborhood_id" id="neighborhood_select" class="form-select" required data-selected="{{ $selectedNeighborhoodId }}">
                            <option value="">Mahalle sec</option>
                        </select>
                    </div>
                </div>

                <div class="row g-3 mt-1">
                    <div class="col-md-6">
                        <label class="form-label">Cadde *</label>
                        <input name="avenue_name" class="form-control" value="{{ old('avenue_name', $item->avenue_name) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Sokak *</label>
                        <input name="street_name" class="form-control" value="{{ old('street_name', $item->street_name) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Dis Kapi No *</label>
                        <input name="building_no" class="form-control" value="{{ old('building_no', $item->building_no) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Ic Kapi No *</label>
                        <input name="unit_no" class="form-control" value="{{ old('unit_no', $item->unit_no) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Adres Notu (opsiyonel)</label>
                        <input name="address_note" class="form-control" value="{{ old('address_note', $item->address_note) }}">
                    </div>
                </div>

                <div class="row g-3 mt-1">
                    <div class="col-md-6">
                        <label class="form-label">Kapsama Modu</label>
                        @php($coverageMode = old('coverage_mode', $item->coverage_mode ?: 'location_only'))
                        <select name="coverage_mode" class="form-select">
                            <option value="location_only" @selected($coverageMode === 'location_only')>Sadece Konum</option>
                            <option value="service_area_only" @selected($coverageMode === 'service_area_only')>Sadece Servis Alani</option>
                            <option value="hybrid" @selected($coverageMode === 'hybrid')>Konum + Servis Alani</option>
                        </select>
                    </div>
                    @php($serviceAreasText = old('service_areas_text', $item->serviceAreasText()))
                    @php($selectedServiceAreaCityIds = old('service_area_city_ids', []))
                    @php($selectedServiceAreaDistrictIds = old('service_area_district_ids', []))
                    @php($selectedServiceAreaCityIds = is_array($selectedServiceAreaCityIds) ? array_values(array_filter($selectedServiceAreaCityIds, static fn($v) => (string)$v !== '')) : [])
                    @php($selectedServiceAreaDistrictIds = is_array($selectedServiceAreaDistrictIds) ? array_values(array_filter($selectedServiceAreaDistrictIds, static fn($v) => (string)$v !== '')) : [])
                    <div class="col-md-6">
                        <label class="form-label">Servis Sehri (coklu)</label>
                        <select
                            name="service_area_city_ids[]"
                            id="service_area_city_ids"
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
                            id="service_area_district_ids"
                            class="form-select"
                            multiple
                            size="6"
                            data-selected='@json($selectedServiceAreaDistrictIds)'
                        >
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Servis Alanlari (otomatik / geriye uyumlu)</label>
                        <textarea id="service_areas_preview" class="form-control" rows="4" readonly></textarea>
                        <textarea id="service_areas_text" name="service_areas_text" class="d-none">{{ $serviceAreasText }}</textarea>
                    </div>
                </div>

                <div class="row g-3 mt-1">
                    <div class="col-md-6">
                        <label class="form-label">Hizmet Turu</label>
                        <input name="service_type" class="form-control" value="{{ old('service_type', $item->service_type) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Fiyat Etiketi *</label>
                        <input name="price_label" class="form-control" value="{{ old('price_label', $item->price_label) }}" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Ozet * (30-500 karakter)</label>
                        <textarea name="summary" class="form-control" rows="3" required>{{ old('summary', $item->summary) }}</textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Icerik * (minimum 80 karakter)</label>
                        <textarea name="content" class="form-control" rows="10" required>{{ old('content', $item->content) }}</textarea>
                    </div>
                </div>
            </section>

            <section>
                <h3 class="h6 mb-3">2) Iletisim ve Ozellikler</h3>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">WhatsApp</label>
                        <input name="whatsapp" class="form-control" value="{{ old('whatsapp', $item->whatsapp) }}" placeholder="+90532...">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Telefon</label>
                        <input name="phone" class="form-control" value="{{ old('phone', $item->phone) }}" placeholder="+90232...">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Ozellikler (her satira bir ozellik)</label>
                        <textarea name="features_text" class="form-control" rows="5" placeholder="Orn:\n6 kisilik ekip\nCanli davul + nefesli\n45 dakika performans">{{ old('features_text', is_array($item->features_json ?? null) ? implode("\n", $item->features_json) : '') }}</textarea>
                    </div>
                </div>
            </section>

            @php($dynamicAttributes = $dynamicAttributes ?? [])
            @if(count($dynamicAttributes))
                <section>
                    <h3 class="h6 mb-3">3) Kategoriye Ozel Alanlar</h3>
                    <div class="row g-3">
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
                    </div>
                </section>
            @endif

            <section>
                <h3 class="h6 mb-3">4) Gorseller</h3>

                @if(!empty($item->cover_image_path))
                    <div class="card p-3 mb-3">
                        <div class="fw-semibold mb-2">Mevcut Kapak</div>
                        <img src="/{{ $item->cover_image_path }}" alt="Kapak" class="img-preview mt-2 mb-2">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remove_cover_image" value="1" id="remove_cover_image">
                            <label class="form-check-label" for="remove_cover_image">Kapak gorselini kaldir</label>
                        </div>
                    </div>
                @endif

                <div class="mb-3">
                    <label class="form-label">Kapak Gorsel</label>
                    <input class="form-control" type="file" name="cover_image" accept="image/*">
                </div>

                @if(is_array($item->gallery_json) && count($item->gallery_json))
                    <div class="card p-3 mb-3">
                        <div class="fw-semibold mb-2">Mevcut Galeri (surukle-birak ile sirala)</div>
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
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" name="reset_gallery" value="1" id="reset_gallery">
                            <label class="form-check-label" for="reset_gallery">Galeriyi sifirla</label>
                        </div>
                    </div>
                @endif

                <div>
                    <label class="form-label">Galeri Gorselleri (coklu secim)</label>
                    <input class="form-control" type="file" name="gallery_images[]" accept="image/*" multiple>
                </div>
            </section>

            <section>
                <h3 class="h6 mb-3">5) SEO</h3>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">SEO Baslik</label>
                        <input name="seo_title" class="form-control" value="{{ old('seo_title', $item->seo_title) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">SEO Aciklama</label>
                        <input name="seo_description" class="form-control" value="{{ old('seo_description', $item->seo_description) }}">
                    </div>
                </div>
            </section>

            <div class="d-flex flex-wrap gap-2">
                <button class="btn btn-primary" type="submit">Kaydet</button>
                <a class="btn btn-outline-secondary" href="{{ route('admin.listings.index', ['site' => old('site', $item->site ?: 'orkestram.net')]) }}">Iptal</a>
            </div>
        </form>
    </div>

    <script>
        (function () {
            var districtMap = @json($locationOptions['district_map'] ?? []);
            var neighborhoodMap = @json($locationOptions['neighborhood_map'] ?? []);
            var cityRows = @json($locationOptions['cities'] ?? []);
            var citySelect = document.getElementById('city_select');
            var districtSelect = document.getElementById('district_select');
            var neighborhoodSelect = document.getElementById('neighborhood_select');
            var serviceAreaCitySelect = document.getElementById('service_area_city_ids');
            var serviceAreaDistrictSelect = document.getElementById('service_area_district_ids');
            var serviceAreasText = document.getElementById('service_areas_text');
            var serviceAreasPreview = document.getElementById('service_areas_preview');

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

            function syncNeighborhoodOptions(resetSelected) {
                if (!districtSelect || !neighborhoodSelect) return;
                var districtId = districtSelect.value || '';
                var selectedNeighborhoodId = resetSelected ? '' : (neighborhoodSelect.getAttribute('data-selected') || neighborhoodSelect.value || '');
                var neighborhoods = neighborhoodMap[districtId] || [];

                neighborhoodSelect.innerHTML = '';
                var neighborhoodEmpty = document.createElement('option');
                neighborhoodEmpty.value = '';
                neighborhoodEmpty.textContent = 'Mahalle sec';
                neighborhoodSelect.appendChild(neighborhoodEmpty);

                neighborhoods.forEach(function (row) {
                    var value = String(row.id);
                    var option = document.createElement('option');
                    option.value = value;
                    option.textContent = row.name;
                    if (value === String(selectedNeighborhoodId)) {
                        option.selected = true;
                    }
                    neighborhoodSelect.appendChild(option);
                });
            }

            function syncDistrictOptions(resetSelected) {
                if (!citySelect || !districtSelect) return;
                var selectedCityId = citySelect.value || '';
                var selectedDistrictId = resetSelected ? '' : (districtSelect.getAttribute('data-selected') || districtSelect.value || '');
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
                    if (value === String(selectedDistrictId)) {
                        option.selected = true;
                    }
                    districtSelect.appendChild(option);
                });

                syncNeighborhoodOptions(resetSelected);
            }

            if (citySelect && districtSelect && neighborhoodSelect) {
                citySelect.addEventListener('change', function () {
                    districtSelect.setAttribute('data-selected', '');
                    neighborhoodSelect.setAttribute('data-selected', '');
                    syncDistrictOptions(true);
                });
                districtSelect.addEventListener('change', function () {
                    neighborhoodSelect.setAttribute('data-selected', '');
                    syncNeighborhoodOptions(true);
                });
                syncDistrictOptions(false);
            }

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
