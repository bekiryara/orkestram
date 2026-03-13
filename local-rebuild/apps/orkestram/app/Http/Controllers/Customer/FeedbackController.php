<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use App\Models\ListingFeedback;
use App\Models\ListingLike;
use App\Services\Portal\PortalContext;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function __construct(private readonly PortalContext $context)
    {
    }

    public function index(Request $request): JsonResponse|View|RedirectResponse
    {
        $site = $this->context->site($request);
        $adminUserId = $this->context->adminUserId($request);

        $rows = ListingFeedback::query()
            ->where('site', $site)
            ->when($adminUserId > 0, fn($q) => $q->where('user_id', $adminUserId), fn($q) => $q->whereRaw('1 = 0'))
            ->with('listing:id,slug,name');

        if (! $request->expectsJson()) {
            return redirect()->route('messages.index', array_merge(['box' => 'personal'], $request->query()));
        }

        $rows = $rows
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

    public function like(Request $request): JsonResponse|RedirectResponse
    {
        $data = $request->validate([
            'listing_slug' => ['required', 'string', 'max:255'],
        ]);

        $site = $this->context->site($request);
        $listing = Listing::query()
            ->where('site', $site)
            ->where('slug', (string) $data['listing_slug'])
            ->firstOrFail();

        $adminUserId = $this->context->adminUserId($request);
        $actor = strtolower(trim((string) $request->attributes->get('admin_user', '')));
        $actorKey = $adminUserId > 0 ? ('user:' . $adminUserId) : ('account:' . sha1($actor));

        $like = ListingLike::query()->firstOrCreate(
            [
                'listing_id' => $listing->id,
                'actor_key' => $actorKey,
            ],
            [
                'site' => $site,
                'user_id' => $adminUserId > 0 ? $adminUserId : null,
            ]
        );

        $payload = [
            'ok' => true,
            'created' => $like->wasRecentlyCreated,
            'likes_count' => ListingLike::query()->where('listing_id', $listing->id)->count(),
        ];

        if (! $request->expectsJson()) {
            $msg = $like->wasRecentlyCreated
                ? 'Begeni kaydin olusturuldu.'
                : 'Bu ilan icin begenin zaten kayitli.';
            return back()->with('ok', $msg);
        }

        return response()->json($payload, $like->wasRecentlyCreated ? 201 : 200);
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $data = $request->validate([
            'listing_slug' => ['required', 'string', 'max:255'],
            'kind' => ['required', 'in:comment,message'],
            'content' => ['required', 'string', 'max:5000'],
            'visibility' => ['nullable', 'in:public,private'],
        ]);

        $site = $this->context->site($request);
        $listing = Listing::query()
            ->where('site', $site)
            ->where('slug', (string) $data['listing_slug'])
            ->firstOrFail();

        $kind = (string) $data['kind'];
        $visibility = $kind === 'message'
            ? 'private'
            : (string) ($data['visibility'] ?? 'public');

        $row = ListingFeedback::query()->create([
            'site' => $site,
            'listing_id' => $listing->id,
            'user_id' => $this->context->adminUserId($request) ?: null,
            'kind' => $kind,
            'visibility' => $visibility,
            'status' => 'pending',
            'content' => (string) $data['content'],
        ]);

        $payload = [
            'ok' => true,
            'feedback_id' => $row->id,
            'status' => $row->status,
            'visibility' => $row->visibility,
        ];

        if (! $request->expectsJson()) {
            $okMessage = $kind === 'comment'
                ? 'Yorumun alindi.'
                : 'Mesajin alindi.';
            return back()->with('ok', $okMessage);
        }

        return response()->json($payload, 201);
    }
}
