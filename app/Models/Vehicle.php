<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

class Vehicle extends Model
{
    use SoftDeletes;

    public const PEAK_SEASON_MONTHS = [12, 1];
    public const LOW_SEASON_MONTHS = [7, 8];

    protected ?string $currentRentalStatusState = null;

    protected $fillable = [
        'name',
        'vehicle_type',
        'plat_number',
        'transmission',
        'year',
        'daily_price',
        'base_price',
        'weekend_multiplier',
        'peak_season_multiplier',
        'low_season_multiplier',
        'status',
        'description',
        'image',
    ];

    protected $attributes = [
        'weekend_multiplier' => 1.2,
        'peak_season_multiplier' => 1.4,
        'low_season_multiplier' => 0.8,
    ];

    protected $casts = [
        'daily_price' => 'decimal:2',
        'base_price' => 'decimal:2',
        'weekend_multiplier' => 'decimal:2',
        'peak_season_multiplier' => 'decimal:2',
        'low_season_multiplier' => 'decimal:2',
        'year' => 'integer',
    ];

    protected static function booted(): void
    {
        static::saving(function (Vehicle $vehicle) {
            $basePrice = $vehicle->base_price ?? $vehicle->daily_price;

            if ($basePrice !== null) {
                $vehicle->base_price = $basePrice;
                $vehicle->daily_price = $basePrice;
            }
        });
    }

    /**
     * Get vehicle type icon.
     */
    public function getTypeIcon(): string
    {
        return $this->vehicle_type === 'motor' ? '🏍️' : '🚗';
    }

    /**
     * Get vehicle type label.
     */
    public function getTypeLabel(): string
    {
        return ucfirst($this->vehicle_type);
    }

    /**
     * Scope for filtering by vehicle type.
     */
    public function scopeOfType($query, $type)
    {
        if ($type) {
            return $query->where('vehicle_type', $type);
        }

        return $query;
    }

    /**
     * Get all bookings for this vehicle.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Get all scheduled maintenance windows for this vehicle.
     */
    public function maintenanceSchedules(): HasMany
    {
        return $this->hasMany(MaintenanceSchedule::class);
    }

    /**
     * Get all pricing rules for this vehicle.
     */
    public function pricingRules(): HasMany
    {
        return $this->hasMany(PricingRule::class);
    }

    /**
     * Get all reviews for this vehicle.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Get approved reviews for this vehicle.
     */
    public function approvedReviews(): HasMany
    {
        return $this->reviews()->approved();
    }

    /**
     * Get the top approved review for browse-card display.
     */
    public function topApprovedReview(): HasOne
    {
        return $this->hasOne(Review::class)->ofMany([
            'helpful_count' => 'max',
            'rating' => 'max',
            'id' => 'max',
        ], function (Builder $query) {
            $query->approved();
        });
    }

    /**
     * Get the approved review count using eager-loaded aggregates when available.
     */
    public function getApprovedReviewCount(): int
    {
        if (array_key_exists('approved_reviews_count', $this->attributes)) {
            return (int) $this->attributes['approved_reviews_count'];
        }

        if ($this->relationLoaded('approvedReviews')) {
            return $this->approvedReviews->count();
        }

        return $this->approvedReviews()->count();
    }

    /**
     * Get the average rating using eager-loaded aggregates when available.
     */
    public function getAverageRatingValue(): float
    {
        $average = $this->attributes['approved_reviews_avg_rating']
            ?? ($this->relationLoaded('approvedReviews') ? $this->approvedReviews->avg('rating') : $this->approvedReviews()->avg('rating'))
            ?? 0;

        return round((float) $average, 1);
    }

    /**
     * Determine whether the vehicle already has any approved reviews.
     */
    public function hasApprovedReviews(): bool
    {
        return $this->getApprovedReviewCount() > 0;
    }

    /**
     * Get the configured base daily price.
     */
    public function getBasePriceAmount(): float
    {
        return (float) ($this->base_price ?? $this->daily_price ?? 0);
    }

