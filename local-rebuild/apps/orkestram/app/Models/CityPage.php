<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CityPage extends Model
{
    protected $fillable = [
        'site',
        'slug',
        'city',
        'district',
        'service_slug',
        'title',
        'status',
        'content',
        'seo_title',
        'seo_description',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];
}
