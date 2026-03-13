@php($itemTitle = (string) ($itemTitle ?? '-'))
@php($itemSubtitle = (string) ($itemSubtitle ?? ''))
@php($itemDate = (string) ($itemDate ?? ''))
@php($itemStatusLabel = (string) ($itemStatusLabel ?? ''))
@php($itemStatusClass = (string) ($itemStatusClass ?? 'text-bg-secondary'))
@php($itemHref = (string) ($itemHref ?? ''))
@php($itemExcerpt = (string) ($itemExcerpt ?? ''))
@php($itemReply = (string) ($itemReply ?? ''))
@php($itemSelectable = (bool) ($itemSelectable ?? false))
@php($itemCheckboxValue = (string) ($itemCheckboxValue ?? ''))
@php($itemCheckboxClass = (string) ($itemCheckboxClass ?? ''))
@php($itemCheckboxAria = (string) ($itemCheckboxAria ?? 'Kaydi sec'))
@php($itemAvatarUrl = (string) ($itemAvatarUrl ?? ''))
@php($itemAvatarText = trim((string) ($itemAvatarText ?? $itemTitle)))
@php($itemAvatarInitial = strtoupper(substr($itemAvatarText !== '' ? $itemAvatarText : '?', 0, 1)))

<div class="message-list-row">
    @if($itemSelectable)
        <div class="message-list-row-check">
            <input class="form-check-input {{ $itemCheckboxClass }}" type="checkbox" value="{{ $itemCheckboxValue }}" aria-label="{{ $itemCheckboxAria }}">
        </div>
    @endif
    <article class="message-list-item">
        <div class="message-list-left">
            <div class="message-avatar">
                @if($itemAvatarUrl !== '')
                    <img src="{{ $itemAvatarUrl }}" alt="{{ $itemAvatarText }}">
                @else
                    <span>{{ $itemAvatarInitial }}</span>
                @endif
            </div>
        </div>
        <div class="message-list-main">
            @if($itemHref !== '')
                <a class="message-list-link" href="{{ $itemHref }}">
            @else
                <div class="message-list-link">
            @endif
                <div class="message-list-head">
                    <div class="message-list-titles">
                        <strong>{{ $itemTitle }}</strong>
                        @if($itemSubtitle !== '')
                            <div class="small text-muted">{{ $itemSubtitle }}</div>
                        @endif
                    </div>
                    @if($itemDate !== '')
                        <small class="text-muted">{{ $itemDate }}</small>
                    @endif
                </div>
                @if($itemExcerpt !== '')
                    <div class="message-list-excerpt small">{{ $itemExcerpt }}</div>
                @endif
                @if($itemReply !== '')
                    <div class="message-list-reply small text-muted">Yanit: {{ $itemReply }}</div>
                @endif
            @if($itemHref !== '')
                </a>
            @else
                </div>
            @endif
        </div>
        <div class="message-list-actions">
            @if($itemStatusLabel !== '')
                <span class="badge {{ $itemStatusClass }}">{{ $itemStatusLabel }}</span>
            @endif
        </div>
    </article>
</div>