    /**
     * Check whether this vehicle should currently be marked as rented.
     */
    public function shouldBeMarkedRented($referenceDate = null): bool
    {
        $referenceDate = $referenceDate
            ? Carbon::parse($referenceDate)
            : Carbon::now();

        $today = $referenceDate->toDateString();
        $currentTime = $referenceDate->format('H:i:s');

        return $this->bookings()
            ->where('status', 'confirmed')
            ->where('payment_status', 'paid')
            ->whereNull('maintenance_hold_at')
            ->where(function ($startedQuery) use ($today, $currentTime) {
                $startedQuery->whereDate('start_date', '<', $today)
                    ->orWhere(function ($sameDayQuery) use ($today, $currentTime) {
                        $sameDayQuery->whereDate('start_date', $today)
                            ->whereRaw('COALESCE(pickup_time, ?) <= ?', [Booking::DEFAULT_PICKUP_TIME, $currentTime]);
                    });
            })
            ->exists();
    }

    /**
     * Resolve the non-maintenance rental status for the current date.
     */
    public function resolveRentalStatus($referenceDate = null): string
    {
        if ($this->status === 'maintenance') {
            return 'maintenance';
        }

        return $this->shouldBeMarkedRented($referenceDate) ? 'rented' : 'available';
    }

    /**
     * Sync stored vehicle status from current booking state.
     */
    public function syncRentalStatus($referenceDate = null): bool
    {
        if ($this->status === 'maintenance') {
            return false;
        }

        $targetStatus = $this->resolveRentalStatus($referenceDate);

        if ($this->status === $targetStatus) {
            return false;
        }

        $this->update(['status' => $targetStatus]);

        return true;
    }

    /**
     * Get the current operational rental status without waiting for the sync command.
     */
    public function getCurrentRentalStatusAttribute(): string
    {
        if ($this->currentRentalStatusState !== null) {
            return $this->currentRentalStatusState;
        }

        if ($this->status === 'maintenance' || $this->hasMaintenanceOnDate(Carbon::now())) {
            return $this->currentRentalStatusState = 'maintenance';
        }

        return $this->currentRentalStatusState = $this->resolveRentalStatus();
    }

    /**
     * Determine whether a vehicle can be booked directly or via waiting list.
     *
     * @return array{available: bool, queue_available: bool, blocking_bookings: \Illuminate\Support\Collection, queue_bookings: \Illuminate\Support\Collection, maintenance_schedules: \Illuminate\Support\Collection}
     */
    public function getBookingAvailability($startDate, $endDate): array
    {
        [$startDate, $endDate] = $this->normalizeDateRange($startDate, $endDate);

        if ($this->status === 'maintenance') {
            return [
                'available' => false,
                'queue_available' => false,
                'blocking_bookings' => collect(),
                'queue_bookings' => collect(),
                'maintenance_schedules' => collect(),
            ];
        }

        $maintenanceSchedules = $this->maintenanceSchedulesForRange($startDate, $endDate);

        if ($maintenanceSchedules->isNotEmpty()) {
            return [
                'available' => false,
                'queue_available' => false,
                'blocking_bookings' => collect(),
                'queue_bookings' => collect(),
                'maintenance_schedules' => $maintenanceSchedules,
            ];
        }

        $blockingBookings = $this->bookings()
            ->blockingAvailability()
            ->overlappingRange($startDate->toDateString(), $endDate->toDateString())
            ->get(['id', 'start_date', 'end_date']);

        if ($blockingBookings->isNotEmpty()) {
            return [
                'available' => false,
                'queue_available' => false,
                'blocking_bookings' => $blockingBookings,
                'queue_bookings' => collect(),
                'maintenance_schedules' => collect(),
            ];
        }

        $queueBookings = $this->bookings()
            ->queueableAvailability()
            ->overlappingRange($startDate->toDateString(), $endDate->toDateString())
            ->get(['id', 'start_date', 'end_date']);

        if ($queueBookings->isNotEmpty()) {
            return [
                'available' => false,
                'queue_available' => true,
                'blocking_bookings' => collect(),
                'queue_bookings' => $queueBookings,
                'maintenance_schedules' => collect(),
            ];
        }

        return [
            'available' => true,
            'queue_available' => false,
            'blocking_bookings' => collect(),
            'queue_bookings' => collect(),
            'maintenance_schedules' => collect(),
        ];
    }

    /**
     * Check full daily availability for a date range.
     */
    public function getAvailabilityForDateRange($startDate, $endDate): bool
    {
        return $this->getUnavailableDates($startDate, $endDate) === [];
    }

