<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RedirectRule extends Model
{
    protected $table = 'redirects';

    protected $fillable = [
        'site',
        'old_path',
        'new_url',
        'http_code',
        'is_active',
        'note',
    ];
}
