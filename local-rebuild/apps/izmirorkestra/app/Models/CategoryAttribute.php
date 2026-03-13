<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CategoryAttribute extends Model
{
    protected $fillable = [
        'category_id',
        'key',
        'label',
        'field_type',
        'filter_mode',
        'options_json',
        'is_required',
        'is_filterable',
        'is_visible_in_card',
        'is_visible_in_detail',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'options_json' => 'array',
        'is_required' => 'boolean',
        'is_filterable' => 'boolean',
        'is_visible_in_card' => 'boolean',
        'is_visible_in_detail' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function values(): HasMany
    {
        return $this->hasMany(ListingAttributeValue::class);
    }
}
