@extends('portal.layout')

@section('content')
    <div class="card">
        <div class="page-head owner-page-head">
            <h2>Owner Ilanlar</h2>
            <a class="btn" href="{{ route('owner.listings.create') }}">Yeni Ilan</a>
        </div>
        @if(session('ok'))
            <p class="muted mb-10 status-ok">{{ session('ok') }}</p>
        @endif
        <div class="table-wrap">
            <table>
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Ad</th>
                    <th>Slug</th>
                    <th>Sehir</th>
                    <th>Durum</th>
                    <th>Aksiyon</th>
                </tr>
                </thead>
                <tbody>
                @forelse($rows as $r)
                    <tr>
                        <td>{{ $r->id }}</td>
                        <td>{{ $r->name }}</td>
                        <td>{{ $r->slug }}</td>
                        <td>{{ $r->city }}</td>
                        <td>{{ $r->status }}</td>
                        <td>
                            <div class="actions">
                                <a class="btn" href="{{ route('owner.listings.edit', ['listing' => $r->id]) }}">Duzenle</a>
                                <form method="post" action="{{ route('owner.listings.status', ['listing' => $r->id]) }}">
                                    @csrf
                                    <input type="hidden" name="status" value="{{ $r->status === 'published' ? 'paused' : 'published' }}">
                                    <button class="btn" type="submit">{{ $r->status === 'published' ? 'Pasife Al' : 'Yayina Al' }}</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6">Kayit yok.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-10">{{ $rows->links() }}</div>
    </div>
@endsection
