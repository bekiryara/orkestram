@php($centerRouteName = $centerRouteName ?? 'messages.index')
@php($centerRouteBase = $centerRouteBase ?? ['box' => $inboxType])

@if(session('ok'))
    <div class="alert alert-success py-2 px-3">{{ session('ok') }}</div>
@endif
@if($errors->any())
    <div class="alert alert-danger py-2 px-3">{{ $errors->first() }}</div>
@endif

@if($selectedConversation)
    <div class="mb-3">
        <a class="btn btn-sm btn-outline-secondary" href="{{ route($centerRouteName, array_merge($centerRouteBase, $listQueryParams ?? [])) }}">&larr; Geri</a>
    </div>

    <div class="message-detail-head">
        <div>
            <strong>{{ $isOwnerView ? ((string) ($selectedConversation->customer?->name ?: 'Musteri')) : ((string) ($selectedConversation->owner?->name ?: 'Firma')) }}</strong>
            <div class="small text-muted">{{ $selectedConversation->listing?->name ?? '-' }}</div>
        </div>
        <span class="badge text-bg-secondary">{{ $statusLabelMap[$selectedConversation->status] ?? $selectedConversation->status }}</span>
    </div>

    <div class="message-thread" data-thread-box="{{ $inboxType }}" data-thread-conversation-id="{{ $selectedConversation->id }}" data-thread-endpoint="{{ route('messages.thread') }}" data-last-message-id="{{ (int) optional(($selectedConversationMessages ?? collect())->last())->id }}">
        @forelse(($selectedConversationMessages ?? []) as $msg)
            @php($isMine = ((int) ($msg->sender_user_id ?? 0)) === ((int) ($currentUserId ?? 0)))
            @php($msgLabel = $isMine ? 'Siz' : ($msg->sender_role === 'owner' ? 'Firma' : 'Musteri'))
            <div class="message-bubble {{ $isMine ? 'message-bubble-outgoing' : 'message-bubble-incoming' }}">
                <div class="small text-muted mb-1">{{ $msgLabel }}</div>
                <div>{{ $msg->body }}</div>
                <div class="small text-muted mt-2">{{ optional($msg->created_at)->format('d.m.Y H:i') }}</div>
            </div>
        @empty
            <div class="message-list-empty">Bu konusmada mesaj bulunamadi.</div>
        @endforelse
    </div>

    <form method="post" action="{{ route('messages.reply') }}" class="message-compose mt-3">
        @csrf
        <input type="hidden" name="box" value="{{ $inboxType }}">
        <input type="hidden" name="conversation_id" value="{{ $selectedConversation->id }}">
        <input type="hidden" name="listing_slug" value="{{ $selectedConversation->listing?->slug ?? $listingInputValue }}">
        <label class="form-label">{{ $isOwnerView ? 'Yanit' : 'Yeni Mesaj' }}</label>
        <textarea class="form-control" name="content" rows="4" placeholder="Mesajinizi yazin...">{{ old('content') }}</textarea>
        <div class="actions mt-2">
            <button class="btn btn-primary" type="submit">Gonder</button>
            @if($selectedConversation->listing?->slug)
                <a class="btn btn-outline-secondary" href="{{ route('listing.show', ['slug' => $selectedConversation->listing->slug]) }}" target="_blank" rel="noopener">Ilana Git</a>
            @endif
        </div>
    </form>
@else
    <h3 class="h5 mb-3">Mesaj Listesi</h3>

    <form id="owner-message-bulk-form" method="post" action="{{ route('messages.bulk') }}" class="message-toolbar card mb-3">
        @csrf
        <input type="hidden" name="box" value="{{ $inboxType }}">
        <input type="hidden" name="ids_csv" id="owner_message_ids_csv" value="">
        <div class="row g-2 align-items-center">
            <div class="col-12 col-lg-4">
                <select class="form-select" name="action" required @disabled(!$canBulk)>
                    <option value="">Toplu Islem</option>
                    <option value="delete">Sil</option>
                    <option value="block">Engelle</option>
                </select>
            </div>
            <div class="col-12 col-lg-auto">
                <button class="btn btn-primary" type="submit" @disabled(!$canBulk)>Uygula</button>
            </div>
            @if(!$canBulk)
                <div class="col-12 col-lg-auto"><small class="text-muted">Bu kutuda toplu islem kapali.</small></div>
            @endif
        </div>
    </form>

    @if($listingInputValue !== '')
        <form method="post" action="{{ route('messages.reply') }}" class="message-toolbar card mb-3 message-compose">
            @csrf
            <input type="hidden" name="box" value="{{ $inboxType }}">
            <input type="hidden" name="listing_slug" value="{{ $listingInputValue }}">
            <div class="d-flex flex-column gap-2">
                <label class="form-label mb-0">Yeni Mesaj (Secili ilan)</label>
                <textarea class="form-control" name="content" rows="3" placeholder="Mesajinizi yazin...">{{ old('content') }}</textarea>
                <div><button class="btn btn-primary" type="submit">Gonder</button></div>
            </div>
        </form>
    @endif

    <div class="message-list message-list-modern">
        @forelse($conversationRows as $row)
            @php($rowQuery = array_merge($centerRouteBase, $listQueryParams ?? [], ['conversation_id' => $row->id]))
            @php($senderName = $isOwnerView ? ((string) ($row->customer?->name ?: 'Musteri')) : ((string) ($row->owner?->name ?: 'Firma')))
            @php($avatarPath = $isOwnerView ? (string) ($row->customer?->profile_photo_path ?? '') : (string) ($row->owner?->profile_photo_path ?? ''))
            @php($avatarUrl = $avatarPath !== '' ? asset('storage/' . ltrim($avatarPath, '/')) : '')
            @php($messageSummary = \Illuminate\Support\Str::words((string) ($row->last_message_preview ?? ''), 5, '...'))
            @include('portal.partials.message-list-item', [
                'itemTitle' => $senderName,
                'itemSubtitle' => (string) ($row->listing?->name ?? '-'),
                'itemDate' => optional($row->last_message_at)->format('d.m.Y H:i'),
                'itemExcerpt' => $messageSummary,
                'itemStatusLabel' => (string) ($statusLabelMap[$row->status] ?? $row->status),
                'itemStatusClass' => 'text-bg-secondary',
                'itemHref' => route($centerRouteName, $rowQuery),
                'itemAvatarUrl' => $avatarUrl,
                'itemAvatarText' => $senderName,
                'itemSelectable' => true,
                'itemCheckboxValue' => (string) $row->id,
                'itemCheckboxClass' => 'owner-message-row-check',
                'itemCheckboxAria' => 'Mesaji sec',
            ])
        @empty
            <div class="message-list-empty">Konusma bulunmuyor.</div>
        @endforelse
    </div>
    <div class="mt-2">{{ $conversationRows->links() }}</div>
