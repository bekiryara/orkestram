<?php

namespace Tests\Feature;

use App\Models\Listing;
use App\Models\ListingFeedback;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ListingFeedbackFlowTest extends TestCase
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

    public function test_customer_can_like_only_once_per_listing(): void
    {
        [$customer] = $this->createUsersAndRoles();
        $listing = Listing::query()->create([
            'site' => 'orkestram.net',
            'slug' => 'grup-moda',
            'name' => 'Grup Moda',
            'status' => 'published',
        ]);

        $this->post('/giris', [
            'username' => $customer->username,
            'password' => 'customer-pass',
        ])->assertRedirect('/hesabim');

        $this->get('/customer/feedbacks')->assertRedirect('/hesabim?tab=comments');

        $this->postJson('/customer/feedbacks/like', [
            'listing_slug' => 'grup-moda',
        ])->assertCreated()->assertJson([
            'ok' => true,
            'created' => true,
            'likes_count' => 1,
        ]);

        $this->postJson('/customer/feedbacks/like', [
            'listing_slug' => 'grup-moda',
        ])->assertOk()->assertJson([
            'ok' => true,
            'created' => false,
            'likes_count' => 1,
        ]);

        $this->assertDatabaseCount('listing_likes', 1);
        $this->assertDatabaseHas('listing_likes', [
            'listing_id' => $listing->id,
            'user_id' => $customer->id,
        ]);
    }

    public function test_customer_can_create_message_and_owner_can_answer(): void
    {
        [$customer, $owner] = $this->createUsersAndRoles();
        $listing = Listing::query()->create([
            'site' => 'orkestram.net',
            'owner_user_id' => $owner->id,
            'slug' => 'owner-servis',
            'name' => 'Owner Servis',
            'status' => 'published',
        ]);

        $this->post('/giris', [
            'username' => $customer->username,
            'password' => 'customer-pass',
        ])->assertRedirect('/hesabim');

        $create = $this->postJson('/customer/feedbacks', [
            'listing_slug' => 'owner-servis',
            'kind' => 'message',
            'content' => 'Merhaba, fiyat alabilir miyim?',
        ])->assertCreated()->json();

        $feedbackId = (int) ($create['feedback_id'] ?? 0);
        $this->assertTrue($feedbackId > 0);

        $this->post('/cikis')->assertRedirect('/giris');

        $this->post('/giris', [
            'username' => $owner->username,
            'password' => 'owner-pass',
        ])->assertRedirect('/hesabim');

        $this->get('/owner/feedbacks')->assertOk()->assertSee('Ilan Mesajlari');

        $this->getJson('/owner/feedbacks')
            ->assertOk()
            ->assertJsonPath('data.0.id', $feedbackId)
            ->assertJsonPath('data.0.visibility', 'private');

        $this->postJson('/owner/feedbacks/' . $feedbackId . '/status', [
            'status' => 'answered',
            'owner_reply' => 'Merhaba, butcenize gore paket sunabiliriz.',
        ])->assertOk()->assertJson([
            'ok' => true,
            'status' => 'answered',
        ]);

        $this->assertDatabaseHas('listing_feedback', [
            'id' => $feedbackId,
            'listing_id' => $listing->id,
            'status' => 'answered',
            'answered_by_user_id' => $owner->id,
        ]);
    }

    public function test_customer_html_form_submission_redirects_with_flash_message(): void
    {
        [$customer, $owner] = $this->createUsersAndRoles();
        Listing::query()->create([
            'site' => 'orkestram.net',
            'owner_user_id' => $owner->id,
            'slug' => 'form-ilan',
            'name' => 'Form Ilan',
            'status' => 'published',
        ]);

        $this->post('/giris', [
            'username' => $customer->username,
            'password' => 'customer-pass',
        ])->assertRedirect('/hesabim');

        $this->post('/customer/feedbacks', [
            'listing_slug' => 'form-ilan',
            'kind' => 'comment',
            'content' => 'Takvim uygunlugu yorumu',
            'visibility' => 'public',
        ])->assertRedirect();

        $this->assertDatabaseHas('listing_feedback', [
            'kind' => 'comment',
            'content' => 'Takvim uygunlugu yorumu',
            'status' => 'pending',
        ]);
    }

    public function test_owner_html_status_update_redirects_with_flash_message(): void
    {
        [$customer, $owner] = $this->createUsersAndRoles();
        $listing = Listing::query()->create([
            'site' => 'orkestram.net',
            'owner_user_id' => $owner->id,
            'slug' => 'owner-form-ilan',
            'name' => 'Owner Form Ilan',
            'status' => 'published',
        ]);

        $feedback = ListingFeedback::query()->create([
            'site' => 'orkestram.net',
            'listing_id' => $listing->id,
            'user_id' => $customer->id,
            'kind' => 'comment',
            'visibility' => 'public',
            'status' => 'pending',
            'content' => 'Harika ekip.',
        ]);

        $this->post('/giris', [
            'username' => $owner->username,
            'password' => 'owner-pass',
        ])->assertRedirect('/hesabim');

        $this->post('/owner/feedbacks/' . $feedback->id . '/status', [
            'status' => 'approved',
            'owner_reply' => 'Tesekkurler.',
        ])->assertRedirect();

        $this->assertDatabaseHas('listing_feedback', [
            'id' => $feedback->id,
            'status' => 'approved',
            'owner_reply' => 'Tesekkurler.',
        ]);
    }

    public function test_public_listing_shows_only_approved_public_comments(): void
    {
        [$customer, $owner] = $this->createUsersAndRoles();
        $listing = Listing::query()->create([
            'site' => 'orkestram.net',
            'owner_user_id' => $owner->id,
            'slug' => 'yorum-gorunurluk-ilan',
            'name' => 'Yorum Gorunurluk Ilan',
            'status' => 'published',
        ]);

        ListingFeedback::query()->create([
            'site' => 'orkestram.net',
            'listing_id' => $listing->id,
            'user_id' => $customer->id,
            'kind' => 'comment',
            'visibility' => 'public',
            'status' => 'approved',
            'content' => 'Onayli yorum icerigi',
        ]);

        ListingFeedback::query()->create([
            'site' => 'orkestram.net',
            'listing_id' => $listing->id,
            'user_id' => $customer->id,
            'kind' => 'comment',
            'visibility' => 'public',
            'status' => 'rejected',
            'content' => 'Reddedilen yorum icerigi',
        ]);

        $this->get('/ilan/' . $listing->slug)
            ->assertOk()
            ->assertSee('Yorumlar')
            ->assertSee('Onayli yorum icerigi')
            ->assertDontSee('Reddedilen yorum icerigi');
    }

    public function test_owner_comment_screen_shows_sender_name(): void
    {
        [$customer, $owner] = $this->createUsersAndRoles();
        $listing = Listing::query()->create([
            'site' => 'orkestram.net',
            'owner_user_id' => $owner->id,
            'slug' => 'owner-yorum-listesi',
            'name' => 'Owner Yorum Listesi',
            'status' => 'published',
        ]);

        ListingFeedback::query()->create([
            'site' => 'orkestram.net',
            'listing_id' => $listing->id,
            'user_id' => $customer->id,
            'kind' => 'comment',
            'visibility' => 'public',
            'status' => 'pending',
            'content' => 'Owner panel yorum kaydi',
        ]);

        $this->post('/giris', [
            'username' => $owner->username,
            'password' => 'owner-pass',
        ])->assertRedirect('/hesabim');

        $this->get('/owner/feedbacks?kind=comment')
            ->assertOk()
            ->assertSee('Gonderen')
            ->assertSee($customer->name)
            ->assertSee('Owner panel yorum kaydi');
    }

    /**
     * @return array{0: User, 1: User}
     */
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
            'username' => 'customer-eng',
            'email' => 'customer-eng@example.test',
            'password' => Hash::make('customer-pass'),
            'is_active' => true,
        ]);
        $customer->roles()->attach($customerRole->id);

        $owner = User::query()->create([
            'name' => 'Owner User',
            'username' => 'owner-eng',
            'email' => 'owner-eng@example.test',
            'password' => Hash::make('owner-pass'),
            'is_active' => true,
        ]);
        $owner->roles()->attach($ownerRole->id);

        return [$customer, $owner];
    }
}

