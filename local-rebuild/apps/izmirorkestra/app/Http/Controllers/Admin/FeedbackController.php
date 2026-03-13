<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ListingFeedback;
use App\Models\ListingLike;
use App\Services\Portal\PortalContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FeedbackController extends Controller
{
    public function __construct(private readonly PortalContext $context)
    {
    }

    public function index(Request $request): View
    {
        $site = trim((string) $request->query('site', $this->context->site($request)));
        $status = trim((string) $request->query('status', ''));

        if (!in_array($site, ['orkestram.net', 'izmirorkestra.net'], true)) {
            $site = $this->context->site($request);
        }

        $commentRows = ListingFeedback::query()
            ->where('site', $site)
            ->where('kind', 'comment')
            ->with('listing:id,name,slug')
            ->with('user:id,name')
            ->when($status !== '', fn($q) => $q->where('status', $status))
            ->latest()
            ->paginate(20, ['*'], 'comments_page')
            ->withQueryString();

        $likeRows = ListingLike::query()
            ->where('site', $site)
            ->with('listing:id,name,slug')
            ->with('user:id,name')
            ->latest()
            ->paginate(20, ['*'], 'likes_page')
            ->withQueryString();

        return view('admin.feedbacks.index', compact('commentRows', 'likeRows', 'site', 'status'));
    }

    public function updateCommentStatus(Request $request, ListingFeedback $feedback): RedirectResponse
    {
        if ((string) $feedback->kind !== 'comment') {
            abort(404);
        }

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

        return back()->with('ok', 'Yorum durumu guncellendi.');
    }
}
