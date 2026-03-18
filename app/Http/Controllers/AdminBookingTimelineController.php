<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class AdminBookingTimelineController extends Controller
{
    public function index(Request $request): View
    {
        $vehicleType = in_array($request->query('vehicle_type'), ['mobil', 'motor'], true)
            ? $request->query('vehicle_type')
            : null;
        $search = trim((string) $request->query('search', ''));
        $problemOnly = $request->boolean('problem_only');
        $weekStart = $this->resolveWeekStart($request->query('week'));
        $weekEnd = $weekStart->copy()->addDays(6);

        $vehicleQuery = Vehicle::query()
            ->with([
                'bookings' => function ($query) use ($weekStart, $weekEnd) {
                    $query->with('user')
                        ->whereIn('status', ['pending', 'confirmed', 'waiting_list'])
                        ->whereDate('start_date', '<=', $weekEnd->toDateString())
                        ->whereDate('end_date', '>=', $weekStart->toDateString())
                        ->orderBy('start_date')
                        ->orderBy('id');
                },
            ])
            ->orderByRaw("CASE WHEN vehicle_type = 'mobil' THEN 0 ELSE 1 END")
            ->orderBy('name');

        if ($vehicleType) {
            $vehicleQuery->ofType($vehicleType);
        }

        if ($search !== '') {
            $vehicleQuery->where(function ($query) use ($search, $weekStart, $weekEnd) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('plat_number', 'like', '%' . $search . '%')
                    ->orWhereHas('bookings', function ($bookingQuery) use ($search, $weekStart, $weekEnd) {
                        $bookingQuery->whereDate('start_date', '<=', $weekEnd->toDateString())
                            ->whereDate('end_date', '>=', $weekStart->toDateString())
                            ->whereHas('user', function ($userQuery) use ($search) {
                                $userQuery->where('name', 'like', '%' . $search . '%');
                            });
                    });
            });
        }

        $vehicles = $vehicleQuery->get();

        $bookingIds = $vehicles->flatMap(fn (Vehicle $vehicle) => $vehicle->bookings->pluck('id'))->filter()->all();

        $awaitingReturnLookup = $bookingIds === []
            ? []
            : Booking::query()
                ->awaitingVehicleReturn()
                ->whereIn('id', $bookingIds)
                ->pluck('id')
                ->flip()
                ->all();

        $timelineVehicles = $vehicles
            ->map(fn (Vehicle $vehicle) => $this->buildVehicleTimelineRow($vehicle, $weekStart, $weekEnd, $awaitingReturnLookup))
            ->when($problemOnly, fn (Collection $collection) => $collection->filter(fn (array $row) => $row['has_attention']))
            ->values();

        $weekDays = collect(range(0, 6))->map(fn (int $offset) => $weekStart->copy()->addDays($offset));

        $summary = [
            'vehicles' => $timelineVehicles->count(),
            'bookings' => $timelineVehicles->sum('booking_count'),
            'attention' => $timelineVehicles->filter(fn (array $row) => $row['has_attention'])->count(),
            'free' => $timelineVehicles->filter(fn (array $row) => $row['booking_count'] === 0 && $row['vehicle']->status !== 'maintenance')->count(),
        ];

        return view('bookings.timeline', [
            'timelineVehicles' => $timelineVehicles,
            'weekDays' => $weekDays,
            'weekStart' => $weekStart,
            'weekEnd' => $weekEnd,
            'previousWeek' => $weekStart->copy()->subWeek(),
            'nextWeek' => $weekStart->copy()->addWeek(),
            'vehicleType' => $vehicleType,
            'search' => $search,
            'problemOnly' => $problemOnly,
            'summary' => $summary,
            'legend' => [
                ['label' => 'Sedang Disewa', 'class' => 'active'],
                ['label' => 'Terjadwal', 'class' => 'scheduled'],
                ['label' => 'Tertahan Maintenance', 'class' => 'maintenance-hold'],
                ['label' => 'Menunggu Unit', 'class' => 'awaiting-return'],
                ['label' => 'Antrean', 'class' => 'waiting-list'],
                ['label' => 'Menunggu Bukti', 'class' => 'awaiting-proof'],
                ['label' => 'Lewat Deadline', 'class' => 'overdue-payment'],
            ],
        ]);
    }

    private function resolveWeekStart(?string $week): Carbon
    {
        if (! $week) {
            return now()->startOfWeek(Carbon::MONDAY);
        }

        try {
            return Carbon::parse($week)->startOfWeek(Carbon::MONDAY);
        } catch (\Throwable) {
            return now()->startOfWeek(Carbon::MONDAY);
        }
    }

    /**
     * @param  array<int|string, mixed>  $awaitingReturnLookup
     * @return array<string, mixed>
     */
    private function buildVehicleTimelineRow(Vehicle $vehicle, Carbon $weekStart, Carbon $weekEnd, array $awaitingReturnLookup): array
    {
        $events = $vehicle->bookings
            ->sortBy([
                fn (Booking $booking) => $booking->start_date->getTimestamp(),
                fn (Booking $booking) => $this->getVariantSortWeight($this->resolveTimelineVariant($booking, $awaitingReturnLookup)),
                fn (Booking $booking) => $booking->id,
            ])
            ->values()
            ->map(function (Booking $booking) use ($weekStart, $weekEnd, $awaitingReturnLookup) {
                $variant = $this->resolveTimelineVariant($booking, $awaitingReturnLookup);
                $meta = $this->getTimelineVariantMeta($variant);
                $quickAction = $this->getTimelineQuickAction($booking);

                $visibleStart = $booking->start_date->copy()->startOfDay();
                $visibleEnd = $booking->end_date->copy()->startOfDay();

                if ($visibleStart->lt($weekStart)) {
                    $visibleStart = $weekStart->copy();
                }

                if ($visibleEnd->gt($weekEnd)) {
                    $visibleEnd = $weekEnd->copy();
                }

                return [
                    'booking' => $booking,
                    'grid_start' => $weekStart->diffInDays($visibleStart) + 1,
                    'span' => $visibleStart->diffInDays($visibleEnd) + 1,
                    'variant' => $variant,
                    'label' => $meta['label'],
                    'class' => $meta['class'],
                    'is_problem' => $meta['is_problem'],
                    'customer_name' => $booking->user->name,
                    'time_label' => $booking->pickup_time_label . ' - ' . $booking->return_time_label,
                    'date_label' => $booking->start_date->format('d M') . ' - ' . $booking->end_date->format('d M'),
                    'detail_url' => route('bookings.show', $booking),
                    'quick_action_label' => $quickAction['label'],
                    'quick_action_url' => $quickAction['url'],
                    'quick_action_icon' => $quickAction['icon'],
                    'modal_payload' => [
                        'booking_id' => $booking->id,
                        'status_label' => $meta['label'],
                        'status_description' => $this->getTimelineVariantDescription($variant, $booking),
                        'customer_name' => $booking->user->name,
                        'customer_email' => $booking->user->email,
                        'vehicle_name' => $booking->vehicle->name,
                        'vehicle_plate' => $booking->vehicle->plat_number,
                        'vehicle_type' => $booking->vehicle->getTypeLabel(),
                        'vehicle_state' => match ($booking->vehicle->status) {
                            'maintenance' => 'Maintenance',
                            'rented' => 'Sedang Disewa',
                            default => 'Tersedia',
                        },
                        'payment_method' => $booking->getPaymentMethodLabel(),
                        'payment_status' => $booking->getPaymentStatusLabel(),
                        'schedule_label' => $booking->start_date->format('d M Y') . ' ' . $booking->pickup_time_label . ' - ' . $booking->end_date->format('d M Y') . ' ' . $booking->return_time_label,
                        'duration_label' => $booking->duration_days . ' hari',
                        'total_label' => 'Rp' . number_format($booking->total_price, 0, ',', '.'),
                        'notes' => (string) ($booking->notes ?? ''),
                        'detail_url' => route('bookings.show', $booking),
                        'quick_action_label' => $quickAction['label'],
                        'quick_action_url' => $quickAction['url'],
                        'quick_action_icon' => $quickAction['icon'],
                    ],
                    'starts_before_week' => $booking->start_date->lt($weekStart),
                    'ends_after_week' => $booking->end_date->gt($weekEnd),
                ];
            })
            ->all();

        $problemCount = collect($events)->where('is_problem', true)->count();
        $bookingCount = count($events);
        $hasAttention = $problemCount > 0 || $vehicle->status === 'maintenance';

        return [
            'vehicle' => $vehicle,
            'lanes' => $this->buildTimelineLanes($events),
            'booking_count' => $bookingCount,
            'problem_count' => $problemCount,
            'has_attention' => $hasAttention,
            'state_label' => $vehicle->status === 'maintenance'
                ? 'Maintenance'
                : ($hasAttention
                    ? 'Perlu perhatian'
                    : ($bookingCount > 0 ? 'Terjadwal minggu ini' : 'Kosong minggu ini')),
            'state_class' => $vehicle->status === 'maintenance'
                ? 'maintenance'
                : ($hasAttention ? 'attention' : 'normal'),
        ];
    }

    /**
     * @param  array<int, array<string, mixed>>  $events
     * @return array<int, array<int, array<string, mixed>>>
     */
    private function buildTimelineLanes(array $events): array
    {
        $lanes = [];
        $laneEndColumns = [];

        foreach ($events as $event) {
            $eventEnd = $event['grid_start'] + $event['span'] - 1;
            $placed = false;

            foreach ($laneEndColumns as $laneIndex => $laneEnd) {
                if ($event['grid_start'] > $laneEnd) {
                    $lanes[$laneIndex][] = $event;
                    $laneEndColumns[$laneIndex] = $eventEnd;
                    $placed = true;

                    break;
                }
            }

            if ($placed) {
                continue;
            }

            $lanes[] = [$event];
            $laneEndColumns[] = $eventEnd;
        }

        return $lanes;
    }

    /**
     * @param  array<int|string, mixed>  $awaitingReturnLookup
     */
    private function resolveTimelineVariant(Booking $booking, array $awaitingReturnLookup): string
    {
        if ($booking->isMaintenanceHold()) {
            return 'maintenance_hold';
        }

        if ($booking->status === 'waiting_list') {
            return 'waiting_list';
        }

        if ($booking->status === 'pending') {
            if ($booking->isOverduePayment()) {
                return 'overdue_payment';
            }

            if ($booking->isAwaitingPaymentProof()) {
                return 'awaiting_proof';
            }

            return 'pending';
        }

        if ($booking->status === 'confirmed' && $booking->payment_status === 'paid') {
            if (isset($awaitingReturnLookup[$booking->id])) {
                return 'awaiting_return';
            }

            if ($booking->hasNotStartedYet()) {
                return 'scheduled';
            }

            return 'active';
        }

        return 'scheduled';
    }

    /**
     * @return array{label: string, class: string, is_problem: bool}
     */
    private function getTimelineVariantMeta(string $variant): array
    {
        return match ($variant) {
            'active' => ['label' => 'Sedang Disewa', 'class' => 'active', 'is_problem' => false],
            'scheduled' => ['label' => 'Terjadwal', 'class' => 'scheduled', 'is_problem' => false],
            'maintenance_hold' => ['label' => 'Tertahan Maintenance', 'class' => 'maintenance-hold', 'is_problem' => true],
            'awaiting_return' => ['label' => 'Menunggu Unit', 'class' => 'awaiting-return', 'is_problem' => true],
            'waiting_list' => ['label' => 'Antrean', 'class' => 'waiting-list', 'is_problem' => true],
            'awaiting_proof' => ['label' => 'Menunggu Bukti', 'class' => 'awaiting-proof', 'is_problem' => true],
            'overdue_payment' => ['label' => 'Lewat Deadline', 'class' => 'overdue-payment', 'is_problem' => true],
            default => ['label' => 'Pending', 'class' => 'pending', 'is_problem' => false],
        };
    }

    private function getTimelineVariantDescription(string $variant, Booking $booking): string
    {
        return match ($variant) {
            'active' => 'Booking sedang berjalan dan unit dipakai sesuai jadwal.',
            'scheduled' => 'Booking sudah aman dan tinggal menunggu tanggal mulai.',
            'maintenance_hold' => 'Unit sedang masuk maintenance setelah inspeksi pengembalian, jadi booking ini menunggu penyesuaian admin.',
            'awaiting_return' => 'Jadwal booking sudah masuk, tetapi unit sebelumnya belum kembali.',
            'waiting_list' => 'Booking menunggu giliran karena kendaraan masih bentrok dengan jadwal lain.',
            'awaiting_proof' => 'Admin masih menunggu customer mengunggah bukti transfer.',
            'overdue_payment' => 'Booking sudah melewati tenggat pembayaran dan perlu tindak lanjut.',
            default => 'Booking masih menunggu progres berikutnya.',
        };
    }

    /**
     * @return array{label: string|null, url: string|null, icon: string|null}
     */
    private function getTimelineQuickAction(Booking $booking): array
    {
        if ($booking->canBeRescheduledByAdmin()) {
            return [
                'label' => 'Jadwalkan Ulang',
                'url' => route('admin.bookings.reschedule-form', $booking),
                'icon' => 'bi-calendar2-week',
            ];
        }

        if ($booking->canBeCompleted()) {
            return [
                'label' => 'Konfirmasi Pengembalian',
                'url' => route('admin.bookings.complete-form', $booking),
                'icon' => 'bi-check-circle',
            ];
        }

        if ($booking->canBeVerified()) {
            return [
                'label' => 'Tinjau Pembayaran',
                'url' => route('bookings.show', $booking),
                'icon' => 'bi-search',
            ];
        }

        return [
            'label' => null,
            'url' => null,
            'icon' => null,
        ];
    }

    private function getVariantSortWeight(string $variant): int
    {
        return match ($variant) {
            'active' => 0,
            'maintenance_hold' => 1,
            'awaiting_return' => 2,
            'scheduled' => 3,
            'waiting_list' => 4,
            'awaiting_proof' => 5,
            'overdue_payment' => 6,
            default => 7,
        };
    }
}