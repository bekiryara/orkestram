@extends('portal.layout')

@section('content')
    <div class="card shadow-sm">
        <div class="account-header">
            <div>
                <h2>{{ __('portal.owner.menu.leads') }}</h2>
                <p class="muted">Ilanlarina gelen talepleri takip et ve durumunu guncelle.</p>
            </div>
        </div>
    </div>

    <div class="account-shell">
        @include('portal.owner.partials.menu', ['ownerTab' => 'leads'])

        <section class="card shadow-sm">
            <div class="table-wrap table-responsive">
                <table class="table table-striped align-middle mb-0">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Ad</th>
                        <th>Telefon</th>
                        <th>Ilan / Fiyat</th>
                        <th>Mesaj</th>
                        <th>Durum</th>
                        <th>Not</th>
                        <th>Aksiyon</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($rows as $r)
                        <tr>
                            <td>{{ $r->id }}</td>
                            <td>{{ $r->name }}</td>
                            <td>{{ $r->phone }}</td>
                            <td>
                                <div>{{ $r->listing?->name ?? 'Genel talep' }}</div>
                                @if($r->hasPricingSnapshot())
                                    <div class="muted">{{ $r->snapshotPriceLabel() }}</div>
                                @else
                                    <div class="muted">-</div>
                                @endif
                            </td>
                            <td>{{ $r->message }}</td>
                            <td>{{ $r->status }}</td>
                            <td>{{ $r->internal_note }}</td>
                            <td>
                                <form method="post" action="{{ route('owner.leads.status', ['customerRequest' => $r->id]) }}">
                                    @csrf
                                    <input class="form-control form-control-sm mb-2" type="text" name="internal_note" value="{{ $r->internal_note }}" placeholder="Kisa not">
                                    <div class="actions">
                                        <button class="btn btn-outline-primary btn-sm" type="submit" name="status" value="contacted">Iletisime Gecildi</button>
                                        <button class="btn btn-outline-secondary btn-sm" type="submit" name="status" value="closed">Kapat</button>
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
        </section>
    </div>
@endsection
