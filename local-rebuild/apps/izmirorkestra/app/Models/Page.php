<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $fillable = [
        'site',
        'slug',
        'title',
        'template',
        'status',
        'excerpt',
        'content',
        'seo_title',
        'seo_description',
        'canonical_url',
        'schema_json',
        'published_at',
    ];

    protected $casts = [
        'schema_json' => 'array',
        'published_at' => 'datetime',
    ];
}
