<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class ReportController extends Controller
{
    public function transactions(Request $request)
    {
        $selectedFileType = $request->query('file_type', 'pdf');
        if (!in_array($selectedFileType, ['pdf', 'excel'], true)) {
            $selectedFileType = 'pdf';
        }

        [$startDateTime, $endDateTime, $fromDate, $toDate] = $this->resolveDateRange($request);

        if ($request->query('download') === '1') {
            $routeName = $selectedFileType === 'excel'
                ? 'admin.reports.transactions.export.excel'
                : 'admin.reports.transactions.export.pdf';

            return redirect()->route($routeName, [
                'from' => $fromDate,
                'to' => $toDate,
            ]);
        }

        $yearExpression = $this->yearExpression();
        $monthExpression = $this->monthExpression();

        $baseQuery = Booking::query()
            ->where('status', 'completed')
            ->where('payment_status', 'paid')
            ->whereBetween('updated_at', [$startDateTime, $endDateTime]);

        $summaryQuery = (clone $baseQuery);

        $totalTransactions = (clone $summaryQuery)->count();
        $totalRevenue = (float) (clone $summaryQuery)->sum('total_price');
        $totalFees = (float) (clone $summaryQuery)->sum(DB::raw('ABS(COALESCE(late_fee, 0)) + ABS(COALESCE(return_damage_fee, 0))'));

        $monthlyRows = (clone $baseQuery)
            ->selectRaw("{$yearExpression} as report_year, {$monthExpression} as report_month, COUNT(*) as total_transactions, SUM(total_price) as total_revenue, SUM(ABS(COALESCE(late_fee, 0)) + ABS(COALESCE(return_damage_fee, 0))) as total_fees")
            ->groupByRaw("{$yearExpression}, {$monthExpression}")
            ->orderByDesc('report_year')
            ->orderByDesc('report_month');

        $statementsByYear = $monthlyRows->get()
            ->groupBy('report_year')
            ->map(function (Collection $rows) {
                return $rows->map(function ($row) {
                    $year = (int) $row->report_year;
                    $month = (int) $row->report_month;

                    return [
                        'year' => $year,
                        'month' => $month,
                        'month_name' => Carbon::create($year, $month, 1)->translatedFormat('F'),
                        'total_transactions' => (int) $row->total_transactions,
                        'total_revenue' => (float) $row->total_revenue,
                        'total_fees' => (float) $row->total_fees,
                    ];
                })->values();
            })
            ->all();

        $periodLabel = Carbon::parse($fromDate)->translatedFormat('d M Y') . ' - ' . Carbon::parse($toDate)->translatedFormat('d M Y');

        return view('admin.reports.transactions', compact(
            'fromDate',
            'toDate',
            'periodLabel',
            'selectedFileType',
            'statementsByYear',
            'totalTransactions',
            'totalRevenue',
            'totalFees'
        ));
    }

    public function exportExcel(Request $request)
    {
        [$startDateTime, $endDateTime, $fromDate, $toDate] = $this->resolveDateRange($request);

        $bookings = Booking::with(['vehicle', 'user'])
            ->where('status', 'completed')
            ->where('payment_status', 'paid')
            ->whereBetween('updated_at', [$startDateTime, $endDateTime])
            ->orderBy('updated_at')
            ->get();

        $filename = sprintf(
            'laporan-admin-%s-%s.csv',
            Carbon::parse($fromDate)->format('Ymd'),
            Carbon::parse($toDate)->format('Ymd')
        );

        return response()->streamDownload(function () use ($bookings) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'ID Booking',
                'Tanggal Selesai',
                'Customer',
                'Email',
                'Kendaraan',
                'Plat Nomor',
                'Durasi (hari)',
                'Total Harga',
                'Total Denda',
            ]);

            foreach ($bookings as $booking) {
                $totalFee = abs((float) ($booking->late_fee ?? 0)) + abs((float) ($booking->return_damage_fee ?? 0));

                fputcsv($handle, [
                    $booking->id,
                    optional($booking->updated_at)->format('d-m-Y H:i'),
                    $booking->user->name ?? '-',
                    $booking->user->email ?? '-',
                    $booking->vehicle->name ?? '-',
                    $booking->vehicle->plat_number ?? '-',
                    $booking->duration_days,
                    (float) $booking->total_price,
                    $totalFee,
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function exportPdf(Request $request)
    {
        [$startDateTime, $endDateTime, $fromDate, $toDate] = $this->resolveDateRange($request);

        $bookings = Booking::with(['vehicle', 'user'])
            ->where('status', 'completed')
            ->where('payment_status', 'paid')
            ->whereBetween('updated_at', [$startDateTime, $endDateTime])
            ->orderBy('updated_at', 'desc')
            ->get();

        $monthLabel = Carbon::parse($fromDate)->translatedFormat('d M Y') . ' - ' . Carbon::parse($toDate)->translatedFormat('d M Y');

        $filename = sprintf(
            'laporan-admin-%s-%s.pdf',
            Carbon::parse($fromDate)->format('Ymd'),
            Carbon::parse($toDate)->format('Ymd')
        );

        $pdf = Pdf::loadView('reports.transactions-pdf', [
            'bookings' => $bookings,
            'monthLabel' => $monthLabel,
            'title' => 'Laporan Keuangan Admin',
            'subtitle' => 'Rekap transaksi selesai dan pembayaran berhasil',
            'showCustomerIdentity' => true,
        ])->setPaper('a4', 'landscape');

        return $pdf->stream($filename);
    }

    private function resolveDateRange(Request $request): array
    {
        $from = $this->parseDateInput($request->query('from')) ?? now()->startOfMonth();
        $to = $this->parseDateInput($request->query('to')) ?? now();

        if ($from->gt($to)) {
            [$from, $to] = [$to->copy(), $from->copy()];
        }

        return [
            $from->copy()->startOfDay(),
            $to->copy()->endOfDay(),
            $from->toDateString(),
            $to->toDateString(),
        ];
    }

    private function parseDateInput(?string $value): ?Carbon
    {
        if (!$value) {
            return null;
        }

        try {
            return Carbon::createFromFormat('Y-m-d', $value);
        } catch (\Throwable) {
            return null;
        }
    }

    private function yearExpression(): string
    {
        if (DB::getDriverName() === 'sqlite') {
            return "CAST(strftime('%Y', updated_at) AS INTEGER)";
        }

        return 'YEAR(updated_at)';
    }

    private function monthExpression(): string
    {
        if (DB::getDriverName() === 'sqlite') {
            return "CAST(strftime('%m', updated_at) AS INTEGER)";
        }

        return 'MONTH(updated_at)';
    }
}
