<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

class MaintenanceSchedule extends Model
{
    protected $fillable = [
        'vehicle_id',
        'start_date',
        'end_date',
        'reason',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    protected static function booted(): void
    {
        static::saving(function (MaintenanceSchedule $schedule) {
            $validator = Validator::make($schedule->attributesToArray(), [
                'vehicle_id' => ['required', 'integer', 'exists:vehicles,id'],
                'start_date' => ['required', 'date'],
                'end_date' => ['required', 'date', 'after_or_equal:start_date'],
                'reason' => ['required', 'string', 'max:255'],
                'notes' => ['nullable', 'string'],
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

    public function scopeOverlappingRange(Builder $query, $startDate, $endDate): Builder
    {
        $start = Carbon::parse($startDate)->toDateString();
        $end = Carbon::parse($endDate)->toDateString();

        return $query->whereDate('start_date', '<=', $end)
            ->whereDate('end_date', '>=', $start);
    }
}