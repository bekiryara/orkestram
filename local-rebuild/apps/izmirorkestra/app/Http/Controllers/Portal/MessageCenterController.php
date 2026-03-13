<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Services\Portal\MessageCenterService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MessageCenterController extends Controller
{
    public function __construct(private readonly MessageCenterService $service)
    {
    }

    public function index(Request $request): RedirectResponse
    {
        $query = $request->only([
            'box',
            'listing',
            'kind',
            'conversation_id',
        ]);
        $query['tab'] = 'messages';

        return redirect()->route('auth.account', $query);
    }

    public function reply(Request $request): JsonResponse|RedirectResponse
    {
        $data = $request->validate([
            'box' => ['nullable', 'in:personal,listing'],
            'conversation_id' => ['nullable', 'integer'],
            'listing_slug' => ['nullable', 'string', 'max:255'],
            'content' => ['required', 'string', 'max:5000'],
        ]);

        $message = $this->service->reply(
            $request,
            (string) ($data['box'] ?? 'personal'),
            (int) ($data['conversation_id'] ?? 0),
            trim((string) ($data['listing_slug'] ?? '')),
            trim((string) $data['content'])
        );

        if (!$message) {
            if ($request->expectsJson()) {
                return response()->json([
                    'ok' => false,
                    'error' => 'Mesaj islemi tamamlanamadi.',
                ], 422);
            }
            return back()->withErrors(['message' => 'Mesaj islemi tamamlanamadi.'])->withInput();
        }

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'message_id' => $message->id,
                'conversation_id' => (int) $message->conversation_id,
                'sender_role' => (string) $message->sender_role,
                'body' => (string) $message->body,
                'created_at' => optional($message->created_at)->format('d.m.Y H:i'),
            ]);
        }

        return back()->with('ok', 'Mesaj gonderildi.');
    }

    public function bulk(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'box' => ['nullable', 'in:personal,listing'],
            'action' => ['required', 'in:delete,block'],
            'ids_csv' => ['nullable', 'string'],
        ]);

        $parts = array_filter(array_map('trim', explode(',', (string) ($data['ids_csv'] ?? ''))));
        $ids = array_values(array_unique(array_filter(array_map('intval', $parts), static fn($id) => $id > 0)));
        if ($ids === []) {
            return back()->withErrors(['bulk' => 'Toplu islem icin en az bir mesaj secin.']);
        }

        $count = $this->service->bulk(
            $request,
            (string) ($data['box'] ?? 'personal'),
            (string) $data['action'],
            $ids
        );
        if ($count === 0) {
            return back()->withErrors(['bulk' => 'Secili konusmalar bulunamadi.']);
        }

        $suffix = (string) $data['action'] === 'delete' ? 'silindi' : 'engellendi';
        return back()->with('ok', $count . ' konusma ' . $suffix . '.');
    }

    public function thread(Request $request): JsonResponse
    {
        $data = $request->validate([
            'box' => ['nullable', 'in:personal,listing'],
            'conversation_id' => ['required', 'integer'],
        ]);

        $payload = $this->service->threadMessages(
            $request,
            (string) ($data['box'] ?? 'personal'),
            (int) $data['conversation_id']
        );

        if (!$payload) {
            return response()->json(['ok' => false], 404);
        }

        return response()->json([
            'ok' => true,
            'thread' => $payload,
        ]);
    }
}
