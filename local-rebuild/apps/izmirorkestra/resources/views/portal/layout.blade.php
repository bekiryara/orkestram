<!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Portal')</title>
    @include('partials.bootstrap-head')
    @php($v1CssVersion = @filemtime(public_path('assets/v1.css')) ?: time())
    <link rel="stylesheet" href="{{ '/assets/v1.css?v=' . $v1CssVersion }}">
</head>
<body class="portal-shell site-{{ $shellSiteMeta['theme'] ?? 'orkestram' }}">
<div class="wrap">
    @php($identity = $shellIdentity ?? [])
    @php($roleLabels = trans('portal.roles'))
    @php($activeRole = (string) ($identity['role'] ?? ''))
    @php($allRoles = array_values(array_filter(array_map('strval', (array) ($identity['roles'] ?? [])))))
    @include('partials.site-header', ['siteHeaderName' => ($shellSiteMeta['name'] ?? 'Orkestram')])
    <div class="identity">
        <div class="identity-name">{{ (string) ($identity['user'] ?? 'Misafir') }}</div>
        <div class="badges">
            <span class="badge badge-primary">Aktif: {{ $roleLabels[$activeRole] ?? ($activeRole !== '' ? $activeRole : 'Misafir') }}</span>
            @foreach($allRoles as $r)
                @if($r !== $activeRole)
                    <span class="badge">{{ $roleLabels[$r] ?? $r }}</span>
                @endif
            @endforeach
        </div>
    </div>
    @yield('content')
</div>
@include('partials.bootstrap-foot')
</body>
</html>
