<?php

namespace App\Http\Controllers\Support;

use App\Http\Controllers\Controller;
use App\Models\CustomerRequest;
use App\Services\Portal\PortalContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupportDashboardController extends Controller
{
    public function __construct(private readonly PortalContext $context)
    {
    }

    public function index(): View
    {
        return view('portal.support.dashboard');
    }

    public function requests(Request $request): View
    {
        $site = $this->context->site($request);
        $status = (string) $request->query('status', '');

        $rows = CustomerRequest::query()->where('site', $site);
        if ($status !== '') {
            $rows->where('status', $status);
        }

        $rows = $rows
            ->latest()
            ->paginate(30)
            ->withQueryString();

        return view('portal.support.requests', compact('rows', 'status'));
    }

    public function updateRequestStatus(Request $request, CustomerRequest $customerRequest): RedirectResponse
    {
        if ($customerRequest->site !== $this->context->site($request)) {
            abort(404);
        }

        $data = $request->validate([
            'status' => ['required', 'in:new,contacted,closed'],
            'internal_note' => ['nullable', 'string', 'max:2000'],
        ]);

        $customerRequest->update([
            'status' => $data['status'],
            'internal_note' => $data['internal_note'] ?? null,
        ]);

        return back()->with('ok', 'Talep durumu guncellendi.');
    }
}
