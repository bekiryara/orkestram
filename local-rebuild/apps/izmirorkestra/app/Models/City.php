<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class City extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'sort_order',
    ];

    public function districts(): HasMany
    {
        return $this->hasMany(District::class)->orderBy('sort_order')->orderBy('name');
    }
}

