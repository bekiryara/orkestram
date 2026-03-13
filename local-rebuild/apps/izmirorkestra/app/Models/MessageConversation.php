<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MessageConversation extends Model
{
    protected $fillable = [
        'site',
        'listing_id',
        'owner_user_id',
        'customer_user_id',
        'status',
        'last_message_at',
        'last_message_preview',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
    ];

    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_user_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(MessageConversationMessage::class, 'conversation_id');
    }
}