    /**
     * Return every unavailable date in a date range.
     *
     * @return array<int, string>
     */
    public function getUnavailableDates($startDate, $endDate): array
    {
        [$startDate, $endDate] = $this->normalizeDateRange($startDate, $endDate);

        return collect($this->buildDailyStatusEntries($startDate, $endDate))
            ->filter(fn (array $entry) => $entry['status'] !== 'available')
            ->pluck('date')
            ->values()
            ->all();
    }

    /**
     * Calculate the final daily price for a specific date.
     */
    public function getPriceForDate($date): float
    {
        $date = $this->normalizeDate($date);

        return $this->buildDatePriceDetails(
            $date,
            $this->pricingRulesForRange($date, $date),
            $date
        )['final_price'];
    }

    /**
     * Check if a vehicle is available on a single date.
     */
    public function isAvailableOnDate($date): bool
    {
        $date = $this->normalizeDate($date);

        return $this->getAvailabilityForDateRange($date, $date);
    }

    /**
     * Get formatted maintenance periods.
     *
     * @return array<int, array{start_date: string, end_date: string, reason: string, notes: ?string}>
     */
    public function getMaintenancePeriods($startDate = null, $endDate = null): array
    {
        if ($startDate !== null || $endDate !== null) {
            [$startDate, $endDate] = $this->normalizeDateRange($startDate ?? $endDate, $endDate ?? $startDate);
            $schedules = $this->maintenanceSchedulesForRange($startDate, $endDate);
        } elseif ($this->relationLoaded('maintenanceSchedules')) {
            $schedules = $this->maintenanceSchedules->sortBy(fn (MaintenanceSchedule $schedule) => $schedule->start_date?->toDateString())->values();
        } else {
            $schedules = $this->maintenanceSchedules()->orderBy('start_date')->get();
        }

        return $schedules
            ->map(fn (MaintenanceSchedule $schedule) => $this->formatMaintenancePeriod($schedule))
            ->values()
            ->all();
    }

    /**
     * Get the next available date suggestion.
     */
    public function getNextAvailableDate(int $lookAheadDays = 365): ?string
    {
        if ($this->status === 'maintenance') {
            return null;
        }

        $startDate = Carbon::now()->startOfDay();
        $endDate = $startDate->copy()->addDays($lookAheadDays);
        $bookings = $this->calendarBookingsForRange($startDate, $endDate);
        $maintenanceSchedules = $this->maintenanceSchedulesForRange($startDate, $endDate);

        for ($cursor = $startDate->copy(); $cursor->lte($endDate); $cursor->addDay()) {
            if ($this->resolveDateStatus($cursor, $bookings, $maintenanceSchedules)['status'] === 'available') {
                return $cursor->toDateString();
            }
        }

        return null;
    }

    /**
     * Build calendar payload for one month.
     *
     * @return array{vehicle_id: int, month: int, year: int, dates: array<int, array<string, mixed>>, maintenance_periods: array<int, array<string, mixed>>, next_available_date: ?string}
     */
    public function getCalendarMonthData(int $month, int $year): array
    {
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        return [
            'vehicle_id' => $this->id,
            'month' => $month,
            'year' => $year,
            'dates' => $this->buildDailyStatusEntries($startDate, $endDate),
            'maintenance_periods' => $this->getMaintenancePeriods($startDate, $endDate),
            'next_available_date' => $this->getNextAvailableDate(),
        ];
    }

