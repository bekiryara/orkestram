<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerRequest extends Model
{
    protected $fillable = [
        'user_id',
        'listing_id',
        'site',
        'name',
        'phone',
        'email',
        'message',
        'status',
        'internal_note',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }
}
