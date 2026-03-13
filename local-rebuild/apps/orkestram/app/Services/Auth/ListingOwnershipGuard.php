<?php

namespace App\Services\Auth;

use App\Models\Listing;
use Illuminate\Http\Request;

class ListingOwnershipGuard
{
    public function canAccess(Request $request, Listing $listing): bool
    {
        $adminUserId = $request->attributes->get('admin_user_id');
        if (!is_int($adminUserId) && !ctype_digit((string) $adminUserId)) {
            return false;
        }

        return (int) $adminUserId === (int) $listing->owner_user_id;
    }
}