    /**
     * Build a detailed price breakdown for a range.
     *
     * @return array<string, mixed>
     */
    public function getPriceBreakdownForRange($startDate, $endDate): array
    {
        [$startDate, $endDate] = $this->normalizeDateRange($startDate, $endDate);
        $entries = $this->buildDailyStatusEntries($startDate, $endDate, $startDate);
        $availability = $this->getBookingAvailability($startDate->toDateString(), $endDate->toDateString());
        $subtotal = collect($entries)->sum('price_before_discount');
        $total = collect($entries)->sum('final_price');
        $durationDays = $startDate->diffInDays($endDate) + 1;

        return [
            'vehicle_id' => $this->id,
            'start_date' => $startDate->toDateString(),
            'end_date' => $endDate->toDateString(),
            'duration_days' => $durationDays,
            'daily_prices' => collect($entries)->map(fn (array $entry) => [
                'date' => $entry['date'],
                'price' => $entry['final_price'],
                'status' => $entry['status'],
            ])->values()->all(),
            'subtotal' => round((float) $subtotal, 2),
            'discount' => round((float) $subtotal - (float) $total, 2),
            'total' => round((float) $total, 2),
            'available' => $availability['available'],
            'queue_available' => $availability['queue_available'],
            'average_daily_price' => round($durationDays > 0 ? ((float) $total / $durationDays) : 0, 2),
            'unavailable_dates' => collect($entries)
                ->filter(fn (array $entry) => $entry['status'] !== 'available')
                ->pluck('date')
                ->values()
                ->all(),
        ];
    }

    /**
     * Scope vehicles that can still accept a booking request for the requested date range.
     */
    public function scopeBookableForDates(Builder $query, $startDate, $endDate): Builder
    {
        return $query->where('status', '!=', 'maintenance')
            ->whereDoesntHave('maintenanceSchedules', function (Builder $scheduleQuery) use ($startDate, $endDate) {
                $scheduleQuery->overlappingRange($startDate, $endDate);
            })
            ->whereDoesntHave('bookings', function (Builder $bookingQuery) use ($startDate, $endDate) {
                $bookingQuery->blockingAvailability()
                    ->overlappingRange($startDate, $endDate);
            });
    }

    /**
     * Check if vehicle is available on specific dates.
     */
    public function isAvailable($startDate, $endDate): bool
    {
        return $this->getBookingAvailability($startDate, $endDate)['available'];
    }

    /**
     * Check if there is scheduled maintenance on a given date.
     */
    public function hasMaintenanceOnDate($date): bool
    {
        $date = $this->normalizeDate($date);

        return $this->maintenanceSchedulesForRange($date, $date)->isNotEmpty();
    }

    /**
     * Build full calendar data for an arbitrary range.
     *
     * @return array<string, mixed>
     */
    public function getCalendarRangeData($startDate, $endDate, $pricingReferenceStart = null): array
    {
        [$startDate, $endDate] = $this->normalizeDateRange($startDate, $endDate);
        $pricingReferenceStart = $pricingReferenceStart ? $this->normalizeDate($pricingReferenceStart) : $startDate;
        $availability = $this->getBookingAvailability($startDate->toDateString(), $endDate->toDateString());

        return [
            'vehicle_id' => $this->id,
            'start_date' => $startDate->toDateString(),
            'end_date' => $endDate->toDateString(),
            'dates' => $this->buildDailyStatusEntries($startDate, $endDate, $pricingReferenceStart),
            'maintenance_periods' => $this->getMaintenancePeriods($startDate, $endDate),
            'available' => $availability['available'],
            'queue_available' => $availability['queue_available'],
            'next_available_date' => $this->getNextAvailableDate(),
        ];
    }

    /**
     * Normalize a date input.
     */
    protected function normalizeDate($date): Carbon
    {
        return $date instanceof Carbon
            ? $date->copy()->startOfDay()
            : Carbon::parse($date)->startOfDay();
    }

    /**
     * Normalize and order a date range.
     *
     * @return array{0: Carbon, 1: Carbon}
     */
    protected function normalizeDateRange($startDate, $endDate): array
    {
        $normalizedStart = $this->normalizeDate($startDate);
        $normalizedEnd = $this->normalizeDate($endDate);

        if ($normalizedEnd->lt($normalizedStart)) {
            [$normalizedStart, $normalizedEnd] = [$normalizedEnd, $normalizedStart];
        }

        return [$normalizedStart, $normalizedEnd];
    }

    /**
     * Get maintenance schedules that overlap a date range.
     */
    protected function maintenanceSchedulesForRange(Carbon $startDate, Carbon $endDate): Collection
    {
        if ($this->relationLoaded('maintenanceSchedules')) {
            return $this->maintenanceSchedules
                ->filter(fn (MaintenanceSchedule $schedule) => $this->dateRangeOverlaps(
                    $startDate,
                    $endDate,
                    Carbon::parse($schedule->start_date),
                    Carbon::parse($schedule->end_date)
                ))
                ->sortBy(fn (MaintenanceSchedule $schedule) => $schedule->start_date?->toDateString())
                ->values();
        }

        return $this->maintenanceSchedules()
            ->overlappingRange($startDate->toDateString(), $endDate->toDateString())
            ->orderBy('start_date')
            ->get();
    }

