<?php

namespace App\Providers;

use App\Models\Booking;
use App\Models\Review;
use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('review-submission', function (Request $request) {
            $userKey = (string) ($request->user()?->getAuthIdentifier() ?? $request->ip());
            $booking = $request->route('booking');
            $vehicleKey = $booking instanceof Booking ? (string) $booking->vehicle_id : 'review';

            return Limit::perMinute(1)
                ->by($userKey . '|' . $vehicleKey)
                ->response(function () {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors([
                            'review_text' => 'Tunggu sebentar sebelum mengirim review lain untuk kendaraan yang sama.',
                        ]);
                });
        });

        View::composer('layouts.admin', function ($view): void {
            $stats = [
                'booking_attention' => 0,
                'booking_overdue' => 0,
                'ktp_pending' => 0,
                'review_pending' => 0,
            ];

            $user = Auth::user();

            if (! $user?->isAdmin()) {
                $view->with('adminSidebarStats', $stats);

                return;
            }

            $pendingBookings = Booking::query()
                ->where('status', 'pending')
                ->where('payment_status', 'pending')
                ->get([
                    'id',
                    'created_at',
                    'duration_days',
                    'status',
                    'payment_status',
                    'payment_proof',
                    'payment_method',
                ]);

            $stats['booking_overdue'] = $pendingBookings
                ->filter(fn (Booking $booking) => $booking->isOverduePayment())
                ->count();

            $stats['booking_attention'] = $pendingBookings
                ->filter(fn (Booking $booking) => $booking->isOverduePayment() || $booking->canBeVerified())
                ->count();

            $stats['ktp_pending'] = User::query()
                ->where('role', 'customer')
                ->where('ktp_status', 'pending')
                ->whereNotNull('ktp_image')
                ->count();

            $stats['review_pending'] = Review::query()
                ->pending()
                ->count();

            $view->with('adminSidebarStats', $stats);
        });
    }
}