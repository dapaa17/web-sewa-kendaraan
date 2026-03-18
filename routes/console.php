<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Auto-cancel unpaid bookings every hour
Schedule::command('bookings:cancel-unpaid --hours=1')
    ->hourly()
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/scheduler.log'));

// Sync vehicle status with confirmed bookings every hour
Schedule::command('vehicles:sync-rental-statuses')
    ->hourly()
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/scheduler.log'));

// Send H-1 reminders for confirmed bookings every morning
Schedule::command('bookings:send-start-reminders')
    ->dailyAt('08:00')
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/scheduler.log'));
