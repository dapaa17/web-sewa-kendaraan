# Lampiran A - Kode Program (Versi Ringkas)

## 1. Potongan route utama aplikasi
Sumber: routes/web.php

```php
Route::get('/', fn () => view('welcome'))->name('home');
Route::get('/vehicles', [VehicleController::class, 'browse'])->name('vehicles.browse');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::prefix('bookings')->name('bookings.')->group(function () {
            Route::get('/', [BookingController::class, 'index'])->name('index');
            Route::post('/{booking}/verify-payment', [BookingController::class, 'verifyPayment'])->name('verify-payment');
            Route::post('/{booking}/reschedule', [BookingController::class, 'reschedule'])->name('reschedule');
            Route::post('/{booking}/complete', [BookingController::class, 'complete'])->name('complete');
        });
    });

    Route::prefix('bookings')->name('bookings.')->group(function () {
        Route::post('/', [BookingController::class, 'store'])->name('store');
        Route::get('/{booking}', [BookingController::class, 'show'])->name('show');
    });
});
```

## 2. Potongan logika validasi anti double booking
Sumber: app/Http/Controllers/BookingController.php dan app/Models/Vehicle.php

```php
// BookingController@store
$lock = Cache::lock('vehicle-booking:' . $validated['vehicle_id'], 10);
if (! $lock->get()) {
    return back()->withErrors(['end_date' => 'Booking sedang diproses, coba lagi.']);
}

try {
    $booking = DB::transaction(function () use ($validated) {
        $vehicle = Vehicle::query()->lockForUpdate()->findOrFail($validated['vehicle_id']);
        $availability = $vehicle->getBookingAvailability($validated['start_date'], $validated['end_date']);

        if (! $availability['available'] && ! $availability['queue_available']) {
            return null;
        }

        return Auth::user()->bookings()->create([
            'vehicle_id' => $validated['vehicle_id'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);
    });
} finally {
    $lock->release();
}
```

```php
// Vehicle@getBookingAvailability (inti cek bentrok tanggal)
$blockingBookings = $this->bookings()
    ->blockingAvailability()
    ->overlappingRange($startDate->toDateString(), $endDate->toDateString())
    ->exists();

if ($blockingBookings) {
    return ['available' => false, 'queue_available' => false];
}

return ['available' => true, 'queue_available' => false];
```

## 3. Potongan controller admin untuk manajemen booking
Sumber: app/Http/Controllers/BookingController.php

```php
// Admin list booking
public function index(Request $request)
{
    if (Auth::user()->isAdmin() && ! $request->routeIs('admin.bookings.*')) {
        return redirect()->route('admin.bookings.index', $request->query());
    }

    $bookings = Booking::with(['vehicle', 'user'])->latest()->paginate(10);
    return view('bookings.index', compact('bookings'));
}
```

```php
// Admin verifikasi pembayaran
public function verifyPayment(Request $request, Booking $booking)
{
    $this->authorize('verifyPayment', $booking);

    $validated = $request->validate([
        'verified' => 'required|boolean',
    ]);

    if ((bool) $validated['verified']) {
        $booking->update(['status' => 'confirmed', 'payment_status' => 'paid']);
        return redirect()->route('admin.bookings.index')->with('success', 'Pembayaran diverifikasi');
    }

    $booking->update(['status' => 'cancelled', 'payment_status' => 'failed']);
    return redirect()->route('admin.bookings.index')->with('success', 'Pembayaran ditolak');
}
```
