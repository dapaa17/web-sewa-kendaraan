<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
    ];

    public static function getValue(string $key, ?string $default = null): ?string
    {
        return static::query()->where('key', $key)->value('value') ?? $default;
    }

    public static function setValue(string $key, string $value): void
    {
        static::query()->updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }

    /**
     * Get default pickup and return times for newly created bookings.
     *
     * @return array{pickup_time: string, return_time: string}
     */
    public static function getBookingScheduleDefaults(): array
    {
        return [
            'pickup_time' => static::getValue('booking_default_pickup_time', '09:00'),
            'return_time' => static::getValue('booking_default_return_time', '17:00'),
        ];
    }
}