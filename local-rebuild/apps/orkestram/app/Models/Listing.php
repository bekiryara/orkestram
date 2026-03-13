<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Listing extends Model
{
    protected $fillable = [
        'site',
        'owner_user_id',
        'site_scope',
        'coverage_mode',
        'slug',
        'name',
        'status',
        'category_id',
        'city_id',
        'district_id',
        'neighborhood_id',
        'city',
        'district',
        'avenue_name',
        'street_name',
        'building_no',
        'unit_no',
        'address_note',
        'service_type',
        'price_label',
        'cover_image_path',
        'gallery_json',
        'whatsapp',
        'phone',
        'features_json',
        'summary',
        'content',
        'seo_title',
        'seo_description',
        'meta_json',
        'published_at',
    ];

    protected $casts = [
        'meta_json' => 'array',
        'gallery_json' => 'array',
        'features_json' => 'array',
        'published_at' => 'datetime',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    public function cityRef(): BelongsTo
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function districtRef(): BelongsTo
    {
        return $this->belongsTo(District::class, 'district_id');
    }

    public function neighborhoodRef(): BelongsTo
    {
        return $this->belongsTo(Neighborhood::class, 'neighborhood_id');
    }

    public function serviceAreas(): HasMany
    {
        return $this->hasMany(ListingServiceArea::class)->orderBy('city')->orderBy('district');
    }

    public function likes(): HasMany
    {
        return $this->hasMany(ListingLike::class);
    }

    public function feedback(): HasMany
    {
        return $this->hasMany(ListingFeedback::class);
    }

    public function attributeValues(): HasMany
    {
        return $this->hasMany(ListingAttributeValue::class);
    }

    public function scopeVisibleForSite(Builder $query, string $site): Builder
    {
        return $query->where(function (Builder $q) use ($site) {
            $q->where('site', $site)->orWhere('site_scope', 'both');
        });
    }

    public function scopeMatchCoverage(
        Builder $query,
        ?string $city,
        ?string $district = null,
        ?int $cityId = null,
        ?int $districtId = null
    ): Builder
    {
        $city = trim((string) $city);
        $district = trim((string) $district);
        $cityId = (int) ($cityId ?? 0);
        $districtId = (int) ($districtId ?? 0);

        if ($city === '' && $cityId <= 0) {
            return $query;
        }

        return $query->where(function (Builder $outer) use ($city, $district, $cityId, $districtId) {
            $outer->where(function (Builder $q) use ($city, $district, $cityId, $districtId) {
                $q->whereIn('coverage_mode', ['location_only', 'hybrid'])
                    ->where(function (Builder $cq) use ($city, $cityId) {
                        if ($cityId > 0) {
                            $cq->where('city_id', $cityId);
                            if ($city !== '') {
                                $cq->orWhere('city', $city);
                            }
                            return;
                        }

                        $cq->where('city', $city);
                    });

                if ($district !== '' || $districtId > 0) {
                    $q->where(function (Builder $dq) use ($district, $districtId) {
                        if ($districtId > 0) {
                            $dq->where('district_id', $districtId);
                            if ($district !== '') {
                                $dq->orWhere('district', $district);
                            }
                            return;
                        }

                        $dq->where('district', $district);
                    });
                }
            })->orWhere(function (Builder $q) use ($city, $district, $cityId, $districtId) {
                $q->whereIn('coverage_mode', ['service_area_only', 'hybrid'])
                    ->whereHas('serviceAreas', function (Builder $sq) use ($city, $district, $cityId, $districtId) {
                        $sq->where(function (Builder $cq) use ($city, $cityId) {
                            if ($cityId > 0) {
                                $cq->where('city_id', $cityId);
                                if ($city !== '') {
                                    $cq->orWhere('city', $city);
                                }
                                return;
                            }

                            $cq->where('city', $city);
                        });

                        if ($district !== '' || $districtId > 0) {
                            $sq->where(function (Builder $dq) use ($district, $districtId) {
                                if ($districtId > 0) {
                                    $dq->where('district_id', $districtId);
                                    if ($district !== '') {
                                        $dq->orWhere('district', $district);
                                    }
                                    return;
                                }

                                $dq->where('district', $district);
                            });
                        }
                    });
            });
        });
    }

    public function serviceAreasText(): string
    {
        $areas = $this->relationLoaded('serviceAreas')
            ? $this->serviceAreas
            : $this->serviceAreas()->get(['city', 'district']);

        return $areas
            ->map(function (ListingServiceArea $area): string {
                $city = trim((string) $area->city);
                $district = trim((string) $area->district);
                return $district === '' ? $city : ($city . ' / ' . $district);
            })
            ->filter(fn(string $line) => $line !== '')
            ->implode("\n");
    }
}
