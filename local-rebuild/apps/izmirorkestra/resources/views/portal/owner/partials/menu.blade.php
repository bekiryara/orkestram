@php($ownerTab = $ownerTab ?? 'overview')

<aside class="card shadow-sm account-nav">
    <a class="account-tab {{ $ownerTab === 'overview' ? 'active' : '' }}" href="{{ route('owner.dashboard') }}">Genel Bakis</a>
    <a class="account-tab {{ $ownerTab === 'listings' ? 'active' : '' }}" href="{{ route('owner.listings.index') }}">Ilanlarim</a>
    <a class="account-tab {{ $ownerTab === 'leads' ? 'active' : '' }}" href="{{ route('owner.leads.index') }}">Isler / Talepler</a>
    <a class="account-tab {{ $ownerTab === 'messages' ? 'active' : '' }}" href="{{ route('owner.feedback.index', ['kind' => 'message']) }}">Ilan Mesajlari</a>
    <a class="account-tab {{ $ownerTab === 'comments' ? 'active' : '' }}" href="{{ route('owner.feedback.index', ['kind' => 'comment']) }}">Yorumlar</a>
    <a class="account-tab {{ $ownerTab === 'settings' ? 'active' : '' }}" href="{{ route('owner.settings') }}">Sahiplik ve Yetki Ayarlari</a>
</aside>
