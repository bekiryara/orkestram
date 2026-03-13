<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminIdentityResolver
{
    public function resolve(Request $request): ?array
    {
        $user = (string) $request->getUser();
        $pass = (string) $request->getPassword();

        return $this->resolveFromCredentials($user, $pass);
    }

    public function resolveFromCredentials(string $user, string $pass): ?array
    {
        if ($user === '' || $pass === '') {
            return null;
        }

        $fromJson = $this->resolveFromJsonAccounts($user, $pass);
        if ($fromJson !== null) {
            return $fromJson;
        }

        $fromDb = $this->resolveFromDatabase($user, $pass);
        if ($fromDb !== null) {
            return $fromDb;
        }

        return $this->resolveLegacyFallback($user, $pass);
    }

    private function resolveFromJsonAccounts(string $user, string $pass): ?array
    {
        $raw = $this->envValue('ADMIN_ACCOUNTS_JSON');
        if ($raw === null) {
            $raw = (string) config('admin_auth.accounts_json', '');
        }
        if ($raw === '') {
            return null;
        }

        $decoded = json_decode($raw, true);
        if (!is_array($decoded)) {
            return null;
        }

        foreach ($decoded as $item) {
            if (!is_array($item)) {
                continue;
            }

            $candidateUser = trim((string) ($item['user'] ?? ''));
            $candidatePass = (string) ($item['pass'] ?? '');
            $candidateRole = trim((string) ($item['role'] ?? ''));

            if ($candidateUser === '' || $candidatePass === '' || $candidateRole === '') {
                continue;
            }

            if (hash_equals($candidateUser, $user) && hash_equals($candidatePass, $pass)) {
                return [
                    'user' => $candidateUser,
                    'role' => $candidateRole,
                    'user_id' => null,
                    'source' => 'json',
                ];
            }
        }

        return null;
    }

    private function resolveFromDatabase(string $user, string $pass): ?array
    {
        $runtimeDbAuth = $this->envValue('ADMIN_DB_AUTH_ENABLED');
        $dbAuthEnabled = filter_var(
            $runtimeDbAuth ?? config('admin_auth.db_auth_enabled', false),
            FILTER_VALIDATE_BOOL
        );
        if (!$dbAuthEnabled) {
            return null;
        }

        $record = User::query()
            ->with(['roles' => fn($q) => $q->where('is_active', true)])
            ->where(function ($query) use ($user): void {
                $query->where('username', $user)
                    ->orWhere('email', $user);
            })
            ->where('is_active', true)
            ->first();

        if ($record === null || !Hash::check($pass, (string) $record->password)) {
            return null;
        }

        $roles = $this->resolveRoleSlugs($record);
        $role = $roles[0] ?? null;
        if ($role === null || $role === '') {
            return null;
        }

        return [
            'user' => (string) $record->username,
            'role' => $role,
            'roles' => $roles,
            'user_id' => $record->id,
            'source' => 'db',
        ];
    }

    private function resolveLegacyFallback(string $user, string $pass): ?array
    {
        $expectedUser = $this->envValue('ADMIN_BASIC_USER') ?? (string) config('admin_auth.basic.user', 'admin');
        $expectedPass = $this->envValue('ADMIN_BASIC_PASS') ?? (string) config('admin_auth.basic.pass', 'change-me');
        $expectedRole = $this->envValue('ADMIN_BASIC_ROLE') ?? (string) config('admin_auth.basic.role', 'super_admin');

        if (hash_equals($expectedUser, $user) && hash_equals($expectedPass, $pass)) {
            return [
                'user' => $expectedUser,
                'role' => $expectedRole,
                'user_id' => null,
                'source' => 'legacy',
            ];
        }

        return null;
    }

    private function envValue(string $key): ?string
    {
        $fromGetEnv = getenv($key);
        if ($fromGetEnv !== false) {
            return (string) $fromGetEnv;
        }

        if (array_key_exists($key, $_ENV)) {
            return (string) $_ENV[$key];
        }

        if (array_key_exists($key, $_SERVER)) {
            return (string) $_SERVER[$key];
        }

        return null;
    }

    /**
     * @return array<int, string>
     */
    private function resolveRoleSlugs(User $record): array
    {
        $slugs = $record->roles
            ->pluck('slug')
            ->filter(fn($v) => is_string($v) && $v !== '')
            ->values()
            ->all();

        if ($slugs === []) {
            return [];
        }

        $priority = (array) config('admin_acl.role_priority', []);
        $sorted = [];
        foreach ($priority as $roleSlug) {
            if (in_array($roleSlug, $slugs, true)) {
                $sorted[] = $roleSlug;
            }
        }

        $remaining = array_values(array_diff($slugs, $sorted));
        sort($remaining, SORT_STRING);

        return array_values(array_unique(array_merge($sorted, $remaining)));
    }
}
