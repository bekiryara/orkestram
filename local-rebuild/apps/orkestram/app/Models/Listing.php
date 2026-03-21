<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Listing extends Model
{
    public const SIMPLE_PRICE_TYPES = [
        'fixed',
        'starting_from',
        'range',
        'hourly',
        'daily',
    ];

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
        'price_min',
        'price_max',
        'currency',
        'price_type',
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
        'price_min' => 'decimal:2',
        'price_max' => 'decimal:2',
        'published_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $listing): void {
            $request = request();
            if (!$request) {
                return;
            }

            $priceLabelInput = $request->input('price_label');
            $priceMinInput = $request->input('price_min');
            $priceMaxInput = $request->input('price_max');
            $currencyInput = $request->input('currency');
            $priceTypeInput = $request->input('price_type');

            $hasStructuredInput = self::normalizeText($priceMinInput) !== null
                || self::normalizeText($priceMaxInput) !== null
                || self::normalizeText($currencyInput) !== null
                || self::normalizeText($priceTypeInput) !== null;
            $hasPriceLabelInput = self::normalizeText($priceLabelInput) !== null;
            if (!$hasStructuredInput && !$hasPriceLabelInput) {
                return;
            }

            if ($hasStructuredInput) {
                $listing->price_min = self::normalizeMoney($priceMinInput);
                $listing->price_max = self::normalizeMoney($priceMaxInput);
                $listing->price_type = self::normalizePriceType($priceTypeInput);

                if ($listing->price_type !== 'range') {
                    $listing->price_max = null;
                }

                $currency = self::normalizeCurrency($currencyInput);
                if ($currency === null && ($listing->price_min !== null || $listing->price_max !== null)) {
                    $currency = 'TRY';
                }
                $listing->currency = $currency;

                $listing->price_label = self::buildPriceLabel(
                    $listing->price_min !== null ? (float) $listing->price_min : null,
                    $listing->price_max !== null ? (float) $listing->price_max : null,
                    self::normalizeCurrency($listing->currency),
                    self::normalizePriceType($listing->price_type)
                ) ?? self::normalizeText($priceLabelInput) ?? self::normalizeText($listing->price_label);

                return;
            }

            if ($hasPriceLabelInput) {
                $listing->price_label = self::normalizeText($priceLabelInput);
            }
        });
    }

    public static function simplePriceTypes(): array
    {
        return self::SIMPLE_PRICE_TYPES;
    }

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

    public function publicPriceValue(): ?float
    {
        if ($this->price_min !== null) {
            return (float) $this->price_min;
        }

        if ($this->price_max !== null) {
            return (float) $this->price_max;
        }

        return null;
    }

    public function publicPriceCurrency(): string
    {
        return self::normalizeCurrency($this->currency) ?? 'TRY';
    }

    public function displayPriceLabel(string $fallback = 'Iletisim ile netlesir'): string
    {
        $built = self::buildPriceLabel(
            $this->price_min !== null ? (float) $this->price_min : null,
            $this->price_max !== null ? (float) $this->price_max : null,
            self::normalizeCurrency($this->currency),
            self::normalizePriceType($this->price_type)
        );

        if ($built !== null) {
            return $built;
        }

        $label = self::normalizeText($this->price_label);
        if ($label !== null) {
            return $label;
        }

        return $fallback;
    }

    private static function normalizeText(mixed $value): ?string
    {
        $normalized = trim((string) ($value ?? ''));
        return $normalized === '' ? null : $normalized;
    }

    private static function normalizeMoney(mixed $value): ?float
    {
        if ($value === null) {
            return null;
        }

        $raw = trim((string) $value);
        if ($raw === '') {
            return null;
        }

        $raw = str_replace([' ', ','], ['', '.'], $raw);
        if (!is_numeric($raw)) {
            return null;
        }

        $amount = (float) $raw;
        if ($amount < 0) {
            return null;
        }

        return round($amount, 2);
    }

    private static function normalizeCurrency(mixed $value): ?string
    {
        $raw = self::normalizeText($value);
        if ($raw === null) {
            return null;
        }

        $currency = strtoupper($raw);
        if (!preg_match('/^[A-Z]{3}$/', $currency)) {
            return null;
        }

        return $currency;
    }

    private static function normalizePriceType(mixed $value): ?string
    {
        $raw = self::normalizeText($value);
        if ($raw === null) {
            return null;
        }

        return in_array($raw, self::simplePriceTypes(), true) ? $raw : null;
    }

    private static function buildPriceLabel(?float $min, ?float $max, ?string $currency, ?string $priceType): ?string
    {
        if ($min === null && $max === null) {
            return null;
        }

        $currency = $currency ?? 'TRY';
        $minText = $min !== null ? self::formatMoney($min) : null;
        $maxText = $max !== null ? self::formatMoney($max) : null;

        if ($priceType === 'starting_from' && $minText !== null) {
            return $minText . ' ' . $currency . " 'den baslar";
        }

        if ($priceType === 'hourly' && $minText !== null) {
            return $minText . ' ' . $currency . ' / saat';
        }

        if ($priceType === 'daily' && $minText !== null) {
            return $minText . ' ' . $currency . ' / gun';
        }

        if ($minText !== null && $maxText !== null) {
            return $minText . ' - ' . $maxText . ' ' . $currency;
        }

        if ($minText !== null) {
            return $minText . ' ' . $currency;
        }

        return $maxText !== null ? ($maxText . ' ' . $currency) : null;
    }

    private static function formatMoney(float $value): string
    {
        $formatted = number_format($value, 2, '.', '');
        $formatted = rtrim(rtrim($formatted, '0'), '.');
        return $formatted;
    }
}
