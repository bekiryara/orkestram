<?php

namespace App\Http\Middleware;

use App\Models\RedirectRule;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApplyRedirectRules
{
    public function handle(Request $request, Closure $next): Response
    {
        // Local development default: skip redirect DB lookup to keep requests fast.
        if (app()->environment('local') && !filter_var(env('REDIRECT_RULES_LOCAL_ENABLED', false), FILTER_VALIDATE_BOOL)) {
            return $next($request);
        }

        $site = $this->siteFromRequest($request);
        $path = '/' . ltrim($request->path(), '/');
        if ($path === '//') {
            $path = '/';
        }

        $rule = RedirectRule::query()
            ->where('site', $site)
            ->where('old_path', $path)
            ->where('is_active', true)
            ->first();

        if ($rule && $this->isValidRedirect($request, $path, $rule->new_url)) {
            return redirect()->to($rule->new_url, $this->normalizeStatusCode((int) $rule->http_code));
        }

        return $next($request);
    }

    private function siteFromRequest(Request $request): string
    {
        $host = strtolower($request->getHost());
        $httpHost = strtolower($request->getHttpHost());
        if (str_contains($httpHost, ':8181') || str_contains($host, 'izmirorkestra')) {
            return 'izmirorkestra.net';
        }
        return 'orkestram.net';
    }

    private function isValidRedirect(Request $request, string $currentPath, string $target): bool
    {
        $target = trim($target);
        if ($target === '') {
            return false;
        }

        // Relative targets must be root-based.
        if (str_starts_with($target, '/')) {
            return strtolower($target) !== strtolower($currentPath);
        }

        $parts = parse_url($target);
        if ($parts === false || !isset($parts['scheme'])) {
            return false;
        }

        $scheme = strtolower((string) $parts['scheme']);
        if (!in_array($scheme, ['http', 'https'], true)) {
            return false;
        }

        // Avoid direct self-loop on same host/path.
        $targetHost = strtolower((string) ($parts['host'] ?? ''));
        $requestHost = strtolower($request->getHost());
        $targetPath = '/' . ltrim((string) ($parts['path'] ?? '/'), '/');

        if ($targetHost === '' || $targetHost !== $requestHost) {
            return true;
        }

        return strtolower($targetPath) !== strtolower($currentPath);
    }

    private function normalizeStatusCode(int $statusCode): int
    {
        $allowed = [301, 302, 307, 308];
        if (in_array($statusCode, $allowed, true)) {
            return $statusCode;
        }

        return 301;
    }
}
