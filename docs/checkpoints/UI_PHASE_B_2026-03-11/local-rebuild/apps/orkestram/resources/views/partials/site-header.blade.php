<header class="topbar">
    <div class="logo">
        <a href="/">{{ $siteHeaderName ?? 'Orkestram' }}</a>
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
