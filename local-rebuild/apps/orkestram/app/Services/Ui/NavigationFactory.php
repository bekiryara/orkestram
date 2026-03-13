<?php

namespace App\Services\Ui;

class NavigationFactory
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function build(string $zone, bool $authenticated, ?string $role): array
    {
        $items = (array) config("navigation.zones.{$zone}", []);
        $out = [];

        foreach ($items as $item) {
            if (!is_array($item)) {
                continue;
            }

            $guestOnly = (bool) ($item['guest_only'] ?? false);
            $authOnly = (bool) ($item['auth_only'] ?? false);
            $roles = (array) ($item['roles'] ?? []);

            if ($guestOnly && $authenticated) {
                continue;
            }
            if ($authOnly && !$authenticated) {
                continue;
            }
            if ($roles !== [] && (!is_string($role) || !in_array($role, $roles, true))) {
                continue;
            }

            $out[] = [
                'label' => (string) ($item['label'] ?? ''),
                'href' => $this->resolveHrefByRole($item, $role),
            ];
        }

        return $out;
    }

    /**
     * @param array<string, mixed> $item
     */
    private function resolveHrefByRole(array $item, ?string $role): string
    {
        $defaultHref = (string) ($item['href'] ?? '#');
        if (!is_string($role) || $role === '') {
            return $defaultHref;
        }

        $roleHref = (array) ($item['role_href'] ?? []);
        if (!array_key_exists($role, $roleHref)) {
            return $defaultHref;
        }

        return (string) $roleHref[$role];
    }
}
