@extends('portal.layout')

@section('content')
    @php($statusClass = ['pending' => 'text-bg-secondary', 'approved' => 'text-bg-success', 'rejected' => 'text-bg-danger', 'answered' => 'text-bg-primary'])
    @php($panelTitle = $panelTitle ?? 'Ilan Mesajlari')
    @php($ownerTab = $ownerTab ?? 'messages')

    <div class="card shadow-sm">
        <div class="account-header">
            <div>
                <h2>{{ $panelTitle }}</h2>
                <p class="muted">Ilan Yonetimi</p>
                <p class="muted">Ilanlarina gelen kayitlari yonet ve gecmisi takip et.</p>
            </div>
            <a class="btn btn-outline-secondary" href="{{ route('auth.account', ['tab' => 'overview']) }}">{{ __('portal.owner.back_to_account') }}</a>
        </div>
    </div>

    <div class="account-shell">
        @include('portal.owner.partials.menu', ['ownerTab' => $ownerTab])

        <section class="card shadow-sm">
            @if(($activeKind ?? 'message') === 'message')
                <div class="message-center-root">
                    @php($centerRouteName = 'owner.feedback.index')
                    @php($centerRouteBase = ['kind' => 'message'])
                    @php(extract($messageCenter ?? []))
                    @include('portal.messages.center-content')
                </div>
            @else
            @if(session('ok'))
                <div class="alert alert-success py-2 px-3">{{ session('ok') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger py-2 px-3">{{ $errors->first() }}</div>
            @endif
            <h3 class="h5 mb-2">Ilanlarima Gelenler</h3>
            <div class="table-wrap table-responsive">
                <table class="table table-striped align-middle mb-0">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Gonderen</th>
                        <th>Ilan</th>
                        <th>Durum</th>
                        <th>Icerik</th>
                        <th>Aksiyon</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($incomingRows as $row)
                        <tr>
                            <td>#{{ $row->id }}</td>
                            <td>{{ $row->user?->name ?? 'Musteri' }}</td>
                            <td>{{ $row->listing?->name ?? '-' }}</td>
                            <td><span class="badge {{ $statusClass[$row->status] ?? 'text-bg-secondary' }}">{{ $row->status }}</span></td>
                            <td>
                                <button
                                    class="btn btn-sm btn-outline-secondary"
                                    type="button"
                                    data-bs-toggle="modal"
                                    data-bs-target="#feedbackDetailModal{{ $row->id }}"
                                >Detay</button>
                            </td>
                            <td>
                                <form method="post" action="{{ route('owner.feedback.status', ['feedback' => $row->id]) }}" class="row g-2 align-items-center">
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
                                        <button class="btn btn-primary btn-sm" type="submit">Hizli Kaydet</button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">Henuz kayit yok.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-10">{{ $incomingRows->links() }}</div>

            @endif
        </section>
    </div>

    @if(($activeKind ?? 'message') !== 'message')
    @foreach($incomingRows as $row)
        <div class="modal fade" id="feedbackDetailModal{{ $row->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title h5 mb-0">Yorum Kaydi #{{ $row->id }}</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label mb-1">Ilan</label>
                                <div class="form-control-plaintext p-2 border rounded">{{ $row->listing?->name ?? '-' }}</div>
                            </div>
                            <div class="col-12 col-md-3">
                                <label class="form-label mb-1">Tur</label>
                                <div class="form-control-plaintext p-2 border rounded">{{ $row->kind }}</div>
                            </div>
                            <div class="col-12 col-md-3">
                                <label class="form-label mb-1">Durum</label>
                                <div class="form-control-plaintext p-2 border rounded">{{ $row->status }}</div>
                            </div>
                            <div class="col-12">
                                <label class="form-label mb-1">Mesaj</label>
                                <div class="p-2 border rounded">{{ $row->content }}</div>
                            </div>
                            <div class="col-12">
                                <label class="form-label mb-1">Mevcut Yanit</label>
                                <div class="p-2 border rounded">{{ $row->owner_reply ?: '-' }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Kapat</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
    @endif
@endsection
