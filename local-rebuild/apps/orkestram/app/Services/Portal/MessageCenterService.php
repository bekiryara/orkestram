<?php

namespace App\Services\Portal;

use App\Models\Listing;
use App\Models\MessageConversation;
use App\Models\MessageConversationMessage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class MessageCenterService
{
    public function __construct(private readonly PortalContext $context)
    {
    }

    public function buildData(Request $request, string $inboxType): array
    {
        $inboxType = $this->normalizeInboxType($request, $inboxType);
        $isOwnerView = $inboxType === 'listing';
        $site = $this->context->site($request);
        $userId = $this->context->adminUserId($request);

        $query = MessageConversation::query()
            ->where('site', $site)
            ->with('listing:id,slug,name')
            ->with('owner:id,name,profile_photo_path')
            ->with('customer:id,name,profile_photo_path');

        if ($isOwnerView) {
            $query->where('owner_user_id', $userId);
        } else {
            $query->where('customer_user_id', $userId);
        }

        $statusFilter = trim((string) $request->query('status_filter', 'all'));
        if (!in_array($statusFilter, ['all', 'new', 'active', 'archived', 'blocked'], true)) {
            $statusFilter = 'all';
        }
        if ($statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        }

        $search = trim((string) $request->query('q', ''));
        if ($search !== '') {
            $query->where(function (Builder $q) use ($search, $isOwnerView): void {
                $q->where('last_message_preview', 'like', '%' . $search . '%')
                    ->orWhereHas('listing', fn($sq) => $sq->where('name', 'like', '%' . $search . '%'));
                if ($isOwnerView) {
                    $q->orWhereHas('customer', fn($sq) => $sq->where('name', 'like', '%' . $search . '%'));
                } else {
                    $q->orWhereHas('owner', fn($sq) => $sq->where('name', 'like', '%' . $search . '%'));
                }
            });
        }

        $conversationRows = (clone $query)
            ->orderByDesc('last_message_at')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        $selectedConversationId = (int) $request->query('conversation_id', 0);
        $selectedConversation = null;
        if ($selectedConversationId > 0) {
            $selectedConversation = (clone $query)->whereKey($selectedConversationId)->first();
        }

        $selectedConversationMessages = collect();
        if ($selectedConversation) {
            $selectedConversationMessages = $selectedConversation->messages()
                ->with('sender:id,name,profile_photo_path')
                ->orderBy('id')
                ->get();
        }

        $listingInputValue = trim((string) $request->query('listing', ''));
        if ($listingInputValue === '' && $selectedConversation?->listing?->slug) {
            $listingInputValue = (string) $selectedConversation->listing->slug;
        }

        return [
            'isOwnerView' => $isOwnerView,
            'inboxType' => $inboxType,
            'canBulk' => true,
            'conversationRows' => $conversationRows,
            'selectedConversation' => $selectedConversation,
            'selectedConversationMessages' => $selectedConversationMessages,
            'statusLabelMap' => [
                'new' => 'Yeni',
                'active' => 'Aktif',
                'archived' => 'Arsivlendi',
                'blocked' => 'Engellendi',
            ],
            'statusFilter' => $statusFilter,
            'search' => $search,
            'listQueryParams' => $request->except('conversation_id'),
            'listingInputValue' => $listingInputValue,
            'currentUserId' => $userId,
        ];
    }

    public function reply(Request $request, string $inboxType, int $conversationId, string $listingSlug, string $content): ?MessageConversationMessage
    {
        $inboxType = $this->normalizeInboxType($request, $inboxType);
        $site = $this->context->site($request);
        $userId = $this->context->adminUserId($request);
        $conversation = null;

        if ($conversationId > 0) {
            $conversation = $this->accessibleQuery($request, $inboxType)->whereKey($conversationId)->first();
        } elseif ($listingSlug !== '') {
            $listing = Listing::query()
                ->where('site', $site)
                ->where('slug', $listingSlug)
                ->first();

            if (!$listing) {
                return null;
            }

            $ownerId = (int) ($listing->owner_user_id ?? 0);
            if ($ownerId <= 0) {
                return null;
            }

            if ($inboxType === 'listing') {
                $conversation = MessageConversation::query()
                    ->where('site', $site)
                    ->where('listing_id', (int) $listing->id)
                    ->where('owner_user_id', $userId)
                    ->latest('id')
                    ->first();
            } else {
                $conversation = MessageConversation::query()->firstOrCreate(
                    [
                        'site' => $site,
                        'listing_id' => (int) $listing->id,
                        'owner_user_id' => $ownerId,
                        'customer_user_id' => $userId,
                    ],
                    [
                        'status' => 'active',
                    ]
                );
            }
        }

        if (!$conversation) {
            return null;
        }

        $senderRole = $inboxType === 'listing' ? 'owner' : 'customer';
        $message = MessageConversationMessage::query()->create([
            'conversation_id' => $conversation->id,
            'sender_user_id' => $userId ?: null,
            'sender_role' => $senderRole,
            'body' => $content,
        ]);

        $preview = mb_substr($content, 0, 240);
        $conversation->update([
            'status' => 'active',
            'last_message_at' => now(),
            'last_message_preview' => $preview,
        ]);

        return $message;
    }

    public function bulk(Request $request, string $inboxType, string $action, array $ids): int
    {
        $inboxType = $this->normalizeInboxType($request, $inboxType);
        $validIds = $this->accessibleQuery($request, $inboxType)
            ->whereIn('id', $ids)
            ->pluck('id')
            ->all();

        if ($validIds === []) {
            return 0;
        }

        if ($action === 'delete') {
            MessageConversation::query()->whereIn('id', $validIds)->delete();
            return count($validIds);
        }

        MessageConversation::query()->whereIn('id', $validIds)->update([
            'status' => 'blocked',
        ]);

        return count($validIds);
    }

    public function threadMessages(Request $request, string $inboxType, int $conversationId): ?array
    {
        if ($conversationId <= 0) {
            return null;
        }

        $inboxType = $this->normalizeInboxType($request, $inboxType);
        $conversation = $this->accessibleQuery($request, $inboxType)
            ->with('listing:id,slug,name')
            ->with('owner:id,name,profile_photo_path')
            ->with('customer:id,name,profile_photo_path')
            ->whereKey($conversationId)
            ->first();
        if (!$conversation) {
            return null;
        }

        $currentUserId = $this->context->adminUserId($request);
        $messages = $conversation->messages()
            ->with('sender:id,name,profile_photo_path')
            ->orderBy('id')
            ->get()
            ->map(function (MessageConversationMessage $m) use ($currentUserId): array {
                $isMine = (int) ($m->sender_user_id ?? 0) === (int) $currentUserId;
                $label = $isMine ? 'Siz' : ((string) $m->sender_role === 'owner' ? 'Firma' : 'Musteri');
                return [
                    'id' => (int) $m->id,
                    'is_mine' => $isMine,
                    'label' => $label,
                    'body' => (string) $m->body,
                    'created_at' => optional($m->created_at)->format('d.m.Y H:i'),
                ];
            })
            ->values()
            ->all();

        return [
            'conversation_id' => (int) $conversation->id,
            'counterparty' => $inboxType === 'listing'
                ? (string) ($conversation->customer?->name ?: 'Musteri')
                : (string) ($conversation->owner?->name ?: 'Firma'),
            'listing_name' => (string) ($conversation->listing?->name ?? '-'),
            'status' => (string) $conversation->status,
            'messages' => $messages,
        ];
    }

    public function normalizeInboxType(Request $request, string $inboxType): string
    {
        $inboxType = strtolower(trim($inboxType));
        if (!in_array($inboxType, ['listing', 'personal'], true)) {
            $inboxType = $this->isPrivilegedRole($request) ? 'listing' : 'personal';
        }
        if ($inboxType === 'listing' && !$this->isPrivilegedRole($request)) {
            return 'personal';
        }

        return $inboxType;
    }

    private function accessibleQuery(Request $request, string $inboxType): Builder
    {
        $site = $this->context->site($request);
        $userId = $this->context->adminUserId($request);
        $query = MessageConversation::query()->where('site', $site);

        if ($inboxType === 'listing') {
            return $query->where('owner_user_id', $userId);
        }

        return $query->where('customer_user_id', $userId);
    }

    private function isPrivilegedRole(Request $request): bool
    {
        $role = strtolower(trim((string) $request->attributes->get('admin_role', '')));
        return in_array($role, ['listing_owner', 'admin', 'super_admin'], true);
    }
}
