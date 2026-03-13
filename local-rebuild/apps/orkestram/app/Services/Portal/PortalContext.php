<?php

namespace App\Services\Portal;

use Illuminate\Http\Request;

class PortalContext
{
    public function site(Request $request): string
    {
        $host = strtolower($request->getHost());
        $httpHost = strtolower($request->getHttpHost());
        if (str_contains($httpHost, ':8181') || str_contains($host, 'izmirorkestra')) {
            return 'izmirorkestra.net';
        }

        return 'orkestram.net';
    }

    public function adminUserId(Request $request): int
    {
        return (int) $request->attributes->get('admin_user_id');
    }
}
