@extends('portal.layout')

@section('content')
    <div class="card shadow-sm">
        <div class="account-header">
            <div>
                <h2>Sahiplik ve Yetki Ayarlari</h2>
                <p class="muted">Hesap sahipligi ve panel erisim yetkileri bu alanda goruntulenir.</p>
            </div>
        </div>
    </div>

    <div class="account-shell">
        @include('portal.owner.partials.menu', ['ownerTab' => 'settings'])

        <section class="card shadow-sm">
            <h3 class="h5 mb-3">Hesap Bilgisi</h3>
            <div class="profile-summary-grid">
                <div class="profile-item"><span class="k">Kullanici</span><span class="v">{{ $ownerUser?->username ?: '-' }}</span></div>
                <div class="profile-item"><span class="k">E-posta</span><span class="v">{{ $ownerUser?->email ?: '-' }}</span></div>
                <div class="profile-item"><span class="k">Sahip Oldugu Ilan Sayisi</span><span class="v">{{ $ownedListingCount }}</span></div>
            </div>

            <h3 class="h5 mt-4 mb-2">Yetki Notu</h3>
            <p class="muted mb-0">
                Bu paneldeki yetkiler role/middleware kurallarina gore uygulanir.
                Detayli yetki tanimi bir sonraki fazda genisletilecektir.
            </p>
        </section>
    </div>
@endsection
