@extends('portal.layout')

@section('content')
    <div class="card">
        <h2>Support Talepler</h2>
        <form method="get" action="{{ route('support.requests.index') }}" class="mb-10">
            <label>Durum Filtresi</label>
            <div class="actions">
                <input type="text" name="status" value="{{ $status ?? '' }}" placeholder="new / contacted / closed">
                <button class="btn" type="submit">Uygula</button>
                <a class="btn" href="{{ route('support.requests.index') }}">Temizle</a>
            </div>
        </form>
        <div class="table-wrap">
            <table>
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Ad</th>
                    <th>Telefon</th>
                    <th>Mesaj</th>
                    <th>Durum</th>
                    <th>Not</th>
                    <th>Tarih</th>
                    <th>Aksiyon</th>
                </tr>
                </thead>
                <tbody>
                @forelse($rows as $r)
                    <tr>
                        <td>{{ $r->id }}</td>
                        <td>{{ $r->name }}</td>
                        <td>{{ $r->phone }}</td>
                        <td>{{ $r->message }}</td>
                        <td>{{ $r->status }}</td>
                        <td>{{ $r->internal_note }}</td>
                        <td>{{ $r->created_at }}</td>
                        <td>
                            <form method="post" action="{{ route('support.requests.status', ['customerRequest' => $r->id]) }}">
                                @csrf
                                <input type="text" name="internal_note" value="{{ $r->internal_note }}" placeholder="Kisa not">
                                <div class="actions">
                                    <button class="btn" type="submit" name="status" value="contacted">Iletisime Gecildi</button>
                                    <button class="btn" type="submit" name="status" value="closed">Kapat</button>
                                </div>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8">Kayit yok.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-10">{{ $rows->links() }}</div>
    </div>
@endsection
