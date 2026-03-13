@extends('admin.layout')

@section('content')
    <div class="card">
        <h2 class="title-reset">{{ $category->name }} | Kategori Ozellikleri</h2>
        <p class="muted">Bu kategori icin dinamik alanlari yonetin.</p>

        <div class="category-filter-actions">
            <a class="btn" href="{{ route('admin.categories.index') }}">Kategorilere Don</a>
            <a class="btn" href="{{ route('admin.category-attributes.create', $category) }}">Yeni Ozellik</a>
        </div>

        <div class="table-wrap">
            <table class="table table-striped align-middle">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Anahtar</th>
                    <th>Etiket</th>
                    <th>Tip</th>
                    <th>Filtre Modu</th>
                    <th>Zorunlu</th>
                    <th>Filtre</th>
                    <th>Kart</th>
                    <th>Detay</th>
                    <th>Durum</th>
                    <th>Sira</th>
                    <th>Islem</th>
                </tr>
                </thead>
                <tbody>
                @forelse($rows as $r)
                    <tr>
                        <td>{{ $r->id }}</td>
                        <td><code>{{ $r->key }}</code></td>
                        <td>{{ $r->label }}</td>
                        <td>{{ $r->field_type }}</td>
                        <td>{{ $r->filter_mode ?: 'exact' }}</td>
                        <td>{{ $r->is_required ? 'evet' : 'hayir' }}</td>
                        <td>{{ $r->is_filterable ? 'evet' : 'hayir' }}</td>
                        <td>{{ $r->is_visible_in_card ? 'evet' : 'hayir' }}</td>
                        <td>{{ $r->is_visible_in_detail ? 'evet' : 'hayir' }}</td>
                        <td>{{ $r->is_active ? 'aktif' : 'pasif' }}</td>
                        <td>{{ $r->sort_order }}</td>
                        <td>
                            <div class="action-stack">
                                <a class="btn" href="{{ route('admin.category-attributes.edit', [$category, $r]) }}">Duzenle</a>
                                <form method="post" action="{{ route('admin.category-attributes.destroy', [$category, $r]) }}" onsubmit="return confirm('Ozellik silinsin mi?')">
                                    @csrf
                                    @method('delete')
                                    <button class="btn" type="submit">Sil</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="12">Ozellik yok.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @include('admin.partials.pagination', ['paginator' => $rows])
    </div>
@endsection
