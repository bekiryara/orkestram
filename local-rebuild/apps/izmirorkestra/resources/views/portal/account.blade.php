@extends('portal.layout')

@section('content')
    @php($defaultTab = 'overview')
    @php($tab = request('tab', $defaultTab))
    @php($allowedTabs = ['overview', 'requests', 'messages', 'comments', 'profile', 'security'])
    @php($tab = in_array($tab, $allowedTabs, true) ? $tab : $defaultTab)
    @php($profileHasErrors = $errors->has('profile') || $errors->has('name') || $errors->has('username') || $errors->has('email'))
    @php($profileMode = request('mode', $profileHasErrors ? 'edit' : 'view'))
    @php($profileMode = in_array($profileMode, ['view', 'edit'], true) ? $profileMode : 'view')
    @php($isEditingProfile = $tab === 'profile' && $profileMode === 'edit')
    @php($roleLabels = ['listing_owner' => 'Ilan Veren', 'customer' => 'Musteri', 'support_agent' => 'Destek', 'admin' => 'Admin', 'super_admin' => 'Super Admin'])
    @php($activeRoleLabel = $roleLabels[$activeRole] ?? $activeRole)

    <div class="card shadow-sm">
        <div class="account-header">
            <div>
                <h2>Hesabim</h2>
                <p class="muted">Hesabini ve kisisel bilgilerini buradan yonetebilirsin.</p>
                <p class="muted">Aktif Rol: {{ $activeRoleLabel }}</p>
            </div>
            @if($activeRole === 'listing_owner')
                <a class="btn btn-primary" href="{{ route('owner.dashboard') }}">Ilan Yonetimine Gec</a>
            @endif
        </div>

        <div class="tabs-mobile">
            <div class="tabs-mobile-wrap">
                <a class="account-tab {{ $tab === 'overview' ? 'active' : '' }}" href="{{ route('auth.account', ['tab' => 'overview']) }}">Genel Bakis</a>
                <a class="account-tab {{ $tab === 'requests' ? 'active' : '' }}" href="{{ route('auth.account', ['tab' => 'requests']) }}">Taleplerim</a>
                <a class="account-tab {{ $tab === 'messages' ? 'active' : '' }}" href="{{ route('auth.account', ['tab' => 'messages']) }}">Mesajlarim</a>
                <a class="account-tab {{ $tab === 'comments' ? 'active' : '' }}" href="{{ route('auth.account', ['tab' => 'comments']) }}">Yorumlarim</a>
                <a class="account-tab {{ $tab === 'profile' ? 'active' : '' }}" href="{{ route('auth.account', ['tab' => 'profile']) }}">Profilim</a>
                <a class="account-tab {{ $tab === 'security' ? 'active' : '' }}" href="{{ route('auth.account', ['tab' => 'security']) }}">Guvenlik</a>
            </div>
        </div>
    </div>

    <div class="account-shell">
        <aside class="card shadow-sm account-nav">
            <a class="account-tab {{ $tab === 'overview' ? 'active' : '' }}" href="{{ route('auth.account', ['tab' => 'overview']) }}">Genel Bakis</a>
            <a class="account-tab {{ $tab === 'requests' ? 'active' : '' }}" href="{{ route('auth.account', ['tab' => 'requests']) }}">Taleplerim</a>
            <a class="account-tab {{ $tab === 'messages' ? 'active' : '' }}" href="{{ route('auth.account', ['tab' => 'messages']) }}">Mesajlarim</a>
            <a class="account-tab {{ $tab === 'comments' ? 'active' : '' }}" href="{{ route('auth.account', ['tab' => 'comments']) }}">Yorumlarim</a>
            <a class="account-tab {{ $tab === 'profile' ? 'active' : '' }}" href="{{ route('auth.account', ['tab' => 'profile']) }}">Profilim</a>
            <a class="account-tab {{ $tab === 'security' ? 'active' : '' }}" href="{{ route('auth.account', ['tab' => 'security']) }}">Guvenlik</a>
        </aside>

        <section class="card shadow-sm">
            @if($tab === 'overview')
                <h2>Genel Bakis</h2>
                <div class="metric-grid account-mt-10">
                    @foreach(($summaryCards ?? []) as $card)
                        <div class="metric">
                            <span class="k">{{ $card['label'] }}</span>
                            <span class="v">{{ $card['value'] }}</span>
                        </div>
                    @endforeach
                </div>
            @endif

            @if($tab === 'requests')
                <h2>Taleplerim</h2>
                <div class="table-wrap table-responsive account-mt-10">
                    <table class="table table-striped align-middle mb-0">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Kisi</th>
                            <th>Iletisim</th>
                            <th>Durum</th>
                            <th>Tarih</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse(($requestRows ?? []) as $row)
                            @php($statusMap = ['new' => 'Yeni', 'contacted' => 'Iletisime Gecildi', 'closed' => 'Kapatildi'])
                            <tr>
                                <td>#{{ $row->id }}</td>
                                <td>{{ $row->name ?: '-' }}</td>
                                <td>{{ $row->phone ?: '-' }}<br>{{ $row->email ?: '-' }}</td>
                                <td><span class="status-pill">{{ $statusMap[$row->status] ?? $row->status }}</span></td>
                                <td>{{ $row->created_at }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5">Kayit bulunmuyor.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                @if($requestRows)
                    <div class="account-mt-10">{{ $requestRows->links() }}</div>
                @endif
            @endif

            @if($tab === 'messages')
                <div class="account-section-top">
                    <h2 class="account-heading-compact">Mesajlarim</h2>
                </div>
                <div class="account-mt-10 message-center-root">
                    @if(!empty($messageCenter) && is_array($messageCenter))
                        @php(extract($messageCenter))
                        @php($centerRouteName = 'auth.account')
                        @php($centerRouteBase = ['tab' => 'messages'])
                        @include('portal.messages.center-content')
                    @else
                        <div class="message-list-empty">Mesaj kutusu hazirlanamadi.</div>
                    @endif
                </div>
            @endif

            @if($tab === 'comments')
                <h2>Yorumlarim</h2>
                <div class="table-wrap table-responsive account-mt-10">
                    <table class="table table-striped align-middle mb-0">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Ilan</th>
                            <th>Durum</th>
                            <th>Icerik</th>
                            <th>Yanit</th>
                            <th>Tarih</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse(($commentRows ?? []) as $row)
                            <tr>
                                <td>#{{ $row->id }}</td>
                                <td>{{ $row->listing?->name ?? '-' }}</td>
                                <td><span class="status-pill">{{ $row->status }}</span></td>
                                <td>{{ $row->content }}</td>
                                <td>{{ $row->owner_reply ?: '-' }}</td>
                                <td>{{ $row->created_at }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6">Kayit bulunmuyor.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                @if($commentRows)
                    <div class="account-mt-10">{{ $commentRows->links() }}</div>
                @endif
            @endif

            @if($tab === 'profile')
                <h2>Profilim</h2>
                @if(session('ok_profile'))
                    <div class="alert alert-success py-2 px-3">{{ session('ok_profile') }}</div>
                @endif
                @if($profileHasErrors)
                    <div class="alert alert-danger py-2 px-3">{{ $errors->first('profile') ?: $errors->first() }}</div>
                @endif
                @if(!$isEditingProfile)
                    <div class="profile-summary-grid">
                        <div class="profile-item"><span class="k">Ad Soyad</span><span class="v">{{ $user?->name ?: '-' }}</span></div>
                        <div class="profile-item"><span class="k">Kullanici Adi</span><span class="v">{{ (string) ($identity['user'] ?? '-') }}</span></div>
                        <div class="profile-item"><span class="k">E-posta</span><span class="v">{{ $user?->email ?: '-' }}</span></div>
                        <div class="profile-item"><span class="k">Telefon</span><span class="v">{{ $user?->phone ?: '-' }}</span></div>
                        <div class="profile-item"><span class="k">Sehir</span><span class="v">{{ $user?->city ?: '-' }}</span></div>
                        <div class="profile-item"><span class="k">Ilce</span><span class="v">{{ $user?->district ?: '-' }}</span></div>
                        @if($activeRole === 'listing_owner')
                            <div class="profile-item"><span class="k">Firma / Sahne Adi</span><span class="v">{{ $user?->company_name ?: '-' }}</span></div>
                            <div class="profile-item"><span class="k">Hizmet Bolgesi</span><span class="v">{{ $user?->service_area ?: '-' }}</span></div>
                            <div class="profile-item"><span class="k">Kisa Tanitim</span><span class="v">{{ $user?->short_bio ?: '-' }}</span></div>
                            <div class="profile-item"><span class="k">Verilen Hizmetler</span><span class="v">{{ $user?->provided_services ?: '-' }}</span></div>
                            <div class="profile-item"><span class="k">Web Sitesi</span><span class="v">{{ $user?->website_url ?: '-' }}</span></div>
                        @endif
                    </div>
                    <div class="actions account-mt-12">
                        <a class="btn btn-primary" href="{{ route('auth.account', ['tab' => 'profile', 'mode' => 'edit']) }}">Profili Duzenle</a>
                    </div>
                @else
                    <form method="post" action="{{ route('auth.account.profile', ['tab' => 'profile', 'mode' => 'edit']) }}" enctype="multipart/form-data">
                        @csrf
                        <label class="form-label">Ad Soyad</label>
                        <input class="form-control" name="name" value="{{ old('name', $user?->name ?? '') }}" required>

                        <label class="form-label mt-2">Kullanici Adi</label>
                        <input class="form-control" name="username" value="{{ old('username', (string) ($identity['user'] ?? '')) }}" required>

                        <label class="form-label mt-2">E-posta</label>
                        <input class="form-control" type="email" name="email" value="{{ old('email', $user?->email ?? '') }}" required>

                        <label class="form-label mt-2">Telefon</label>
                        <input class="form-control" name="phone" value="{{ old('phone', $user?->phone ?? '') }}">

                        <label class="form-label mt-2">Sehir</label>
                        <input class="form-control" name="city" value="{{ old('city', $user?->city ?? '') }}">

                        <label class="form-label mt-2">Ilce</label>
                        <input class="form-control" name="district" value="{{ old('district', $user?->district ?? '') }}">

                        <label class="form-label mt-2">Profil Fotografi (Opsiyonel)</label>
                        <input class="form-control" type="file" name="profile_photo" accept="image/*">

                        @if($activeRole === 'listing_owner')
                            <label class="form-label mt-2">Firma / Sahne Adi</label>
                            <input class="form-control" name="company_name" value="{{ old('company_name', $user?->company_name ?? '') }}">

                            <label class="form-label mt-2">Hizmet Bolgesi</label>
                            <input class="form-control" name="service_area" value="{{ old('service_area', $user?->service_area ?? '') }}">

                            <label class="form-label mt-2">Kisa Tanitim</label>
                            <textarea class="form-control" name="short_bio" rows="3">{{ old('short_bio', $user?->short_bio ?? '') }}</textarea>

                            <label class="form-label mt-2">Verilen Hizmetler</label>
                            <textarea class="form-control" name="provided_services" rows="3">{{ old('provided_services', $user?->provided_services ?? '') }}</textarea>

                            <label class="form-label mt-2">Sosyal Medya / Website (Opsiyonel)</label>
                            <input class="form-control" name="website_url" value="{{ old('website_url', $user?->website_url ?? '') }}" placeholder="https://...">
                            <textarea class="form-control mt-2" name="social_links" rows="2">{{ old('social_links', $user?->social_links ?? '') }}</textarea>
                        @endif

                        <div class="actions">
                            <button class="btn btn-primary" type="submit">Profili Kaydet</button>
                            <a class="btn btn-outline-secondary" href="{{ route('auth.account', ['tab' => 'profile']) }}">Iptal</a>
                        </div>
                    </form>
                @endif
            @endif

            @if($tab === 'security')
                <h2>Guvenlik</h2>
                @if(session('ok_password'))
                    <div class="alert alert-success py-2 px-3">{{ session('ok_password') }}</div>
                @endif
                @if($errors->has('password'))
                    <div class="alert alert-danger py-2 px-3">{{ $errors->first('password') }}</div>
                @endif
                <form method="post" action="{{ route('auth.account.password', ['tab' => 'security']) }}">
                    @csrf
                    <label class="form-label">Mevcut Sifre</label>
                    <input class="form-control" type="password" name="current_password" required>

                    <label class="form-label mt-2">Yeni Sifre</label>
                    <input class="form-control" type="password" name="new_password" required>

                    <label class="form-label mt-2">Yeni Sifre (Tekrar)</label>
                    <input class="form-control" type="password" name="new_password_confirmation" required>

                    <div class="actions">
                        <button class="btn btn-primary" type="submit">Sifreyi Guncelle</button>
                    </div>
                </form>
            @endif
        </section>
    </div>
@endsection
