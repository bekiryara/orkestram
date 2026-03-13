<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ListingAttributeValue extends Model
{
    protected $fillable = [
        'listing_id',
        'category_attribute_id',
        'value_text',
        'value_number',
        'value_bool',
        'value_json',
        'normalized_value',
    ];

    protected $casts = [
        'value_number' => 'decimal:2',
        'value_bool' => 'boolean',
        'value_json' => 'array',
    ];

    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }

    public function attribute(): BelongsTo
    {
        return $this->belongsTo(CategoryAttribute::class, 'category_attribute_id');
    }
}

