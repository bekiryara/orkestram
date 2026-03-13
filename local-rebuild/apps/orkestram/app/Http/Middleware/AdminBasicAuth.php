<?php

namespace App\Http\Middleware;

use App\Services\Auth\AdminIdentityResolver;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminBasicAuth
{
    private const SESSION_KEY = 'admin_identity';

    public function __construct(private readonly AdminIdentityResolver $resolver)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $identity = $this->sessionIdentity($request) ?? $this->resolver->resolve($request);
        if ($identity === null) {
            return response('Unauthorized', 401, [
                'WWW-Authenticate' => 'Basic realm="Admin Area"',
            ]);
        }

        $request->attributes->set('admin_user', (string) $identity['user']);
        $request->attributes->set('admin_role', (string) $identity['role']);
        $request->attributes->set('admin_user_id', $identity['user_id'] ?? null);
        $request->attributes->set('admin_auth_source', (string) ($identity['source'] ?? 'unknown'));

        return $next($request);
    }

    private function sessionIdentity(Request $request): ?array
    {
        $identity = $request->session()->get(self::SESSION_KEY);
        if (!is_array($identity)) {
            return null;
        }

        $user = trim((string) ($identity['user'] ?? ''));
        $role = trim((string) ($identity['role'] ?? ''));
        if ($user === '' || $role === '') {
            return null;
        }

        return [
            'user' => $user,
            'role' => $role,
            'user_id' => isset($identity['user_id']) ? (int) $identity['user_id'] : null,
            'source' => (string) ($identity['source'] ?? 'session'),
        ];
    }
}
