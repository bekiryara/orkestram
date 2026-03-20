<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class LocalAccountFixtureCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_local_account_fixture_command_creates_deterministic_users(): void
    {
        $this->seedRoles();

        $exitCode = Artisan::call('local:prepare-account-fixture');

        $this->assertSame(0, $exitCode, Artisan::output());
        $this->assertDatabaseHas('users', ['username' => 'local-admin', 'is_active' => true]);
        $this->assertDatabaseHas('users', ['username' => 'local-owner', 'is_active' => true]);
        $this->assertDatabaseHas('users', ['username' => 'local-customer', 'is_active' => true]);
        $this->assertDatabaseHas('users', ['username' => 'local-support', 'is_active' => true]);

        $owner = User::query()->where('username', 'local-owner')->firstOrFail();
        $this->assertSame(['listing_owner'], $owner->roles()->pluck('slug')->all());

        $secondExitCode = Artisan::call('local:prepare-account-fixture');

        $this->assertSame(0, $secondExitCode, Artisan::output());
        $this->assertSame(4, User::query()->count());
    }

    public function test_local_prepare_reset_recovery_runs_account_layer_without_resetting_database(): void
    {
        $this->seedRoles();

        User::query()->create([
            'name' => 'Keep Me',
            'username' => 'keep-me',
            'email' => 'keep-me@example.test',
            'password' => 'secret',
            'is_active' => true,
        ]);

        $exitCode = Artisan::call('local:prepare-reset-recovery');

        $this->assertSame(0, $exitCode, Artisan::output());
        $this->assertDatabaseHas('users', ['username' => 'keep-me']);
        $this->assertDatabaseHas('users', ['username' => 'local-admin']);
        $this->assertDatabaseHas('users', ['username' => 'local-owner']);
        $this->assertDatabaseHas('users', ['username' => 'local-customer']);
        $this->assertDatabaseHas('users', ['username' => 'local-support']);
        $this->assertSame(5, User::query()->count());
    }

    private function seedRoles(): void
    {
        Role::insert([
            ['slug' => 'super_admin', 'name' => 'Super Admin', 'is_active' => true],
            ['slug' => 'listing_owner', 'name' => 'Listing Owner', 'is_active' => true],
            ['slug' => 'customer', 'name' => 'Customer', 'is_active' => true],
            ['slug' => 'support_agent', 'name' => 'Support Agent', 'is_active' => true],
        ]);
    }
}
