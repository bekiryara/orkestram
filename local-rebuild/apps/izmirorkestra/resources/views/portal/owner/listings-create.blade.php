@extends('portal.layout')

@section('content')
    <div class="card shadow-sm">
        <div class="account-header">
            <div>
                <h2>Yeni Ilan</h2>
                <p class="muted">Ilan taslagi olustur, sonra icerik ve medya alanlarini guncelle.</p>
            </div>
        </div>
    </div>

    <div class="account-shell">
        @include('portal.owner.partials.menu', ['ownerTab' => 'listings'])

        <section class="card shadow-sm p-3 p-md-4">
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

            <form method="post" action="{{ route('owner.listings.store') }}" class="vstack gap-3">
                @csrf
                @php($locationOptions = $locationOptions ?? ['cities' => [], 'district_map' => [], 'neighborhood_map' => []])
                @php($selectedCityId = (int) old('city_id'))
                @php($selectedDistrictId = (int) old('district_id'))
                @php($selectedNeighborhoodId = (int) old('neighborhood_id'))

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Baslik</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Slug (opsiyonel)</label>
                        <input type="text" name="slug" class="form-control" value="{{ old('slug') }}" placeholder="orn: izmir-dugun-orkestrasi">
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
                    <div class="col-md-12">
                        <label class="form-label">Kategori</label>
                        @php($categoriesByMain = $categoriesByMain ?? collect())
                        @php($selectedCategory = (int) old('category_id'))
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
                    <div class="col-md-6">
                        <label class="form-label">Cadde</label>
                        <input type="text" name="avenue_name" class="form-control" value="{{ old('avenue_name') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Sokak</label>
                        <input type="text" name="street_name" class="form-control" value="{{ old('street_name') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Dis Kapi No</label>
                        <input type="text" name="building_no" class="form-control" value="{{ old('building_no') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Ic Kapi No</label>
                        <input type="text" name="unit_no" class="form-control" value="{{ old('unit_no') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Adres Notu (opsiyonel)</label>
                        <input type="text" name="address_note" class="form-control" value="{{ old('address_note') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Servis Tipi</label>
                        <input type="text" name="service_type" class="form-control" value="{{ old('service_type') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Fiyat Etiketi</label>
                        <input type="text" name="price_label" class="form-control" value="{{ old('price_label') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Telefon</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">WhatsApp</label>
                        <input type="text" name="whatsapp" class="form-control" value="{{ old('whatsapp') }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Ozet</label>
                        <textarea name="summary" class="form-control" rows="3">{{ old('summary') }}</textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Icerik</label>
                        <textarea name="content" class="form-control" rows="6">{{ old('content') }}</textarea>
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
                    <button class="btn btn-primary" type="submit">Taslak Olustur</button>
                    <a class="btn btn-outline-secondary" href="{{ route('owner.listings.index') }}">Geri</a>
                </div>
            </form>
        </section>
    </div>

    <script>
        (function () {
            var districtMap = @json($locationOptions['district_map'] ?? []);
            var neighborhoodMap = @json($locationOptions['neighborhood_map'] ?? []);
            var citySelect = document.getElementById('owner_city_select');
            var districtSelect = document.getElementById('owner_district_select');
            var neighborhoodSelect = document.getElementById('owner_neighborhood_select');
            if (!citySelect || !districtSelect || !neighborhoodSelect) return;

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
        })();
    </script>
@endsection
