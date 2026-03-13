@extends('portal.layout')

@section('content')
    <div class="card">
        <h1>{{ __('portal.customer.new_request_title') }}</h1>
        <p class="muted">Etkinligin icin ihtiyacini yaz, uygun ekipler seninle iletisime gecsin.</p>
        @if($listing)
            <p class="muted mt-8">Secili ilan: <strong>{{ $listing->name }}</strong></p>
        @endif
        <form method="post" action="/customer/requests">
            @csrf
            <input type="hidden" name="listing_slug" value="{{ $listing?->slug ?? $listingSlug }}">
            <label class="form-label">Ad Soyad</label>
            <input class="form-control" name="name" value="{{ old('name') }}" required>
            <label class="form-label">Telefon</label>
            <input class="form-control" name="phone" value="{{ old('phone') }}" placeholder="05xx xxx xx xx">
            <label class="form-label">E-posta</label>
            <input class="form-control" name="email" type="email" value="{{ old('email') }}" placeholder="ornek@mail.com">
            <label class="form-label">Ihtiyacin</label>
            <textarea class="form-control" name="message" rows="4" placeholder="Etkinlik tarihi, saat araligi, konum ve beklentini yazabilirsin.">{{ old('message') }}</textarea>
            <div class="actions">
                <button class="btn btn-primary" type="submit">Talep Gonder</button>
                <a class="btn" href="/customer/requests">{{ __('portal.customer.requests_title') }}</a>
                <a class="btn btn-outline-secondary" href="{{ route('messages.index', ['box' => 'personal']) }}">{{ __('portal.customer.messages') }}</a>
            </div>
        </form>
    </div>
@endsection
