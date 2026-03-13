@extends('portal.layout')

@section('content')
    <div class="card shadow-sm">
        <div class="account-header">
            <div>
                <h2>Ilan Yonetimi</h2>
                <p class="muted">Ilan operasyonlarini, mesajlarini ve yorumlarini tek merkezden yonet.</p>
            </div>
            <a class="btn btn-outline-secondary" href="{{ route('auth.account', ['tab' => 'overview']) }}">Hesap Paneline Don</a>
        </div>
    </div>

    <div class="account-shell">
        @include('portal.owner.partials.menu', ['ownerTab' => 'overview'])

        <section class="card shadow-sm">
            <h2>Genel Bakis</h2>
            <div class="metric-grid account-mt-10">
                <div class="metric">
                    <span class="k">Toplam Ilan</span>
                    <span class="v">{{ $listingsCount }}</span>
                </div>
                <div class="metric">
                    <span class="k">Yayindaki Ilan</span>
                    <span class="v">{{ $publishedListingsCount }}</span>
                </div>
                <div class="metric">
                    <span class="k">Taslak Ilan</span>
                    <span class="v">{{ $draftListingsCount }}</span>
                </div>
                <div class="metric">
                    <span class="k">Pasif Ilan</span>
                    <span class="v">{{ $pausedListingsCount }}</span>
                </div>
                <div class="metric">
                    <span class="k">Toplam Talep</span>
                    <span class="v">{{ $leadCount }}</span>
                </div>
                <div class="metric">
                    <span class="k">Yeni Talep</span>
                    <span class="v">{{ $newLeadCount }}</span>
                </div>
                <div class="metric">
                    <span class="k">Iletisime Gecilen Talep</span>
                    <span class="v">{{ $contactedLeadCount }}</span>
                </div>
                <div class="metric">
                    <span class="k">Kapatilan Talep</span>
                    <span class="v">{{ $closedLeadCount }}</span>
                </div>
                <div class="metric">
                    <span class="k">Toplam Geri Bildirim</span>
                    <span class="v">{{ $feedbackCount }}</span>
                </div>
                <div class="metric">
                    <span class="k">Mesajlar</span>
                    <span class="v">{{ $messageCount }}</span>
                </div>
                <div class="metric">
                    <span class="k">Yorumlar</span>
                    <span class="v">{{ $commentCount }}</span>
                </div>
                <div class="metric">
                    <span class="k">Begeniler</span>
                    <span class="v">{{ $likeCount }}</span>
                </div>
            </div>
        </section>
    </div>
@endsection
