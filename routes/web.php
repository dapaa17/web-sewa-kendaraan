<?php

use App\Http\Controllers\Admin\AdminCalendarController;
use App\Http\Controllers\AdminSettingsController;
use App\Http\Controllers\AdminKtpController;
use App\Http\Controllers\AdminBookingTimelineController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\VehicleController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome'); 
})->name('home');

Route::get('/about', function () {
    return view('about');
})->name('about');

Route::get('/panduan', function () {
    return view('guide');
})->name('guide');

// Authentication routes (built-in Laravel)
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Admin Panel (Hidden/Private Routes)
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'adminDashboard'])->name('dashboard');

        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/', [AdminSettingsController::class, 'index'])->name('index');
            Route::post('/booking-schedule', [AdminSettingsController::class, 'updateBookingScheduleDefaults'])->name('booking-schedule');
        });

        Route::get('/calendar', [AdminCalendarController::class, 'index'])->name('calendar.index');
        Route::post('/calendar/block-dates', [AdminCalendarController::class, 'blockDates'])->name('calendar.block-dates');
        Route::post('/calendar/unblock-dates', [AdminCalendarController::class, 'unblockDates'])->name('calendar.unblock-dates');
        Route::post('/calendar/pricing-rules', [AdminCalendarController::class, 'setPricingRule'])->name('calendar.pricing-rules');
        Route::get('/api/fleet-availability', [AdminCalendarController::class, 'getFleetAvailability'])->name('api.fleet-availability');
        
        // Vehicle Management
        Route::prefix('vehicles')->name('vehicles.')->group(function () {
            Route::get('/', [VehicleController::class, 'index'])->name('index');
            Route::get('/create', [VehicleController::class, 'create'])->name('create');
            Route::post('/', [VehicleController::class, 'store'])->name('store');
            Route::get('/{vehicle}/edit', [VehicleController::class, 'edit'])->name('edit');
            Route::put('/{vehicle}', [VehicleController::class, 'update'])->name('update');
            Route::delete('/{vehicle}', [VehicleController::class, 'destroy'])->name('destroy');
            Route::post('/{vehicle}/toggle-maintenance', [VehicleController::class, 'toggleMaintenance'])->name('toggle-maintenance');
        });

        // Booking Management
        Route::prefix('bookings')->name('bookings.')->group(function () {
            Route::get('/', [BookingController::class, 'index'])->name('index');
            Route::get('/timeline', [AdminBookingTimelineController::class, 'index'])->name('timeline');
            Route::post('/{booking}/resend-notification', [BookingController::class, 'resendNotification'])->name('resend-notification');
            Route::post('/{booking}/verify-payment', [BookingController::class, 'verifyPayment'])->name('verify-payment');
            Route::get('/{booking}/reschedule', [BookingController::class, 'showRescheduleForm'])->name('reschedule-form');
            Route::post('/{booking}/reschedule', [BookingController::class, 'reschedule'])->name('reschedule');
            Route::get('/{booking}/complete', [BookingController::class, 'showCompleteForm'])->name('complete-form');
            Route::post('/{booking}/complete', [BookingController::class, 'complete'])->name('complete');
        });

        Route::prefix('reviews')->name('reviews.')->group(function () {
            Route::get('/', [ReviewController::class, 'adminIndex'])->name('index');
            Route::post('/{review}/approve', [ReviewController::class, 'approve'])->name('approve');
            Route::post('/{review}/reject', [ReviewController::class, 'reject'])->name('reject');
            Route::delete('/{review}', [ReviewController::class, 'destroy'])->name('destroy');
        });

        // KTP Verification
        Route::prefix('ktp')->name('ktp.')->group(function () {
            Route::get('/', [AdminKtpController::class, 'index'])->name('index');
            Route::get('/{user}', [AdminKtpController::class, 'show'])->name('show');
            Route::post('/{user}/verify', [AdminKtpController::class, 'verify'])->name('verify');
        });

    });

    // Public Browse & Booking Routes
    Route::get('/vehicles', [VehicleController::class, 'browse'])->name('vehicles.browse');
    Route::get('/vehicles/{vehicle}', [VehicleController::class, 'show'])->name('vehicles.show');
    Route::get('/vehicles/{vehicle}/calendar', [VehicleController::class, 'showWithCalendar'])->name('vehicles.calendar');
    Route::get('/api/vehicles/{vehicle}/availability', [CalendarController::class, 'getVehicleAvailability'])->name('api.vehicle.availability');
    Route::get('/api/vehicles/{vehicle}/price', [CalendarController::class, 'getVehiclePrice'])->name('api.vehicle.price');
    Route::get('/api/vehicles/{vehicle}/calendar-data', [CalendarController::class, 'getCalendarData'])->name('api.vehicle.calendar-data');

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/profile/ktp', [ProfileController::class, 'showKtp'])->name('profile.ktp');
    Route::post('/profile/ktp', [ProfileController::class, 'uploadKtp'])->name('profile.ktp.upload');

    Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews.index');
    Route::get('/bookings/{booking}/review/create', [ReviewController::class, 'create'])->name('reviews.create');
    Route::post('/bookings/{booking}/review', [ReviewController::class, 'store'])
        ->middleware('throttle:review-submission')
        ->name('reviews.store');
    Route::get('/reviews/{review}/edit', [ReviewController::class, 'edit'])->name('reviews.edit');
    Route::put('/reviews/{review}', [ReviewController::class, 'update'])->name('reviews.update');
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
    Route::post('/reviews/{review}/helpful', [ReviewController::class, 'toggleHelpful'])->name('reviews.helpful');

    // Booking Routes
    Route::prefix('bookings')->name('bookings.')->group(function () {
        Route::get('/', [BookingController::class, 'index'])->name('index');
        Route::get('/create/{vehicle}', [BookingController::class, 'create'])->name('create');
        Route::post('/', [BookingController::class, 'store'])->name('store');
        Route::get('/{booking}', [BookingController::class, 'show'])->name('show');
        Route::delete('/{booking}', [BookingController::class, 'cancel'])->name('cancel');
        
        // Payment Routes
        Route::get('/{booking}/payment', [BookingController::class, 'payment'])->name('payment');
        Route::post('/{booking}/process-payment', [BookingController::class, 'processPayment'])->name('process-payment');
        Route::get('/{booking}/whatsapp-confirmation', [BookingController::class, 'whatsappPayment'])->name('whatsapp-payment');
        Route::get('/{booking}/transfer-proof', [BookingController::class, 'transferProofPayment'])->name('transfer-proof');
        Route::post('/{booking}/upload-proof', [BookingController::class, 'uploadPaymentProof'])->name('upload-proof');
    });

    // WhatsApp confirmation flow
    Route::prefix('payments')->name('payments.')->group(function () {
        Route::get('/{booking}/whatsapp-confirmation', [PaymentController::class, 'showWhatsAppConfirmation'])->name('whatsapp-confirmation');
    });
});

require __DIR__ . '/auth.php';