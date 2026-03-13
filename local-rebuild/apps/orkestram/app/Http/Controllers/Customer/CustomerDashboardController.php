<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\CustomerRequest;
use App\Models\Listing;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerDashboardController extends Controller
{
    public function index(Request $request): View
    {
        $site = $this->site($request);
        $listingSlug = trim((string) $request->query('listing', ''));
        $listing = null;

        if ($listingSlug !== '') {
            $listing = Listing::query()
                ->where('site', $site)
                ->where('slug', $listingSlug)
                ->first();
        }

        return view('portal.customer.dashboard', [
            'listing' => $listing,
            'listingSlug' => $listingSlug,
        ]);
    }

    public function requests(Request $request): View
    {
        $adminUserId = (int) $request->attributes->get('admin_user_id');
        $site = $this->site($request);

        $query = CustomerRequest::query()->where('site', $site);
        if ($adminUserId > 0) {
            $query->where('user_id', $adminUserId);
        } else {
            $query->whereRaw('1 = 0');
        }

        $rows = $query->latest()->paginate(20)->withQueryString();

        return view('portal.customer.requests', compact('rows'));
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $data = $request->validate([
            'listing_slug' => ['nullable', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:64'],
            'email' => ['nullable', 'email', 'max:255'],
            'message' => ['nullable', 'string', 'max:5000'],
        ]);

        $site = $this->site($request);
        $listingId = null;
        if (!empty($data['listing_slug'])) {
            $listingId = Listing::query()
                ->where('site', $site)
                ->where('slug', $data['listing_slug'])
                ->value('id');
        }

        $adminUserId = (int) $request->attributes->get('admin_user_id');
        $created = CustomerRequest::query()->create([
            'site' => $site,
            'user_id' => $adminUserId > 0 ? $adminUserId : null,
            'listing_id' => $listingId,
            'name' => $data['name'],
            'phone' => $data['phone'] ?? null,
            'email' => $data['email'] ?? null,
            'message' => $data['message'] ?? null,
            'status' => 'new',
        ]);

        if (! $request->expectsJson()) {
            return redirect('/customer/requests')->with('ok', 'Talebin alindi. En kisa surede geri donulecek.');
        }

        return response()->json([
            'ok' => true,
            'request_id' => $created->id,
        ], 201);
    }

    private function site(Request $request): string
    {
        $host = strtolower($request->getHost());
        $httpHost = strtolower($request->getHttpHost());
        if (str_contains($httpHost, ':8181') || str_contains($host, 'izmirorkestra')) {
            return 'izmirorkestra.net';
        }
        return 'orkestram.net';
    }
}
