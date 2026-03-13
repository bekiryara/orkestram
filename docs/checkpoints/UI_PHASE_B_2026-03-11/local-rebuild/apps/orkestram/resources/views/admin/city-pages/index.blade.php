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
                <a class="btn" href="{{ route('admin.city-pages.create', ['site' => $site]) }}">Yeni Sehir Sayfasi</a>
            </div>
        </form>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Baslik</th>
                    <th>Slug</th>
                    <th>Sehir/Ilce</th>
                    <th>Durum</th>
                    <th>Islem</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rows as $r)
                    <tr>
                        <td>{{ $r->id }}</td>
                        <td>{{ $r->title }}</td>
                        <td><code>{{ $r->slug }}</code></td>
                        <td>{{ $r->city }}{{ $r->district ? ' / ' . $r->district : '' }}</td>
                        <td>{{ $r->status }}</td>
                        <td>
                            <div class="actions">
                                <a class="btn" href="{{ route('admin.city-pages.edit', $r) }}">Duzenle</a>
                                <form method="post" action="{{ route('admin.city-pages.destroy', $r) }}" onsubmit="return confirm('Silinsin mi?')">
                                    @csrf
                                    @method('delete')
                                    <button class="btn" type="submit">Sil</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6">Kayit yok.</td></tr>
                @endforelse
            </tbody>
        </table>
        @include('admin.partials.pagination', ['paginator' => $rows])
    </div>
@endsection
