<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\MaintenanceSchedule;
use App\Models\PricingRule;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AdminCalendarController extends Controller
{
    public function index(): \Illuminate\Contracts\View\View
    {
        $referenceDate = now()->startOfMonth();
        $vehicles = Vehicle::query()
            ->select(['id', 'name', 'plat_number', 'total_units', 'vehicle_type', 'status', 'base_price', 'daily_price'])
            ->orderBy('name')
            ->get();
        $fleetVehicles = $vehicles->map(function (Vehicle $vehicle) {
            return [
                'id' => $vehicle->id,
                'name' => $vehicle->name,
                'plate' => $vehicle->plat_number,
                'total_units' => $vehicle->getTotalUnitCount(),
                'type' => $vehicle->vehicle_type,
                'base_price' => (float) ($vehicle->base_price ?? $vehicle->daily_price),
            ];
        })->values()->all();
        $pricingRules = PricingRule::query()
            ->with('vehicle:id,name')
            ->latest('start_date')
            ->latest('id')
            ->get();

        return view('admin.calendar.fleet-calendar', compact('vehicles', 'fleetVehicles', 'referenceDate', 'pricingRules'));
    }

    public function getFleetAvailability(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'month' => ['nullable', 'integer', 'between:1,12'],
            'year' => ['nullable', 'integer', 'between:2024,2100'],
        ]);

        $month = (int) ($validated['month'] ?? now()->month);
        $year = (int) ($validated['year'] ?? now()->year);
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        $vehicles = Vehicle::query()
            ->with([
                'maintenanceSchedules' => fn ($query) => $query->overlappingRange($startDate->toDateString(), $endDate->toDateString()),
                'pricingRules' => fn ($query) => $query->active()->overlappingRange($startDate->toDateString(), $endDate->toDateString()),
                'bookings' => fn ($query) => $query->calendarUnavailable()->overlappingRange($startDate->toDateString(), $endDate->toDateString()),
            ])
            ->orderBy('name')
            ->get();

        $bookedDensity = [];
        $fleet = $vehicles->map(function (Vehicle $vehicle) use ($month, $year, &$bookedDensity) {
            $monthData = $vehicle->getCalendarMonthData($month, $year);
            $dateEntries = collect($monthData['dates']);

            foreach ($dateEntries->where('status', 'booked') as $entry) {
                $bookedDensity[$entry['date']] = ($bookedDensity[$entry['date']] ?? 0) + 1;
            }

            return [
                'vehicle_id' => $vehicle->id,
                'vehicle_name' => $vehicle->name,
                'availability' => $dateEntries->map(function (array $entry) {
                    return match ($entry['status']) {
                        'booked' => 'R',
                        'maintenance' => 'M',
                        default => 'G',
                    };
                })->implode(''),
                'occupancy_rate' => (int) round(($dateEntries->where('status', 'booked')->count() / max($dateEntries->count(), 1)) * 100),
                'bookings_count' => $vehicle->bookings->filter(fn (Booking $booking) => $booking->blocksCalendarAvailability())->count(),
                'next_available_date' => $vehicle->getNextAvailableDate(),
                'dates' => $dateEntries->values()->all(),
                'maintenance_periods' => $monthData['maintenance_periods'],
            ];
        })->values();

        $summary = [
            'total_vehicles' => $vehicles->count(),
            'average_occupancy' => (int) round($fleet->avg('occupancy_rate') ?? 0),
            'peak_dates' => collect($bookedDensity)->sortDesc()->take(3)->keys()->values()->all(),
            'low_dates' => collect($bookedDensity)->sort()->take(3)->keys()->values()->all(),
            'forecast_revenue' => (float) Booking::query()
                ->whereIn('status', ['pending', 'confirmed', 'waiting_list'])
                ->whereIn('payment_status', ['pending', 'paid'])
                ->whereDate('start_date', '<=', $endDate->toDateString())
                ->whereDate('end_date', '>=', $startDate->toDateString())
                ->sum('total_price'),
        ];

        return response()->json([
            'month' => $month,
            'year' => $year,
            'vehicles' => $fleet->all(),
            'summary' => $summary,
        ]);
    }

    public function blockDates(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'vehicle_id' => ['required', 'exists:vehicles,id'],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'reason' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $vehicle = Vehicle::query()->findOrFail($validated['vehicle_id']);
        $blockingBookings = $vehicle->bookings()
            ->calendarUnavailable()
            ->overlappingRange($validated['start_date'], $validated['end_date'])
            ->exists();

        if ($blockingBookings) {
            return $this->calendarErrorResponse($request, 'Tanggal maintenance bentrok dengan booking aktif atau booking yang masih diproses.');
        }

        $schedule = MaintenanceSchedule::create($validated);

        return $this->calendarSuccessResponse($request, 'Tanggal maintenance berhasil diblokir.', [
            'maintenance_schedule_id' => $schedule->id,
        ]);
    }

    public function unblockDates(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'vehicle_id' => ['required', 'exists:vehicles,id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
        ]);

        $startDate = Carbon::parse($validated['start_date'])->startOfDay();
        $endDate = Carbon::parse($validated['end_date'])->startOfDay();
        $schedules = MaintenanceSchedule::query()
            ->where('vehicle_id', $validated['vehicle_id'])
            ->overlappingRange($startDate->toDateString(), $endDate->toDateString())
            ->orderBy('start_date')
            ->get();

        if ($schedules->isEmpty()) {
            return $this->calendarErrorResponse($request, 'Tidak ada jadwal maintenance yang cocok untuk dibuka blokirnya.');
        }

        DB::transaction(function () use ($schedules, $startDate, $endDate) {
            foreach ($schedules as $schedule) {
                $scheduleStart = Carbon::parse($schedule->start_date)->startOfDay();
                $scheduleEnd = Carbon::parse($schedule->end_date)->startOfDay();

                if ($startDate->lte($scheduleStart) && $endDate->gte($scheduleEnd)) {
                    $schedule->delete();
                    continue;
                }

                if ($startDate->gt($scheduleStart) && $endDate->lt($scheduleEnd)) {
                    $originalEnd = $scheduleEnd->copy();
                    $schedule->update(['end_date' => $startDate->copy()->subDay()->toDateString()]);

                    MaintenanceSchedule::create([
                        'vehicle_id' => $schedule->vehicle_id,
                        'start_date' => $endDate->copy()->addDay()->toDateString(),
                        'end_date' => $originalEnd->toDateString(),
                        'reason' => $schedule->reason,
                        'notes' => $schedule->notes,
                    ]);

                    continue;
                }

                if ($startDate->lte($scheduleStart)) {
                    $schedule->update(['start_date' => $endDate->copy()->addDay()->toDateString()]);
                    continue;
                }

                $schedule->update(['end_date' => $startDate->copy()->subDay()->toDateString()]);
            }
        });

        return $this->calendarSuccessResponse($request, 'Blok maintenance berhasil dibuka.');
    }

    public function setPricingRule(Request $request): JsonResponse|RedirectResponse
    {
        $action = $request->input('action', 'save');

        if ($action === 'delete') {
            $validated = $request->validate([
                'pricing_rule_id' => ['required', 'exists:pricing_rules,id'],
            ]);

            PricingRule::query()->whereKey($validated['pricing_rule_id'])->delete();

            return $this->calendarSuccessResponse($request, 'Aturan harga berhasil dihapus.');
        }

        $validated = $request->validate([
            'pricing_rule_id' => ['nullable', 'exists:pricing_rules,id'],
            'vehicle_id' => ['required', 'exists:vehicles,id'],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'discount_percentage' => ['required', 'integer', 'between:0,100'],
            'type' => ['required', 'in:peak_season,low_season,early_bird,last_minute,custom'],
            'description' => ['nullable', 'string', 'max:255'],
            'active' => ['nullable', 'boolean'],
        ]);

        $pricingRule = PricingRule::query()->updateOrCreate(
            ['id' => $validated['pricing_rule_id'] ?? null],
            [
                'vehicle_id' => $validated['vehicle_id'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'discount_percentage' => $validated['discount_percentage'],
                'type' => $validated['type'],
                'description' => $validated['description'] ?? null,
                'active' => (bool) ($validated['active'] ?? true),
            ]
        );

        return $this->calendarSuccessResponse($request, 'Aturan harga berhasil disimpan.', [
            'pricing_rule_id' => $pricingRule->id,
        ]);
    }

    protected function calendarSuccessResponse(Request $request, string $message, array $data = []): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson()) {
            return response()->json(array_merge(['message' => $message], $data));
        }

        return redirect()->route('admin.calendar.index')->with('success', $message);
    }

    protected function calendarErrorResponse(Request $request, string $message): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => $message], 422);
        }

        return redirect()->route('admin.calendar.index')->with('error', $message);
    }
}