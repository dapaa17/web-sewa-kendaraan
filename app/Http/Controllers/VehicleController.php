<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Vehicle;
use App\Models\ReviewHelpfulVote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class VehicleController extends Controller
{
    private const CALENDAR_ADD_ONS = [
        ['id' => 'helmet_extra', 'label' => 'Helm tambahan', 'description' => 'Cocok untuk motor atau penumpang tambahan.'],
        ['id' => 'phone_holder', 'label' => 'Phone holder', 'description' => 'Bantu navigasi tetap aman selama perjalanan.'],
        ['id' => 'charger_kit', 'label' => 'Charger kit', 'description' => 'Permintaan charger mobil atau USB adapter.'],
        ['id' => 'child_seat', 'label' => 'Child seat', 'description' => 'Ajukan kebutuhan kursi anak di catatan booking.'],
    ];

    // Admin: Show all vehicles
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $vehicleType = $request->query('vehicle_type');
        $status = $request->query('status');
        $transmission = $request->query('transmission');
        $today = now()->toDateString();

        $query = Vehicle::query();
        $activeBookingConstraint = function ($bookingQuery) use ($today) {
            $bookingQuery->where('status', 'confirmed')
                ->where('payment_status', 'paid')
                ->whereDate('start_date', '<=', $today);
        };

        if ($search !== '') {
            $query->where(function ($vehicleQuery) use ($search) {
                $vehicleQuery->where('name', 'like', '%' . $search . '%')
                    ->orWhere('plat_number', 'like', '%' . $search . '%');
            });
        }

        if (in_array($vehicleType, ['mobil', 'motor'], true)) {
            $query->where('vehicle_type', $vehicleType);
        }

        if (in_array($transmission, ['Manual', 'Otomatis'], true)) {
            $query->where('transmission', $transmission);
        }

        if ($status === 'maintenance') {
            $query->where('status', 'maintenance');
        } elseif ($status === 'rented') {
            $query->where('status', '!=', 'maintenance')
                ->whereHas('bookings', $activeBookingConstraint);
        } elseif ($status === 'available') {
            $query->where('status', '!=', 'maintenance')
                ->whereRaw('(
                    SELECT COUNT(*)
                    FROM bookings
                    WHERE bookings.vehicle_id = vehicles.id
                        AND bookings.deleted_at IS NULL
                        AND bookings.status = ?
                        AND bookings.payment_status = ?
                        AND bookings.start_date <= ?
                ) < COALESCE(vehicles.total_units, 1)', ['confirmed', 'paid', $today]);
        }

        $vehicles = $query->latest()->paginate(10)->withQueryString();
        $filters = compact('search', 'vehicleType', 'status', 'transmission');

        return view('vehicles.index', compact('vehicles', 'filters'));
    }

    // Admin: Show create form
    public function create()
    {
        return view('vehicles.create');
    }

    // Admin: Store vehicle
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'vehicle_type' => 'required|in:mobil,motor',
            'plat_number' => 'required|string|unique:vehicles',
            'total_units' => 'required|integer|min:1|max:999',
            'transmission' => 'required|in:Manual,Otomatis',
            'year' => 'required|integer|min:1900|max:' . date('Y'),
            'daily_price' => 'required|numeric|min:0',
            'weekend_multiplier' => 'nullable|numeric|min:1',
            'peak_season_multiplier' => 'nullable|numeric|min:1',
            'low_season_multiplier' => 'nullable|numeric|min:0.1',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $validated['base_price'] = $validated['daily_price'];
        $validated['weekend_multiplier'] = $validated['weekend_multiplier'] ?? 1.2;
        $validated['peak_season_multiplier'] = $validated['peak_season_multiplier'] ?? 1.4;
        $validated['low_season_multiplier'] = $validated['low_season_multiplier'] ?? 0.8;

        // Handle image upload
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('vehicles', 'public');
        }

        Vehicle::create($validated);

        return redirect()->route('admin.vehicles.index')->with('success', 'Kendaraan berhasil ditambahkan');
    }

    // Admin: Show edit form
    public function edit(Vehicle $vehicle)
    {
        return view('vehicles.edit', compact('vehicle'));
    }

    // Admin: Update vehicle
    public function update(Request $request, Vehicle $vehicle)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'vehicle_type' => 'required|in:mobil,motor',
            'plat_number' => 'required|string|unique:vehicles,plat_number,' . $vehicle->id,
            'total_units' => 'sometimes|integer|min:1|max:999',
            'transmission' => 'required|in:Manual,Otomatis',
            'year' => 'required|integer|min:1900|max:' . date('Y'),
            'daily_price' => 'required|numeric|min:0',
            'weekend_multiplier' => 'nullable|numeric|min:1',
            'peak_season_multiplier' => 'nullable|numeric|min:1',
            'low_season_multiplier' => 'nullable|numeric|min:0.1',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $validated['base_price'] = $validated['daily_price'];
        $validated['total_units'] = $validated['total_units'] ?? $vehicle->getTotalUnitCount();
        $validated['weekend_multiplier'] = $validated['weekend_multiplier'] ?? $vehicle->weekend_multiplier ?? 1.2;
        $validated['peak_season_multiplier'] = $validated['peak_season_multiplier'] ?? $vehicle->peak_season_multiplier ?? 1.4;
        $validated['low_season_multiplier'] = $validated['low_season_multiplier'] ?? $vehicle->low_season_multiplier ?? 0.8;

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($vehicle->image) {
                Storage::disk('public')->delete($vehicle->image);
            }
            $validated['image'] = $request->file('image')->store('vehicles', 'public');
        }

        $vehicle->update($validated);

        $vehicle->syncRentalStatus();

        return redirect()->route('admin.vehicles.index')->with('success', 'Kendaraan berhasil diperbarui');
    }

    // Admin: Delete vehicle (soft delete)
    public function destroy(Vehicle $vehicle)
    {
        $hasActiveBookings = $vehicle->bookings()
            ->whereIn('status', ['pending', 'confirmed', 'waiting_list'])
            ->exists();

        if ($hasActiveBookings) {
            return redirect()->route('admin.vehicles.index')
                ->with('error', 'Tidak dapat menghapus kendaraan yang masih memiliki booking aktif.');
        }

        $vehicle->delete();
        return redirect()->route('admin.vehicles.index')->with('success', 'Kendaraan berhasil dihapus');
    }

    // Customer: Browse vehicles
    public function browse(Request $request)
    {
        $query = Vehicle::query()
            ->withCount('approvedReviews')
            ->withAvg('approvedReviews', 'rating')
            ->with(['topApprovedReview.user']);
        $hasAvailabilityFilter = $request->filled('start_date') && $request->filled('end_date');
        $selectedDateLabel = null;

        // Filter by vehicle type
        if ($request->filled('vehicle_type')) {
            $query->ofType($request->vehicle_type);
        }

        // Filter by transmission
        if ($request->filled('transmission')) {
            $query->where('transmission', $request->transmission);
        }

        // Search
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($hasAvailabilityFilter) {
            $selectedDateLabel = Carbon::parse($request->query('start_date'))->format('d M Y')
                . ' - '
                . Carbon::parse($request->query('end_date'))->format('d M Y');
        }

        // Sort by price
        if ($request->filled('sort')) {
            if ($request->sort === 'price_asc') {
                $query->orderBy('daily_price', 'asc');
            } elseif ($request->sort === 'price_desc') {
                $query->orderBy('daily_price', 'desc');
            }
        }

        $vehicles = $query->paginate(6)->withQueryString();

        if ($hasAvailabilityFilter) {
            $vehicles->getCollection()->transform(function (Vehicle $vehicle) use ($request) {
                $vehicle->bookingAvailability = $vehicle->getBookingAvailability(
                    $request->query('start_date'),
                    $request->query('end_date')
                );

                return $vehicle;
            });

            $vehicles->setCollection(
                $vehicles->getCollection()
                    ->filter(function (Vehicle $vehicle) {
                        $availability = $vehicle->bookingAvailability ?? ['available' => false, 'queue_available' => false];

                        return !empty($availability['available']) || !empty($availability['queue_available']);
                    })
                    ->values()
            );
        }

        return view('vehicles.browse', compact('vehicles', 'hasAvailabilityFilter', 'selectedDateLabel'));
    }

    // Show vehicle details
    public function show(Request $request, Vehicle $vehicle)
    {
        $vehicle->loadCount('approvedReviews');
        $vehicle->loadAvg('approvedReviews', 'rating');

        $approvedReviews = $vehicle->approvedReviews()
            ->with(['user', 'booking'])
            ->latest('moderated_at')
            ->latest('id')
            ->paginate(6)
            ->withQueryString();

        $ratingBreakdownCounts = $vehicle->approvedReviews()
            ->selectRaw('rating, COUNT(*) as aggregate')
            ->groupBy('rating')
            ->pluck('aggregate', 'rating');

        $ratingBreakdown = collect(range(5, 1))->mapWithKeys(function (int $rating) use ($ratingBreakdownCounts) {
            return [$rating => (int) ($ratingBreakdownCounts[$rating] ?? 0)];
        });

        $helpfulReviewIds = collect();

        if ($request->user()) {
            $helpfulReviewIds = ReviewHelpfulVote::query()
                ->where('user_id', $request->user()->id)
                ->whereIn('review_id', $approvedReviews->pluck('id'))
                ->pluck('review_id');
        }

        return view('vehicles.show', compact('vehicle', 'approvedReviews', 'ratingBreakdown', 'helpfulReviewIds'));
    }

    // Customer: Show vehicle details with availability calendar
    public function showWithCalendar(Request $request, Vehicle $vehicle)
    {
        $vehicle->loadCount('approvedReviews');
        $vehicle->loadAvg('approvedReviews', 'rating');

        $calendarStart = now()->startOfMonth();
        $calendarMonths = collect(range(0, 2))->map(function (int $offset) use ($calendarStart) {
            $month = $calendarStart->copy()->addMonths($offset);

            return [
                'month' => $month->month,
                'year' => $month->year,
                'label' => $month->translatedFormat('F Y'),
            ];
        })->values();

        $nextAvailableDate = $vehicle->getNextAvailableDate();
        $calendarData = $vehicle->getCalendarRangeData(
            $calendarStart,
            $calendarStart->copy()->addMonths(2)->endOfMonth(),
            now()->startOfDay()
        );

        return view('vehicles.show-with-calendar', [
            'vehicle' => $vehicle,
            'calendarMonths' => $calendarMonths,
            'calendarData' => $calendarData,
            'nextAvailableDate' => $nextAvailableDate,
            'calendarAddOns' => collect(self::CALENDAR_ADD_ONS),
        ]);
    }

    // Admin: Toggle maintenance status
    public function toggleMaintenance(Vehicle $vehicle)
    {
        if ($vehicle->status === 'maintenance') {
            $vehicle->update(['status' => 'available']);
            $message = 'Kendaraan berhasil diaktifkan kembali.';
        } else {
            // Check if vehicle has active bookings
            $hasActiveBookings = $vehicle->bookings()
                ->whereIn('status', ['pending', 'waiting_list', 'confirmed'])
                ->whereIn('payment_status', ['pending', 'paid'])
                ->exists();

            if ($hasActiveBookings) {
                return back()->with('error', 'Tidak dapat set maintenance - kendaraan memiliki booking aktif.');
            }

            $vehicle->update(['status' => 'maintenance']);
            $message = 'Kendaraan berhasil di-set ke maintenance.';
        }

        return back()->with('success', $message);
    }

    // API: Check vehicle availability for dates
    public function checkAvailability(Request $request, Vehicle $vehicle)
    {
        $validator = Validator::make($request->query(), [
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();
        $startDate = $validated['start_date'];
        $endDate = $validated['end_date'];

        $availability = $vehicle->getBookingAvailability($startDate, $endDate);

        return response()->json([
            'available' => $availability['available'],
            'queue_available' => $availability['queue_available'],
            'blocking_bookings' => $availability['blocking_bookings'],
            'queue_bookings' => $availability['queue_bookings'],
        ]);
    }
}