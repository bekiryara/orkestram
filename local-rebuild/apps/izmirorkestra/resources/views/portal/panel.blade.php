@extends('portal.layout')

@section('content')
    @php($roleLabels = ['customer' => 'Musteri', 'listing_owner' => 'Ilan Veren', 'support_agent' => 'Destek', 'admin' => 'Admin', 'super_admin' => 'Super Admin', 'content_editor' => 'Icerik Editoru', 'listing_editor' => 'Ilan Editoru', 'viewer' => 'Goruntuleyici'])

    <div class="card">
        <h2>Panel</h2>
        <p class="muted">Hizli islemler ve ozet bilgilerin bu alanda.</p>
    </div>

    <div class="card mt-12">
        <h2>Genel Durum</h2>
        <div class="metric-grid">
            @foreach(($summaryCards ?? []) as $card)
                <div class="metric">
                    <span class="k">{{ $card['label'] }}</span>
                    <span class="v">{{ $card['value'] }}</span>
                </div>
            @endforeach
        </div>
    </div>

    <div class="card mt-12">
        <h2>{{ $activeRole === 'listing_owner' ? 'Firma Islemleri' : 'Musteri Islemleri' }}</h2>
        <div class="metric-grid">
            @foreach(($quickActions ?? []) as $action)
                <div class="metric">
                    <span class="k">{{ $action['title'] }}</span>
                    <span class="muted muted-block">{{ $action['description'] }}</span>
                    <a class="btn btn-primary" href="{{ $action['href'] }}">{{ $action['button'] }}</a>
                </div>
            @endforeach
        </div>
    </div>

    @if($activeRole === 'listing_owner' && count($customerQuickActions ?? []) > 0)
        <div class="card mt-12">
            <h2>Musteri Islemleri</h2>
            <p class="muted">Ayni hesapla kendi gonderdigin talepleri de takip edebilirsin.</p>
            <div class="metric-grid">
                @foreach(($customerQuickActions ?? []) as $action)
                    <div class="metric">
                        <span class="k">{{ $action['title'] }}</span>
                        <span class="muted muted-block">{{ $action['description'] }}</span>
                        <a class="btn" href="{{ $action['href'] }}">{{ $action['button'] }}</a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
@endsection
