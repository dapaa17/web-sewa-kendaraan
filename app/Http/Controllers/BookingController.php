<?php

namespace App\Http\Controllers;

use App\Mail\BookingReadyForPickupMail;
use App\Mail\BookingRescheduledMail;
use App\Mail\PaymentFailedMail;
use App\Mail\PaymentVerifiedMail;
use App\Mail\WaitingListActivatedMail;
use App\Models\AppSetting;
use App\Models\Booking;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Mail\Mailable;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');
        $isAdmin = Auth::user()->isAdmin();

        if ($isAdmin && ! $request->routeIs('admin.bookings.*')) {
            return redirect()->route('admin.bookings.index', $request->query());
        }
        
        // Admin sees all bookings, customer sees only their own
        if ($isAdmin) {
            $query = Booking::with(['vehicle', 'user']);
            $countQuery = Booking::query();
        } else {
            $query = Auth::user()->bookings()->with('vehicle');
            $countQuery = Auth::user()->bookings();
        }
        
        // Apply status filter
        if ($status && $status !== 'all') {
            if ($status === 'active') {
                // Active = confirmed + paid, already started, and unit is truly with this booking.
                $query->operationallyActive();
            } elseif ($status === 'pending') {
                $query->displayPending();
            } elseif ($status === 'maintenance_hold') {
                $query->maintenanceHold();
            } elseif ($status === 'overdue_payment') {
                $query->where('status', 'pending')
                    ->where('payment_status', 'pending')
                    ->whereNull('payment_proof');
            } elseif ($status === 'awaiting_proof') {
                $query->awaitingPaymentProof();
            } elseif ($status === 'scheduled') {
                $query->scheduled();
            } elseif ($status === 'awaiting_return') {
                $query->awaitingVehicleReturn();
            } elseif ($status === 'waiting_list') {
                $query->where('status', 'waiting_list')
                    ->whereNull('maintenance_hold_at');
            } elseif ($status === 'cancelled') {
                $query->displayCancelled();
            } else {
                $query->where('status', $status);
            }
        }

        if ($isAdmin && in_array($status, [null, 'all', 'pending', 'awaiting_proof', 'overdue_payment', 'maintenance_hold'], true)) {
            $bookings = $this->paginateAdminBookings($query, $request, $status);
        } else {
            $bookings = $query->latest()->paginate(10)->withQueryString();
        }

        $overduePaymentCount = (clone $countQuery)
            ->where('status', 'pending')
            ->where('payment_status', 'pending')
            ->whereNull('payment_proof')
            ->get()
            ->filter(fn (Booking $booking) => $booking->isOverduePayment())
            ->count();
        
        $counts = [
            'all' => (clone $countQuery)->count(),
            'pending' => (clone $countQuery)->displayPending()->count(),
            'maintenance_hold' => (clone $countQuery)->maintenanceHold()->count(),
            'overdue_payment' => $overduePaymentCount,
            'awaiting_proof' => (clone $countQuery)->awaitingPaymentProof()->count(),
            'scheduled' => (clone $countQuery)->scheduled()->count(),
            'awaiting_return' => (clone $countQuery)->awaitingVehicleReturn()->count(),
            'waiting_list' => (clone $countQuery)->where('status', 'waiting_list')->whereNull('maintenance_hold_at')->count(),
            'active' => (clone $countQuery)->operationallyActive()->count(),
            'completed' => (clone $countQuery)->where('status', 'completed')->count(),
            'cancelled' => (clone $countQuery)->displayCancelled()->count(),
        ];
        
        return view('bookings.index', compact('bookings', 'counts', 'status'));
    }

    private function paginateAdminBookings($query, Request $request, ?string $status): LengthAwarePaginator
    {
        $sortedBookings = $query->get()
            ->when($status === 'overdue_payment', fn ($bookings) => $bookings->filter(fn (Booking $booking) => $booking->isOverduePayment()))
            ->sort(fn (Booking $left, Booking $right) => $this->compareAdminBookings($left, $right, $status))
            ->values();

        $perPage = 10;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();

        return new LengthAwarePaginator(
            $sortedBookings->forPage($currentPage, $perPage)->values(),
            $sortedBookings->count(),
            $perPage,
            $currentPage,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );
    }

    private function compareAdminBookings(Booking $left, Booking $right, ?string $status): int
    {
        if ($status === 'maintenance_hold') {
            return $this->compareBookingsByScheduleStart($left, $right);
        }

        $leftMaintenanceHold = $left->isMaintenanceHold();
        $rightMaintenanceHold = $right->isMaintenanceHold();

        if ($leftMaintenanceHold !== $rightMaintenanceHold) {
            return $leftMaintenanceHold ? -1 : 1;
        }

        if ($leftMaintenanceHold && $rightMaintenanceHold) {
            return $this->compareBookingsByScheduleStart($left, $right);
        }

        if ($status === 'overdue_payment') {
            return $this->compareBookingsByPaymentDeadline($left, $right);
        }

        if ($status === 'awaiting_proof') {
            return $this->compareBookingsByPaymentDeadline($left, $right);
        }

        $leftAwaitingProof = $left->isAwaitingPaymentProof();
        $rightAwaitingProof = $right->isAwaitingPaymentProof();

        if ($leftAwaitingProof !== $rightAwaitingProof) {
            return $leftAwaitingProof ? -1 : 1;
        }

        if ($leftAwaitingProof && $rightAwaitingProof) {
            return $this->compareBookingsByPaymentDeadline($left, $right);
        }

        return $right->created_at->getTimestamp() <=> $left->created_at->getTimestamp();
    }

    private function compareBookingsByScheduleStart(Booking $left, Booking $right): int
    {
        $startComparison = $left->start_date->getTimestamp() <=> $right->start_date->getTimestamp();

        if ($startComparison !== 0) {
            return $startComparison;
        }

        $leftHoldAt = $left->maintenance_hold_at?->getTimestamp() ?? 0;
        $rightHoldAt = $right->maintenance_hold_at?->getTimestamp() ?? 0;
        $holdComparison = $leftHoldAt <=> $rightHoldAt;

        if ($holdComparison !== 0) {
            return $holdComparison;
        }

        return $left->id <=> $right->id;
    }

    private function compareBookingsByPaymentDeadline(Booking $left, Booking $right): int
    {
        $deadlineComparison = $left->getPaymentDeadline()->getTimestamp() <=> $right->getPaymentDeadline()->getTimestamp();

        if ($deadlineComparison !== 0) {
            return $deadlineComparison;
        }

        return $left->id <=> $right->id;
    }

    public function create($vehicle)
    {
        $vehicle = Vehicle::findOrFail($vehicle);
        $bookingScheduleDefaults = AppSetting::getBookingScheduleDefaults();

        return view('bookings.create', compact('vehicle', 'bookingScheduleDefaults'));
    }

    public function store(Request $request): RedirectResponse
    {
        $bookingScheduleDefaults = AppSetting::getBookingScheduleDefaults();

        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'pickup_time' => 'nullable|date_format:H:i',
            'return_time' => 'nullable|date_format:H:i',
            'notes' => 'nullable|string|max:2000',
        ]);

        $validated['pickup_time'] = $validated['pickup_time'] ?? $bookingScheduleDefaults['pickup_time'];
        $validated['return_time'] = $validated['return_time'] ?? $bookingScheduleDefaults['return_time'];

        if (
            $validated['start_date'] === $validated['end_date']
            && strcmp($validated['return_time'], $validated['pickup_time']) <= 0
        ) {
            return redirect()->back()->withErrors([
                'return_time' => 'Jam kembali harus setelah jam ambil untuk sewa di hari yang sama.',
            ])->withInput();
        }

        $lock = Cache::lock('vehicle-booking:' . $validated['vehicle_id'], 10);

        if (! $lock->get()) {
            return redirect()->back()->withErrors([
                'end_date' => 'Permintaan booking untuk kendaraan ini sedang diproses. Silakan coba lagi beberapa saat.',
            ])->withInput();
        }

        try {
            $booking = DB::transaction(function () use ($validated) {
                $vehicle = Vehicle::query()->lockForUpdate()->findOrFail($validated['vehicle_id']);
                $availability = $vehicle->getBookingAvailability($validated['start_date'], $validated['end_date']);

                if (!$availability['available'] && !$availability['queue_available']) {
                    return null;
                }

                $startDate = Carbon::parse($validated['start_date']);
                $endDate = Carbon::parse($validated['end_date']);
                $pricingBreakdown = $vehicle->getPriceBreakdownForRange($validated['start_date'], $validated['end_date']);
                $durationDays = $pricingBreakdown['duration_days'];
                $totalPrice = $pricingBreakdown['total'];

                $bookingData = array_merge($validated, [
                    'pickup_time' => Carbon::createFromFormat('H:i', $validated['pickup_time'])->format('H:i:s'),
                    'return_time' => Carbon::createFromFormat('H:i', $validated['return_time'])->format('H:i:s'),
                    'duration_days' => $durationDays,
                    'daily_price' => $pricingBreakdown['average_daily_price'],
                    'total_price' => $totalPrice,
                    'status' => 'pending',
                    'payment_status' => 'pending',
                    'payment_method' => 'whatsapp',
                ]);

                return Auth::user()->bookings()->create($bookingData);
            });
        } finally {
            $lock->release();
        }

        if ($booking === null) {
            return redirect()->back()->withErrors([
                'end_date' => 'Kendaraan tidak tersedia untuk tanggal yang dipilih. Silakan pilih tanggal lain.',
            ])->withInput();
        }

        return redirect()->route('bookings.show', $booking)->with(
            'success',
            'Booking berhasil dibuat. Jika kendaraan masih dipakai saat pembayaran diverifikasi, booking akan masuk antrean.'
        );
    }

    public function show(Booking $booking)
    {
        $this->authorize('view', $booking);

        $booking->loadMissing(['vehicle', 'user', 'review']);

        $waitingListPosition = $booking->getWaitingListPosition();
        $waitingListQueue = collect();

        if (Auth::user()->isAdmin()) {
            $waitingListQueue = $booking->getVehicleWaitingListQueue();
        }

        return view('bookings.show', compact('booking', 'waitingListPosition', 'waitingListQueue'));
    }

    public function resendNotification(Booking $booking): RedirectResponse
    {
        $booking->loadMissing(['user', 'vehicle']);

        $notificationType = $booking->getResendableNotificationType();

        if ($notificationType === null) {
            return redirect()->route('bookings.show', $booking)
                ->with('warning', 'Belum ada notifikasi customer yang bisa dikirim ulang untuk booking ini.');
        }

        $mailable = match ($notificationType) {
            'payment_failed' => new PaymentFailedMail($booking, $booking->notes),
            'booking_rescheduled' => new BookingRescheduledMail($booking),
            'waiting_list_activated' => new WaitingListActivatedMail($booking),
            default => new PaymentVerifiedMail($booking),
        };

        $message = match ($notificationType) {
            'payment_failed' => 'Email penolakan pembayaran berhasil dikirim ulang.',
            'booking_rescheduled' => 'Email jadwal baru berhasil dikirim ulang.',
            'waiting_list_activated' => 'Email aktivasi antrean berhasil dikirim ulang.',
            default => 'Email status pembayaran berhasil dikirim ulang.',
        };

        $this->sendMailToBookingUser($booking, $mailable);

        return redirect()->route('bookings.show', $booking)->with('success', $message);
    }

    public function cancel(Booking $booking)
    {
        $this->authorize('cancel', $booking);

        $booking->update(['status' => 'cancelled']);

        return redirect()->route('bookings.index')->with('success', 'Booking dibatalkan');
    }

    public function complete(Request $request, Booking $booking)
    {
        $this->authorize('complete', $booking);

        $request->merge([
            'return_fuel_level' => $request->filled('return_fuel_level') ? $request->input('return_fuel_level') : null,
            'return_odometer' => $request->filled('return_odometer') ? $request->input('return_odometer') : null,
            'return_damage_fee' => $request->filled('return_damage_fee') ? $request->input('return_damage_fee') : 0,
            'return_notes' => $request->filled('return_notes') ? $request->input('return_notes') : null,
        ]);

        $validated = $request->validate([
            'return_date' => 'required|date|after_or_equal:' . $booking->start_date->toDateString() . '|before_or_equal:today',
            'return_condition_status' => 'required|string|in:' . implode(',', array_keys(Booking::getReturnConditionOptions())),
            'return_fuel_level' => 'nullable|string|in:' . implode(',', array_keys(Booking::getReturnFuelLevelOptions())),
            'return_odometer' => 'nullable|integer|min:0',
            'return_checklist' => 'nullable|array',
            'return_checklist.*' => 'string|in:' . implode(',', array_keys(Booking::getReturnChecklistOptions())),
            'return_damage_fee' => 'nullable|numeric|min:0',
            'return_notes' => 'nullable|string|max:2000',
            'return_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $returnDate = $validated['return_date'];
        $returnChecklist = array_values($validated['return_checklist'] ?? []);
        $returnDamageFee = (float) ($validated['return_damage_fee'] ?? 0);
        $returnPhotoPath = null;

        if ($request->hasFile('return_photo') && $request->file('return_photo')->isValid()) {
            $returnPhotoPath = $request->file('return_photo')->store('returns', 'public');
        }

        try {
            $completion = DB::transaction(function () use ($booking, $returnDate, $validated, $returnChecklist, $returnDamageFee, $returnPhotoPath) {
                /** @var Booking $booking */
                $booking = Booking::query()->with('vehicle')->lockForUpdate()->findOrFail($booking->id);
                $vehicle = Vehicle::query()->lockForUpdate()->findOrFail($booking->vehicle_id);
                $needsMaintenance = $validated['return_condition_status'] === 'needs_attention';
                $readyBookingId = Booking::query()
                    ->lockForUpdate()
                    ->where('vehicle_id', $vehicle->id)
                    ->whereKeyNot($booking->id)
                    ->awaitingVehicleReturn()
                    ->orderBy('start_date')
                    ->orderBy('id')
                    ->value('id');

                $lateDays = $booking->calculateLateDays($returnDate);
                $lateFeePerDay = $booking->daily_price;
                $lateFee = $lateDays * $lateFeePerDay;

                $booking->update([
                    'status' => 'completed',
                    'actual_return_date' => $returnDate,
                    'return_condition_status' => $validated['return_condition_status'],
                    'return_fuel_level' => $validated['return_fuel_level'] ?? null,
                    'return_odometer' => $validated['return_odometer'] ?? null,
                    'return_checklist' => $returnChecklist,
                    'return_damage_fee' => $returnDamageFee,
                    'return_notes' => $validated['return_notes'] ?? null,
                    'return_photo' => $returnPhotoPath,
                    'late_days' => $lateDays,
                    'late_fee_per_day' => $lateFeePerDay,
                    'late_fee' => $lateFee,
                ]);

                if ($needsMaintenance) {
                    $maintenanceNote = 'Unit masuk maintenance setelah pengembalian pada '
                        . Carbon::parse($returnDate)->format('d M Y')
                        . '. Admin akan menghubungi customer jika perlu penyesuaian jadwal.';
                    $maintenanceHoldAt = now();

                    $affectedBookings = Booking::query()
                        ->lockForUpdate()
                        ->where('vehicle_id', $vehicle->id)
                        ->whereKeyNot($booking->id)
                        ->whereIn('status', ['confirmed', 'waiting_list'])
                        ->where('payment_status', 'paid')
                        ->whereDate('end_date', '>=', Carbon::parse($returnDate)->toDateString())
                        ->orderBy('start_date')
                        ->orderBy('id')
                        ->get();

                    foreach ($affectedBookings as $affectedBooking) {
                        $affectedBooking->update([
                            'maintenance_hold_at' => $maintenanceHoldAt,
                            'maintenance_hold_reason' => $maintenanceNote,
                            'notes' => collect([$affectedBooking->notes, $maintenanceNote])->filter()->implode(PHP_EOL),
                        ]);
                    }

                    if ($vehicle->status !== 'maintenance') {
                        $vehicle->update(['status' => 'maintenance']);
                    }

                    return [
                        'late_days' => $lateDays,
                        'late_fee' => $lateFee,
                        'return_damage_fee' => $returnDamageFee,
                        'promoted_waiting_list' => false,
                        'promoted_booking_id' => null,
                        'ready_booking_id' => null,
                        'forced_maintenance' => true,
                        'maintenance_hold_count' => $affectedBookings->count(),
                    ];
                }

                $nextWaitingBooking = Booking::query()
                    ->lockForUpdate()
                    ->where('vehicle_id', $vehicle->id)
                    ->paidWaitingList()
                    ->orderBy('created_at')
                    ->orderBy('id')
                    ->first();

                if ($readyBookingId) {
                    $vehicle->syncRentalStatus();

                    return [
                        'late_days' => $lateDays,
                        'late_fee' => $lateFee,
                        'return_damage_fee' => $returnDamageFee,
                        'promoted_waiting_list' => false,
                        'promoted_booking_id' => null,
                        'ready_booking_id' => $readyBookingId,
                        'forced_maintenance' => false,
                        'maintenance_hold_count' => 0,
                    ];
                }

                if (! $nextWaitingBooking) {
                    $vehicle->syncRentalStatus();

                    return [
                        'late_days' => $lateDays,
                        'late_fee' => $lateFee,
                        'return_damage_fee' => $returnDamageFee,
                        'promoted_waiting_list' => false,
                        'promoted_booking_id' => null,
                        'ready_booking_id' => $readyBookingId,
                        'forced_maintenance' => false,
                        'maintenance_hold_count' => 0,
                    ];
                }

                $newStartDate = Carbon::parse($returnDate)->startOfDay();
                if ($nextWaitingBooking->start_date->copy()->startOfDay()->gt($newStartDate)) {
                    $newStartDate = $nextWaitingBooking->start_date->copy()->startOfDay();
                }

                $newEndDate = $newStartDate->copy()->addDays(max($nextWaitingBooking->duration_days - 1, 0));
                $activationNote = 'Booking diaktifkan dari antrean pada ' . $newStartDate->format('d M Y') . '.';

                $nextWaitingBooking->update([
                    'status' => 'confirmed',
                    'start_date' => $newStartDate->toDateString(),
                    'end_date' => $newEndDate->toDateString(),
                    'notes' => collect([$nextWaitingBooking->notes, $activationNote])->filter()->implode(PHP_EOL),
                ]);

                $vehicle->syncRentalStatus();

                return [
                    'late_days' => $lateDays,
                    'late_fee' => $lateFee,
                    'return_damage_fee' => $returnDamageFee,
                    'promoted_waiting_list' => true,
                    'promoted_booking_id' => $nextWaitingBooking->id,
                    'ready_booking_id' => $readyBookingId,
                    'forced_maintenance' => false,
                    'maintenance_hold_count' => 0,
                ];
            });
        } catch (\Throwable $exception) {
            if ($returnPhotoPath && Storage::disk('public')->exists($returnPhotoPath)) {
                Storage::disk('public')->delete($returnPhotoPath);
            }

            throw $exception;
        }

        if ($completion['promoted_booking_id']) {
            $promotedBooking = Booking::query()
                ->with(['user', 'vehicle'])
                ->find($completion['promoted_booking_id']);

            if ($promotedBooking) {
                $this->sendMailToBookingUser($promotedBooking, new WaitingListActivatedMail($promotedBooking));
            }
        }

        if ($completion['ready_booking_id']) {
            $readyBooking = Booking::query()
                ->with(['user', 'vehicle'])
                ->find($completion['ready_booking_id']);

            if ($readyBooking && $readyBooking->isActive()) {
                $this->sendMailToBookingUser($readyBooking, new BookingReadyForPickupMail($readyBooking));
            }
        }
        
        $message = 'Booking selesai, kendaraan tersedia kembali';
        if ($completion['forced_maintenance']) {
            $message = 'Booking selesai, kendaraan dipindahkan ke maintenance.';

            if ($completion['maintenance_hold_count'] > 0) {
                $message .= ' ' . $completion['maintenance_hold_count'] . ' booking lanjutan menunggu penyesuaian admin.';
            }
        } elseif ($completion['promoted_waiting_list']) {
            $message = 'Booking selesai, 1 booking antrean diaktifkan.';
        } elseif ($completion['ready_booking_id']) {
            $message = 'Booking selesai, booking berikutnya sekarang sudah aktif.';
        }

        $messageDetails = [];

        if ($completion['late_days'] > 0) {
            $messageDetails[] = 'Denda keterlambatan: Rp ' . number_format($completion['late_fee'], 0, ',', '.') . " ({$completion['late_days']} hari)";
        }

        if ($completion['return_damage_fee'] > 0) {
            $messageDetails[] = 'Biaya tambahan inspeksi: Rp ' . number_format($completion['return_damage_fee'], 0, ',', '.');
        }

        if ($messageDetails !== []) {
            $message = rtrim($message, '.') . '. ' . implode('. ', $messageDetails);
        }
        
        return redirect()->route('admin.bookings.index')->with('success', $message);
    }

    /**
     * Show reschedule form for a booking held because of maintenance.
     */
    public function showRescheduleForm(Booking $booking)
    {
        $this->authorize('reschedule', $booking);

        $booking->loadMissing(['vehicle', 'user']);

        return view('bookings.reschedule', compact('booking'));
    }

    /**
     * Update a maintenance-held booking with a new schedule.
     */
    public function reschedule(Request $request, Booking $booking): RedirectResponse
    {
        $this->authorize('reschedule', $booking);

        $validated = $request->validate([
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'pickup_time' => 'nullable|date_format:H:i',
            'return_time' => 'nullable|date_format:H:i',
            'admin_note' => 'nullable|string|max:1000',
        ]);

        $pickupTime = $validated['pickup_time'] ?? $booking->pickup_time_label;
        $returnTime = $validated['return_time'] ?? $booking->return_time_label;

        if ($validated['start_date'] === $validated['end_date'] && strcmp($returnTime, $pickupTime) <= 0) {
            return redirect()->back()->withErrors([
                'return_time' => 'Jam kembali harus setelah jam ambil untuk sewa di hari yang sama.',
            ])->withInput();
        }

        $startDate = Carbon::parse($validated['start_date'])->startOfDay();
        $endDate = Carbon::parse($validated['end_date'])->startOfDay();

        $hasConflict = Booking::query()
            ->where('vehicle_id', $booking->vehicle_id)
            ->whereKeyNot($booking->id)
            ->whereIn('status', ['pending', 'confirmed', 'waiting_list'])
            ->whereIn('payment_status', ['pending', 'paid'])
            ->overlappingRange($startDate->toDateString(), $endDate->toDateString())
            ->exists();

        if ($hasConflict) {
            return redirect()->back()->withErrors([
                'start_date' => 'Jadwal baru masih bentrok dengan booking lain untuk kendaraan ini. Pilih tanggal yang benar-benar kosong.',
            ])->withInput();
        }

        $rescheduleNote = 'Jadwal booking disesuaikan admin ke '
            . $startDate->format('d M Y')
            . ' '
            . $pickupTime
            . ' - '
            . $endDate->format('d M Y')
            . ' '
            . $returnTime
            . ' setelah maintenance.';

        if (! empty($validated['admin_note'])) {
            $rescheduleNote .= ' Catatan admin: ' . trim($validated['admin_note']);
        }

        DB::transaction(function () use ($booking, $validated, $pickupTime, $returnTime, $rescheduleNote) {
            /** @var Booking $booking */
            $booking = Booking::query()->lockForUpdate()->findOrFail($booking->id);

            $booking->update([
                'status' => 'confirmed',
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'pickup_time' => Carbon::createFromFormat('H:i', $pickupTime)->format('H:i:s'),
                'return_time' => Carbon::createFromFormat('H:i', $returnTime)->format('H:i:s'),
                'maintenance_hold_at' => null,
                'maintenance_hold_reason' => null,
                'notes' => collect([$booking->notes, $rescheduleNote])->filter()->implode(PHP_EOL),
            ]);

            Vehicle::query()->find($booking->vehicle_id)?->syncRentalStatus();
        });

        $booking->refresh()->loadMissing(['user', 'vehicle']);
        $this->sendMailToBookingUser($booking, new BookingRescheduledMail($booking));

        return redirect()->route('bookings.show', $booking)->with('success', 'Jadwal booking berhasil diperbarui dan tidak lagi tertahan oleh maintenance.');
    }

    /**
     * Show complete booking form with late fee calculation
     */
    public function showCompleteForm(Request $request, Booking $booking)
    {
        $this->authorize('complete', $booking);

        $validated = $request->validate([
            'return_date' => 'nullable|date|after_or_equal:' . $booking->start_date->toDateString() . '|before_or_equal:today',
        ]);

        $returnDate = $validated['return_date'] ?? now()->toDateString();
        $lateDays = $booking->calculateLateDays($returnDate);
        $lateFee = $booking->calculateLateFee($lateDays);
        $returnConditionOptions = Booking::getReturnConditionOptions();
        $returnFuelOptions = Booking::getReturnFuelLevelOptions();
        $returnChecklistOptions = Booking::getReturnChecklistOptions();
        
        return view('bookings.complete', compact(
            'booking',
            'lateDays',
            'lateFee',
            'returnDate',
            'returnConditionOptions',
            'returnFuelOptions',
            'returnChecklistOptions'
        ));
    }

    public function payment(Booking $booking)
    {
        $this->authorize('pay', $booking);

        // Check KTP verification
        if (!Auth::user()->isKtpVerified()) {
            return redirect()->route('profile.ktp')
                ->with('warning', 'Silakan verifikasi KTP terlebih dahulu sebelum melakukan pembayaran.');
        }
        
        return view('bookings.payment', compact('booking'));
    }

    public function processPayment(Request $request, Booking $booking)
    {
        $this->authorize('pay', $booking);

        // Check KTP verification
        if (!Auth::user()->isKtpVerified()) {
            return redirect()->route('profile.ktp')
                ->with('warning', 'Silakan verifikasi KTP terlebih dahulu sebelum melakukan pembayaran.');
        }
        
        $request->validate([
            'payment_method' => 'nullable|in:whatsapp',
        ]);

        // Upload bukti transfer dihapus, sehingga semua alur pembayaran diarahkan ke WhatsApp.
        $booking->update([
            'payment_method' => 'whatsapp',
        ]);

        return redirect()->route('bookings.whatsapp-payment', $booking);
    }

    public function whatsappPayment(Booking $booking)
    {
        $this->authorize('pay', $booking);

        // Redirect to payment method selection
        try {
            return redirect()->route('payments.whatsapp-confirmation', $booking);
        } catch (\Exception $e) {
            return redirect()->route('bookings.show', $booking)->withErrors([
                'error' => 'Tidak dapat mengakses halaman pembayaran: ' . $e->getMessage()
            ]);
        }
    }

    public function verifyPayment(Request $request, Booking $booking)
    {
        $this->authorize('verifyPayment', $booking);

        $validated = $request->validate([
            'verified' => 'required|boolean',
            'notes' => 'nullable|string',
        ]);

        $verified = (bool) $validated['verified'];
        $notes = $validated['notes'] ?? null;

        if ($verified) {
            $shouldWaitlist = $booking->vehicle->bookings()
                ->whereKeyNot($booking->id)
                ->queueableAvailability()
                ->overlappingRange($booking->start_date, $booking->end_date)
                ->exists();

            $booking->update([
                'status' => $shouldWaitlist ? 'waiting_list' : 'confirmed',
                'payment_status' => 'paid',
                'notes' => $notes,
            ]);

            Vehicle::query()->find($booking->vehicle_id)?->syncRentalStatus();

            $booking->refresh()->loadMissing(['user', 'vehicle']);
            $this->sendMailToBookingUser($booking, new PaymentVerifiedMail($booking));

            return redirect()->route('admin.bookings.index')->with(
                'success',
                $shouldWaitlist
                    ? 'Pembayaran berhasil diverifikasi dan booking masuk antrean'
                    : 'Pembayaran berhasil diverifikasi'
            );
        }

        if ($booking->payment_proof && Storage::disk('public')->exists($booking->payment_proof)) {
            Storage::disk('public')->delete($booking->payment_proof);
        }

        $booking->update([
            'status' => 'cancelled',
            'payment_status' => 'failed',
            'payment_proof' => null,
            'notes' => $notes,
        ]);

        $booking->refresh()->loadMissing(['user', 'vehicle']);
        $this->sendMailToBookingUser($booking, new PaymentFailedMail($booking, $notes));

        return redirect()->route('admin.bookings.index')->with('success', 'Pembayaran ditolak');
    }

    private function sendMailToBookingUser(Booking $booking, Mailable $mailable): void
    {
        try {
            Mail::to($booking->user)->send($mailable);
        } catch (\Throwable $exception) {
            report($exception);
        }
    }
}