@php
    $usesAdminLayout = (bool) auth()->user()?->isAdmin();
    $bookingIndexRoute = $usesAdminLayout ? 'admin.bookings.index' : 'bookings.index';
@endphp
            margin-bottom: 1.2rem;

@section('title', 'Detail Booking')
@if($usesAdminLayout)
@section('page-title', 'Detail Booking')
@endif
            margin-bottom: 0.5rem;
@section('content')
<style>
    .detail-header {
        background: radial-gradient(circle at top right, rgba(255,255,255,0.14), transparent 34%), var(--gradient-brand);
        color: white;
        padding: 3rem 0 2.5rem;
        margin-bottom: 2rem;
        border-radius: 0 0 2rem 2rem;
    }
    .detail-header h1 {
        font-weight: 700;
            padding-inline: 1rem;
        font-size: clamp(2rem, 4.8vw, 3rem);
        margin-bottom: 0.75rem;
    }
    .detail-header .booking-id {
        opacity: 0.78;
        font-size: 0.95rem;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }
    .detail-header .header-meta {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
        margin-top: 1.25rem;
    }
    .detail-header .meta-chip {
        display: inline-flex;
        align-items: center;
            gap: 0.65rem;
        gap: 0.5rem;
        padding: 0.7rem 1rem;
        border-radius: 999px;
        background: rgba(255,255,255,0.12);
        border: 1px solid rgba(255,255,255,0.18);
        color: white;
        font-weight: 600;
        backdrop-filter: blur(10px);
    }
    .detail-container {
        max-width: 980px;
        margin: 0 auto;
        padding-bottom: 3rem;
    }
    .back-link {
        color: rgba(255,255,255,0.8);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
    }
    .back-link:hover {
        color: white;
    }
    .status-card {
        background: linear-gradient(135deg, rgba(255,255,255,0.98) 0%, rgba(248,250,252,0.96) 100%);
        border-radius: 1.5rem;
        padding: 1.65rem;
        box-shadow: var(--shadow-card);
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 1rem;
        border: 1px solid rgba(203,213,225,0.65);
    }
    .status-info h5 {
        margin: 0 0 0.25rem;
        font-weight: 700;
        font-size: 1.05rem;
        color: #1a202c;
    }
    .status-info p {
        margin: 0;
        color: #718096;
        font-size: 0.9rem;
    }
    .status-badges {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }
    .badge-status {
        padding: 0.6rem 1.25rem;
        border-radius: 2rem;
        font-weight: 600;
        font-size: 0.85rem;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.06);
    }
    .badge-status i {
        font-size: 0.9rem;
    }
    .badge-rented { background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; }
    .badge-pending { background: #fef3c7; color: #92400e; }
    .badge-confirmed { background: rgba(var(--color-secondary-rgb), 0.18); color: var(--color-primary); }
    .badge-completed { background: #dcfce7; color: #166534; }
    .badge-cancelled { background: #fee2e2; color: #991b1b; }
    .badge-paid { background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; }
    .badge-waiting { background: #fef3c7; color: #92400e; }
    .badge-verify { background: rgba(var(--color-secondary-rgb), 0.18); color: var(--color-primary); }
    .alert-card {
        border-radius: 1rem;
        padding: 1.25rem 1.5rem;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        border: 1px solid rgba(203,213,225,0.4);
        box-shadow: var(--shadow-soft);
    }
    .alert-card.warning {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        border-left: 4px solid #f59e0b;
    }
    .alert-card.info {
        background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%);
        border-left: 4px solid #0ea5e9;
    }
    .alert-card.danger {
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        border-left: 4px solid #ef4444;
    }
    .alert-card i {
        font-size: 1.5rem;
        flex-shrink: 0;
    }
    .alert-card.warning i { color: #d97706; }
    .alert-card.info i { color: #0284c7; }
    .alert-card.danger i { color: #dc2626; }
    .alert-card .content strong {
        display: block;
        margin-bottom: 0.25rem;
        color: #1a202c;
    }
    .alert-card .content p {
        margin: 0;
        font-size: 0.9rem;
        color: #4a5568;
    }
    .section-card {
        background: white;
        border-radius: 1.5rem;
        padding: 2rem;
        box-shadow: var(--shadow-card);
        margin-bottom: 1.5rem;
        border: 1px solid rgba(203,213,225,0.65);
    }
    .section-title {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #f1f5f9;
    }
    .section-title .icon {
        width: 40px;
        height: 40px;
        background: var(--gradient-brand);
        border-radius: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
    }
    .section-title h5 {
        margin: 0;
        font-weight: 700;
        font-size: 1.1rem;
        color: #1a202c;
    }
    .vehicle-info {
        display: flex;
        gap: 2rem;
        flex-wrap: wrap;
        align-items: stretch;
    }
    .vehicle-image {
        flex: 0 0 230px;
    }
    .vehicle-image img,
    .vehicle-placeholder {
        width: 100%;
        min-height: 230px;
        border-radius: 1rem;
    }
    .vehicle-image img {
        height: 100%;
        object-fit: cover;
    }
    .vehicle-placeholder {
        background: radial-gradient(circle at top, rgba(255,255,255,0.14), transparent 38%), var(--gradient-brand);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .vehicle-placeholder i {
        font-size: 4.4rem;
        color: white;
        filter: drop-shadow(0 14px 26px rgba(15, 23, 42, 0.24));
    }
    .vehicle-details {
        flex: 1;
        min-width: 200px;
    }
    .vehicle-details h4 {
        font-weight: 700;
        color: #1a202c;
        margin-bottom: 1.25rem;
        font-size: 1.6rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex-wrap: wrap;
    }
    .vehicle-details .type-badge {
        background: var(--gradient-brand);
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: 2rem;
        font-size: 0.75rem;
        font-weight: 500;
    }
    .info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }
    .info-item {
        display: flex;
        flex-direction: column;
        background: #f8fafc;
        border: 1px solid rgba(203,213,225,0.8);
        border-radius: 1rem;
        padding: 0.95rem 1rem;
    }
    .info-item .label {
        font-size: 0.75rem;
        color: #718096;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.25rem;
    }
    .info-item .value {
        font-weight: 700;
        color: #1a202c;
    }
    .detail-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 1rem;
        padding: 0.75rem 0;
        border-bottom: 1px solid #f1f5f9;
    }
    .detail-row:last-child {
        border-bottom: none;
    }
    .detail-row .label {
        color: #718096;
        font-weight: 500;
    }
    .detail-row .value {
        font-weight: 600;
        color: #1a202c;
    }
    .detail-row .value.price {
        font-size: 1.25rem;
        background: var(--gradient-brand);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    .detail-row .value.danger {
        color: #ef4444;
    }
    .late-fee-card {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        border-radius: 1rem;
        padding: 1.5rem;
        margin-top: 1.5rem;
        border: 1px solid rgba(245,158,11,0.24);
    }
    .late-fee-card h6 {
        color: #92400e;
        font-weight: 600;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .inspection-badges {
        display: flex;
        justify-content: flex-end;
        flex-wrap: wrap;
        gap: 0.5rem;
    }
    .inspection-badge {
        background: rgba(var(--color-secondary-rgb), 0.12);
        border: 1px solid rgba(var(--color-secondary-rgb), 0.18);
        color: var(--color-primary);
        border-radius: 999px;
        padding: 0.45rem 0.8rem;
        font-size: 0.82rem;
        font-weight: 600;
        line-height: 1.2;
    }
    .inspection-photo {
        max-width: 280px;
        width: 100%;
        border-radius: 1rem;
        object-fit: cover;
        box-shadow: var(--shadow-soft);
        border: 1px solid rgba(203,213,225,0.65);
    }
    .inspection-note {
        white-space: pre-line;
        text-align: right;
    }
    .payment-status-box {
        background: linear-gradient(135deg, rgba(255,255,255,0.85) 0%, rgba(6,182,212,0.08) 100%);
        border-radius: 1rem;
        padding: 1.25rem;
        margin-top: 1.5rem;
        border: 1px solid rgba(var(--color-secondary-rgb), 0.14);
    }
    .payment-status-box h6 {
        font-weight: 600;
        color: #4a5568;
        margin-bottom: 1rem;
    }
    .payment-status-row {
        display: flex;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
        justify-content: space-between;
    }
    .payment-status-main,
    .payment-status-actions {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        flex-wrap: wrap;
    }
    .method-chip {
        color: var(--color-primary);
        background: rgba(var(--color-secondary-rgb), 0.12);
        border: 1px solid rgba(var(--color-secondary-rgb), 0.18);
        border-radius: 999px;
        padding: 0.55rem 0.9rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
    }
    .payment-info-box {
        border-radius: 0.75rem;
        padding: 1rem;
        margin-top: 1rem;
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        border: 1px solid rgba(148,163,184,0.18);
    }
    .payment-info-box.success {
        background: #dcfce7;
        border-left: 3px solid #22c55e;
    }
    .payment-info-box.pending {
        background: #fef3c7;
        border-left: 3px solid #f59e0b;
    }
    .payment-info-box.danger {
        background: #fee2e2;
        border-left: 3px solid #ef4444;
    }
    .payment-info-box i {
        font-size: 1.25rem;
    }
    .payment-info-box.success i { color: #16a34a; }
    .payment-info-box.pending i { color: #d97706; }
    .payment-info-box.danger i { color: #dc2626; }
    .payment-info-box .content strong {
        display: block;
        color: #1a202c;
        margin-bottom: 0.25rem;
    }
    .payment-info-box .content p {
        margin: 0;
        font-size: 0.85rem;
        color: #4a5568;
    }
    .action-buttons {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
        margin-top: 1.5rem;
        align-items: stretch;
    }
    .action-buttons form {
        margin: 0;
    }
    .btn-action {
        padding: 0.75rem 1.5rem;
        border-radius: 0.75rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        min-height: 52px;
        transition: all 0.3s ease;
    }
    .btn-action:hover {
        transform: translateY(-2px);
    }
    .btn-primary-gradient {
        background: var(--color-primary);
        border: none;
        color: white;
    }
    .btn-primary-gradient:hover {
        background: var(--color-secondary);
        box-shadow: 0 8px 25px rgba(var(--color-secondary-rgb), 0.24);
        color: var(--color-primary);
    }
    .btn-success-gradient {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        border: none;
        color: white;
    }
    .btn-success-gradient:hover {
        box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4);
        color: white;
    }
    .btn-danger-outline {
        background: white;
        border: 2px solid #ef4444;
        color: #ef4444;
    }
    .btn-danger-outline:hover {
        background: #ef4444;
        color: white;
    }
    .admin-section {
        background: radial-gradient(circle at top right, rgba(255,255,255,0.14), transparent 34%), var(--gradient-brand);
        border-radius: 1.5rem;
        padding: 2rem;
        color: white;
        margin-top: 1.5rem;
    }
    .admin-section h5 {
        color: white;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    .admin-section .proof-image {
        max-width: 300px;
        border-radius: 1rem;
        margin-bottom: 1.5rem;
    }
    .admin-section textarea {
        background: rgba(255,255,255,0.1);
        border: 1px solid rgba(255,255,255,0.2);
        color: white;
        border-radius: 0.75rem;
    }
    .admin-section textarea::placeholder {
        color: rgba(255,255,255,0.5);
    }
    .admin-section .alert {
        border-radius: 0.75rem;
    }
    .admin-section .action-row {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
    }
    .admin-section .action-row .btn-action {
        min-width: 220px;
    }
    .grand-total-row {
        border-top: 2px dashed #d97706;
        margin-top: 0.5rem;
        padding-top: 1rem;
    }
    .grand-total-row .label strong {
        color: #7c2d12;
    }
    .grand-total-row .value {
        font-size: 1.3rem;
        color: #dc2626;
    }
    @media (max-width: 768px) {
        .detail-header {
            padding: 2.55rem 0 2.15rem;
            margin-bottom: 1.2rem;
            border-radius: 0 0 1.35rem 1.35rem;
        }
        .detail-header h1 {
            font-size: 1.42rem;
            line-height: 1.35;
            margin-bottom: 0.5rem;
        }
        .detail-header .booking-id {
            font-size: 0.8rem;
            letter-spacing: 0.06em;
        }
        .detail-container {
            padding-inline: 1rem;
            padding-bottom: 2rem;
        }
        .detail-header .header-meta {
            flex-direction: column;
            align-items: flex-start;
            width: 100%;
        }
        .detail-header .meta-chip {
            width: 100%;
            justify-content: flex-start;
            font-size: 0.8rem;
            padding: 0.55rem 0.8rem;
        }
        .status-card,
        .section-card,
        .admin-section {
            border-radius: 1rem;
            padding: 1rem;
            gap: 0.65rem;
        }
        .status-info h5 {
            font-size: 0.96rem;
        }
        .status-info p {
            font-size: 0.84rem;
            line-height: 1.55;
        }
        .badge-status {
            font-size: 0.72rem;
            padding: 0.45rem 0.8rem;
        }
        .alert-card {
            padding: 0.9rem;
            border-radius: 0.85rem;
            gap: 0.75rem;
        }
        .alert-card .content p {
            font-size: 0.82rem;
        }
        .section-title {
            margin-bottom: 1rem;
            padding-bottom: 0.75rem;
            gap: 0.6rem;
        }
        .section-title .icon {
            width: 34px;
            height: 34px;
            border-radius: 0.65rem;
        }
        .section-title h5 {
            font-size: 0.98rem;
        }
        .info-grid {
            grid-template-columns: 1fr;
            gap: 0.65rem;
        }
        .vehicle-info {
            flex-direction: column;
            gap: 1rem;
        }
        .vehicle-image {
            flex: none;
            width: 100%;
        }
        .vehicle-image img,
        .vehicle-placeholder {
            min-height: 180px;
        }
        .vehicle-details h4 {
            font-size: 1.15rem;
            margin-bottom: 0.85rem;
        }
        .info-item {
            border-radius: 0.8rem;
            padding: 0.75rem 0.8rem;
        }
        .info-item .label {
            font-size: 0.7rem;
        }
        .info-item .value {
            font-size: 0.88rem;
        }
        .detail-row {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.25rem;
            padding: 0.65rem 0;
        }
        .detail-row .value.price {
            font-size: 1.05rem;
        }
        .inspection-note {
            text-align: left;
        }
        .payment-status-box {
            padding: 0.9rem;
            border-radius: 0.85rem;
        }
        .method-chip {
            font-size: 0.78rem;
            padding: 0.48rem 0.72rem;
        }
        .status-badges,
        .payment-status-main,
        .payment-status-actions,
        .action-buttons,
        .admin-section .action-row,
        .admin-section .col-12.d-flex {
            width: 100%;
        }
        .payment-status-row,
        .action-buttons,
        .admin-section .action-row,
        .admin-section .col-12.d-flex {
            flex-direction: column;
            align-items: stretch;
        }
        .action-buttons a,
        .action-buttons form,
        .payment-status-actions .btn,
        .admin-section .action-row .btn-action,
        .admin-section .col-12.d-flex .btn-action {
            width: 100%;
        }
        .btn-action {
            min-height: 44px;
            padding: 0.62rem 0.9rem;
            font-size: 0.84rem;
        }
    }
    @media (max-width: 420px) {
        .detail-header h1 {
            font-size: 1.24rem;
        }
        .status-badges .badge-status {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<!-- Detail Header -->
<div class="detail-header">
    <div class="container detail-container">
        <a href="{{ route($bookingIndexRoute) }}" class="back-link">
            <i class="bi bi-arrow-left"></i> Kembali ke Daftar Booking
        </a>
        <h1><i class="bi bi-receipt me-2"></i>Detail Booking</h1>
        <span class="booking-id">#{{ $booking->id }}</span>
        <div class="header-meta">
            <span class="meta-chip">
                <i class="bi bi-calendar-range"></i>{{ $booking->start_date->format('d M Y') }} {{ $booking->pickup_time_label }} - {{ $booking->end_date->format('d M Y') }} {{ $booking->return_time_label }}
            </span>
            <span class="meta-chip">
                <i class="bi bi-credit-card-2-front"></i>{{ $booking->getPaymentMethodShortLabel() }}
            </span>
            <span class="meta-chip">
                <i class="bi bi-cash-stack"></i>Rp{{ number_format($booking->total_price, 0, ',', '.') }}
            </span>
        </div>
    </div>
</div>

<div class="container detail-container">
    @php
        $displayStatusKey = $booking->getDisplayStatusKey();
    @endphp

    <!-- Status Card -->
    <div class="status-card">
        <div class="status-info">
            <h5>Status Booking</h5>
            <p>
                @if($displayStatusKey === 'maintenance_hold')
                    Kendaraan sedang masuk maintenance setelah inspeksi pengembalian dan booking ini menunggu penyesuaian admin
                @elseif($displayStatusKey === 'active')
                    Kendaraan sedang disewa sampai {{ $booking->end_date->format('d M Y') }} pukul {{ $booking->return_time_label }}
                @elseif($displayStatusKey === 'awaiting_return')
                    Jadwal sewa sudah masuk, tetapi unit masih menunggu pengembalian dari customer sebelumnya
                @elseif($displayStatusKey === 'payment_failed')
                    Pembayaran sudah ditolak admin dan booking ini tidak bisa dilanjutkan lewat pembayaran ulang
                @elseif($displayStatusKey === 'pending')
                    Menunggu pembayaran
                @elseif($displayStatusKey === 'waiting_list')
                    Pembayaran sudah diverifikasi dan booking menunggu kendaraan tersedia
                @elseif($displayStatusKey === 'scheduled')
                    Booking sudah terjadwal dan akan mulai pada {{ $booking->start_date->format('d M Y') }} pukul {{ $booking->pickup_time_label }}
                @elseif($displayStatusKey === 'confirmed')
                    Booking dikonfirmasi dan sedang berjalan sampai {{ $booking->end_date->format('d M Y') }} pukul {{ $booking->return_time_label }}
                @elseif($displayStatusKey === 'completed')
                    Rental telah selesai
                @elseif($displayStatusKey === 'cancelled')
                    Booking dibatalkan
                @endif
            </p>
        </div>
        <div class="status-badges">
            @switch($displayStatusKey)
                @case('maintenance_hold')
                    <span class="badge-status badge-cancelled"><i class="bi bi-tools"></i> Tertahan Maintenance</span>
                    @break
                @case('active')
                    <span class="badge-status badge-rented"><i class="bi bi-car-front-fill"></i> Sedang Disewa</span>
                    @break
                @case('awaiting_return')
                    <span class="badge-status badge-pending"><i class="bi bi-hourglass-split"></i> Menunggu Pengembalian Unit</span>
                    @break
                @case('payment_failed')
                    <span class="badge-status badge-cancelled"><i class="bi bi-x-circle-fill"></i> Pembayaran Ditolak</span>
                    @break
                @case('pending')
                    <span class="badge-status badge-pending"><i class="bi bi-hourglass-split"></i> Pending</span>
                    @break
                @case('waiting_list')
                    <span class="badge-status badge-pending"><i class="bi bi-list-ol"></i> Antrean</span>
                    @break
                @case('scheduled')
                    <span class="badge-status badge-confirmed"><i class="bi bi-calendar-event"></i> Terjadwal</span>
                    @break
                @case('confirmed')
                    <span class="badge-status badge-confirmed"><i class="bi bi-patch-check-fill"></i> Dikonfirmasi</span>
                    @break
                @case('completed')
                    <span class="badge-status badge-completed"><i class="bi bi-check2-circle"></i> Selesai</span>
                    @break
                @case('cancelled')
                    <span class="badge-status badge-cancelled"><i class="bi bi-x-circle-fill"></i> Dibatalkan</span>
                    @break
            @endswitch
            
            @if($booking->payment_status === 'paid')
                <span class="badge-status badge-paid"><i class="bi bi-wallet2"></i> Lunas</span>
            @elseif($booking->payment_status === 'failed' && $displayStatusKey !== 'payment_failed')
                <span class="badge-status badge-cancelled"><i class="bi bi-x-circle"></i> Pembayaran Ditolak</span>
            @elseif($booking->payment_proof && $booking->payment_status === 'pending')
                <span class="badge-status badge-verify"><i class="bi bi-search"></i> Verifikasi</span>
            @elseif($booking->payment_status === 'pending')
                <span class="badge-status badge-waiting"><i class="bi bi-credit-card-2-front"></i> Belum Bayar</span>
            @endif
        </div>
    </div>

    @php
        [$statusGlossaryLabel, $statusGlossaryDescription] = match ($displayStatusKey) {
            'maintenance_hold' => [
                'Tertahan Maintenance',
                'Status ini berarti kendaraan masuk maintenance setelah inspeksi pengembalian, sehingga booking menunggu penyesuaian atau tindak lanjut admin.',
            ],
            'active' => [
                'Sedang Disewa',
                'Status ini berarti masa sewa sedang berjalan dan kendaraan sudah dipakai sesuai jadwal booking.',
            ],
            'awaiting_return' => [
                'Menunggu Pengembalian Unit',
                'Status ini berarti tanggal sewa Anda sudah masuk, tetapi kendaraan dari booking sebelumnya belum dikembalikan.',
            ],
            'payment_failed' => [
                'Pembayaran Ditolak',
                'Status ini berarti admin sudah menolak pembayaran, sehingga booking tidak bisa dilanjutkan lagi lewat pembayaran ulang dari customer.',
            ],
            'waiting_list' => [
                'Antrean',
                'Status ini berarti jadwal yang dipilih bentrok dengan kendaraan yang masih dipakai, sehingga booking Anda masuk antrean.',
            ],
            'scheduled' => [
                'Terjadwal',
                'Status ini berarti booking sudah dikonfirmasi dan aman, tetapi tanggal sewanya memang belum mulai.',
            ],
            'pending' => [
                'Pending',
                'Status ini berarti booking sudah dibuat, tetapi masih menunggu pembayaran atau verifikasi admin.',
            ],
            'completed' => [
                'Selesai',
                'Status ini berarti masa sewa sudah berakhir dan kendaraan telah dikembalikan.',
            ],
            'cancelled' => [
                'Dibatalkan',
                'Status ini berarti booking tidak lagi aktif dan tidak masuk jadwal sewa.',
            ],
            default => [null, null],
        };
    @endphp

    @if($statusGlossaryDescription)
        <div class="alert-card info">
            <i class="bi bi-info-circle-fill"></i>
            <div class="content">
                <strong>Arti status: {{ $statusGlossaryLabel }}</strong>
                <p>{{ $statusGlossaryDescription }}</p>
            </div>
        </div>
    @endif

    <!-- Payment Deadline Warning -->
    @if($booking->status === 'pending' && $booking->payment_status === 'pending' && !$booking->payment_proof)
        @php
            $deadline = $booking->getPaymentDeadline();
            $isPastDeadline = $booking->isPastDeadline();
            $isExpiringSoon = $booking->isExpiringSoon();
        @endphp
        
        @if($isPastDeadline)
            <div class="alert-card danger">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <div class="content">
                    <strong>Batas waktu pembayaran telah habis!</strong>
                    <p>Booking ini akan segera dibatalkan secara otomatis oleh sistem.</p>
                </div>
            </div>
        @else
            <div class="alert-card {{ $isExpiringSoon ? 'danger' : 'warning' }}">
                <i class="bi bi-clock-fill"></i>
                <div class="content">
                    <strong>Segera selesaikan pembayaran!</strong>
                    <p>Batas waktu: <strong>{{ $deadline->format('d M Y H:i') }}</strong> ({{ $booking->getTimeRemaining() }} lagi)<br>
                    <small>Booking otomatis dibatalkan jika belum ada pembayaran sampai batas waktu ini.</small></p>
                </div>
            </div>
        @endif
    @endif

    @if($booking->isBlockedByMaintenance())
        <div class="alert-card danger">
            <i class="bi bi-tools"></i>
            <div class="content">
                <strong>Unit sedang masuk maintenance</strong>
                <p>
                    Admin menahan kendaraan ini setelah inspeksi pengembalian. Booking tetap tercatat, tetapi unit belum bisa diserahkan sampai penanganan selesai.
                    @if($booking->maintenance_hold_reason)
                        <br><small>{{ $booking->maintenance_hold_reason }}</small>
                    @endif
                </p>
            </div>
        </div>
    @elseif($booking->isAwaitingVehicleReturn())
        <div class="alert-card warning">
            <i class="bi bi-hourglass-split"></i>
            <div class="content">
                <strong>Unit masih dipakai customer sebelumnya</strong>
                <p>Booking Anda tetap aman. Begitu kendaraan dikembalikan, status ini akan berubah dan unit bisa segera diserahkan.</p>
            </div>
        </div>
    @elseif($booking->status === 'confirmed' && $booking->hasNotStartedYet())
        <div class="alert-card info">
            <i class="bi bi-calendar-check"></i>
            <div class="content">
                <strong>Booking Anda sudah terjadwal</strong>
                <p>Status ini normal. Booking sudah dikonfirmasi dan akan berjalan sesuai tanggal sewa pada {{ $booking->start_date->format('d M Y') }}.</p>
            </div>
        </div>
    @endif

    <!-- Overdue Warning -->
    @if($booking->isActive())
        @php
            $today = now()->startOfDay();
            $endDate = \Carbon\Carbon::parse($booking->end_date);
            $isOverdue = $today->gt($endDate);
            $daysOverdue = $isOverdue ? $today->diffInDays($endDate) : 0;
            $potentialLateFee = $daysOverdue * $booking->daily_price;
        @endphp
        
        @if($isOverdue)
            <div class="alert-card danger">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <div class="content">
                    <strong>Kendaraan terlambat dikembalikan!</strong>
                    <p>Masa sewa berakhir {{ $endDate->format('d M Y') }}. Terlambat: <strong>{{ $daysOverdue }} hari</strong><br>
                    <span style="color: #dc2626;">Estimasi denda: <strong>Rp{{ number_format($potentialLateFee, 0, ',', '.') }}</strong></span></p>
                </div>
            </div>
        @endif
    @endif

    <!-- Vehicle Section -->
    <div class="section-card">
        <div class="section-title">
            <div class="icon"><i class="bi bi-car-front"></i></div>
            <h5>Kendaraan</h5>
        </div>
        <div class="vehicle-info">
            <div class="vehicle-image">
                @if($booking->vehicle->image)
                    <img src="{{ Storage::url($booking->vehicle->image) }}" alt="{{ $booking->vehicle->name }}">
                @else
                    <div class="vehicle-placeholder">
                        <i class="bi {{ $booking->vehicle->vehicle_type === 'motor' ? 'bi-bicycle' : 'bi-car-front-fill' }}"></i>
                    </div>
                @endif
            </div>
            <div class="vehicle-details">
                <h4>
                    {{ $booking->vehicle->getTypeIcon() }} {{ $booking->vehicle->name }}
                    <span class="type-badge">{{ $booking->vehicle->getTypeLabel() }}</span>
                </h4>
                <div class="info-grid">
                    <div class="info-item">
                        <span class="label">Plat Nomor</span>
                        <span class="value">{{ $booking->vehicle->plat_number }}</span>
                    </div>
                    <div class="info-item">
                        <span class="label">Tahun</span>
                        <span class="value">{{ $booking->vehicle->year }}</span>
                    </div>
                    <div class="info-item">
                        <span class="label">Transmisi</span>
                        <span class="value">{{ $booking->vehicle->transmission }}</span>
                    </div>
                    <div class="info-item">
                        <span class="label">Harga per Hari</span>
                        <span class="value">Rp{{ number_format($booking->daily_price, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Rental Info Section -->
    <div class="section-card">
        <div class="section-title">
            <div class="icon"><i class="bi bi-calendar-check"></i></div>
            <h5>Informasi Rental</h5>
        </div>
        <div class="detail-row">
            <span class="label">Tanggal Mulai</span>
            <span class="value">{{ $booking->start_date->format('d M Y') }}</span>
        </div>
        <div class="detail-row">
            <span class="label">Jam Ambil</span>
            <span class="value">{{ $booking->pickup_time_label }}</span>
        </div>
        <div class="detail-row">
            <span class="label">Tanggal Selesai</span>
            <span class="value">{{ $booking->end_date->format('d M Y') }}</span>
        </div>
        <div class="detail-row">
            <span class="label">Jam Kembali</span>
            <span class="value">{{ $booking->return_time_label }}</span>
        </div>
        <div class="detail-row">
            <span class="label">Durasi</span>
            <span class="value">{{ $booking->duration_days }} hari</span>
        </div>
    </div>

    <!-- Payment Section -->
    <div class="section-card">
        <div class="section-title">
            <div class="icon"><i class="bi bi-credit-card"></i></div>
            <h5>Rincian Pembayaran</h5>
        </div>
        <div class="detail-row">
            <span class="label">Harga per Hari</span>
            <span class="value">Rp{{ number_format($booking->daily_price, 0, ',', '.') }}</span>
        </div>
        <div class="detail-row">
            <span class="label">Durasi</span>
            <span class="value">{{ $booking->duration_days }} hari</span>
        </div>
        <div class="detail-row">
            <span class="label">Total Harga</span>
            <span class="value price">Rp{{ number_format($booking->total_price, 0, ',', '.') }}</span>
        </div>

        @if($booking->status === 'completed' && ($booking->late_days > 0 || $booking->return_damage_fee > 0))
            <div class="late-fee-card">
                <h6><i class="bi bi-receipt"></i> Biaya Setelah Pengembalian</h6>
                @if($booking->late_days > 0)
                    <div class="detail-row">
                        <span class="label">Tanggal Seharusnya Kembali</span>
                        <span class="value">{{ $booking->end_date->format('d M Y') }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="label">Tanggal Aktual Kembali</span>
                        <span class="value">{{ $booking->actual_return_date ? $booking->actual_return_date->format('d M Y') : '-' }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="label">Terlambat</span>
                        <span class="value danger">{{ $booking->late_days }} hari</span>
                    </div>
                    <div class="detail-row">
                        <span class="label">Total Denda</span>
                        <span class="value danger">Rp{{ number_format($booking->late_fee, 0, ',', '.') }}</span>
                    </div>
                @endif
                @if($booking->return_damage_fee > 0)
                    <div class="detail-row">
                        <span class="label">Biaya Tambahan Inspeksi</span>
                        <span class="value danger">Rp{{ number_format($booking->return_damage_fee, 0, ',', '.') }}</span>
                    </div>
                @endif
                <div class="detail-row grand-total-row">
                    <span class="label"><strong>Grand Total</strong></span>
                    <span class="value"><strong>Rp{{ number_format($booking->getTotalWithCompletionCharges(), 0, ',', '.') }}</strong></span>
                </div>
            </div>
        @endif

        <!-- Payment Status -->
        <div class="payment-status-box">
            <h6>Status Pembayaran</h6>
            <div class="payment-status-row">
                <div class="payment-status-main">
                    @if($booking->payment_status === 'paid')
                        <span class="badge-status badge-paid"><i class="bi bi-check-circle"></i> Terbayar</span>
                    @elseif($booking->payment_status === 'pending')
                        <span class="badge-status badge-waiting"><i class="bi bi-hourglass"></i> Menunggu</span>
                    @else
                        <span class="badge-status badge-cancelled"><i class="bi bi-x-circle"></i> Gagal</span>
                    @endif
                    <span class="method-chip">
                        <i class="bi bi-wallet"></i>Metode: {{ $booking->getPaymentMethodLabel() }}
                    </span>
                </div>
                @if($booking->canEnterPaymentFlow())
                    <div class="payment-status-actions">
                        <a href="{{ route('bookings.payment', $booking) }}" class="btn btn-sm btn-primary-gradient">
                            <i class="bi bi-pencil"></i> Pilih Metode
                        </a>
                    </div>
                @endif
            </div>

            @if($booking->status === 'waiting_list' && $booking->payment_status === 'paid')
                <div class="payment-info-box pending">
                    <i class="bi bi-list-ol"></i>
                    <div class="content">
                        <strong>Booking Anda masuk antrean</strong>
                        <p>
                            Booking akan aktif otomatis setelah kendaraan yang sedang dipakai selesai.
                            @if($waitingListPosition)
                                Posisi antrean Anda saat ini: #{{ $waitingListPosition }}.
                            @endif
                        </p>
                    </div>
                </div>
            @elseif($booking->payment_status === 'pending' && !$booking->payment_proof)
                <div class="payment-info-box pending">
                    <i class="bi bi-info-circle"></i>
                    <div class="content">
                        <strong>Pembayaran menunggu aksi Anda</strong>
                        <p>Silakan selesaikan pembayaran untuk mengaktifkan booking.</p>
                    </div>
                </div>
            @elseif($booking->payment_status === 'pending' && $booking->payment_proof)
                <div class="payment-info-box pending">
                    <i class="bi bi-hourglass"></i>
                    <div class="content">
                        <strong>Bukti transfer sudah diupload</strong>
                        <p>Menunggu verifikasi admin. Status booking akan diperbarui setelah pembayaran dicek.</p>
                    </div>
                </div>
            @elseif($booking->payment_status === 'failed')
                <div class="payment-info-box danger">
                    <i class="bi bi-exclamation-circle"></i>
                    <div class="content">
                        <strong>Pembayaran ditolak admin</strong>
                        <p>Booking ini tidak bisa dilanjutkan lagi lewat pembayaran ulang. Silakan hubungi admin jika butuh penjelasan lebih lanjut.</p>
                    </div>
                </div>
            @elseif($booking->payment_status === 'paid')
                <div class="payment-info-box success">
                    <i class="bi bi-check-circle"></i>
                    <div class="content">
                        <strong>Pembayaran dikonfirmasi</strong>
                        <p>
                            @if($booking->isBlockedByMaintenance())
                                Pembayaran aman. Saat ini unit sedang masuk maintenance setelah inspeksi pengembalian, sehingga admin perlu menyesuaikan penyerahan kendaraan.
                            @elseif($booking->isAwaitingVehicleReturn())
                                Pembayaran aman. Jadwal sewa Anda sudah mulai, tetapi unit masih dipakai customer sebelumnya.
                            @elseif($booking->status === 'confirmed' && $booking->hasNotStartedYet())
                                Pembayaran aman. Booking Anda sudah dikonfirmasi dan terjadwal mulai pada {{ $booking->start_date->format('d M Y') }} pukul {{ $booking->pickup_time_label }}.
                            @elseif($booking->status === 'confirmed')
                                Terima kasih! Booking Anda sudah aktif.
                            @else
                                Pembayaran sudah aman dan booking sedang menunggu giliran kendaraan.
                            @endif
                        </p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    @if($booking->status === 'completed' && $booking->hasReturnInspection())
        <div class="section-card">
            <div class="section-title">
                <div class="icon"><i class="bi bi-clipboard2-check"></i></div>
                <h5>Checklist Pengembalian</h5>
            </div>

            <div class="detail-row">
                <span class="label">Tanggal Aktual Kembali</span>
                <span class="value">{{ $booking->actual_return_date ? $booking->actual_return_date->format('d M Y') : '-' }}</span>
            </div>
            <div class="detail-row">
                <span class="label">Kondisi Unit</span>
                <span class="value">{{ $booking->getReturnConditionStatusLabel() }}</span>
            </div>

            @if($booking->return_fuel_level)
                <div class="detail-row">
                    <span class="label">Level BBM Saat Kembali</span>
                    <span class="value">{{ $booking->getReturnFuelLevelLabel() }}</span>
                </div>
            @endif

            @if($booking->return_odometer !== null)
                <div class="detail-row">
                    <span class="label">Odometer Saat Kembali</span>
                    <span class="value">{{ number_format($booking->return_odometer, 0, ',', '.') }} km</span>
                </div>
            @endif

            @if(!empty($booking->getReturnChecklistLabels()))
                <div class="detail-row">
                    <span class="label">Checklist Lolos</span>
                    <div class="value">
                        <div class="inspection-badges">
                            @foreach($booking->getReturnChecklistLabels() as $checkLabel)
                                <span class="inspection-badge">{{ $checkLabel }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            @if($booking->return_notes)
                <div class="detail-row">
                    <span class="label">Catatan Admin</span>
                    <div class="value inspection-note">{{ $booking->return_notes }}</div>
                </div>
            @endif

            @if($booking->return_photo)
                <div class="detail-row">
                    <span class="label">Foto Pengembalian</span>
                    <div class="value">
                        <a href="{{ asset('storage/' . $booking->return_photo) }}" target="_blank" rel="noopener noreferrer">
                            <img src="{{ asset('storage/' . $booking->return_photo) }}" class="inspection-photo" alt="Foto pengembalian booking {{ $booking->id }}">
                        </a>
                    </div>
                </div>
            @endif
        </div>
    @endif

    @if($booking->status === 'completed' || $booking->review)
        <div class="section-card">
            <div class="section-title">
                <div class="icon"><i class="bi bi-star-fill"></i></div>
                <h5>Review Kendaraan</h5>
            </div>

            @if($booking->review)
                <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap mb-3">
                    <div>
                        <span class="badge {{ $booking->review->getStatusBadgeClass() }} rounded-pill px-3 py-2">{{ $booking->review->getStatusLabel() }}</span>
                        <div class="mt-3 fw-semibold" style="font-size: 1.1rem;">{{ $booking->review->getStarsLabel() }}</div>
                        <h5 class="mt-3 mb-2" style="font-weight: 700; color: #0f172a;">{{ $booking->review->title }}</h5>
                    </div>
                    <div class="text-muted small">{{ $booking->review->created_at->format('d M Y H:i') }}</div>
                </div>

                <p class="mb-0 text-muted">{{ $booking->review->review_text }}</p>

                <div class="detail-row mt-3">
                    <span class="label">Helpful</span>
                    <span class="value">{{ $booking->review->helpful_count }} orang menandai review ini membantu</span>
                </div>

                @if($booking->review->admin_note)
                    <div class="alert-card {{ $booking->review->isRejected() ? 'warning' : 'info' }} mt-3 mb-0">
                        <i class="bi {{ $booking->review->isRejected() ? 'bi-exclamation-triangle-fill' : 'bi-info-circle-fill' }}"></i>
                        <div class="content">
                            <strong>Catatan Admin</strong>
                            <p>{{ $booking->review->admin_note }}</p>
                        </div>
                    </div>
                @endif

                @if(!$usesAdminLayout && $booking->review->canBeManagedByOwner())
                    <div class="action-buttons">
                        <a href="{{ route('reviews.edit', $booking->review) }}" class="btn btn-action btn-primary-gradient">
                            <i class="bi bi-pencil-square"></i> Edit Review
                        </a>
                        <form method="POST" action="{{ route('reviews.destroy', $booking->review) }}" style="display: inline;" onsubmit="return confirm('Hapus review ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-action btn-danger-outline">
                                <i class="bi bi-trash3"></i> Hapus Review
                            </button>
                        </form>
                    </div>
                @endif
            @elseif(!$usesAdminLayout && $booking->canBeReviewed())
                <div class="alert-card info mb-0">
                    <i class="bi bi-chat-square-heart-fill"></i>
                    <div class="content">
                        <strong>Booking selesai. Saatnya beri review</strong>
                        <p>Bagikan pengalaman Anda soal kondisi kendaraan dan proses layanannya. Review akan tampil setelah dimoderasi admin.</p>
                    </div>
                </div>

                <div class="action-buttons">
                    <a href="{{ route('reviews.create', $booking) }}" class="btn btn-action btn-primary-gradient">
                        <i class="bi bi-star-fill"></i> Tulis Review
                    </a>
                </div>
            @elseif($usesAdminLayout)
                <div class="alert-card info mb-0">
                    <i class="bi bi-info-circle-fill"></i>
                    <div class="content">
                        <strong>Belum ada review customer</strong>
                        <p>Customer bisa menulis review setelah booking selesai dan pembayaran sudah lunas.</p>
                    </div>
                </div>
            @endif
        </div>
    @endif

    <!-- Action Buttons -->
    @if(!Auth::user()->isAdmin() && ($booking->canEnterPaymentFlow() || $booking->canBeCancelled()))
        @if($booking->canEnterPaymentFlow() && !Auth::user()->isKtpVerified())
            <div class="alert-card warning" style="margin-bottom: 1rem;">
                <i class="bi bi-person-badge"></i>
                <div class="content">
                    <strong>Verifikasi KTP Diperlukan</strong>
                    <p>Anda harus memverifikasi KTP sebelum dapat melakukan pembayaran.</p>
                </div>
            </div>
        @endif
        <div class="action-buttons">
            @if($booking->canEnterPaymentFlow() && Auth::user()->isKtpVerified())
                <a href="{{ route('bookings.payment', $booking) }}" class="btn btn-action btn-primary-gradient">
                    <i class="bi bi-credit-card"></i> Lanjutkan Pembayaran
                </a>
            @elseif($booking->canEnterPaymentFlow())
                <a href="{{ route('profile.ktp') }}" class="btn btn-action btn-primary-gradient">
                    <i class="bi bi-person-badge"></i> Verifikasi KTP
                </a>
            @endif

            @if($booking->canBeCancelled())
                <form method="POST" action="{{ route('bookings.cancel', $booking) }}" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-action btn-danger-outline" onclick="return confirm('Batalkan booking ini?')">
                        <i class="bi bi-x-circle"></i> Batalkan Booking
                    </button>
                </form>
            @endif
        </div>
    @endif

    <!-- Admin Section -->
    @if(Auth::check() && Auth::user()->isAdmin())
        <div class="admin-section">
            <h5><i class="bi bi-shield-check me-2"></i>Panel Admin</h5>

            @if($waitingListQueue->isNotEmpty())
                <div class="alert alert-secondary mb-4">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
                        <div>
                            <strong><i class="bi bi-list-ol me-1"></i>Antrean Booking Kendaraan Ini</strong><br>
                            <small class="text-muted">Urutan aktivasi mengikuti pembayaran yang sudah diverifikasi lebih dulu.</small>
                        </div>
                        <span class="badge text-bg-dark">{{ $waitingListQueue->count() }} booking antre</span>
                    </div>

                    <div class="list-group">
                        @foreach($waitingListQueue as $queueIndex => $queueBooking)
                            <div class="list-group-item {{ $queueBooking->id === $booking->id ? 'active border-0' : '' }}">
                                <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap">
                                    <div>
                                        <div class="fw-semibold">
                                            Antrean #{{ $queueIndex + 1 }} · {{ $queueBooking->user->name }}
                                            @if($queueBooking->id === $booking->id)
                                                <span class="badge text-bg-light ms-2">Booking ini</span>
                                            @endif
                                        </div>
                                        <small class="d-block {{ $queueBooking->id === $booking->id ? 'text-white-50' : 'text-muted' }}">
                                            {{ $queueBooking->start_date->format('d M Y') }} {{ $queueBooking->pickup_time_label }} - {{ $queueBooking->end_date->format('d M Y') }} {{ $queueBooking->return_time_label }}
                                            · {{ $queueBooking->duration_days }} hari
                                        </small>
                                        <small class="d-block {{ $queueBooking->id === $booking->id ? 'text-white-50' : 'text-muted' }}">
                                            Dibayar {{ $queueBooking->updated_at?->diffForHumans() ?? $queueBooking->created_at->diffForHumans() }}
                                        </small>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-semibold">Rp{{ number_format($queueBooking->total_price, 0, ',', '.') }}</div>
                                        <small class="{{ $queueBooking->id === $booking->id ? 'text-white-50' : 'text-muted' }}">#{{ $queueBooking->id }}</small>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
            
            @if($booking->canBeVerified())
                @if($booking->usesTransferProof() && $booking->payment_proof)
                    <p class="mb-2" style="opacity: 0.8;">Bukti Transfer:</p>
                    <img src="{{ asset('storage/' . $booking->payment_proof) }}" class="proof-image" alt="Bukti transfer">
                @elseif($booking->usesWhatsAppConfirmation())
                    <div class="alert alert-warning">
                        <strong><i class="bi bi-whatsapp"></i> Konfirmasi via WhatsApp</strong><br>
                        Customer akan mengirim bukti pembayaran langsung ke admin melalui WhatsApp.
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.bookings.verify-payment', $booking) }}" class="row g-3">
                    @csrf
                    <div class="col-12">
                        <textarea class="form-control" name="notes" placeholder="Catatan verifikasi (opsional)" rows="3"></textarea>
                    </div>
                    <div class="col-12 action-row">
                        <button type="submit" name="verified" value="1" class="btn btn-success-gradient btn-action">
                            <i class="bi bi-check-circle"></i> Verifikasi Pembayaran
                        </button>
                        <button type="submit" name="verified" value="0" class="btn btn-danger-outline btn-action">
                            <i class="bi bi-x-circle"></i> Tolak
                        </button>
                    </div>
                </form>
            @elseif($booking->status === 'waiting_list' && $booking->payment_status === 'paid')
                <div class="alert alert-warning">
                    <i class="bi bi-list-ol"></i> <strong>Booking sedang menunggu kendaraan tersedia</strong>
                    @if($booking->notes)
                        <br><small>Catatan: {{ $booking->notes }}</small>
                    @endif
                </div>
            @elseif($booking->isBlockedByMaintenance())
                <div class="alert alert-warning">
                    <i class="bi bi-tools"></i> <strong>Booking tertahan karena maintenance</strong>
                    @if($booking->maintenance_hold_reason)
                        <br><small>Alasan hold: {{ $booking->maintenance_hold_reason }}</small>
                    @elseif($booking->notes)
                        <br><small>Catatan: {{ $booking->notes }}</small>
                    @endif

                    @if($booking->canBeRescheduledByAdmin())
                        <div class="mt-3">
                            <a href="{{ route('admin.bookings.reschedule-form', $booking) }}" class="btn btn-warning rounded-pill px-4">
                                <i class="bi bi-calendar2-week me-1"></i>Jadwalkan Ulang Booking
                            </a>
                        </div>
                    @endif
                </div>
            @elseif($booking->isAwaitingVehicleReturn())
                <div class="alert alert-warning">
                    <i class="bi bi-hourglass-split"></i> <strong>Booking berikutnya sudah masuk jadwal, tetapi unit belum kembali</strong>
                    @if($booking->notes)
                        <br><small>Catatan: {{ $booking->notes }}</small>
                    @endif
                </div>
            @elseif($booking->payment_status === 'paid')
                <div class="alert alert-success">
                    <i class="bi bi-check-circle"></i> <strong>Pembayaran sudah diverifikasi</strong>
                    @if($booking->notes)
                        <br><small>Catatan: {{ $booking->notes }}</small>
                    @endif
                </div>
            @elseif($booking->payment_status === 'failed')
                <div class="alert alert-danger">
                    <i class="bi bi-x-circle"></i> <strong>Pembayaran ditolak</strong>
                    @if($booking->notes)
                        <br><small>Catatan: {{ $booking->notes }}</small>
                    @endif
                </div>
            @elseif($booking->usesTransferProof() && !$booking->payment_proof)
                <div class="alert alert-warning">
                    <i class="bi bi-hourglass"></i> <strong>Menunggu customer upload bukti transfer</strong>
                </div>
            @else
                <p style="opacity: 0.7;">Tidak ada aksi verifikasi yang diperlukan.</p>
            @endif

            @if($booking->canResendCustomerNotification())
                <div class="alert alert-light mt-3 mb-0">
                    <div class="d-flex justify-content-between align-items-center gap-3 flex-wrap">
                        <div>
                            <strong><i class="bi bi-envelope-paper me-1"></i>Notifikasi Customer</strong><br>
                            <small>Email akan dikirim ke {{ $booking->user->email }} sesuai status booking saat ini.</small>
                        </div>
                        <form method="POST" action="{{ route('admin.bookings.resend-notification', $booking) }}">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                <i class="bi bi-send me-1"></i>{{ $booking->getResendableNotificationLabel() }}
                            </button>
                        </form>
                    </div>
                </div>
            @endif

            @if($booking->canBeRescheduledByAdmin())
                <div class="alert alert-warning mt-3 mb-0">
                    <div class="d-flex justify-content-between align-items-center gap-3 flex-wrap">
                        <div>
                            <strong><i class="bi bi-calendar2-week me-1"></i>Penyesuaian Jadwal</strong><br>
                            <small class="text-muted">Booking ini masih butuh jadwal baru agar keluar dari daftar tindakan admin.</small>
                        </div>
                        <a href="{{ route('admin.bookings.reschedule-form', $booking) }}" class="btn btn-sm btn-warning rounded-pill px-3">
                            <i class="bi bi-arrow-repeat me-1"></i>Jadwalkan Ulang
                        </a>
                    </div>
                </div>
            @endif
            
            @if($booking->canBeCompleted())
                <hr style="border-color: rgba(255,255,255,0.2); margin: 2rem 0;">
                <h6 style="margin-bottom: 1rem;"><i class="bi bi-check2-all"></i> Selesaikan Booking</h6>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> Klik tombol di bawah jika customer sudah mengembalikan kendaraan.
                </div>
                <a href="{{ route('admin.bookings.complete-form', $booking) }}" class="btn btn-success-gradient btn-action">
                    <i class="bi bi-check2-all"></i> Selesaikan Booking & Kembalikan Kendaraan
                </a>
            @endif
        </div>
    @endif
</div>
@endsection
