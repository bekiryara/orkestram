<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ListingServiceArea extends Model
{
    protected $fillable = [
        'listing_id',
        'city_id',
        'district_id',
        'city',
        'district',
    ];

    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }

    public function cityRef(): BelongsTo
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function districtRef(): BelongsTo
    {
        return $this->belongsTo(District::class, 'district_id');
    }
}
