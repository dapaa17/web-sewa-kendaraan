<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function transactions(Request $request)
    {
        $month = (int) $request->query('month', now()->month);
        $year = (int) $request->query('year', now()->year);

        // Validate month/year
        $month = max(1, min(12, $month));
        $year = max(2020, min((int) now()->year + 1, $year));

        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        // Get completed bookings in the selected month
        $bookings = Booking::with(['vehicle', 'user'])
            ->where('status', 'completed')
            ->where('payment_status', 'paid')
            ->whereBetween('updated_at', [$startDate, $endDate])
            ->orderBy('updated_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        // Summary stats for the month
        $summaryQuery = Booking::where('status', 'completed')
            ->where('payment_status', 'paid')
            ->whereBetween('updated_at', [$startDate, $endDate]);

        $totalTransactions = (clone $summaryQuery)->count();
        $totalRevenue = (clone $summaryQuery)->sum('total_price');
        // Legacy rows may contain negative fee values. Normalize to absolute value in summary.
        $totalLateFees = (clone $summaryQuery)->sum(DB::raw('ABS(COALESCE(late_fee, 0))'));
        $totalDamageFees = (clone $summaryQuery)->sum(DB::raw('ABS(COALESCE(return_damage_fee, 0))'));
        $avgPerTransaction = $totalTransactions > 0 ? $totalRevenue / $totalTransactions : 0;

        // Available months for filter (from earliest booking to now)
        $earliestBooking = Booking::where('status', 'completed')
            ->orderBy('updated_at', 'asc')
            ->first();

        $availableYears = range(
            $earliestBooking ? $earliestBooking->updated_at->year : now()->year,
            (int) now()->year
        );

        $monthLabel = $startDate->translatedFormat('F Y');

        return view('admin.reports.transactions', compact(
            'bookings',
            'month',
            'year',
            'monthLabel',
            'totalTransactions',
            'totalRevenue',
            'totalLateFees',
            'totalDamageFees',
            'avgPerTransaction',
            'availableYears'
        ));
    }
}
