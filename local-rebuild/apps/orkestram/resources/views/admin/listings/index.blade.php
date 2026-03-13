@extends('admin.layout')

@section('content')
    <div class="card">
        <form method="get" class="row">
            <div>
                <label>Site</label>
                <select name="site">
                    <option value="orkestram.net" @selected($site === 'orkestram.net')>orkestram.net</option>
                    <option value="izmirorkestra.net" @selected($site === 'izmirorkestra.net')>izmirorkestra.net</option>
                </select>
            </div>
            <div class="form-inline-actions">
                <button class="btn" type="submit">Filtrele</button>
                <a class="btn" href="{{ route('admin.listings.create', ['site' => $site]) }}">Yeni Ilan</a>
            </div>
        </form>

        <div class="table-wrap">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Kapak</th>
                        <th>Ad</th>
                        <th>Slug</th>
                        <th>Kategori</th>
                        <th>Kapsam</th>
                        <th>Sehir</th>
                        <th>Fiyat</th>
                        <th>Durum</th>
                        <th>Islem</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rows as $r)
                        <tr>
                            <td>{{ $r->id }}</td>
                            <td>
                                @if($r->cover_image_path)
                                    <img src="/{{ $r->cover_image_path }}" alt="Kapak" class="thumb-80">
                                @else
                                    <span class="muted">Yok</span>
                                @endif
                            </td>
                            <td>{{ $r->name }}</td>
                            <td><code>{{ $r->slug }}</code></td>
                            <td>{{ $r->category?->name ?: '-' }}</td>
                            <td><code>{{ $r->site_scope ?: 'single' }}</code></td>
                            <td>{{ $r->city }}</td>
                            <td>{{ $r->price_label }}</td>
                            <td>{{ $r->status }}</td>
                            <td>
                                <div class="actions">
                                    <a class="btn" href="{{ route('admin.listings.edit', $r) }}">Duzenle</a>
                                    <form method="post" action="{{ route('admin.listings.destroy', $r) }}" onsubmit="return confirm('Silinsin mi?')">
                                        @csrf
                                        @method('delete')
                                        <button class="btn" type="submit">Sil</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="10">Kayit yok.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @include('admin.partials.pagination', ['paginator' => $rows])
    </div>
@endsection
