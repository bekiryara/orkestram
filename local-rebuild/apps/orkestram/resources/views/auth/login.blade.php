@extends('auth.layout')

@section('title', 'Giris Yap')

@section('content')
    <div class="card auth-card shadow-sm">
        <h2>Hesabina Giris Yap</h2>
        <p class="muted">Kullanici adin veya e-postan ile giris yapabilirsin.</p>
        @if($errors->any())
            <div class="alert alert-danger py-2 px-3">{{ $errors->first() }}</div>
        @endif
        <form method="post" action="{{ route('auth.login.attempt') }}">
            @csrf
            @if(!empty($next))
                <input type="hidden" name="next" value="{{ $next }}">
            @endif
            <label class="form-label" for="username">E-posta veya Kullanici Adi</label>
            <input class="form-control" id="username" type="text" name="username" value="{{ old('username') }}" required autocomplete="username">

            <label class="form-label mt-2" for="password">Sifre</label>
            <input class="form-control" id="password" type="password" name="password" required autocomplete="current-password">

            <div class="actions">
                <button class="btn btn-primary" type="submit">Giris Yap</button>
                <a class="btn btn-outline-secondary" href="{{ route('auth.register', !empty($next) ? ['next' => $next] : []) }}">Hesap Olustur</a>
            </div>
        </form>
    </div>
@endsection
