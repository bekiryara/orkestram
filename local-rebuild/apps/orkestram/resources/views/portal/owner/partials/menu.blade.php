@php($ownerTab = $ownerTab ?? 'overview')

<aside class="card shadow-sm account-nav">
    <a class="account-tab {{ $ownerTab === 'overview' ? 'active' : '' }}" href="{{ route('owner.dashboard') }}">{{ __('portal.owner.menu.overview') }}</a>
    <a class="account-tab {{ $ownerTab === 'listings' ? 'active' : '' }}" href="{{ route('owner.listings.index') }}">{{ __('portal.owner.menu.listings') }}</a>
    <a class="account-tab {{ $ownerTab === 'leads' ? 'active' : '' }}" href="{{ route('owner.leads.index') }}">{{ __('portal.owner.menu.leads') }}</a>
    <a class="account-tab {{ $ownerTab === 'messages' ? 'active' : '' }}" href="{{ route('owner.feedback.index', ['kind' => 'message']) }}">{{ __('portal.owner.menu.messages') }}</a>
    <a class="account-tab {{ $ownerTab === 'comments' ? 'active' : '' }}" href="{{ route('owner.feedback.index', ['kind' => 'comment']) }}">{{ __('portal.owner.menu.comments') }}</a>
    <a class="account-tab {{ $ownerTab === 'settings' ? 'active' : '' }}" href="{{ route('owner.settings') }}">{{ __('portal.owner.menu.settings') }}</a>
</aside>
