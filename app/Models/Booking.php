<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class Booking extends Model
{
    use SoftDeletes;

    public const DEFAULT_PICKUP_TIME = '09:00:00';
    public const DEFAULT_RETURN_TIME = '17:00:00';
    public const DEFAULT_PAYMENT_WINDOW_HOURS = 1;
    public const SHORT_BOOKING_PAYMENT_WINDOW_HOURS = 1;
    public const SHORT_BOOKING_EXPIRING_SOON_HOURS = 1;
    public const RETURN_CONDITION_OPTIONS = [
        'excellent' => 'Aman',
        'minor_issue' => 'Ada catatan kecil',
        'needs_attention' => 'Perlu tindak lanjut',
    ];
    public const RETURN_FUEL_LEVEL_OPTIONS = [
        'full' => 'Penuh',
        'three_quarters' => '3/4',
        'half' => '1/2',
        'quarter' => '1/4',
        'empty' => 'Hampir habis',
    ];
    public const RETURN_CHECKLIST_OPTIONS = [
        'documents_received' => 'Dokumen kendaraan diterima',
        'main_key_received' => 'Kunci utama diterima',
        'spare_key_received' => 'Kunci cadangan diterima',
        'accessories_received' => 'Helm / aksesori diterima',
        'interior_clean' => 'Interior bersih',
        'no_new_body_damage' => 'Tidak ada kerusakan bodi baru',
    ];

    protected ?bool $awaitingVehicleReturnState = null;

    protected $fillable = [
        'user_id',
        'vehicle_id',
        'start_date',
        'pickup_time',
        'end_date',
        'return_time',
        'reminder_sent_at',
        'actual_return_date',
        'return_condition_status',
        'return_fuel_level',
        'return_odometer',
        'return_checklist',
        'return_damage_fee',
        'return_notes',
        'return_photo',
        'maintenance_hold_at',
        'maintenance_hold_reason',
        'late_days',
        'late_fee',
        'late_fee_per_day',
        'duration_days',
        'daily_price',
        'total_price',
        'status',
        'payment_method',
        'payment_status',
        'payment_proof',

        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'reminder_sent_at' => 'datetime',
        'actual_return_date' => 'date',
        'maintenance_hold_at' => 'datetime',
        'return_odometer' => 'integer',
        'return_checklist' => 'array',
        'return_damage_fee' => 'decimal:2',
        'daily_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'late_fee' => 'decimal:2',
        'late_fee_per_day' => 'decimal:2',
        'duration_days' => 'integer',
        'late_days' => 'integer',
    ];

    /**
     * Get the pickup time formatted for display.
     */
    public function getPickupTimeLabelAttribute(): string
    {
        return $this->formatScheduleTime($this->pickup_time, '09:00:00');
    }

    /**
     * Get the return time formatted for display.
     */
    public function getReturnTimeLabelAttribute(): string
    {
        return $this->formatScheduleTime($this->return_time, '17:00:00');
    }

    /**
     * Normalize stored time values for display.
     */
    protected function formatScheduleTime(?string $time, string $fallback): string
    {
        $value = $time ?: $fallback;

        foreach (['H:i:s', 'H:i'] as $format) {
            try {
                return Carbon::createFromFormat($format, $value)->format('H:i');
            } catch (\Throwable) {
                continue;
            }
        }

        return Carbon::parse($value)->format('H:i');
    }

    /**
     * Combine a booking date and its stored schedule time into one moment.
     */
    protected function combineScheduleMoment(Carbon $date, ?string $time, string $fallback): Carbon
    {
        return $date->copy()->setTimeFromTimeString($this->formatScheduleTime($time, $fallback));
    }

    /**
     * Get the exact planned pickup moment.
     */
    public function getPickupDateTime(): Carbon
    {
        return $this->combineScheduleMoment($this->start_date, $this->pickup_time, self::DEFAULT_PICKUP_TIME);
    }

    /**
     * Get the exact planned return moment.
     */
    public function getReturnDateTime(): Carbon
    {
        return $this->combineScheduleMoment($this->end_date, $this->return_time, self::DEFAULT_RETURN_TIME);
    }

    /**
     * Apply a constraint for bookings whose pickup time is still in the future.
     */
    protected static function applyFutureStartConstraint(Builder|QueryBuilder $query, Carbon $moment): Builder|QueryBuilder
    {
        $today = $moment->toDateString();
        $currentTime = $moment->format('H:i:s');

        return $query->where(function ($futureStartQuery) use ($today, $currentTime) {
            $futureStartQuery->whereDate('start_date', '>', $today)
                ->orWhere(function ($sameDayQuery) use ($today, $currentTime) {
                    $sameDayQuery->whereDate('start_date', $today)
                        ->whereRaw('COALESCE(pickup_time, ?) > ?', [self::DEFAULT_PICKUP_TIME, $currentTime]);
                });
        });
    }

    /**
     * Apply a constraint for bookings whose pickup time has arrived.
     */
    protected static function applyStartedByMomentConstraint(Builder|QueryBuilder $query, Carbon $moment): Builder|QueryBuilder
    {
        $today = $moment->toDateString();
        $currentTime = $moment->format('H:i:s');

        return $query->where(function ($startedQuery) use ($today, $currentTime) {
            $startedQuery->whereDate('start_date', '<', $today)
                ->orWhere(function ($sameDayQuery) use ($today, $currentTime) {
                    $sameDayQuery->whereDate('start_date', $today)
                        ->whereRaw('COALESCE(pickup_time, ?) <= ?', [self::DEFAULT_PICKUP_TIME, $currentTime]);
                });
        });
    }

    /**
     * Compare two booking rows by pickup time first, then by id.
     */
    protected static function applyEarlierStartMomentComparison(
        Builder|QueryBuilder $query,
        string $earlierTable,
        string $currentTable
    ): Builder|QueryBuilder {
        return $query->where(function ($comparisonQuery) use ($earlierTable, $currentTable) {
            $comparisonQuery->whereColumn("{$earlierTable}.start_date", '<', "{$currentTable}.start_date")
                ->orWhere(function ($sameDateQuery) use ($earlierTable, $currentTable) {
                    $sameDateQuery->whereColumn("{$earlierTable}.start_date", "{$currentTable}.start_date")
                        ->where(function ($sameMomentQuery) use ($earlierTable, $currentTable) {
                            $sameMomentQuery->whereRaw(
                                "COALESCE({$earlierTable}.pickup_time, ?) < COALESCE({$currentTable}.pickup_time, ?)",
                                [self::DEFAULT_PICKUP_TIME, self::DEFAULT_PICKUP_TIME]
                            )->orWhere(function ($sameTimeQuery) use ($earlierTable, $currentTable) {
                                $sameTimeQuery->whereRaw(
                                    "COALESCE({$earlierTable}.pickup_time, ?) = COALESCE({$currentTable}.pickup_time, ?)",
                                    [self::DEFAULT_PICKUP_TIME, self::DEFAULT_PICKUP_TIME]
                                )->whereColumn("{$earlierTable}.id", '<', "{$currentTable}.id");
                            });
                        });
                });
        });
    }

    /**
     * Get the user that owns this booking
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    /**
     * Get the vehicle that is booked
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class)->withTrashed();
    }

    /**
     * Get the review submitted for this booking.
     */
    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }

    /**
     * Scope bookings that overlap a requested date range.
     */
    public function scopeOverlappingRange(Builder $query, $startDate, $endDate): Builder
    {
        return $query->where(function (Builder $query) use ($startDate, $endDate) {
            $query->whereBetween('start_date', [$startDate, $endDate])
                ->orWhereBetween('end_date', [$startDate, $endDate])
                ->orWhere(function (Builder $nestedQuery) use ($startDate, $endDate) {
                    $nestedQuery->where('start_date', '<=', $startDate)
                        ->where('end_date', '>=', $endDate);
                });
        });
    }

    /**
     * Scope bookings that should fully block a new booking request.
     */
    public function scopeBlockingAvailability(Builder $query): Builder
    {
        $moment = Carbon::now();

        return $query->where(function (Builder $query) use ($moment) {
            $query->where(function (Builder $pendingQuery) {
                $pendingQuery->where('status', 'pending')
                    ->whereIn('payment_status', ['pending', 'paid']);
            })->orWhere(function (Builder $futureConfirmedQuery) use ($moment) {
                $futureConfirmedQuery->where('status', 'confirmed')
                    ->where('payment_status', 'paid');

                static::applyFutureStartConstraint($futureConfirmedQuery, $moment);
            });
        });
    }

    /**
     * Scope bookings that may still accept new queueing bookings.
     */
    public function scopeQueueableAvailability(Builder $query): Builder
    {
        $moment = Carbon::now();

        $query->where('status', 'confirmed')
            ->where('payment_status', 'paid')
            ->whereNull('maintenance_hold_at');

        return static::applyStartedByMomentConstraint($query, $moment);
    }

    /**
     * Scope bookings that should appear as unavailable on the daily calendar.
     */
    public function scopeCalendarUnavailable(Builder $query): Builder
    {
        return $query->where(function (Builder $calendarQuery) {
            $calendarQuery->where(function (Builder $pendingQuery) {
                $pendingQuery->where('status', 'pending')
                    ->whereIn('payment_status', ['pending', 'paid']);
            })->orWhere(function (Builder $confirmedQuery) {
                $confirmedQuery->where('status', 'confirmed')
                    ->where('payment_status', 'paid')
                    ->whereNull('maintenance_hold_at');
            });
        });
    }

    /**
     * Scope confirmed paid bookings that are scheduled for a future date.
     */
    public function scopeScheduled(Builder $query): Builder
    {
        $moment = Carbon::now();

        $query->where('status', 'confirmed')
            ->where('payment_status', 'paid')
            ->whereNull('maintenance_hold_at');

        return static::applyFutureStartConstraint($query, $moment);
    }

    /**
     * Scope confirmed paid bookings that already started but still wait for an earlier booking to return the vehicle.
     */
    public function scopeAwaitingVehicleReturn(Builder $query): Builder
    {
        $moment = Carbon::now();

        $query->where('status', 'confirmed')
            ->where('payment_status', 'paid')
            ->whereNull('maintenance_hold_at');

        static::applyStartedByMomentConstraint($query, $moment);

        return $query
            ->whereExists(function ($subQuery) {
                $subQuery->selectRaw('1')
                    ->from('bookings as earlier_bookings')
                    ->whereColumn('earlier_bookings.vehicle_id', 'bookings.vehicle_id')
                    ->where('earlier_bookings.status', 'confirmed')
                    ->where('earlier_bookings.payment_status', 'paid')
                    ->where(function ($comparisonQuery) {
                        static::applyEarlierStartMomentComparison($comparisonQuery, 'earlier_bookings', 'bookings');
                    });
            });
    }

    /**
     * Scope confirmed paid bookings that are truly active in the field.
     */
    public function scopeOperationallyActive(Builder $query): Builder
    {
        $moment = Carbon::now();

        $query->where('status', 'confirmed')
            ->where('payment_status', 'paid')
            ->whereNull('maintenance_hold_at')
            ->whereHas('vehicle', fn (Builder $vehicleQuery) => $vehicleQuery->where('status', '!=', 'maintenance'));

        static::applyStartedByMomentConstraint($query, $moment);

        return $query
            ->whereNotExists(function ($subQuery) {
                $subQuery->selectRaw('1')
                    ->from('bookings as earlier_bookings')
                    ->whereColumn('earlier_bookings.vehicle_id', 'bookings.vehicle_id')
                    ->where('earlier_bookings.status', 'confirmed')
                    ->where('earlier_bookings.payment_status', 'paid')
                    ->where(function ($comparisonQuery) {
                        static::applyEarlierStartMomentComparison($comparisonQuery, 'earlier_bookings', 'bookings');
                    });
            });
    }

    /**
     * Scope pending bookings that are still waiting for customer payment confirmation.
     */
    public function scopeAwaitingPaymentProof(Builder $query): Builder
    {
        return $query->where('status', 'pending')
            ->where('payment_status', 'pending')
            ->whereNull('payment_proof');
    }

    /**
     * Scope bookings that are still genuinely pending payment or verification.
     */
    public function scopeDisplayPending(Builder $query): Builder
    {
        return $query->where('status', 'pending')
            ->where('payment_status', 'pending');
    }

    /**
     * Scope bookings that should appear in the cancelled bucket.
     */
    public function scopeDisplayCancelled(Builder $query): Builder
    {
        return $query->where(function (Builder $cancelledQuery) {
            $cancelledQuery->where('status', 'cancelled')
                ->orWhere(function (Builder $failedPaymentQuery) {
                    $failedPaymentQuery->where('status', 'pending')
                        ->where('payment_status', 'failed');
                });
        });
    }

    /**
     * Scope paid bookings that are already in the waiting list.
     */
    public function scopePaidWaitingList(Builder $query): Builder
    {
        return $query->where('status', 'waiting_list')
            ->whereNull('maintenance_hold_at')
            ->where('payment_status', 'paid');
    }

    /**
     * Scope bookings that are explicitly held because of maintenance follow-up.
     */
    public function scopeMaintenanceHold(Builder $query): Builder
    {
        return $query->whereNotNull('maintenance_hold_at');
    }

    /**
     * Calculate total price based on duration and daily price
     */
    public static function calculateTotalPrice($startDate, $endDate, $dailyPrice): float
    {
        $duration = $endDate->diffInDays($startDate) + 1;
        return $duration * $dailyPrice;
    }

    /**
     * Check if booking is paid
     */
    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    /**
     * Check if booking is pending payment
     */
    public function isPendingPayment(): bool
    {
        return $this->payment_status === 'pending';
    }

    /**
     * Check if booking payment was rejected by admin.
     */
    public function hasFailedPayment(): bool
    {
        return $this->payment_status === 'failed';
    }

    /**
     * Get the effective status key that should be shown in the UI.
     */
    public function getDisplayStatusKey(): string
    {
        if ($this->isBlockedByMaintenance()) {
            return 'maintenance_hold';
        }

        if ($this->isActive()) {
            return 'active';
        }

        if ($this->isAwaitingVehicleReturn()) {
            return 'awaiting_return';
        }

        if ($this->hasFailedPayment()) {
            return 'payment_failed';
        }

        if ($this->status === 'confirmed' && $this->hasNotStartedYet()) {
            return 'scheduled';
        }

        return (string) $this->status;
    }

    /**
     * Get the effective status label that should be shown in the UI.
     */
    public function getDisplayStatusLabel(): string
    {
        return match ($this->getDisplayStatusKey()) {
            'maintenance_hold' => 'Tertahan Maintenance',
            'active' => 'Sedang Disewa',
            'awaiting_return' => 'Menunggu Pengembalian Unit',
            'payment_failed' => 'Pembayaran Ditolak',
            'pending' => 'Pending',
            'waiting_list' => 'Antrean',
            'scheduled' => 'Terjadwal',
            'confirmed' => 'Dikonfirmasi',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
            default => Str::headline((string) $this->status),
        };
    }

    /**
     * Check if booking is active (confirmed & paid)
     */
    public function isActive(): bool
    {
        return $this->status === 'confirmed'
            && $this->isPaid()
            && ! $this->isBlockedByMaintenance()
            && $this->hasStarted()
            && ! $this->isAwaitingVehicleReturn();
    }

    /**
     * Check whether the booking is explicitly held because of maintenance follow-up.
     */
    public function isMaintenanceHold(): bool
    {
        return $this->maintenance_hold_at !== null;
    }

    /**
     * Check whether the booking is temporarily blocked because the vehicle is in maintenance.
     */
    public function isBlockedByMaintenance(): bool
    {
        return $this->isMaintenanceHold();
    }

    /**
     * Check if booking is confirmed and already paid.
     */
    public function isConfirmedPaid(): bool
    {
        return $this->status === 'confirmed' && $this->isPaid();
    }

    /**
     * Check if booking is confirmed, paid, and has not started yet.
     */
    public function isUpcomingConfirmed(): bool
    {
        return $this->isConfirmedPaid()
            && ! $this->isMaintenanceHold()
            && $this->hasNotStartedYet();
    }

    /**
     * Check if the booking already reached its start date but still waits for the previous booking to release the vehicle.
     */
    public function isAwaitingVehicleReturn(): bool
    {
        if ($this->awaitingVehicleReturnState !== null) {
            return $this->awaitingVehicleReturnState;
        }

        if (! $this->exists || ! $this->isConfirmedPaid() || ! $this->hasStarted()) {
            return $this->awaitingVehicleReturnState = false;
        }

        return $this->awaitingVehicleReturnState = static::query()
            ->whereKey($this->getKey())
            ->awaitingVehicleReturn()
            ->exists();
    }

    /**
     * Check if booking is already in the waiting list.
     */
    public function isWaitingList(): bool
    {
        return $this->status === 'waiting_list';
    }

    /**
     * Get the waiting-list queue for this booking vehicle.
     */
    public function getVehicleWaitingListQueue(): Collection
    {
        return static::query()
            ->with('user')
            ->where('vehicle_id', $this->vehicle_id)
            ->paidWaitingList()
            ->orderBy('created_at')
            ->orderBy('id')
            ->get();
    }

    /**
     * Get the 1-based queue position for a waiting-list booking.
     */
    public function getWaitingListPosition(): ?int
    {
        if (! $this->exists || ! $this->isWaitingList() || ! $this->isPaid()) {
            return null;
        }

        return static::query()
            ->where('vehicle_id', $this->vehicle_id)
            ->paidWaitingList()
            ->where(function (Builder $query) {
                $query->where('created_at', '<', $this->created_at)
                    ->orWhere(function (Builder $sameTimestampQuery) {
                        $sameTimestampQuery->where('created_at', $this->created_at)
                            ->where('id', '<=', $this->id);
                    });
            })
            ->count();
    }

    /**
     * Check whether this booking was activated from the waiting list.
     */
    public function wasPromotedFromWaitingList(): bool
    {
        return Str::contains(
            Str::lower((string) $this->notes),
            ['diaktifkan dari waiting list', 'diaktifkan dari antrean']
        );
    }

    /**
     * Get the latest reschedule log line added after a maintenance hold.
     */
    public function getLatestMaintenanceRescheduleNote(): ?string
    {
        $notes = preg_split('/\R+/', (string) $this->notes) ?: [];

        return collect($notes)
            ->map(fn (string $note) => trim($note))
            ->filter()
            ->reverse()
            ->first(function (string $note) {
                $normalized = Str::lower($note);

                return Str::contains($normalized, 'jadwal booking disesuaikan admin')
                    && Str::contains($normalized, 'setelah maintenance');
            });
    }

    /**
     * Check whether this booking has been rescheduled after maintenance handling.
     */
    public function wasRescheduledAfterMaintenance(): bool
    {
        return $this->getLatestMaintenanceRescheduleNote() !== null;
    }

    /**
     * Extract the latest admin note attached to a maintenance reschedule log.
     */
    public function getMaintenanceRescheduleAdminNote(): ?string
    {
        $note = $this->getLatestMaintenanceRescheduleNote();

        if (! $note || ! Str::contains($note, 'Catatan admin: ')) {
            return null;
        }

        return trim(Str::after($note, 'Catatan admin: '));
    }

    /**
     * Get the current notification type that can be resent to the customer.
     */
    public function getResendableNotificationType(): ?string
    {
        if ($this->payment_status === 'failed') {
            return 'payment_failed';
        }

        if ($this->status === 'confirmed' && $this->isPaid() && $this->wasRescheduledAfterMaintenance()) {
            return 'booking_rescheduled';
        }

        if ($this->status === 'confirmed' && $this->isPaid() && $this->wasPromotedFromWaitingList()) {
            return 'waiting_list_activated';
        }

        if (in_array($this->status, ['confirmed', 'waiting_list'], true) && $this->isPaid()) {
            return 'payment_verified';
        }

        return null;
    }

    /**
     * Check if the booking has a resendable customer notification.
     */
    public function canResendCustomerNotification(): bool
    {
        return $this->getResendableNotificationType() !== null;
    }

    /**
     * Get the admin button label for the resendable notification.
     */
    public function getResendableNotificationLabel(): ?string
    {
        return match ($this->getResendableNotificationType()) {
            'payment_failed' => 'Kirim Ulang Email Penolakan',
            'booking_rescheduled' => 'Kirim Ulang Email Jadwal Baru',
            'waiting_list_activated' => 'Kirim Ulang Email Aktivasi Antrean',
            'payment_verified' => 'Kirim Ulang Email Status Pembayaran',
            default => null,
        };
    }

    /**
     * Check if the rental period has started.
     */
    public function hasStarted(): bool
    {
        return Carbon::now()->gte($this->getPickupDateTime());
    }

    /**
     * Check if the rental period has not started yet.
     */
    public function hasNotStartedYet(): bool
    {
        return Carbon::now()->lt($this->getPickupDateTime());
    }

    /**
     * Check if the booking starts tomorrow.
     */
    public function startsTomorrow(): bool
    {
        return $this->getPickupDateTime()->isSameDay(Carbon::now()->addDay());
    }

    /**
     * Check if the booking should receive an H-1 reminder.
     */
    public function shouldSendStartReminder(): bool
    {
        return $this->isUpcomingConfirmed()
            && $this->startsTomorrow()
            && $this->reminder_sent_at === null;
    }

    /**
     * Check if the booking can still be cancelled by the owner.
     */
    public function canBeCancelled(): bool
    {
        return $this->status === 'pending' && $this->payment_status === 'pending';
    }

    /**
     * Check if this booking should block the daily availability calendar.
     */
    public function blocksCalendarAvailability(): bool
    {
        if ($this->status === 'pending' && in_array($this->payment_status, ['pending', 'paid'], true)) {
            return true;
        }

        return $this->status === 'confirmed'
            && $this->payment_status === 'paid'
            && $this->maintenance_hold_at === null;
    }

    /**
     * Check if the owner can still enter the payment flow.
     */
    public function canEnterPaymentFlow(): bool
    {
        return $this->status === 'pending'
            && $this->payment_status === 'pending'
            && $this->payment_proof === null
            && ! $this->isPastDeadline();
    }

    /**
     * Check if this booking uses WhatsApp confirmation flow.
     */
    public function usesWhatsAppConfirmation(): bool
    {
        return in_array($this->payment_method, ['whatsapp', 'online'], true);
    }

    /**
     * Check if this booking uses transfer proof upload flow.
     */
    public function usesTransferProof(): bool
    {
        return in_array($this->payment_method, ['transfer_proof', 'offline'], true);
    }

    /**
     * Check if the owner can upload payment proof.
     */
    public function canUploadPaymentProof(): bool
    {
        return false;
    }

    /**
     * Check if this booking is still waiting for customer payment confirmation.
     */
    public function isAwaitingPaymentProof(): bool
    {
        return $this->status === 'pending'
            && $this->payment_status === 'pending'
            && $this->payment_proof === null;
    }

    /**
     * Check if this booking has passed its payment deadline and is still unpaid.
     */
    public function isOverduePayment(): bool
    {
        return $this->status === 'pending'
            && $this->payment_status === 'pending'
            && $this->payment_proof === null
            && $this->isPastDeadline();
    }

    /**
     * Check if an admin can verify the booking payment.
     */
    public function canBeVerified(): bool
    {
        if ($this->status !== 'pending' || $this->payment_status !== 'pending') {
            return false;
        }

        return $this->usesWhatsAppConfirmation() || $this->payment_proof !== null;
    }

    /**
     * Check if an admin can complete the booking.
     */
    public function canBeCompleted(): bool
    {
        return $this->isActive();
    }

    /**
     * Check if an admin can reschedule the booking after a maintenance hold.
     */
    public function canBeRescheduledByAdmin(): bool
    {
        return $this->isMaintenanceHold()
            && in_array($this->status, ['confirmed', 'waiting_list'], true)
            && $this->isPaid();
    }

    /**
     * Check whether this booking already has a review.
     */
    public function hasReview(): bool
    {
        if ($this->relationLoaded('review')) {
            return $this->review !== null;
        }

        return $this->review()->exists();
    }

    /**
     * Check if the booking is eligible for review submission.
     */
    public function canBeReviewed(): bool
    {
        return $this->status === 'completed'
            && $this->isPaid()
            && ! $this->hasReview();
    }

    /**
     * Get a user-facing payment method label.
     */
    public function getPaymentMethodLabel(): string
    {
        if ($this->usesWhatsAppConfirmation()) {
            return 'Konfirmasi WhatsApp';
        }

        if ($this->usesTransferProof()) {
            return $this->payment_proof
                ? 'Bukti Transfer (Legacy)'
                : 'Belum Konfirmasi WhatsApp';
        }

        return ucfirst((string) $this->payment_method);
    }

    /**
     * Get a short payment method label for compact UI.
     */
    public function getPaymentMethodShortLabel(): string
    {
        if ($this->usesWhatsAppConfirmation()) {
            return 'WhatsApp';
        }

        if ($this->usesTransferProof()) {
            return $this->payment_proof ? 'Bukti Transfer' : 'Belum Konfirmasi';
        }

        return ucfirst((string) $this->payment_method);
    }

    /**
     * Get a user-facing payment status label.
     */
    public function getPaymentStatusLabel(): string
    {
        if ($this->payment_status === 'paid') {
            return 'Lunas';
        }

        if ($this->payment_proof && $this->payment_status === 'pending') {
            return 'Menunggu Verifikasi';
        }

        if ($this->payment_status === 'pending') {
            return 'Belum Bayar';
        }

        return 'Gagal';
    }

    /**
     * Get payment deadline based on booking duration.
     */
    public function getPaymentDeadline(?int $maxHours = null): Carbon
    {
        return $this->created_at->copy()->addHours($this->getPaymentWindowHours($maxHours));
    }

    /**
     * Get the active payment window in hours.
     */
    public function getPaymentWindowHours(?int $maxHours = null): int
    {
        $maxHours ??= self::DEFAULT_PAYMENT_WINDOW_HOURS;

        return min($maxHours, self::DEFAULT_PAYMENT_WINDOW_HOURS);
    }

    /**
     * Get a short human-readable payment deadline policy label.
     */
    public function getPaymentDeadlineLabel(?int $maxHours = null): string
    {
        $hours = $this->getPaymentWindowHours($maxHours);

        return $hours === 1 ? '1 jam' : $hours . ' jam';
    }

    /**
     * Check if booking will be auto-cancelled soon.
     */
    public function isExpiringSoon(?int $maxHours = null): bool
    {
        if ($this->status !== 'pending' || $this->payment_status !== 'pending') {
            return false;
        }
        
        $deadline = $this->getPaymentDeadline($maxHours);
        $hoursRemaining = Carbon::now()->diffInHours($deadline, false);
        $warningHours = $this->getPaymentWindowHours($maxHours) <= self::SHORT_BOOKING_PAYMENT_WINDOW_HOURS
            ? self::SHORT_BOOKING_EXPIRING_SOON_HOURS
            : 6;

        return $hoursRemaining <= $warningHours && $hoursRemaining > 0;
    }

    /**
     * Check if booking is past payment deadline
     */
    public function isPastDeadline(?int $maxHours = null): bool
    {
        if ($this->status !== 'pending' || $this->payment_status !== 'pending') {
            return false;
        }
        
        return Carbon::now()->gt($this->getPaymentDeadline($maxHours));
    }

    /**
     * Get time remaining until auto-cancel (human readable)
     */
    public function getTimeRemaining(?int $maxHours = null): string
    {
        if ($this->status !== 'pending' || $this->payment_status !== 'pending') {
            return '-';
        }
        
        $deadline = $this->getPaymentDeadline($maxHours);
        
        if (Carbon::now()->gt($deadline)) {
            return 'Expired';
        }
        
        return $deadline->diffForHumans(['parts' => 2]);
    }

    /**
     * Check if booking is overdue (past end_date)
     */
    public function isOverdue(): bool
    {
        if ($this->status !== 'pending' || $this->payment_status !== 'paid') {
            return false;
        }
        
        return Carbon::now()->startOfDay()->gt($this->end_date);
    }

    /**
     * Calculate late days from end_date to actual return date
     */
    public function calculateLateDays($returnDate = null): int
    {
        $returnDate = $returnDate ? Carbon::parse($returnDate) : Carbon::now();
        $endDate = Carbon::parse($this->end_date);
        
        if ($returnDate->startOfDay()->lte($endDate)) {
            return 0;
        }
        
        return $returnDate->startOfDay()->diffInDays($endDate);
    }

    /**
     * Calculate late fee (default: 100% of daily_price per late day)
     */
    public function calculateLateFee($lateDays = null, $feePerDay = null): float
    {
        $lateDays = $lateDays ?? $this->calculateLateDays();
        $feePerDay = $feePerDay ?? $this->daily_price; // Default 100% of daily rate
        
        return $lateDays * $feePerDay;
    }

    /**
     * Get selectable condition labels for return inspection.
     *
     * @return array<string, string>
     */
    public static function getReturnConditionOptions(): array
    {
        return self::RETURN_CONDITION_OPTIONS;
    }

    /**
     * Get selectable fuel labels for return inspection.
     *
     * @return array<string, string>
     */
    public static function getReturnFuelLevelOptions(): array
    {
        return self::RETURN_FUEL_LEVEL_OPTIONS;
    }

    /**
     * Get selectable checklist labels for return inspection.
     *
     * @return array<string, string>
     */
    public static function getReturnChecklistOptions(): array
    {
        return self::RETURN_CHECKLIST_OPTIONS;
    }

    /**
     * Get the human-readable return condition label.
     */
    public function getReturnConditionStatusLabel(): string
    {
        return self::RETURN_CONDITION_OPTIONS[$this->return_condition_status] ?? '-';
    }

    /**
     * Get the human-readable return fuel label.
     */
    public function getReturnFuelLevelLabel(): string
    {
        return self::RETURN_FUEL_LEVEL_OPTIONS[$this->return_fuel_level] ?? '-';
    }

    /**
     * Get the selected checklist labels.
     *
     * @return array<int, string>
     */
    public function getReturnChecklistLabels(): array
    {
        return collect($this->return_checklist ?? [])
            ->map(fn (string $item) => self::RETURN_CHECKLIST_OPTIONS[$item] ?? null)
            ->filter()
            ->values()
            ->all();
    }

    /**
     * Determine whether the booking has a stored return inspection.
     */
    public function hasReturnInspection(): bool
    {
        return $this->return_condition_status !== null
            || $this->return_fuel_level !== null
            || $this->return_odometer !== null
            || ! empty($this->return_checklist)
            || (float) $this->return_damage_fee > 0
            || $this->return_notes !== null
            || $this->return_photo !== null;
    }

    /**
     * Get total amount including late fee
     */
    public function getTotalWithLateFee(): float
    {
        return (float) $this->total_price + (float) $this->late_fee;
    }

    /**
     * Get total amount including return-time charges.
     */
    public function getTotalWithCompletionCharges(): float
    {
        return (float) $this->total_price + (float) $this->late_fee + (float) $this->return_damage_fee;
    }
}