    /**
     * Get pricing rules that overlap a date range.
     */
    protected function pricingRulesForRange(Carbon $startDate, Carbon $endDate): Collection
    {
        if ($this->relationLoaded('pricingRules')) {
            return $this->pricingRules
                ->filter(fn (PricingRule $pricingRule) => $pricingRule->active
                    && $this->dateRangeOverlaps(
                        $startDate,
                        $endDate,
                        Carbon::parse($pricingRule->start_date),
                        Carbon::parse($pricingRule->end_date)
                    ))
                ->sortBy(fn (PricingRule $pricingRule) => $pricingRule->start_date?->toDateString())
                ->values();
        }

        return $this->pricingRules()
            ->active()
            ->overlappingRange($startDate->toDateString(), $endDate->toDateString())
            ->orderBy('start_date')
            ->get();
    }

    /**
     * Get bookings that should block the daily calendar for a range.
     */
    protected function calendarBookingsForRange(Carbon $startDate, Carbon $endDate): Collection
    {
        if ($this->relationLoaded('bookings')) {
            return $this->bookings
                ->filter(fn (Booking $booking) => $booking->blocksCalendarAvailability()
                    && $this->dateRangeOverlaps(
                        $startDate,
                        $endDate,
                        Carbon::parse($booking->start_date),
                        Carbon::parse($booking->end_date)
                    ))
                ->sortBy(fn (Booking $booking) => sprintf('%s-%010d', $booking->start_date?->toDateString(), $booking->id))
                ->values();
        }

        return $this->bookings()
            ->calendarUnavailable()
            ->overlappingRange($startDate->toDateString(), $endDate->toDateString())
            ->orderBy('start_date')
            ->orderBy('id')
            ->get();
    }

    /**
     * Check whether two date ranges overlap.
     */
    protected function dateRangeOverlaps(Carbon $rangeStart, Carbon $rangeEnd, Carbon $itemStart, Carbon $itemEnd): bool
    {
        return $itemStart->startOfDay()->lte($rangeEnd) && $itemEnd->startOfDay()->gte($rangeStart);
    }

    /**
     * Build daily calendar entries for a range.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function buildDailyStatusEntries(Carbon $startDate, Carbon $endDate, ?Carbon $pricingReferenceStart = null): array
    {
        $bookings = $this->calendarBookingsForRange($startDate, $endDate);
        $maintenanceSchedules = $this->maintenanceSchedulesForRange($startDate, $endDate);
        $pricingRules = $this->pricingRulesForRange($startDate, $endDate);
        $entries = [];

        for ($cursor = $startDate->copy(); $cursor->lte($endDate); $cursor->addDay()) {
            $status = $this->resolveDateStatus($cursor, $bookings, $maintenanceSchedules);
            $price = $this->buildDatePriceDetails($cursor, $pricingRules, $pricingReferenceStart ?? $startDate);

            $entries[] = array_merge($status, $price);
        }

        return $entries;
    }

    /**
     * Resolve one day's calendar status.
     *
     * @return array{date: string, status: string, reason: ?string}
     */
    protected function resolveDateStatus(Carbon $date, Collection $bookings, Collection $maintenanceSchedules): array
    {
        if ($this->status === 'maintenance') {
            return [
                'date' => $date->toDateString(),
                'status' => 'maintenance',
                'reason' => 'Kendaraan sedang dinonaktifkan untuk maintenance.',
            ];
        }

        $schedule = $maintenanceSchedules->first(function (MaintenanceSchedule $maintenanceSchedule) use ($date) {
            return Carbon::parse($maintenanceSchedule->start_date)->startOfDay()->lte($date)
                && Carbon::parse($maintenanceSchedule->end_date)->startOfDay()->gte($date);
        });

        if ($schedule instanceof MaintenanceSchedule) {
            return [
                'date' => $date->toDateString(),
                'status' => 'maintenance',
                'reason' => $schedule->reason,
            ];
        }

        $booking = $bookings->first(function (Booking $booking) use ($date) {
            return Carbon::parse($booking->start_date)->startOfDay()->lte($date)
                && Carbon::parse($booking->end_date)->startOfDay()->gte($date);
        });

        if ($booking instanceof Booking) {
            return [
                'date' => $date->toDateString(),
                'status' => 'booked',
                'reason' => 'Dibooking sampai ' . Carbon::parse($booking->end_date)->toDateString(),
            ];
        }

        return [
            'date' => $date->toDateString(),
            'status' => 'available',
            'reason' => null,
        ];
    }

