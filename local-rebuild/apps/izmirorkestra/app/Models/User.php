<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'phone',
        'city',
        'district',
        'profile_photo_path',
        'company_name',
        'service_area',
        'short_bio',
        'provided_services',
        'website_url',
        'social_links',
        'password',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class)
            ->withTimestamps();
    }

    public function ownedListings(): HasMany
    {
        return $this->hasMany(Listing::class, 'owner_user_id');
    }

    public function customerRequests(): HasMany
    {
        return $this->hasMany(CustomerRequest::class);
    }

    public function listingLikes(): HasMany
    {
        return $this->hasMany(ListingLike::class);
    }

    public function listingFeedback(): HasMany
    {
        return $this->hasMany(ListingFeedback::class);
    }
}
