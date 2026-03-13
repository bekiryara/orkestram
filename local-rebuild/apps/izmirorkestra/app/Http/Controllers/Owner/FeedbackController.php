<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\ListingFeedback;
use App\Services\Portal\MessageCenterService;
use App\Services\Portal\OwnerResourceAccess;
use App\Services\Portal\PortalContext;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FeedbackController extends Controller
{
    public function __construct(
        private readonly PortalContext $context,
        private readonly OwnerResourceAccess $ownerAccess,
        private readonly MessageCenterService $messageCenterService
    ) {
    }

    public function index(Request $request): JsonResponse|View
    {
        $activeKind = trim((string) $request->query('kind', 'message'));
        if (!in_array($activeKind, ['message', 'comment'], true)) {
            $activeKind = 'message';
        }

        $incomingRowsQuery = $this->ownerAccess->feedbackQuery($request)
            ->with('listing:id,slug,name')
            ->with('user:id,name')
            ->where('kind', $activeKind);
        $ownerUserId = (int) ($this->context->adminUserId($request) ?? 0);

        if (! $request->expectsJson()) {
            if ($activeKind === 'message') {
                $messageCenter = $this->messageCenterService->buildData($request, 'listing');
                $ownerTab = 'messages';
                $panelTitle = 'Ilan Mesajlari';

                return view('portal.owner.feedbacks', compact(
                    'panelTitle',
                    'ownerTab',
                    'activeKind',
                    'messageCenter'
                ));
            }

            $incomingRows = $incomingRowsQuery->latest()->paginate(20, ['*'], 'incoming_page')->withQueryString();
            $tabMap = ['message' => 'messages', 'comment' => 'comments'];
            $titleMap = ['message' => 'Ilan Mesajlari', 'comment' => 'Yorumlar'];
            $ownerTab = $tabMap[$activeKind] ?? 'comments';
            $panelTitle = $titleMap[$activeKind] ?? 'Yorumlar';

            return view('portal.owner.feedbacks', compact('incomingRows', 'panelTitle', 'ownerTab', 'activeKind'));
        }

        $rows = $incomingRowsQuery
            ->latest()
            ->paginate(20)
            ->through(static function (ListingFeedback $item): array {
                return [
                    'id' => $item->id,
                    'kind' => $item->kind,
                    'visibility' => $item->visibility,
                    'status' => $item->status,
                    'content' => $item->content,
                    'owner_reply' => $item->owner_reply,
                    'listing' => $item->listing ? [
                        'slug' => $item->listing->slug,
                        'name' => $item->listing->name,
                    ] : null,
                    'created_at' => optional($item->created_at)->toIso8601String(),
                ];
            });

        return response()->json($rows);
    }

    public function updateStatus(Request $request, ListingFeedback $feedback): JsonResponse|RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:pending,approved,rejected,answered'],
            'owner_reply' => ['nullable', 'string', 'max:5000'],
        ]);

        $payload = [
            'status' => (string) $data['status'],
            'owner_reply' => $data['owner_reply'] ?? null,
        ];

        if ($payload['status'] === 'answered') {
            $payload['answered_at'] = now();
            $payload['answered_by_user_id'] = $this->context->adminUserId($request) ?: null;
        }

        $feedback->update($payload);

        $payload = [
            'ok' => true,
            'feedback_id' => $feedback->id,
            'status' => $feedback->status,
        ];

        if (! $request->expectsJson()) {
            return back()->with('ok', 'Kayit guncellendi.');
        }

        return response()->json($payload);
    }

    public function bulkUpdate(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'action' => ['required', 'in:delete,block'],
            'ids' => ['nullable', 'array'],
            'ids.*' => ['integer'],
            'ids_csv' => ['nullable', 'string'],
        ]);

        $ids = [];
        if (!empty($data['ids']) && is_array($data['ids'])) {
            $ids = array_merge($ids, array_map('intval', $data['ids']));
        }
        if (!empty($data['ids_csv'])) {
            $parts = array_filter(array_map('trim', explode(',', (string) $data['ids_csv'])));
            $ids = array_merge($ids, array_map('intval', $parts));
        }
        $ids = array_values(array_unique(array_filter($ids, static fn($id) => $id > 0)));

        if ($ids === []) {
            return back()->withErrors(['bulk' => 'Toplu islem icin en az bir mesaj secin.']);
        }

        $query = $this->ownerAccess
            ->feedbackQuery($request)
            ->where('kind', 'message')
            ->whereIn('id', $ids);

        $validIds = $query->pluck('id')->all();
        if ($validIds === []) {
            return back()->withErrors(['bulk' => 'Secili mesajlar icin yetkiniz yok veya kayit bulunamadi.']);
        }

        if ((string) $data['action'] === 'delete') {
            ListingFeedback::query()->whereIn('id', $validIds)->delete();

            return back()->with('ok', count($validIds) . ' mesaj silindi.');
        }

        ListingFeedback::query()->whereIn('id', $validIds)->update([
            'status' => 'rejected',
        ]);

        return back()->with('ok', count($validIds) . ' mesaj engellendi.');
    }
}
