<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    public function getVehicleAvailability(Request $request, Vehicle $vehicle): JsonResponse
    {
        if ($request->filled('start_date') || $request->filled('end_date')) {
            $validated = $request->validate([
                'start_date' => ['required', 'date', 'after_or_equal:today'],
                'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            ]);

            $startDate = Carbon::parse($validated['start_date'])->startOfDay();
            $endDate = Carbon::parse($validated['end_date'])->startOfDay();
            $rangeData = $vehicle->getCalendarRangeData($startDate, $endDate, $startDate);
            $availability = $vehicle->getBookingAvailability($validated['start_date'], $validated['end_date']);

            return response()->json(array_merge($rangeData, [
                'blocking_bookings' => $this->formatBookings($availability['blocking_bookings']),
                'queue_bookings' => $this->formatBookings($availability['queue_bookings']),
                'maintenance_schedules' => $vehicle->getMaintenancePeriods($startDate, $endDate),
            ]));
        }

        $validated = $request->validate([
            'month' => ['nullable', 'integer', 'between:1,12'],
            'year' => ['nullable', 'integer', 'between:2024,2100'],
        ]);

        $month = (int) ($validated['month'] ?? now()->month);
        $year = (int) ($validated['year'] ?? now()->year);

        return response()->json($vehicle->getCalendarMonthData($month, $year));
    }

    public function getVehiclePrice(Request $request, Vehicle $vehicle): JsonResponse
    {
        $validated = $request->validate([
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
        ]);

        return response()->json($vehicle->getPriceBreakdownForRange(
            $validated['start_date'],
            $validated['end_date']
        ));
    }

    public function getCalendarData(Vehicle $vehicle): JsonResponse
    {
        $windowStart = now()->startOfMonth();
        $windowEnd = $windowStart->copy()->addMonths(2)->endOfMonth();

        return response()->json([
            'vehicle_id' => $vehicle->id,
            'vehicle_name' => $vehicle->name,
            'months' => collect(range(0, 2))->map(function (int $offset) use ($vehicle, $windowStart) {
                $date = $windowStart->copy()->addMonths($offset);

                return $vehicle->getCalendarMonthData($date->month, $date->year);
            })->values()->all(),
            'maintenance_periods' => $vehicle->getMaintenancePeriods($windowStart, $windowEnd),
            'next_available_date' => $vehicle->getNextAvailableDate(),
        ]);
    }

    protected function formatBookings($bookings): array
    {
        return collect($bookings)
            ->map(function (Booking $booking) {
                return [
                    'id' => $booking->id,
                    'start_date' => Carbon::parse($booking->start_date)->toDateString(),
                    'end_date' => Carbon::parse($booking->end_date)->toDateString(),
                ];
            })
            ->values()
            ->all();
    }
}