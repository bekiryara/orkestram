@extends('admin.layout')

@section('content')
    @php($statusClass = ['pending' => 'text-bg-secondary', 'approved' => 'text-bg-success', 'rejected' => 'text-bg-danger', 'answered' => 'text-bg-primary'])
    <div class="card">
        <h2>Geri Bildirimler</h2>
        <p class="muted">Yorum moderasyonunu yonet, begeni kayitlarini takip et.</p>

        <form method="get" class="row g-2 mb-3">
            <div class="col-12 col-md-4">
                <label class="form-label mb-1">Site</label>
                <select class="form-select" name="site">
                    <option value="orkestram.net" @selected($site === 'orkestram.net')>orkestram.net</option>
                    <option value="izmirorkestra.net" @selected($site === 'izmirorkestra.net')>izmirorkestra.net</option>
                </select>
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label mb-1">Yorum Durumu</label>
                <select class="form-select" name="status">
                    <option value="">Tum Durumlar</option>
                    @foreach(['pending', 'approved', 'rejected', 'answered'] as $s)
                        <option value="{{ $s }}" @selected($status === $s)>{{ $s }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-4 d-flex gap-2 align-items-end">
                <button class="btn btn-primary" type="submit">Filtrele</button>
                <a class="btn btn-outline-secondary" href="{{ route('admin.feedback.index') }}">Temizle</a>
            </div>
        </form>

        <h3 class="h5 mb-2">Yorumlar</h3>
        <div class="table-wrap table-responsive">
            <table class="table table-striped align-middle mb-0">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Gonderen</th>
                    <th>Ilan</th>
                    <th>Durum</th>
                    <th>Icerik</th>
                    <th>Yanit</th>
                    <th>Aksiyon</th>
                </tr>
                </thead>
                <tbody>
                @forelse($commentRows as $row)
                    <tr>
                        <td>#{{ $row->id }}</td>
                        <td>{{ $row->user?->name ?? '-' }}</td>
                        <td>{{ $row->listing?->name ?? '-' }}</td>
                        <td><span class="badge {{ $statusClass[$row->status] ?? 'text-bg-secondary' }}">{{ $row->status }}</span></td>
                        <td>{{ $row->content }}</td>
                        <td>{{ $row->owner_reply ?: '-' }}</td>
                        <td>
                            <form method="post" action="{{ route('admin.feedback.comment.status', ['feedback' => $row->id]) }}" class="row g-2 align-items-center">
                                @csrf
                                <div class="col-12 col-md-4">
                                    <select class="form-select form-select-sm" name="status" required>
                                        <option value="pending" @selected($row->status === 'pending')>Beklemede</option>
                                        <option value="approved" @selected($row->status === 'approved')>Onayli</option>
                                        <option value="rejected" @selected($row->status === 'rejected')>Reddedildi</option>
                                        <option value="answered" @selected($row->status === 'answered')>Yanitlandi</option>
                                    </select>
                                </div>
                                <div class="col-12 col-md-5">
                                    <input class="form-control form-control-sm" name="owner_reply" value="{{ $row->owner_reply }}" placeholder="Kisa yanit">
                                </div>
                                <div class="col-12 col-md-3 d-grid">
                                    <button class="btn btn-sm" type="submit">Kaydet</button>
                                </div>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7">Yorum kaydi yok.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-10">{{ $commentRows->links() }}</div>

        <h3 class="h5 mt-4 mb-2">Begeniler</h3>
        <div class="table-wrap table-responsive">
            <table class="table table-striped align-middle mb-0">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Kullanici</th>
                    <th>Ilan</th>
                    <th>Actor Key</th>
                    <th>Tarih</th>
                </tr>
                </thead>
                <tbody>
                @forelse($likeRows as $row)
                    <tr>
                        <td>#{{ $row->id }}</td>
                        <td>{{ $row->user?->name ?? '-' }}</td>
                        <td>{{ $row->listing?->name ?? '-' }}</td>
                        <td>{{ $row->actor_key }}</td>
                        <td>{{ $row->created_at }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5">Begeni kaydi yok.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-10">{{ $likeRows->links() }}</div>
    </div>
@endsection
