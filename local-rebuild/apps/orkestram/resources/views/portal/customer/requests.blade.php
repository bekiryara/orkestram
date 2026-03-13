@extends('portal.layout')

@section('content')
    <div class="card">
        <div class="page-head">
            <h2>Taleplerim</h2>
            <a class="btn btn-primary" href="/customer">Yeni Talep</a>
        </div>
        @if(session('ok'))
            <p class="muted mt-8 status-ok">{{ session('ok') }}</p>
        @endif
        <div class="table-wrap">
            <table>
                <thead>
                <tr>
                    <th>Talep</th>
                    <th>Iletisim</th>
                    <th>Mesaj</th>
                    <th>Durum</th>
                    <th>Tarih</th>
                </tr>
                </thead>
                <tbody>
                @forelse($rows as $r)
                    <tr>
                        <td>#{{ $r->id }} - {{ $r->name }}</td>
                        <td>{{ $r->phone ?: '-' }}<br>{{ $r->email ?: '-' }}</td>
                        <td>{{ $r->message ?: '-' }}</td>
                        <td>
                            @php($statusMap = ['new' => 'Yeni', 'contacted' => 'Iletisime Gecildi', 'closed' => 'Kapatildi'])
                            {{ $statusMap[$r->status] ?? $r->status }}
                        </td>
                        <td>{{ $r->created_at }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5">Henuz talep olusturmadin.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-10">{{ $rows->links() }}</div>
    </div>
@endsection
