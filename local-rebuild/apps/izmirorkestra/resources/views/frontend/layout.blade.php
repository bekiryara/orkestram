<!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $metaTitle ?? 'Orkestram' }}</title>
    <meta name="description" content="{{ $metaDescription ?? 'Canli muzik ve organizasyon hizmetleri' }}">
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $metaTitle ?? 'Orkestram' }}">
    <meta property="og:description" content="{{ $metaDescription ?? 'Canli muzik ve organizasyon hizmetleri' }}">
    <meta property="og:url" content="{{ $canonicalUrl ?? request()->fullUrl() }}">
    <meta name="robots" content="{{ $metaRobots ?? 'index, follow' }}">
    <link rel="canonical" href="{{ $canonicalUrl ?? request()->fullUrl() }}">
    @include('partials.bootstrap-head')
    <link rel="stylesheet" href="/assets/v1.css">
</head>
<body class="site-{{ $siteMeta['theme'] ?? 'orkestram' }}">
<div class="wrap">
    @include('frontend.partials.site-shell-header')

    @yield('content')

    @include('frontend.partials.site-shell-footer')
</div>
@include('partials.bootstrap-foot')
</body>
</html>
