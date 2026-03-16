<?php

namespace Tests\Feature;

use App\Models\Listing;
use App\Models\MessageConversation;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PortalProfileMediaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        putenv('ADMIN_ACCOUNTS_JSON=');
        $_ENV['ADMIN_ACCOUNTS_JSON'] = '';
        $_SERVER['ADMIN_ACCOUNTS_JSON'] = '';

        putenv('ADMIN_DB_AUTH_ENABLED=true');
        $_ENV['ADMIN_DB_AUTH_ENABLED'] = 'true';
        $_SERVER['ADMIN_DB_AUTH_ENABLED'] = 'true';
    }

    public function test_profile_update_stores_avatar_under_storage_uploads_and_replaces_legacy_profile_photo(): void
    {
        Storage::fake('public');
        [$customer] = $this->createUsersAndRoles();
        $customer->update(['profile_photo_path' => 'profile-photos/old-avatar.jpg']);
        Storage::disk('public')->put('profile-photos/old-avatar.jpg', 'old-avatar');

        $this->withServerVariables($this->izmirHost())->post('/giris', [
            'username' => $customer->username,
            'password' => 'customer-pass',
        ])->assertRedirect('/hesabim');

        $this->withServerVariables($this->izmirHost())->post('/hesabim/profil', [
            'name' => $customer->name,
            'username' => $customer->username,
            'email' => $customer->email,
            'phone' => '',
            'city' => '',
            'district' => '',
            'profile_photo' => UploadedFile::fake()->create('avatar.jpg', 120, 'image/jpeg'),
        ])->assertRedirect('/hesabim?tab=profile');

        $customer->refresh();
        $this->assertStringStartsWith('storage/uploads/profile-photos/user-' . $customer->id . '/', (string) $customer->profile_photo_path);
        Storage::disk('public')->assertMissing('profile-photos/old-avatar.jpg');
        Storage::disk('public')->assertExists(str_replace('storage/', '', (string) $customer->profile_photo_path));
    }

    public function test_message_center_renders_legacy_profile_photo_url_when_legacy_file_exists(): void
    {
        Storage::fake('public');
        [$customer, $owner] = $this->createUsersAndRoles();
        $owner->update(['profile_photo_path' => 'profile-photos/owner-legacy.jpg']);
        Storage::disk('public')->put('profile-photos/owner-legacy.jpg', 'avatar');
        $listing = Listing::query()->create([
            'site' => 'izmirorkestra.net',
            'owner_user_id' => $owner->id,
            'slug' => 'profil-legacy-ilan',
            'name' => 'Profil Legacy Ilan',
            'status' => 'published',
        ]);
        MessageConversation::query()->create([
            'site' => 'izmirorkestra.net',
            'listing_id' => $listing->id,
            'owner_user_id' => $owner->id,
            'customer_user_id' => $customer->id,
            'status' => 'active',
            'last_message_at' => now(),
            'last_message_preview' => 'Merhaba',
        ]);

        $this->withServerVariables($this->izmirHost())->post('/giris', [
            'username' => $customer->username,
            'password' => 'customer-pass',
        ])->assertRedirect('/hesabim');

        $this->withServerVariables($this->izmirHost())->followingRedirects()->get('/messages?box=personal')
            ->assertOk()
            ->assertSee('/storage/profile-photos/owner-legacy.jpg', false);
    }

    public function test_message_center_hides_missing_profile_image_and_keeps_text_fallback(): void
    {
        [$customer, $owner] = $this->createUsersAndRoles();
        $owner->update(['profile_photo_path' => 'storage/uploads/profile-photos/missing-avatar.jpg']);
        $listing = Listing::query()->create([
            'site' => 'izmirorkestra.net',
            'owner_user_id' => $owner->id,
            'slug' => 'profil-missing-ilan',
            'name' => 'Profil Missing Ilan',
            'status' => 'published',
        ]);
        MessageConversation::query()->create([
            'site' => 'izmirorkestra.net',
            'listing_id' => $listing->id,
            'owner_user_id' => $owner->id,
            'customer_user_id' => $customer->id,
            'status' => 'active',
            'last_message_at' => now(),
            'last_message_preview' => 'Merhaba',
        ]);

        $this->withServerVariables($this->izmirHost())->post('/giris', [
            'username' => $customer->username,
            'password' => 'customer-pass',
        ])->assertRedirect('/hesabim');

        $this->withServerVariables($this->izmirHost())->followingRedirects()->get('/messages?box=personal')
            ->assertOk()
            ->assertDontSee('/storage/uploads/profile-photos/missing-avatar.jpg', false)
            ->assertSee($owner->name);
    }

    private function createUsersAndRoles(): array
    {
        $customerRole = Role::query()->create([
            'slug' => 'customer',
            'name' => 'Customer',
            'is_active' => true,
        ]);
        $ownerRole = Role::query()->create([
            'slug' => 'listing_owner',
            'name' => 'Listing Owner',
            'is_active' => true,
        ]);

        $customer = User::query()->create([
            'name' => 'Customer User',
            'username' => 'customer-media-izmir',
            'email' => 'customer-media-izmir@example.test',
            'password' => Hash::make('customer-pass'),
            'is_active' => true,
        ]);
        $customer->roles()->attach($customerRole->id);

        $owner = User::query()->create([
            'name' => 'Owner User',
            'username' => 'owner-media-izmir',
            'email' => 'owner-media-izmir@example.test',
            'password' => Hash::make('owner-pass'),
            'is_active' => true,
        ]);
        $owner->roles()->attach($ownerRole->id);

        return [$customer, $owner];
    }
}




