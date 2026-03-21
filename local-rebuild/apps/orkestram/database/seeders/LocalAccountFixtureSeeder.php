<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class LocalAccountFixtureSeeder extends Seeder
{
    /**
     * @return array<int, array{username: string, role: string}>
     */
    public function run(): array
    {
        $accounts = [
            [
                'role' => 'super_admin',
                'name' => 'Local Admin',
                'username' => 'local-admin',
                'email' => 'local-admin@example.test',
                'password' => 'local-admin-pass',
                'city' => 'Izmir',
                'district' => 'Konak',
            ],
            [
                'role' => 'listing_owner',
                'name' => 'Local Owner',
                'username' => 'local-owner',
                'email' => 'local-owner@example.test',
                'password' => 'local-owner-pass',
                'city' => 'Izmir',
                'district' => 'Konak',
                'company_name' => 'Local Owner Demo',
                'service_area' => 'Izmir / Konak',
                'short_bio' => 'Deterministic local owner fixture hesabi.',
                'provided_services' => 'Dugun Muzik, Bando',
            ],
            [
                'role' => 'customer',
                'name' => 'Local Customer',
                'username' => 'local-customer',
                'email' => 'local-customer@example.test',
                'password' => 'local-customer-pass',
                'city' => 'Izmir',
                'district' => 'Bornova',
            ],
            [
                'role' => 'support_agent',
                'name' => 'Local Support',
                'username' => 'local-support',
                'email' => 'local-support@example.test',
                'password' => 'local-support-pass',
                'city' => 'Izmir',
                'district' => 'Karsiyaka',
            ],
        ];

        $result = [];

        foreach ($accounts as $account) {
            $role = Role::query()
                ->where('slug', $account['role'])
                ->where('is_active', true)
                ->first();

            if ($role === null) {
                continue;
            }

            $user = User::query()->updateOrCreate(
                ['username' => $account['username']],
                [
                    'name' => $account['name'],
                    'email' => $account['email'],
                    'password' => Hash::make($account['password']),
                    'city' => $account['city'] ?? null,
                    'district' => $account['district'] ?? null,
                    'company_name' => $account['company_name'] ?? null,
                    'service_area' => $account['service_area'] ?? null,
                    'short_bio' => $account['short_bio'] ?? null,
                    'provided_services' => $account['provided_services'] ?? null,
                    'is_active' => true,
                ]
            );

            DB::table('role_user')->where('user_id', $user->id)->delete();
            DB::table('role_user')->updateOrInsert(
                ['role_id' => $role->id, 'user_id' => $user->id],
                ['created_at' => now(), 'updated_at' => now()]
            );

            $result[] = [
                'username' => $user->username,
                'role' => $role->slug,
            ];
        }

        return $result;
    }
}
