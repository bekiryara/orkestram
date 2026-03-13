<header class="topbar">
    @php($headerLogo = $siteHeaderLogo ?? ($siteMeta['logo_url'] ?? ($shellSiteMeta['logo_url'] ?? null)))
    @php($headerName = $siteHeaderName ?? 'Orkestram')
    <div class="logo">
        <a href="/">
            @if(!empty($headerLogo))
                <img src="{{ $headerLogo }}" alt="{{ $headerName }}" class="site-logo-image">
            @else
                {{ $headerName }}
            @endif
        </a>
    </div>
    <nav class="menu">
        @foreach(($shellNav ?? []) as $item)
            <a href="{{ $item['href'] }}">{{ $item['label'] }}</a>
        @endforeach
        @if($shellAuthenticated ?? false)
            <form method="post" action="/cikis" class="menu-logout-form">
                @csrf
                <button type="submit" class="menu-logout">Cikis</button>
            </form>
        @endif
    </nav>
</header>
