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
                <a class="btn" href="{{ route('admin.pages.create', ['site' => $site]) }}">Yeni Sayfa</a>
            </div>
        </form>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Baslik</th>
                    <th>Slug</th>
                    <th>Durum</th>
                    <th>Islem</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pages as $p)
                    <tr>
                        <td>{{ $p->id }}</td>
                        <td>{{ $p->title }}</td>
                        <td><code>{{ $p->slug }}</code></td>
                        <td>{{ $p->status }}</td>
                        <td>
                            <div class="actions">
                                <a class="btn" href="{{ route('admin.pages.edit', $p) }}">Duzenle</a>
                                <form method="post" action="{{ route('admin.pages.destroy', $p) }}" onsubmit="return confirm('Silinsin mi?')">
                                    @csrf
                                    @method('delete')
                                    <button class="btn" type="submit">Sil</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">Kayit yok.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @include('admin.partials.pagination', ['paginator' => $pages])
    </div>
@endsection
