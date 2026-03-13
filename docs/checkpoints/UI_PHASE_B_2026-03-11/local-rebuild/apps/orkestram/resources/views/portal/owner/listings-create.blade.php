@extends('portal.layout')

@section('content')
    <div class="card">
        <h2>Yeni Owner Ilani</h2>
        <p class="muted">Ilan taslak olarak olusur. Sonrasinda icerik ve medya guncellenebilir.</p>

        @if($errors->any())
            <p class="muted mb-10 status-error">Form hatasi var. Alanlari kontrol edin.</p>
        @endif

        <form method="post" action="{{ route('owner.listings.store') }}">
            @csrf
            <label>Baslik</label>
            <input type="text" name="name" value="{{ old('name') }}" required>

            <label>Slug (opsiyonel)</label>
            <input type="text" name="slug" value="{{ old('slug') }}" placeholder="orn: izmir-dugun-orkestrasi">

            <label>Sehir</label>
            <input type="text" name="city" value="{{ old('city') }}">

            <label>Ilce</label>
            <input type="text" name="district" value="{{ old('district') }}">

            <label>Servis Tipi</label>
            <input type="text" name="service_type" value="{{ old('service_type') }}">

            <label>Fiyat Etiketi</label>
            <input type="text" name="price_label" value="{{ old('price_label') }}">

            <label>Telefon</label>
            <input type="text" name="phone" value="{{ old('phone') }}">

            <label>WhatsApp</label>
            <input type="text" name="whatsapp" value="{{ old('whatsapp') }}">

            <label>Ozet</label>
            <textarea name="summary" rows="3">{{ old('summary') }}</textarea>

            <label>Icerik</label>
            <textarea name="content" rows="6">{{ old('content') }}</textarea>

            <div class="actions">
                <button class="btn btn-primary" type="submit">Taslak Olustur</button>
                <a class="btn" href="{{ route('owner.listings.index') }}">Geri</a>
            </div>
        </form>
    </div>
@endsection
