@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<style>
    .dash-header {
        background: var(--gradient-brand);
        color: white;
        padding: 3rem 0 5.5rem;
        position: relative;
        overflow: hidden;
    }
    .dash-header::before {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at 85% 20%, rgba(6,182,212,0.25), transparent 55%),
                    radial-gradient(circle at 15% 80%, rgba(255,255,255,0.06), transparent 45%);
        pointer-events: none;
    }
    .dash-header h1 {
        font-weight: 800;
        font-size: 1.9rem;
        letter-spacing: -0.06em;
    }
    .dash-header .subtitle {
        opacity: 0.7;
        font-size: 0.95rem;
        line-height: 1.7;
        max-width: 28rem;
    }
    .dash-body {
        margin-top: -3.5rem;
        position: relative;
        z-index: 2;
        padding-bottom: 3rem;
    }

    /* Stat Cards */
    .stat-row {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 0.85rem;
    }
    @media (max-width: 991px) { .stat-row { grid-template-columns: repeat(3, 1fr); } }
    @media (max-width: 575px) { .stat-row { grid-template-columns: repeat(2, 1fr); gap: 0.65rem; } }

    .s-card {
        background: white;
        border-radius: 1rem;
        padding: 1.4rem 1.3rem;
        box-shadow: 0 4px 24px rgba(15,23,42,0.07);
        border: 1px solid rgba(203,213,225,0.45);
        transition: transform 0.25s ease, box-shadow 0.25s ease;
        position: relative;
        overflow: hidden;
        text-align: center;
    }
    .s-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 32px rgba(15,23,42,0.12);
    }
    .s-card::after {
        content: '';
        position: absolute;
        bottom: 0; left: 0; right: 0;
        height: 3px;
    }
    .s-card.blue::after   { background: var(--gradient-brand); }
    .s-card.green::after  { background: var(--gradient-success); }
    .s-card.orange::after { background: var(--gradient-warning); }
    .s-card.cyan::after   { background: var(--gradient-cyan); }
    .s-icon {
        width: 48px; height: 48px;
        border-radius: 0.85rem;
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 1.2rem;
        margin-bottom: 0.75rem;
    }
    .s-icon.blue   { background: rgba(var(--color-secondary-rgb), 0.14); color: var(--color-secondary-strong); }
    .s-icon.orange { background: rgba(var(--color-warning-rgb), 0.14); color: #D97706; }
    .s-icon.cyan   { background: rgba(var(--color-secondary-rgb), 0.16); color: var(--color-secondary); }
    .s-icon.green  { background: rgba(var(--color-success-rgb), 0.14); color: var(--color-success); }
    .s-num {
        font-size: 1.85rem;
        font-weight: 800;
        color: var(--color-heading);
        line-height: 1;
        letter-spacing: -0.05em;
    }
    .s-label {
        color: var(--color-muted);
        font-size: 0.8rem;
        margin-top: 0.25rem;
    }

    /* Action Cards */
    .act-card {
        border-radius: 1.15rem;
        padding: 1.75rem 1.6rem;
        color: white;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 1.1rem;
        transition: all 0.25s ease;
        position: relative;
        overflow: hidden;
    }
    .act-card::before {
        content: '';
        position: absolute;
        top: -30%; right: -10%;
        width: 140px; height: 140px;
        border-radius: 50%;
        background: rgba(255,255,255,0.08);
        filter: blur(30px);
    }
    .act-card:hover {
        transform: translateY(-3px);
        color: white;
    }
    .act-card.primary {
        background: var(--gradient-brand);
        box-shadow: 0 14px 36px rgba(var(--color-primary-rgb), 0.22);
    }
    .act-card.primary:hover { box-shadow: 0 18px 40px rgba(var(--color-secondary-rgb), 0.24); }
    .act-card.secondary {
        background: linear-gradient(135deg, var(--color-secondary-strong) 0%, #155E75 100%);
        box-shadow: 0 14px 36px rgba(14, 116, 144, 0.22);
    }
    .act-card.secondary:hover { box-shadow: 0 18px 40px rgba(14, 116, 144, 0.28); }
    .act-visual {
        width: 3.5rem; height: 3.5rem;
        flex: 0 0 3.5rem;
        border-radius: 1rem;
        display: flex; align-items: center; justify-content: center;
        background: rgba(255,255,255,0.14);
        border: 1px solid rgba(255,255,255,0.16);
        font-size: 1.4rem;
    }
    .act-card h5 { font-weight: 700; font-size: 1rem; margin-bottom: 0.2rem; }
    .act-card p { opacity: 0.7; font-size: 0.82rem; margin-bottom: 0; }

    /* Section Title */
    .sec-title {
        font-weight: 700;
        font-size: 1.2rem;
        color: var(--color-heading);
        letter-spacing: -0.04em;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 1.15rem;
    }
    .sec-title i { color: var(--color-secondary-strong); font-size: 1.1rem; }

    /* Upcoming Booking Cards */
    .up-card {
        background: white;
        border-radius: 1rem;
        padding: 1.3rem;
        border: 1px solid rgba(203,213,225,0.45);
        border-left: 4px solid var(--color-secondary);
        box-shadow: 0 2px 14px rgba(15,23,42,0.05);
        transition: all 0.25s ease;
    }
    .up-card:hover {
        box-shadow: 0 8px 28px rgba(15,23,42,0.1);
        transform: translateY(-2px);
    }
    .up-card .veh-name {
        font-weight: 700;
        color: var(--color-heading);
        font-size: 1rem;
    }
    .up-card .meta {
        color: var(--color-muted);
        font-size: 0.82rem;
        margin-top: 0.35rem;
        line-height: 1.65;
    }
    .up-card .meta i { width: 1rem; text-align: center; }

    /* Table */
    .tbl-wrap {
        background: white;
        border-radius: 1rem;
        border: 1px solid rgba(203,213,225,0.45);
        box-shadow: 0 4px 24px rgba(15,23,42,0.06);
        overflow: hidden;
    }
    .tbl-wrap table { width: 100%; border-collapse: collapse; }
    .tbl-wrap thead th {
        background: #f8fafc;
        color: var(--color-muted);
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        padding: 0.85rem 1rem;
        border-bottom: 1px solid rgba(226,232,240,0.85);
        white-space: nowrap;
    }
    .tbl-wrap tbody td {
        padding: 0.9rem 1rem;
        vertical-align: middle;
        border-bottom: 1px solid rgba(241,245,249,0.95);
        font-size: 0.88rem;
    }
    .tbl-wrap tbody tr:last-child td { border-bottom: none; }
    .tbl-wrap tbody tr { transition: background 0.2s; }
    .tbl-wrap tbody tr:hover { background: rgba(var(--color-secondary-rgb), 0.04); }
    .badge-status {
        padding: 0.38rem 0.72rem;
        border-radius: 2rem;
        font-weight: 600;
        font-size: 0.72rem;
    }
    .tbl-empty {
        text-align: center;
        padding: 3.5rem 2rem;
        color: var(--color-muted);
    }
    .tbl-empty i {
        font-size: 2.5rem;
        opacity: 0.35;
        margin-bottom: 0.75rem;
    }

    /* Empty State */
    .empty-box {
        text-align: center;
        padding: 3rem 2.5rem;
        background: white;
        border-radius: 1rem;
        border: 1px solid rgba(203,213,225,0.45);
        box-shadow: 0 4px 24px rgba(15,23,42,0.06);
    }
    .empty-box i {
        font-size: 3.5rem;
        opacity: 0.35;
        margin-bottom: 1rem;
    }
    .empty-box h5 { color: var(--color-heading); font-weight: 700; }
    .empty-box p { color: var(--color-muted); font-size: 0.9rem; }

    @media (max-width: 767.98px) {
        .act-card { padding: 1.35rem; }
        .act-visual { width: 3rem; height: 3rem; flex-basis: 3rem; font-size: 1.2rem; }
        .s-num { font-size: 1.5rem; }
    }
</style>

<!-- Header -->
<div class="dash-header">
    <div class="container position-relative" style="z-index:2">
        <h1 class="mb-2">Dashboard</h1>
        <p class="subtitle mb-0">Selamat datang kembali, {{ Auth::user()->name }}!</p>
    </div>
</div>

<div class="container dash-body">
    <!-- Stat Cards -->
    <div class="stat-row mb-5">
        <div class="s-card blue">
            <div class="s-icon blue"><i class="bi bi-calendar-check"></i></div>
            <div class="s-num">{{ $totalBookings }}</div>
            <div class="s-label">Total Booking</div>
        </div>
        <div class="s-card orange">
            <div class="s-icon orange"><i class="bi bi-clock"></i></div>
            <div class="s-num">{{ $pendingBookings }}</div>
            <div class="s-label">Menunggu Bayar</div>
        </div>
        <div class="s-card cyan">
            <div class="s-icon cyan"><i class="bi bi-list-ol"></i></div>
            <div class="s-num">{{ $waitingListBookings }}</div>
            <div class="s-label">Antrean</div>
        </div>
        <div class="s-card blue">
            <div class="s-icon blue"><i class="bi bi-car-front"></i></div>
            <div class="s-num">{{ $confirmedBookings }}</div>
            <div class="s-label">Dikonfirmasi</div>
        </div>
        <div class="s-card green">
            <div class="s-icon green"><i class="bi bi-check-circle"></i></div>
            <div class="s-num">{{ $completedBookings }}</div>
            <div class="s-label">Selesai</div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row g-3 mb-5">
        <div class="col-md-6">
            <a href="{{ route('vehicles.browse') }}" class="act-card primary">
                <div class="act-visual"><i class="bi bi-search"></i></div>
                <div>
                    <h5>Cari Kendaraan</h5>
                    <p>Temukan mobil atau motor untuk rental</p>
                </div>
            </a>
        </div>
        <div class="col-md-6">
            <a href="{{ route('bookings.index') }}" class="act-card secondary">
                <div class="act-visual"><i class="bi bi-calendar3"></i></div>
                <div>
                    <h5>Semua Booking</h5>
                    <p>Lihat riwayat dan kelola booking Anda</p>
                </div>
            </a>
        </div>
    </div>

    <!-- Upcoming Bookings -->
    <div class="mb-5">
        <h4 class="sec-title"><i class="bi bi-calendar-event"></i> Booking Mendatang</h4>

        @if($upcomingBookings->count() > 0)
            <div class="row g-3">
                @foreach($upcomingBookings as $booking)
                    <div class="col-md-6">
                        <div class="up-card">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="veh-name">
                                        {{ $booking->vehicle->getTypeIcon() }} {{ $booking->vehicle->name }}
                                    </div>
                                    <div class="meta">
                                        <div><i class="bi bi-calendar me-1"></i>{{ $booking->start_date->format('d M Y') }} {{ $booking->pickup_time_label }} — {{ $booking->end_date->format('d M Y') }} {{ $booking->return_time_label }}</div>
                                        <div>
                                            <i class="bi bi-clock me-1"></i>{{ $booking->duration_days }} hari
                                            <span class="ms-2"><i class="bi bi-cash me-1"></i>Rp{{ number_format($booking->total_price, 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                </div>
                                <a href="{{ route('bookings.show', $booking) }}" class="btn btn-sm btn-primary rounded-pill px-3" style="font-size:0.78rem">
                                    <i class="bi bi-eye me-1"></i>Detail
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-box">
                <i class="bi bi-calendar-x d-block"></i>
                <h5>Belum Ada Booking Mendatang</h5>
                <p>Yuk mulai booking kendaraan untuk perjalananmu!</p>
                <a href="{{ route('vehicles.browse') }}" class="btn btn-primary rounded-pill px-4 mt-2">
                    <i class="bi bi-search me-2"></i>Cari Kendaraan
                </a>
            </div>
        @endif
    </div>

    <!-- Recent Bookings Table -->
    <div>
        <h4 class="sec-title"><i class="bi bi-list-check"></i> Riwayat Booking</h4>

        <div class="tbl-wrap">
            <div class="table-responsive">
                <table class="mb-0">
                    <thead>
                        <tr>
                            <th>Kendaraan</th>
                            <th>Tanggal</th>
                            <th>Durasi</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentBookings as $booking)
                            @php
                                $displayStatusKey = $booking->getDisplayStatusKey();
                            @endphp
                            <tr>
                                <td>
                                    {{ $booking->vehicle->getTypeIcon() }}
                                    <strong>{{ $booking->vehicle->name }}</strong>
                                </td>
                                <td>
                                    <div style="font-size:0.82rem">{{ $booking->start_date->format('d/m/Y') }} {{ $booking->pickup_time_label }}</div>
                                    <div style="font-size:0.78rem" class="text-muted">s/d {{ $booking->end_date->format('d/m/Y') }} {{ $booking->return_time_label }}</div>
                                </td>
                                <td>{{ $booking->duration_days }} hari</td>
                                <td><strong style="color:var(--color-secondary-strong)">Rp{{ number_format($booking->total_price, 0, ',', '.') }}</strong></td>
                                <td>
                                    @switch($displayStatusKey)
                                        @case('active')
                                            <span class="badge badge-status bg-success">🚗 Rented</span>
                                            @break
                                        @case('awaiting_return')
                                            <span class="badge badge-status bg-warning text-dark">⏳ Menunggu Unit</span>
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
                                        @case('maintenance_hold')
                                            <span class="badge badge-status bg-danger">❌ Dibatalkan</span>
                                            @break
                                    @endswitch
                                </td>
                                <td>
                                    <a href="{{ route('bookings.show', $booking) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3" style="font-size:0.78rem">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <div class="tbl-empty">
                                        <i class="bi bi-inbox d-block"></i>
                                        <p class="mb-0">Belum ada riwayat booking</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