    /**
     * Build price details for a specific date.
     *
     * @return array{base_price: float, multiplier: float, price_before_discount: float, discount_percentage: int, final_price: float, season_type: string, rule_type: ?string, rule_description: ?string}
     */
    protected function buildDatePriceDetails(Carbon $date, Collection $pricingRules, ?Carbon $pricingReferenceStart = null): array
    {
        $basePrice = $this->getBasePriceAmount();
        $seasonType = $this->determineSeasonType($date);
        $multiplier = match ($seasonType) {
            'peak' => (float) $this->peak_season_multiplier,
            'low' => (float) $this->low_season_multiplier,
            'weekend' => (float) $this->weekend_multiplier,
            default => 1.0,
        };
        $priceBeforeDiscount = round($basePrice * $multiplier, 2);
        $discount = $this->resolveApplicableDiscountForDate($date, $pricingRules, $pricingReferenceStart ?? $date);
        $finalPrice = round($priceBeforeDiscount * ((100 - $discount['discount_percentage']) / 100), 2);

        return [
            'base_price' => round($basePrice, 2),
            'multiplier' => round($multiplier, 2),
            'price_before_discount' => $priceBeforeDiscount,
            'discount_percentage' => $discount['discount_percentage'],
            'final_price' => $finalPrice,
            'season_type' => $seasonType,
            'rule_type' => $discount['rule_type'],
            'rule_description' => $discount['rule_description'],
        ];
    }

    /**
     * Determine pricing season priority for a date.
     */
    protected function determineSeasonType(Carbon $date): string
    {
        if (in_array($date->month, self::PEAK_SEASON_MONTHS, true)) {
            return 'peak';
        }

        if (in_array($date->month, self::LOW_SEASON_MONTHS, true)) {
            return 'low';
        }

        if ($date->isFriday() || $date->isSaturday() || $date->isSunday()) {
            return 'weekend';
        }

        return 'normal';
    }

    /**
     * Resolve the best discount rule for a given date.
     *
     * @return array{discount_percentage: int, rule_type: ?string, rule_description: ?string}
     */
    protected function resolveApplicableDiscountForDate(Carbon $date, Collection $pricingRules, Carbon $pricingReferenceStart): array
    {
        $leadDays = Carbon::now()->startOfDay()->diffInDays($pricingReferenceStart->copy()->startOfDay(), false);

        $applicableRule = $pricingRules
            ->filter(function (PricingRule $pricingRule) use ($date, $leadDays) {
                if (Carbon::parse($pricingRule->start_date)->startOfDay()->gt($date)
                    || Carbon::parse($pricingRule->end_date)->startOfDay()->lt($date)) {
                    return false;
                }

                return match ($pricingRule->type) {
                    'early_bird' => $leadDays >= 7,
                    'last_minute' => $leadDays >= 0 && $leadDays < 3,
                    default => true,
                };
            })
            ->sortByDesc('discount_percentage')
            ->first();

        return [
            'discount_percentage' => (int) ($applicableRule?->discount_percentage ?? 0),
            'rule_type' => $applicableRule?->type,
            'rule_description' => $applicableRule?->description,
        ];
    }

    /**
     * Format a maintenance schedule for API responses.
     *
     * @return array{start_date: string, end_date: string, reason: string, notes: ?string}
     */
    protected function formatMaintenancePeriod(MaintenanceSchedule $schedule): array
    {
        return [
            'start_date' => Carbon::parse($schedule->start_date)->toDateString(),
            'end_date' => Carbon::parse($schedule->end_date)->toDateString(),
            'reason' => $schedule->reason,
            'notes' => $schedule->notes,
        ];
    }
}