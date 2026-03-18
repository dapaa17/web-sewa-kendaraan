<div class="bookings-list-header">
    <div>
        <h3>{{ $activeFilterLabel }}</h3>
        <p>{{ $activeFilterDescription }}</p>
    </div>
</div>

@if($currentStatus === 'overdue_payment')
    <div class="alert alert-danger border-0 rounded-4 mb-4 shadow-sm">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        Daftar ini diurutkan dari booking yang paling lama melewati batas pembayaran.
    </div>
@elseif($currentStatus === 'maintenance_hold')
    <div class="alert alert-warning border-0 rounded-4 mb-4 shadow-sm">
        <i class="bi bi-tools me-2"></i>
        Booking maintenance hold diurutkan dari jadwal sewa yang paling dekat supaya penyesuaian admin tidak telat.
    </div>
@elseif(in_array($currentStatus, ['awaiting_proof', 'pending', 'all'], true))
    <div class="alert alert-warning border-0 rounded-4 mb-4 shadow-sm">
        <i class="bi bi-alarm me-2"></i>
        Booking yang masih menunggu bukti transfer diprioritaskan berdasarkan deadline pembayaran terdekat.
    </div>
@endif

@if ($bookings->count() > 0)
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Booking</th>
                    <th>Pelanggan</th>
                    <th>Jadwal</th>
                    <th>Pembayaran</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($bookings as $booking)
                    @php
                        $displayStatusKey = $booking->getDisplayStatusKey();
                    @endphp
                    <tr class="{{ $booking->isOverduePayment() ? 'is-overdue' : ($booking->isAwaitingPaymentProof() ? 'is-awaiting-proof' : ($booking->isMaintenanceHold() ? 'is-maintenance-hold' : '')) }}">
                        <td>
                            <div class="admin-cell-title">{{ $booking->vehicle->getTypeIcon() }} {{ $booking->vehicle->name ?? 'N/A' }}</div>
                            <div class="admin-cell-sub">Booking #{{ $booking->id }}</div>
                        </td>
                        <td>
                            <div class="admin-cell-title">{{ $booking->user->name ?? 'N/A' }}</div>
                            <div class="admin-cell-sub">{{ $booking->user->email ?? '-' }}</div>
                        </td>
                        <td>
                            <div class="admin-cell-stack">
                                <div>{{ \Carbon\Carbon::parse($booking->start_date)->format('d M Y') }} • {{ $booking->pickup_time_label }}</div>
                                <div>{{ \Carbon\Carbon::parse($booking->end_date)->format('d M Y') }} • {{ $booking->return_time_label }}</div>
                                <div class="admin-cell-sub">{{ $booking->duration_days }} hari</div>
                            </div>
                        </td>
                        <td>
                            <div class="admin-cell-stack">
                                @if($booking->payment_method)
                                    <span class="badge badge-method {{ $booking->usesWhatsAppConfirmation() ? 'whatsapp' : 'transfer' }}">
                                        <i class="bi {{ $booking->usesWhatsAppConfirmation() ? 'bi-whatsapp' : 'bi-bank' }} me-1"></i>{{ $booking->getPaymentMethodShortLabel() }}
                                    </span>
                                @endif

                                @if($booking->payment_status === 'paid')
                                    <div class="admin-cell-sub">Pembayaran sudah lunas</div>
                                @elseif($booking->payment_proof && $booking->payment_status === 'pending')
                                    <div class="admin-cell-sub">Bukti masuk, menunggu verifikasi</div>
                                @elseif($booking->payment_status === 'pending')
                                    <div class="admin-cell-sub">Belum ada konfirmasi pembayaran</div>
                                @endif

                                @if($booking->status === 'pending' && $booking->payment_status === 'pending' && !$booking->payment_proof)
                                    <div class="admin-payment-note">Batas bayar {{ $booking->getPaymentDeadline()->format('d M Y H:i') }}</div>
                                @endif
                            </div>
                        </td>
                        <td>
                            <div class="admin-status-stack">
                                @switch($displayStatusKey)
                                    @case('maintenance_hold')
                                        <span class="badge badge-status bg-danger">🛠️ Tertahan Maintenance</span>
                                        @break
                                    @case('active')
                                        <span class="badge badge-status bg-success">🚗 Rented</span>
                                        @break
                                    @case('awaiting_return')
                                        <span class="badge badge-status bg-warning text-dark">⏳ Menunggu Unit Kembali</span>
                                        @break
                                    @case('payment_failed')
                                        <span class="badge badge-status bg-danger">❌ Pembayaran Ditolak</span>
                                        @break
                                    @case('pending')
                                        <span class="badge badge-status bg-warning text-dark">⏳ Pending</span>
                                        @break
                                    @case('waiting_list')
                                        <span class="badge badge-status bg-warning text-dark">🕒 Antrean</span>
                                        @break
                                    @case('scheduled')
                                        <span class="badge badge-status bg-info">📅 Terjadwal</span>
                                        @break
                                    @case('confirmed')
                                        <span class="badge badge-status bg-info">✓ Confirmed</span>
                                        @break
                                    @case('completed')
                                        <span class="badge badge-status bg-success">✅ Selesai</span>
                                        @break
                                    @case('cancelled')
                                        <span class="badge badge-status bg-danger">❌ Dibatalkan</span>
                                        @break
                                @endswitch

                                @if($booking->payment_status === 'paid')
                                    <span class="badge badge-status bg-success">💰 Lunas</span>
                                @elseif($booking->payment_status === 'failed' && $displayStatusKey !== 'payment_failed')
                                    <span class="badge badge-status bg-danger">❌ Pembayaran Ditolak</span>
                                @elseif($booking->payment_proof && $booking->payment_status === 'pending')
                                    <span class="badge badge-status bg-info">🔍 Verifikasi</span>
                                @elseif($booking->payment_status === 'pending')
                                    <span class="badge badge-status bg-secondary">💳 Belum Bayar</span>
                                @endif

                                @if($booking->isAwaitingPaymentProof())
                                    @if($booking->isPastDeadline())
                                        <span class="badge badge-priority expired">
                                            <i class="bi bi-exclamation-octagon me-1"></i>Lewat Deadline
                                        </span>
                                    @elseif($booking->isExpiringSoon())
                                        <span class="badge badge-priority urgent">
                                            <i class="bi bi-bell-fill me-1"></i>Perlu Follow Up
                                        </span>
                                    @endif
                                @elseif($booking->isBlockedByMaintenance())
                                    <span class="badge badge-priority urgent">
                                        <i class="bi bi-tools me-1"></i>Perlu Tindakan Admin
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td>
                            <div class="admin-actions">
                                <a href="{{ route('bookings.show', $booking) }}" class="btn btn-action btn-view">
                                    <i class="bi bi-eye me-1"></i>Detail
                                </a>

                                @if($booking->canBeRescheduledByAdmin())
                                    <a href="{{ route('admin.bookings.reschedule-form', $booking) }}" class="btn btn-action btn-reschedule">
                                        <i class="bi bi-calendar2-week me-1"></i>Jadwalkan Ulang
                                    </a>
                                @endif

                                @if($booking->canBeCompleted())
                                    <a href="{{ route('admin.bookings.complete-form', $booking) }}" class="btn btn-action btn-complete">
                                        <i class="bi bi-check-circle me-1"></i>Konfirmasi Pengembalian
                                    </a>
                                @endif

                                @if($booking->canBeVerified())
                                    <form method="POST" action="{{ route('admin.bookings.verify-payment', $booking) }}" onsubmit="return confirm('Verifikasi pembayaran booking #{{ $booking->id }} sekarang?');">
                                        @csrf
                                        <input type="hidden" name="verified" value="1">
                                        <button type="submit" class="btn btn-action btn-complete">
                                            <i class="bi bi-patch-check me-1"></i>Verifikasi Cepat
                                        </button>
                                    </form>

                                    <a href="{{ route('bookings.show', $booking) }}" class="btn btn-action btn-view">
                                        <i class="bi bi-search me-1"></i>Tinjau Pembayaran
                                    </a>
                                @endif

                                @if(
                                    $booking->status === 'pending'
                                    && $booking->payment_status === 'pending'
                                    && $booking->usesTransferProof()
                                    && !$booking->payment_proof
                                )
                                    <button type="button" class="btn btn-action btn-pending-review" disabled>
                                        <i class="bi bi-hourglass-split me-1"></i>Menunggu Bukti Transfer
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $bookings->links() }}
    </div>
@else
    <div class="empty-state">
        <i class="bi bi-calendar-x"></i>
        @if($currentStatus !== 'all')
            @php
                $statusLabel = match ($currentStatus) {
                    'active' => 'Aktif',
                    'pending' => 'Pending',
                    'maintenance_hold' => 'Tertahan Maintenance',
                    'overdue_payment' => 'Lewat Deadline',
                    'awaiting_proof' => 'Menunggu Bukti',
                    'scheduled' => 'Terjadwal',
                    'awaiting_return' => 'Menunggu Unit',
                    'waiting_list' => 'Antrean',
                    'completed' => 'Selesai',
                    'cancelled' => 'Dibatalkan',
                    default => ucfirst((string) $currentStatus),
                };
            @endphp
            <h4>Tidak Ada Booking {{ $statusLabel }}</h4>
            <p>Tidak ada booking dengan status ini</p>
            <a href="{{ $bookingIndexUrl() }}" class="btn btn-outline-primary rounded-pill px-4 mt-3">
                <i class="bi bi-arrow-left me-2"></i>Lihat Semua Booking
            </a>
        @else
            <h4>Belum Ada Booking</h4>
            <p>Belum ada booking dari pelanggan</p>
        @endif
    </div>
@endif