<!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Hesap')</title>
    @include('partials.bootstrap-head')
    <link rel="stylesheet" href="/assets/v1.css">
</head>
<body class="auth-page portal-shell site-{{ $shellSiteMeta['theme'] ?? 'orkestram' }}">
<div class="wrap">
    @include('partials.site-header', ['siteHeaderName' => ($shellSiteMeta['name'] ?? 'Orkestram')])
</div>
<main class="auth-main">
    @yield('content')
</main>
@include('partials.bootstrap-foot')
</body>
</html>
