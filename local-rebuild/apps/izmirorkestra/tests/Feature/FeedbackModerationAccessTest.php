<?php

namespace Tests\Feature;

use App\Models\Listing;
use App\Models\ListingFeedback;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class FeedbackModerationAccessTest extends TestCase
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

    public function test_legacy_engagement_moderation_routes_are_not_available(): void
    {
        [$customer, $support, $admin] = $this->seedActors();

        $listing = Listing::query()->create([
            'site' => 'orkestram.net',
            'slug' => 'mod-listing',
            'name' => 'Moderation Listing',
            'status' => 'published',
        ]);

        $feedback = ListingFeedback::query()->create([
            'site' => 'orkestram.net',
            'listing_id' => $listing->id,
            'user_id' => $customer->id,
            'kind' => 'comment',
            'visibility' => 'public',
            'status' => 'pending',
            'content' => 'Yorum kaydi',
        ]);

        $this->post('/giris', [
            'username' => $support->username,
            'password' => 'support-pass',
        ])->assertRedirect('/hesabim');

        $this->get('/support/engagements')->assertNotFound();
        $this->post('/support/engagements/' . $feedback->id . '/status', [
            'status' => 'approved',
            'owner_reply' => 'Support notu',
        ])->assertStatus(405);
        $this->post('/cikis')->assertRedirect('/giris');

        $this->post('/giris', [
            'username' => $customer->username,
            'password' => 'customer-pass',
        ])->assertRedirect('/hesabim');

        $this->get('/support/engagements')->assertNotFound();
        $this->get('/admin/engagements')->assertNotFound();
        $this->post('/cikis')->assertRedirect('/giris');

        $this->post('/giris', [
            'username' => $admin->username,
            'password' => 'admin-pass',
        ])->assertRedirect('/admin/pages');

        $this->get('/admin/engagements')->assertNotFound();
        $this->post('/admin/engagements/' . $feedback->id . '/status', [
            'status' => 'answered',
            'owner_reply' => 'Admin yaniti',
        ])->assertStatus(405);
    }

    public function test_admin_can_access_feedback_panel_and_update_comment_status(): void
    {
        [$customer, $support, $admin] = $this->seedActors();

        $listing = Listing::query()->create([
            'site' => 'orkestram.net',
            'slug' => 'admin-feedback-listing',
            'name' => 'Admin Feedback Listing',
            'status' => 'published',
        ]);

        $feedback = ListingFeedback::query()->create([
            'site' => 'orkestram.net',
            'listing_id' => $listing->id,
            'user_id' => $customer->id,
            'kind' => 'comment',
            'visibility' => 'public',
            'status' => 'pending',
            'content' => 'Admin panel yorum kaydi',
        ]);

        $this->post('/giris', [
            'username' => $support->username,
            'password' => 'support-pass',
        ])->assertRedirect('/hesabim');
        $this->get('/admin/feedbacks')->assertForbidden();
        $this->post('/cikis')->assertRedirect('/giris');

        $this->post('/giris', [
            'username' => $customer->username,
            'password' => 'customer-pass',
        ])->assertRedirect('/hesabim');
        $this->get('/admin/feedbacks')->assertForbidden();
        $this->post('/cikis')->assertRedirect('/giris');

        $this->post('/giris', [
            'username' => $admin->username,
            'password' => 'admin-pass',
        ])->assertRedirect('/admin/pages');

        $this->get('/admin/feedbacks')
            ->assertOk()
            ->assertSee('Geri Bildirimler')
            ->assertSee('Admin panel yorum kaydi');

        $this->post('/admin/feedbacks/' . $feedback->id . '/comment-status', [
            'status' => 'approved',
            'owner_reply' => 'Admin onayi',
        ])->assertRedirect();

        $this->assertDatabaseHas('listing_feedback', [
            'id' => $feedback->id,
            'status' => 'approved',
            'owner_reply' => 'Admin onayi',
            'answered_by_user_id' => null,
        ]);
    }

    /**
     * @return array{0: User, 1: User, 2: User}
     */
    private function seedActors(): array
    {
        $roles = [
            'customer' => Role::query()->create(['slug' => 'customer', 'name' => 'Customer', 'is_active' => true]),
            'support_agent' => Role::query()->create(['slug' => 'support_agent', 'name' => 'Support', 'is_active' => true]),
            'admin' => Role::query()->create(['slug' => 'admin', 'name' => 'Admin', 'is_active' => true]),
        ];

        $customer = User::query()->create([
            'name' => 'Customer',
            'username' => 'mod-customer',
            'email' => 'mod-customer@example.test',
            'password' => Hash::make('customer-pass'),
            'is_active' => true,
        ]);
        $customer->roles()->attach($roles['customer']->id);

        $support = User::query()->create([
            'name' => 'Support',
            'username' => 'mod-support',
            'email' => 'mod-support@example.test',
            'password' => Hash::make('support-pass'),
            'is_active' => true,
        ]);
        $support->roles()->attach($roles['support_agent']->id);

        $admin = User::query()->create([
            'name' => 'Admin',
            'username' => 'mod-admin',
            'email' => 'mod-admin@example.test',
            'password' => Hash::make('admin-pass'),
            'is_active' => true,
        ]);
        $admin->roles()->attach($roles['admin']->id);

        return [$customer, $support, $admin];
    }
}

