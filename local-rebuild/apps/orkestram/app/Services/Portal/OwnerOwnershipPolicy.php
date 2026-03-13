<?php

namespace App\Services\Portal;

use App\Models\CustomerRequest;
use App\Models\Listing;
use App\Models\ListingFeedback;
use Illuminate\Http\Request;

class OwnerOwnershipPolicy
{
    public function __construct(
        private readonly PortalContext $context,
        private readonly OwnerResourceAccess $ownerAccess
    ) {
    }

    public function ownsListing(Request $request, Listing $listing): bool
    {
        $site = $this->context->site($request);
        $ownerId = $this->context->adminUserId($request);

        return $listing->site === $site && (int) $listing->owner_user_id === $ownerId;
    }

    public function ownsLead(Request $request, CustomerRequest $lead): bool
    {
        return $this->ownerAccess->leadsQuery($request)
            ->whereKey($lead->id)
            ->exists();
    }

    public function ownsFeedback(Request $request, ListingFeedback $feedback): bool
    {
        return $this->ownerAccess->feedbackQuery($request)
            ->whereKey($feedback->id)
            ->exists();
    }
}
