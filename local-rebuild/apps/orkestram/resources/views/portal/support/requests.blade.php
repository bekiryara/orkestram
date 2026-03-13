@extends('portal.layout')

@section('content')
    @php($statusClass = ['new' => 'text-bg-secondary', 'contacted' => 'text-bg-primary', 'closed' => 'text-bg-success'])
    <div class="card">
        <div class="page-head">
            <h2>Support Talepler</h2>
            <a class="btn btn-outline-secondary" href="{{ route('support.dashboard') }}">Support Paneli</a>
        </div>
        @if(session('ok'))
            <div class="alert alert-success py-2 px-3">{{ session('ok') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger py-2 px-3">{{ $errors->first() }}</div>
        @endif

        <form method="get" action="{{ route('support.requests.index') }}" class="row g-2 mb-3">
            <div class="col-12 col-md-4">
                <label class="form-label mb-1">Durum</label>
                <select class="form-select" name="status">
                    <option value="">Tum Durumlar</option>
                    @foreach(['new', 'contacted', 'closed'] as $s)
                        <option value="{{ $s }}" @selected(($status ?? '') === $s)>{{ $s }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-8 d-flex gap-2 align-items-end">
                <button class="btn btn-primary" type="submit">Filtrele</button>
                <a class="btn btn-outline-secondary" href="{{ route('support.requests.index') }}">Temizle</a>
            </div>
        </form>

        <div class="table-wrap table-responsive">
            <table class="table table-striped align-middle mb-0">
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
                        <td>#{{ $r->id }}</td>
                        <td>{{ $r->name ?: '-' }}</td>
                        <td>{{ $r->phone ?: '-' }}</td>
                        <td>{{ $r->message ?: '-' }}</td>
                        <td><span class="badge {{ $statusClass[$r->status] ?? 'text-bg-secondary' }}">{{ $r->status }}</span></td>
                        <td>{{ $r->internal_note ?: '-' }}</td>
                        <td>{{ $r->created_at }}</td>
                        <td>
                            <form method="post" action="{{ route('support.requests.status', ['customerRequest' => $r->id]) }}" class="row g-2 align-items-center">
                                @csrf
                                <div class="col-12 col-md-4">
                                    <select class="form-select form-select-sm" name="status" required>
                                        <option value="new" @selected($r->status === 'new')>Yeni</option>
                                        <option value="contacted" @selected($r->status === 'contacted')>Iletisime Gecildi</option>
                                        <option value="closed" @selected($r->status === 'closed')>Kapatildi</option>
                                    </select>
                                </div>
                                <div class="col-12 col-md-5">
                                    <input class="form-control form-control-sm" name="internal_note" value="{{ $r->internal_note }}" placeholder="Kisa not">
                                </div>
                                <div class="col-12 col-md-3 d-grid">
                                    <button class="btn btn-primary btn-sm" type="submit">Kaydet</button>
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
