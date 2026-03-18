<details class="booking-explainer" {{ in_array($currentStatus, ['awaiting_return', 'waiting_list'], true) ? 'open' : '' }}>
    <summary class="title">
        <span><i class="bi bi-info-circle-fill me-2"></i>Arti status di halaman ini</span>
    </summary>
    <div class="items mt-3">
        <div class="item">
            <strong>Terjadwal</strong> berarti booking sudah dikonfirmasi dan aman, tetapi tanggal sewanya memang belum mulai.
        </div>
        <div class="item">
            <strong>Antrean</strong> berarti booking menunggu giliran karena jadwal kendaraan masih bentrok dengan booking lain.
        </div>
        <div class="item">
            <strong>Menunggu Unit</strong> muncul saat jadwal booking bentrok dengan kendaraan yang masih dipakai dan kendaraan sebelumnya belum dikembalikan.
        </div>
    </div>
</details>

<div class="bookings-list-header">
    <div>
        <h3>{{ $activeFilterLabel }}</h3>
        <p>{{ $activeFilterDescription }}</p>
    </div>
</div>

@if ($bookings->count() > 0)
    <div class="bookings-stack">
        @foreach ($bookings as $booking)
            @php
                $displayStatusKey = $booking->getDisplayStatusKey();
            @endphp
            <div class="booking-card {{ $displayStatusKey }} {{ $booking->isOverduePayment() ? 'payment-overdue' : '' }}">
                <div class="header-row">
                    <div class="vehicle-info">
                        <h5>
                            {{ $booking->vehicle->getTypeIcon() }} {{ $booking->vehicle->name ?? 'N/A' }}
                        </h5>
                        <div class="meta">Booking #{{ $booking->id }}</div>
                    </div>
                    <div class="badges">
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

                        @if($booking->status === 'pending' && $booking->payment_status === 'pending' && !$booking->payment_proof)
                            @if($booking->isPastDeadline())
                                <span class="badge badge-deadline bg-danger">⛔ Lewat Deadline</span>
                            @elseif($booking->isExpiringSoon())
                                <span class="badge badge-deadline bg-danger">🔥 {{ $booking->getTimeRemaining() }}</span>
                            @else
                                <span class="badge badge-deadline bg-warning text-dark">⏰ {{ $booking->getTimeRemaining() }}</span>
                            @endif
                        @endif
                    </div>
                </div>

                @if($booking->status === 'pending' && $booking->payment_status === 'pending' && !$booking->payment_proof)
                    <div class="booking-deadline-row">
                        <i class="bi bi-hourglass"></i>Batas bayar {{ $booking->getPaymentDeadline()->format('d M Y H:i') }}
                    </div>
                @endif

                <div class="details-row">
                    <div class="detail-item">
                        <span class="label">Mulai Sewa</span>
                        <span class="value">{{ \Carbon\Carbon::parse($booking->start_date)->format('d M Y') }} • {{ $booking->pickup_time_label }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="label">Selesai Sewa</span>
                        <span class="value">{{ \Carbon\Carbon::parse($booking->end_date)->format('d M Y') }} • {{ $booking->return_time_label }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="label">Durasi</span>
                        <span class="value">{{ $booking->duration_days }} hari</span>
                    </div>
                    <div class="detail-item">
                        <span class="label">Total Harga</span>
                        <span class="value price">Rp{{ number_format($booking->total_price, 0, ',', '.') }}</span>
                    </div>
                    @if($booking->late_fee > 0)
                        <div class="detail-item">
                            <span class="label">Denda</span>
                            <span class="value" style="color: #ef4444;">Rp{{ number_format($booking->late_fee, 0, ',', '.') }}</span>
                        </div>
                    @endif
                </div>

                @if($booking->isBlockedByMaintenance())
                    <div class="alert alert-danger border-0 rounded-4 mb-3">
                        <i class="bi bi-tools me-2"></i>
                        Unit sedang masuk maintenance setelah inspeksi pengembalian. Admin akan menghubungi Anda jika jadwal perlu disesuaikan.
                    </div>
                @elseif($displayStatusKey === 'payment_failed')
                    <div class="alert alert-danger border-0 rounded-4 mb-3">
                        <i class="bi bi-x-octagon me-2"></i>
                        Pembayaran sudah ditolak admin. Booking ini tidak bisa dilanjutkan lewat pembayaran ulang.
                    </div>
                @elseif($booking->isAwaitingVehicleReturn())
                    <div class="alert alert-warning border-0 rounded-4 mb-3">
                        <i class="bi bi-hourglass-split me-2"></i>
                        Jadwal booking bentrok dengan kendaraan yang masih dipakai dan kendaraan sebelumnya belum dikembalikan.
                    </div>
                @elseif($displayStatusKey === 'scheduled')
                    <div class="alert alert-info border-0 rounded-4 mb-3">
                        <i class="bi bi-calendar-event me-2"></i>
                        Booking ini sudah aman dan akan mulai pada {{ \Carbon\Carbon::parse($booking->start_date)->format('d M Y') }} pukul {{ $booking->pickup_time_label }}.
                    </div>
                @endif

                <div class="actions">
                    <a href="{{ route('bookings.show', $booking) }}" class="btn btn-action btn-view">
                        <i class="bi bi-eye me-1"></i>Detail
                    </a>

                    @if($booking->canEnterPaymentFlow())
                        <a href="{{ route('bookings.payment', $booking) }}" class="btn btn-action btn-complete">
                            <i class="bi bi-credit-card me-1"></i>Bayar
                        </a>
                    @endif

                    @if($booking->canBeCancelled())
                        <form method="POST" action="{{ route('bookings.cancel', $booking) }}" style="display:inline;" onsubmit="return confirm('Batalkan booking ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-action btn-cancel">
                                <i class="bi bi-x-circle me-1"></i>Batalkan
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @endforeach
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
            <a href="{{ route('bookings.index') }}" class="btn btn-outline-primary rounded-pill px-4 mt-3">
                <i class="bi bi-arrow-left me-2"></i>Lihat Semua Booking
            </a>
        @else
            <h4>Belum Ada Booking</h4>
            <p>Yuk mulai booking kendaraan untuk perjalananmu!</p>
            <a href="{{ route('vehicles.browse') }}" class="btn btn-primary rounded-pill px-4 mt-3">
                <i class="bi bi-search me-2"></i>Cari Kendaraan
            </a>
        @endif
    </div>
@endif