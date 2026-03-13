<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ListingFeedback extends Model
{
    protected $table = 'listing_feedback';

    protected $fillable = [
        'listing_id',
        'user_id',
        'answered_by_user_id',
        'site',
        'kind',
        'visibility',
        'status',
        'content',
        'owner_reply',
        'answered_at',
    ];

    protected $casts = [
        'answered_at' => 'datetime',
    ];

    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function answeredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'answered_by_user_id');
    }

    public function scopeForPublic(Builder $query): Builder
    {
        return $query->where('visibility', 'public')->where('status', 'approved');
    }
}