@endif

<script>
    (function () {
        function escapeHtml(s) {
            return String(s || '').replace(/[&<>"']/g, function (m) {
                return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'})[m];
            });
        }

        var bulkForm = document.getElementById('owner-message-bulk-form');
        var idsCsvInput = document.getElementById('owner_message_ids_csv');
        var checks = Array.from(document.querySelectorAll('.owner-message-row-check'));

        if (bulkForm && idsCsvInput) {
            bulkForm.addEventListener('submit', function (event) {
                var ids = checks.filter(function (cb) { return cb.checked; }).map(function (cb) { return cb.value; });
                idsCsvInput.value = ids.join(',');
                if (ids.length === 0) {
                    event.preventDefault();
                    alert('Toplu islem icin en az bir mesaj secin.');
                }
            });
        }

        var thread = document.querySelector('.message-thread');
        var lastMessageId = thread ? parseInt(thread.getAttribute('data-last-message-id') || '0', 10) : 0;
        var composeForms = Array.from(document.querySelectorAll('form.message-compose'));
        composeForms.forEach(function (form) {
            form.addEventListener('submit', function (event) {
                event.preventDefault();

                var textarea = form.querySelector('textarea[name="content"]');
                if (!textarea) {
                    form.submit();
                    return;
                }

                var body = (textarea.value || '').trim();
                if (body === '') {
                    return;
                }

                var formData = new FormData(form);
                fetch(form.getAttribute('action') || '', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                }).then(function (resp) {
                    return resp.json();
                }).then(function (data) {
                    if (!data || !data.ok) {
                        return;
                    }

                    textarea.value = '';
                    if (!thread) {
                        if (data.conversation_id) {
                            var u = new URL(window.location.href);
                            u.searchParams.set('conversation_id', String(data.conversation_id));
                            window.location.href = u.toString();
                        }
                        return;
                    }
                    refreshThread(true);
                }).catch(function () {
                    form.submit();
                });
            });
        });

        function renderThread(messages) {
            if (!thread) return;
            var html = '';
            (messages || []).forEach(function (m) {
                html += '<div class="message-bubble ' + (m.is_mine ? 'message-bubble-outgoing' : 'message-bubble-incoming') + '">';
                html += '<div class="small text-muted mb-1">' + escapeHtml(m.label || '') + '</div>';
                html += '<div>' + escapeHtml(m.body || '') + '</div>';
                html += '<div class="small text-muted mt-2">' + escapeHtml(m.created_at || '') + '</div>';
                html += '</div>';
            });
            thread.innerHTML = html || '<div class="message-list-empty">Bu konusmada mesaj bulunamadi.</div>';
            if (messages && messages.length > 0) {
                var newest = parseInt(messages[messages.length - 1].id || 0, 10);
                lastMessageId = isNaN(newest) ? lastMessageId : newest;
                thread.setAttribute('data-last-message-id', String(lastMessageId || 0));
            }
        }

        function refreshThread(scrollToBottom) {
            if (!thread) return;
            var endpoint = thread.getAttribute('data-thread-endpoint') || '';
            var box = thread.getAttribute('data-thread-box') || '';
            var conversationId = thread.getAttribute('data-thread-conversation-id') || '';
            if (!endpoint || !conversationId) return;

            var url = endpoint + '?box=' + encodeURIComponent(box) + '&conversation_id=' + encodeURIComponent(conversationId);
            fetch(url, {headers: {'Accept':'application/json','X-Requested-With':'XMLHttpRequest'}})
                .then(function (resp) { return resp.json(); })
                .then(function (data) {
                    if (!data || !data.ok || !data.thread) return;
                    var messages = data.thread.messages || [];
                    var newest = messages.length ? parseInt(messages[messages.length - 1].id || 0, 10) : 0;
                    var hasNew = newest > (lastMessageId || 0);
                    renderThread(messages);
                    if (scrollToBottom || hasNew) {
                        thread.scrollTop = thread.scrollHeight;
                    }
                })
                .catch(function () {});
        }

        if (thread) {
            thread.scrollTop = thread.scrollHeight;
            setInterval(function () { refreshThread(false); }, 3000);
        }
    })();
</script>
