<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerRequest extends Model
{
    protected $fillable = [
        'user_id',
        'listing_id',
        'site',
        'name',
        'phone',
        'email',
        'message',
        'status',
        'internal_note',
        'pricing_mode',
        'price_type',
        'price_min',
        'price_max',
        'currency',
        'price_label',
    ];

    protected $casts = [
        'price_min' => 'decimal:2',
        'price_max' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }

    public function hasPricingSnapshot(): bool
    {
        return $this->normalizeText($this->price_label) !== null
            || $this->normalizeText($this->currency) !== null
            || $this->normalizeText($this->price_type) !== null
            || $this->price_min !== null
            || $this->price_max !== null;
    }

    public function snapshotPriceLabel(string $fallback = '-'): string
    {
        $label = $this->normalizeText($this->price_label);
        if ($label !== null) {
            return $label;
        }

        $currency = $this->normalizeText($this->currency);
        $min = $this->price_min !== null ? (float) $this->price_min : null;
        $max = $this->price_max !== null ? (float) $this->price_max : null;
        $type = $this->normalizeText($this->price_type);

        if ($min === null && $max === null) {
            return $fallback;
        }

        $currency = $currency ?? 'TRY';
        $minText = $min !== null ? $this->formatMoney($min) : null;
        $maxText = $max !== null ? $this->formatMoney($max) : null;

        if ($type === 'starting_from' && $minText !== null) {
            return $minText . ' ' . $currency . " 'den baslar";
        }

        if ($type === 'hourly' && $minText !== null) {
            return $minText . ' ' . $currency . ' / saat';
        }

        if ($type === 'daily' && $minText !== null) {
            return $minText . ' ' . $currency . ' / gun';
        }

        if ($minText !== null && $maxText !== null) {
            return $minText . ' - ' . $maxText . ' ' . $currency;
        }

        if ($minText !== null) {
            return $minText . ' ' . $currency;
        }

        return $maxText !== null ? ($maxText . ' ' . $currency) : $fallback;
    }

    private function normalizeText(mixed $value): ?string
    {
        $normalized = trim((string) ($value ?? ''));
        return $normalized === '' ? null : $normalized;
    }

    private function formatMoney(float $value): string
    {
        $formatted = number_format($value, 2, '.', '');
        return rtrim(rtrim($formatted, '0'), '.');
    }
}
