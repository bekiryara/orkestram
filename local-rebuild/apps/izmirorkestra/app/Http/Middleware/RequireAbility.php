<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireAbility
{
    public function handle(Request $request, Closure $next, string $ability): Response
    {
        $role = (string) $request->attributes->get('admin_role', '');
        $roleMap = (array) config('admin_acl.roles', []);
        $abilities = (array) ($roleMap[$role] ?? []);

        if (in_array('*', $abilities, true) || in_array($ability, $abilities, true)) {
            return $next($request);
        }

        abort(403);
    }
}
