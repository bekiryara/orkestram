<?php

namespace App\Services\Portal;

use App\Models\CustomerRequest;
use App\Models\ListingFeedback;
use App\Models\Listing;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class OwnerResourceAccess
{
    public function __construct(private readonly PortalContext $context)
    {
    }

    public function listingsQuery(Request $request): Builder
    {
        return Listing::query()
            ->where('site', $this->context->site($request))
            ->where('owner_user_id', $this->context->adminUserId($request));
    }

    public function leadsQuery(Request $request): Builder
    {
        $site = $this->context->site($request);
        $ownerId = $this->context->adminUserId($request);

        return CustomerRequest::query()
            ->where('site', $site)
            ->whereIn('listing_id', function ($q) use ($site, $ownerId) {
                $q->select('id')
                    ->from('listings')
                    ->where('site', $site)
                    ->where('owner_user_id', $ownerId);
            });
    }

    public function assertOwnsListing(Request $request, Listing $listing): void
    {
        $site = $this->context->site($request);
        $ownerId = $this->context->adminUserId($request);

        if ($listing->site !== $site || (int) $listing->owner_user_id !== $ownerId) {
            abort(403);
        }
    }

    public function assertOwnsLead(Request $request, CustomerRequest $lead): void
    {
        $exists = $this->leadsQuery($request)
            ->whereKey($lead->id)
            ->exists();

        if (!$exists) {
            abort(403);
        }
    }

    public function feedbackQuery(Request $request): Builder
    {
        $site = $this->context->site($request);
        $ownerId = $this->context->adminUserId($request);

        return ListingFeedback::query()
            ->where('site', $site)
            ->whereIn('listing_id', function ($q) use ($site, $ownerId) {
                $q->select('id')
                    ->from('listings')
                    ->where('site', $site)
                    ->where('owner_user_id', $ownerId);
            });
    }

    public function assertOwnsFeedback(Request $request, ListingFeedback $feedback): void
    {
        $exists = $this->feedbackQuery($request)
            ->whereKey($feedback->id)
            ->exists();

        if (!$exists) {
            abort(403);
        }
    }
}
