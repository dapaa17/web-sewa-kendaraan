<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Review extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'booking_id',
        'user_id',
        'vehicle_id',
        'rating',
        'title',
        'review_text',
        'status',
        'helpful_count',
        'admin_note',
        'moderated_at',
        'moderated_by',
    ];

    protected $casts = [
        'rating' => 'integer',
        'helpful_count' => 'integer',
        'moderated_at' => 'datetime',
    ];

    /**
     * Get the booking for this review.
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class)->withTrashed();
    }

    /**
     * Get the review author.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    /**
     * Get the reviewed vehicle.
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class)->withTrashed();
    }

    /**
     * Get the admin who moderated this review.
     */
    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderated_by')->withTrashed();
    }

    /**
     * Get helpful votes for this review.
     */
    public function helpfulVotes(): HasMany
    {
        return $this->hasMany(ReviewHelpfulVote::class);
    }

    /**
     * Scope approved reviews.
     */
    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    /**
     * Scope pending reviews.
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope rejected reviews.
     */
    public function scopeRejected(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    /**
     * Check if the review is still pending moderation.
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if the review is approved.
     */
    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    /**
     * Check if the review is rejected.
     */
    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    /**
     * Check if the owner may still edit/delete this review.
     */
    public function canBeManagedByOwner(): bool
    {
        return ! $this->isApproved();
    }

    /**
     * Get a human-readable moderation label.
     */
    public function getStatusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_APPROVED => 'Disetujui',
            self::STATUS_REJECTED => 'Ditolak',
            default => 'Menunggu Moderasi',
        };
    }

    /**
     * Get a badge class for the moderation state.
     */
    public function getStatusBadgeClass(): string
    {
        return match ($this->status) {
            self::STATUS_APPROVED => 'bg-success-subtle text-success',
            self::STATUS_REJECTED => 'bg-danger-subtle text-danger',
            default => 'bg-warning-subtle text-warning-emphasis',
        };
    }

    /**
     * Get a compact star label for display.
     */
    public function getStarsLabel(): string
    {
        return str_repeat('⭐', $this->rating);
    }
}