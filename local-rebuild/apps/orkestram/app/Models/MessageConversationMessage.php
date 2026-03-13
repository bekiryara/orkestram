<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MessageConversationMessage extends Model
{
    protected $table = 'message_conversation_messages';

    protected $fillable = [
        'conversation_id',
        'sender_user_id',
        'sender_role',
        'body',
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(MessageConversation::class, 'conversation_id');
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_user_id');
    }
}
