@extends('admin.layout')

@section('content')
    <div class="card">
        <form method="get">
            <div class="category-filter-grid">
                <div>
                    <label>Site</label>
                    <select name="site">
                        <option value="orkestram.net" @selected($site === 'orkestram.net')>orkestram.net</option>
                        <option value="izmirorkestra.net" @selected($site === 'izmirorkestra.net')>izmirorkestra.net</option>
                    </select>
                </div>
                <div>
                    <label>Ana Kategori</label>
                    <select name="main_category_id">
                        <option value="">Hepsi</option>
                        @foreach($mainCategories as $main)
                            <option value="{{ $main->id }}" @selected($mainCategoryId === (int) $main->id)>{{ $main->name }} ({{ (int) $main->categories_count }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label>Arama</label>
                    <input type="text" name="q" value="{{ $search }}" placeholder="ad veya slug">
                </div>
            </div>
            <label style="display:flex;align-items:center;gap:8px;margin-top:10px;">
                <input type="checkbox" name="with_listings" value="1" @checked($withListings)>
                <span>Sadece secili site + both kapsaminda ilani olan kategorileri goster</span>
            </label>
            <div class="category-filter-actions">
                <button class="btn" type="submit">Filtrele</button>
                <a class="btn" href="{{ route('admin.categories.index') }}">Temizle</a>
                <a class="btn" href="{{ route('admin.categories.create') }}">Yeni Kategori</a>
            </div>
        </form>

        <form id="bulk-category-form" method="post" action="{{ route('admin.categories.bulk-update') }}">
            @csrf
            <input type="hidden" name="site" value="{{ $site }}">
            <input type="hidden" name="main_category_id" value="{{ $mainCategoryId }}">
            <input type="hidden" name="q" value="{{ $search }}">
            <input type="hidden" name="with_listings" value="{{ $withListings ? '1' : '0' }}">
            <input type="hidden" name="ids_csv" id="ids_csv" value="">

            <div class="bulk-toolbar">
                <select name="bulk_action" class="bulk-action-select" required>
                    <option value="">Toplu Islem Sec</option>
                    <option value="activate">Secilenleri Aktif Yap</option>
                    <option value="passive">Secilenleri Pasife Cek</option>
                    <option value="indexable">Secilenleri Indexable Yap</option>
                    <option value="noindex">Secilenleri Noindex Yap</option>
                </select>
                <button class="btn" type="submit" onclick="return confirm('Secili kategorilere toplu islem uygulansin mi?')">Toplu Uygula</button>
                <span class="muted">Site secimi ilan sayisini etkiler; isaretli secenekte listeyi de daraltir.</span>
            </div>
        </form>

        <div class="table-wrap">
            <table class="table table-striped align-middle">
                <thead>
                <tr>
                    <th><input type="checkbox" id="select-all-categories" class="category-check" title="Tumunu sec"></th>
                    <th>ID</th>
                    <th>Ana Kategori</th>
                    <th>Ad</th>
                    <th>Kapak</th>
                    <th>Slug</th>
                    <th>Durum</th>
                    <th>Index</th>
                    <th>Yayinda Ilan</th>
                    <th>Toplam Ilan</th>
                    <th>Sira</th>
                    <th>Islem</th>
                </tr>
                </thead>
                <tbody>
                @forelse($rows as $r)
                    <tr>
                        <td><input type="checkbox" value="{{ $r->id }}" class="category-check category-row-check"></td>
                        <td>{{ $r->id }}</td>
                        <td>{{ $r->mainCategory?->name }}</td>
                        <td>{{ $r->name }}</td>
                        <td>
                            @if($r->cover_image_path)
                                <img src="/{{ $r->cover_image_path }}" alt="{{ $r->name }}" class="category-cover-thumb">
                            @else
                                <span class="muted">Yok</span>
                            @endif
                        </td>
                        <td><code>{{ $r->slug }}</code></td>
                        <td>{{ $r->is_active ? 'active' : 'passive' }}</td>
                        <td>{{ $r->is_indexable ? 'indexable' : 'noindex' }}</td>
                        <td>{{ (int) $r->published_listings_count }}</td>
                        <td>{{ (int) $r->total_listings_count }}</td>
                        <td>{{ $r->sort_order }}</td>
                        <td>
                            <div class="action-stack">
                                <a class="btn" href="{{ route('admin.categories.edit', $r) }}">Duzenle</a>
                                <a class="btn" href="{{ route('admin.category-attributes.index', $r) }}">Ozellikler</a>
                                <form method="post" action="{{ route('admin.categories.destroy', $r) }}" onsubmit="return confirm('Kategori pasife cekilsin mi?')">
                                    @csrf
                                    @method('delete')
                                    <button class="btn" type="submit">Pasife Cek</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="12">
                            Kategori yok.
                            @if($mainCategoryId > 0 || $search !== '' || $withListings)
                                <span class="muted">Secili filtrelerle eslesen kayit bulunamadi. Filtreleri temizleyip tekrar deneyin.</span>
                            @endif
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @include('admin.partials.pagination', ['paginator' => $rows])
    </div>
    <script>
        (function () {
            var master = document.getElementById('select-all-categories');
            var rows = Array.from(document.querySelectorAll('.category-row-check'));
            var bulkForm = document.getElementById('bulk-category-form');
            var idsCsvInput = document.getElementById('ids_csv');

            if (master) {
                master.addEventListener('change', function () {
                    rows.forEach(function (cb) { cb.checked = master.checked; });
                });
            }

            if (bulkForm && idsCsvInput) {
                bulkForm.addEventListener('submit', function (event) {
                    var ids = rows.filter(function (cb) { return cb.checked; }).map(function (cb) { return cb.value; });
                    idsCsvInput.value = ids.join(',');
                    if (ids.length === 0) {
                        event.preventDefault();
                        alert('Toplu islem icin en az bir kategori secin.');
                    }
                });
            }
        })();
    </script>
@endsection
