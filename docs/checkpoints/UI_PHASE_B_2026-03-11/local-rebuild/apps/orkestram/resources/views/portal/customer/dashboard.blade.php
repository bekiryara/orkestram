@extends('portal.layout')

@section('content')
    <div class="card">
        <h1>Yeni Talep Olustur</h1>
        <p class="muted">Etkinligin icin ihtiyacini yaz, uygun ekipler seninle iletisime gecsin.</p>
        @if($listing)
            <p class="muted mt-8">Secili ilan: <strong>{{ $listing->name }}</strong></p>
        @endif
        <form method="post" action="/customer/requests">
            @csrf
            <input type="hidden" name="listing_slug" value="{{ $listing?->slug ?? $listingSlug }}">
            <label>Ad Soyad</label>
            <input name="name" value="{{ old('name') }}" required>
            <label>Telefon</label>
            <input name="phone" value="{{ old('phone') }}" placeholder="05xx xxx xx xx">
            <label>E-posta</label>
            <input name="email" type="email" value="{{ old('email') }}" placeholder="ornek@mail.com">
            <label>Ihtiyacin</label>
            <textarea name="message" rows="4" placeholder="Etkinlik tarihi, saat araligi, konum ve beklentini yazabilirsin.">{{ old('message') }}</textarea>
            <div class="actions">
                <button class="btn btn-primary" type="submit">Talep Gonder</button>
                <a class="btn" href="/customer/requests">Taleplerim</a>
            </div>
        </form>
    </div>
@endsection
