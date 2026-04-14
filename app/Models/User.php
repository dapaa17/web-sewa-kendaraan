<?php

namespace App\Models;

use Illuminate\Support\Facades\File;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'ktp_number',
        'ktp_image',
        'ktp_status',
        'ktp_verified_at',
        'ktp_rejection_reason',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'ktp_verified_at' => 'datetime',
    ];

    /**
     * Get all bookings for this user
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Get all reviews written by the user.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is customer
     */
    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }

    /**
     * Check if KTP is verified
     */
    public function isKtpVerified(): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        return $this->ktp_status === 'verified';
    }

    /**
     * Check if KTP is pending verification
     */
    public function isKtpPending(): bool
    {
        if ($this->isAdmin()) {
            return false;
        }

        return $this->ktp_status === 'pending' && $this->ktp_image !== null;
    }

    /**
     * Check if user has uploaded KTP
     */
    public function hasUploadedKtp(): bool
    {
        return $this->ktp_image !== null;
    }

    /**
     * Get accessible URL for KTP image with fallback when public/storage symlink is unavailable.
     */
    public function getKtpImageUrlAttribute(): ?string
    {
        if (! $this->ktp_image) {
            return null;
        }

        $relativePath = ltrim($this->ktp_image, '/');
        $publicStoragePath = public_path('storage/' . $relativePath);

        if (File::exists($publicStoragePath)) {
            return asset('storage/' . $relativePath);
        }

        return route('profile.ktp.image', $this);
    }

    /**
     * Get KTP status badge class
     */
    public function getKtpStatusBadge(): string
    {
        if ($this->isAdmin()) {
            return 'bg-info';
        }

        return match($this->ktp_status) {
            'verified' => 'bg-success',
            'rejected' => 'bg-danger',
            default => $this->ktp_image ? 'bg-warning' : 'bg-secondary',
        };
    }

    /**
     * Get KTP status label
     */
    public function getKtpStatusLabel(): string
    {
        if ($this->isAdmin()) {
            return 'Tidak Diperlukan';
        }

        return match($this->ktp_status) {
            'verified' => 'Terverifikasi',
            'rejected' => 'Ditolak',
            default => $this->ktp_image ? 'Menunggu Verifikasi' : 'Belum Upload',
        };
    }
}