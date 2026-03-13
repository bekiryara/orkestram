<!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ ($shellSiteMeta['name'] ?? 'Orkestram') . ' Admin' }}</title>
    @include('partials.bootstrap-head')
    <link rel="stylesheet" href="/assets/v1.css">
</head>
<body class="admin-shell site-{{ $shellSiteMeta['theme'] ?? 'orkestram' }}">
    <div class="wrap">
        @php($shellNav = [
            ['href' => route('admin.pages.index'), 'label' => 'Sayfalar'],
            ['href' => route('admin.categories.index'), 'label' => 'Kategoriler'],
            ['href' => route('admin.listings.index'), 'label' => 'Ilanlar'],
            ['href' => route('admin.city-pages.index'), 'label' => 'Sehir Sayfalari'],
            ['href' => route('admin.feedback.index'), 'label' => 'Geri Bildirimler'],
        ])
        @php($shellAuthenticated = true)
        @include('partials.site-header', ['siteHeaderName' => ($shellSiteMeta['name'] ?? 'Orkestram') . ' Admin'])

        <div class="admin-head card">
            <h1>{{ ($shellSiteMeta['name'] ?? 'Orkestram') }} Yonetim Paneli</h1>
            <p class="muted">Sayfa, kategori, ilan ve sehir sayfalarini tek panelden yonet.</p>
        </div>

        @if (session('ok'))
            <div class="ok">{{ session('ok') }}</div>
        @endif

        @yield('content')
    </div>
    @include('partials.bootstrap-foot')
</body>
</html>
