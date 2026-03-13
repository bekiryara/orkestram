<?php

namespace App\Http\Middleware;

use App\Models\CustomerRequest;
use App\Models\Listing;
use App\Models\ListingFeedback;
use App\Services\Portal\OwnerOwnershipPolicy;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOwnerOwnsResource
{
    public function __construct(private readonly OwnerOwnershipPolicy $policy)
    {
    }

    public function handle(Request $request, Closure $next, string $resource): Response
    {
        $allowed = match ($resource) {
            'listing' => $this->canAccessListing($request),
            'lead' => $this->canAccessLead($request),
            'feedback' => $this->canAccessFeedback($request),
            default => false,
        };

        if (!$allowed) {
            abort(403);
        }

        return $next($request);
    }

    private function canAccessListing(Request $request): bool
    {
        $listing = $request->route('listing');
        return $listing instanceof Listing
            && $this->policy->ownsListing($request, $listing);
    }

    private function canAccessLead(Request $request): bool
    {
        $lead = $request->route('customerRequest');
        return $lead instanceof CustomerRequest
            && $this->policy->ownsLead($request, $lead);
    }

    private function canAccessFeedback(Request $request): bool
    {
        $feedback = $request->route('feedback');
        return $feedback instanceof ListingFeedback
            && $this->policy->ownsFeedback($request, $feedback);
    }
}
