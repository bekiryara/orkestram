@extends('auth.layout')

@section('title', 'Hesap Olustur')

@section('content')
    <div class="card auth-card shadow-sm">
        <h2>Hesap Olustur</h2>
        <p class="muted">Bilgilerini girerek yeni hesap olusturabilirsin.</p>
        @if($errors->any())
            <div class="alert alert-danger py-2 px-3">{{ $errors->first() }}</div>
        @endif
        <form method="post" action="{{ route('auth.register.submit') }}">
            @csrf
            @if(!empty($next))
                <input type="hidden" name="next" value="{{ $next }}">
            @endif
            <label class="form-label" for="name">Ad Soyad</label>
            <input class="form-control" id="name" type="text" name="name" value="{{ old('name') }}" required autocomplete="name">

            <label class="form-label mt-2" for="username">Kullanici Adi</label>
            <input class="form-control" id="username" type="text" name="username" value="{{ old('username') }}" required autocomplete="username">

            <label class="form-label mt-2" for="email">E-posta</label>
            <input class="form-control" id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email">

            <label class="form-label mt-2" for="password">Sifre</label>
            <input class="form-control" id="password" type="password" name="password" required autocomplete="new-password">

            <label class="form-label mt-2" for="password_confirmation">Sifre (Tekrar)</label>
            <input class="form-control" id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password">

            <div class="actions">
                <button class="btn btn-primary" type="submit">Kayit Ol</button>
                <a class="btn btn-outline-secondary" href="{{ route('auth.login', !empty($next) ? ['next' => $next] : []) }}">Giris Yap</a>
            </div>
        </form>
    </div>
@endsection
