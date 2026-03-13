@php($ownerTab = $ownerTab ?? 'overview')
@php($ownerTabs = [
    ['key' => 'overview', 'href' => route('owner.dashboard'), 'label' => __('portal.owner.menu.overview')],
    ['key' => 'listings', 'href' => route('owner.listings.index'), 'label' => __('portal.owner.menu.listings')],
    ['key' => 'leads', 'href' => route('owner.leads.index'), 'label' => __('portal.owner.menu.leads')],
    ['key' => 'messages', 'href' => route('owner.feedback.index', ['kind' => 'message']), 'label' => __('portal.owner.menu.messages')],
    ['key' => 'comments', 'href' => route('owner.feedback.index', ['kind' => 'comment']), 'label' => __('portal.owner.menu.comments')],
    ['key' => 'settings', 'href' => route('owner.settings'), 'label' => __('portal.owner.menu.settings')],
])

<div class="tabs-mobile">
    <div class="tabs-mobile-wrap">
        @foreach($ownerTabs as $tabItem)
            <a class="account-tab {{ $ownerTab === $tabItem['key'] ? 'active' : '' }}" href="{{ $tabItem['href'] }}">{{ $tabItem['label'] }}</a>
        @endforeach
    </div>
</div>

<aside class="card shadow-sm account-nav">
    @foreach($ownerTabs as $tabItem)
        <a class="account-tab {{ $ownerTab === $tabItem['key'] ? 'active' : '' }}" href="{{ $tabItem['href'] }}">{{ $tabItem['label'] }}</a>
    @endforeach
</aside>
