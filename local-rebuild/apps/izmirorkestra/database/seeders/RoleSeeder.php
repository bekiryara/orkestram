<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['slug' => 'super_admin', 'name' => 'Super Admin'],
            ['slug' => 'admin', 'name' => 'Admin'],
            ['slug' => 'content_editor', 'name' => 'Content Editor'],
            ['slug' => 'listing_editor', 'name' => 'Listing Editor'],
            ['slug' => 'listing_owner', 'name' => 'Listing Owner'],
            ['slug' => 'customer', 'name' => 'Customer'],
            ['slug' => 'support_agent', 'name' => 'Support Agent'],
            ['slug' => 'viewer', 'name' => 'Viewer'],
        ];

        foreach ($roles as $role) {
            Role::query()->updateOrCreate(
                ['slug' => $role['slug']],
                ['name' => $role['name'], 'is_active' => true]
            );
        }
    }
}
