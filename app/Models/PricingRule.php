<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PricingRule extends Model
{
    public const TYPES = [
        'peak_season',
        'low_season',
        'early_bird',
        'last_minute',
        'custom',
    ];

    protected $fillable = [
        'vehicle_id',
        'start_date',
        'end_date',
        'discount_percentage',
        'type',
        'description',
        'active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'discount_percentage' => 'integer',
        'active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saving(function (PricingRule $pricingRule) {
            $validator = Validator::make($pricingRule->attributesToArray(), [
                'vehicle_id' => ['required', 'integer', 'exists:vehicles,id'],
                'start_date' => ['required', 'date'],
                'end_date' => ['required', 'date', 'after_or_equal:start_date'],
                'discount_percentage' => ['required', 'integer', 'between:0,100'],
                'type' => ['required', 'string', Rule::in(self::TYPES)],
                'description' => ['nullable', 'string', 'max:255'],
                'active' => ['nullable', 'boolean'],
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }
        });
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', true);
    }

    public function scopeOverlappingRange(Builder $query, $startDate, $endDate): Builder
    {
        $start = Carbon::parse($startDate)->toDateString();
        $end = Carbon::parse($endDate)->toDateString();

        return $query->whereDate('start_date', '<=', $end)
            ->whereDate('end_date', '>=', $start);
    }
}