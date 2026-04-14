@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('page-title', 'Dashboard')

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
    .dash-date-chip {
        background: rgba(255,255,255,0.12);
        border: 1px solid rgba(255,255,255,0.16);
        border-radius: 2rem;
        padding: 0.45rem 1rem;
        font-size: 0.82rem;
        backdrop-filter: blur(6px);
    }
    .dash-body {
        margin-top: -3.5rem;
        position: relative;
        z-index: 2;
        padding-bottom: 3rem;
    }
    .stat-grid,
    .booking-stat-grid {
        display: grid;
        gap: 1rem;
    }
    .stat-grid {
        grid-template-columns: repeat(4, minmax(0, 1fr));
    }
    .booking-stat-grid {
        grid-template-columns: repeat(4, minmax(0, 1fr));
    }
    @media (max-width: 1399.98px) { .booking-stat-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
    @media (max-width: 991px) { .stat-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
    @media (max-width: 575px) {
        .stat-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 0.75rem; }
        .booking-stat-grid { grid-template-columns: 1fr; gap: 0.75rem; }
    }
    .s-card {
        background: white;
        border-radius: 1rem;
        padding: 1.5rem 1.4rem;
        box-shadow: 0 4px 24px rgba(15,23,42,0.07);
        border: 1px solid rgba(203,213,225,0.45);
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        transition: transform 0.25s ease, box-shadow 0.25s ease;
        position: relative;
        overflow: hidden;
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
    .s-card.red::after    { background: var(--gradient-danger); }
    .s-card.cyan::after   { background: var(--gradient-cyan); }
    .s-icon {
        width: 46px; height: 46px;
        border-radius: 0.85rem;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.15rem;
        flex-shrink: 0;
    }
    .s-icon.blue   { background: rgba(var(--color-secondary-rgb), 0.14); color: var(--color-secondary-strong); }
    .s-icon.green  { background: rgba(var(--color-success-rgb), 0.14); color: var(--color-success); }
    .s-icon.orange { background: rgba(var(--color-warning-rgb), 0.14); color: #D97706; }
    .s-icon.red    { background: rgba(var(--color-accent-rgb), 0.12); color: var(--color-accent); }
    .s-icon.cyan   { background: rgba(var(--color-secondary-rgb), 0.16); color: var(--color-secondary); }
    .s-num {
        font-size: 1.85rem;
        font-weight: 800;
        color: var(--color-heading);
        line-height: 1;
        letter-spacing: -0.05em;
    }
    .s-label {
        color: var(--color-muted);
        font-size: 0.82rem;
        margin-top: 0.25rem;
        line-height: 1.4;
    }
    .revenue-card {
        background: var(--gradient-brand);
        border-radius: 1.15rem;
        padding: 1.75rem 1.6rem;
        color: white;
        box-shadow: 0 14px 36px rgba(var(--color-primary-rgb), 0.22);
        display: flex;
        align-items: center;
        gap: 1.2rem;
        position: relative;
        overflow: hidden;
    }
    .revenue-card::before {
        content: '';
        position: absolute;
        top: -30%; right: -10%;
        width: 180px; height: 180px;
        border-radius: 50%;
        background: rgba(6,182,212,0.18);
        filter: blur(40px);
    }
    .rev-chip {
        width: 3rem; height: 3rem;
        border-radius: 1rem;
        display: flex; align-items: center; justify-content: center;
        background: rgba(255,255,255,0.14);
        border: 1px solid rgba(255,255,255,0.16);
        font-size: 1.2rem;
        flex-shrink: 0;
    }
    .rev-amount {
        font-size: 1.75rem;
        font-weight: 800;
        letter-spacing: -0.05em;
        line-height: 1.1;
    }
    .rev-label {
        opacity: 0.65;
        font-size: 0.8rem;
    }
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
    .qa-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 0.85rem;
    }
    @media (max-width: 991px) { .qa-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 575px) { .qa-grid { grid-template-columns: 1fr; } }
    .qa-btn {
        background: white;
        border: 1px solid rgba(203,213,225,0.5);
        border-radius: 1rem;
        padding: 1.25rem 1.2rem;
        display: flex; align-items: center; gap: 0.85rem;
        text-decoration: none;
        color: var(--color-heading);
        transition: all 0.25s ease;
        box-shadow: 0 2px 12px rgba(15,23,42,0.05);
    }
    .qa-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 28px rgba(15,23,42,0.1);
        color: var(--color-heading);
        border-color: rgba(var(--color-secondary-rgb), 0.35);
    }
    .qa-icon {
        width: 44px; height: 44px;
        border-radius: 0.75rem;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.15rem;
        flex-shrink: 0;
        color: white;
    }
    .qa-icon.blue   { background: var(--gradient-brand); }
    .qa-icon.green  { background: var(--gradient-success); }
    .qa-icon.cyan   { background: var(--gradient-cyan); }
    .qa-icon.orange { background: var(--gradient-warning); }
    .qa-text strong {
        display: block;
        font-size: 0.88rem;
        font-weight: 650;
    }
    .qa-text small {
        color: var(--color-muted);
        font-size: 0.76rem;
    }
    .tbl-wrap {
        background: white;
        border-radius: 1rem;
        border: 1px solid rgba(203,213,225,0.45);
        box-shadow: 0 4px 24px rgba(15,23,42,0.06);
        overflow: hidden;
    }
    .tbl-wrap table {
        width: 100%;
        border-collapse: collapse;
    }
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
    .tbl-avatar {
        width: 30px; height: 30px;
        border-radius: 50%;
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 0.72rem; font-weight: 700;
        background: var(--gradient-brand);
        color: white;
        box-shadow: 0 4px 12px rgba(var(--color-primary-rgb), 0.16);
    }
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
    .calendar-widget-card {
        background: rgba(255,255,255,0.96);
        border: 1px solid rgba(203,213,225,0.72);
        border-radius: 1.25rem;
        box-shadow: 0 4px 24px rgba(15,23,42,0.06);
        padding: 1.3rem;
    }
    .calendar-widget-head {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 1rem;
        margin-bottom: 1rem;
    }
    .calendar-widget-head .eyebrow {
        font-size: 0.72rem;
        color: var(--color-secondary-strong);
        text-transform: uppercase;
        letter-spacing: 0.08em;
        font-weight: 700;
    }
    .calendar-widget-head h3 {
        margin: 0;
        color: var(--color-heading);
        font-size: 1.15rem;
        font-weight: 800;
        letter-spacing: -0.04em;
    }
    .calendar-widget-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 0.85rem;
        margin-bottom: 1rem;
    }
    .calendar-widget-stat {
        background: linear-gradient(135deg, rgba(31,41,55,0.04) 0%, rgba(6,182,212,0.12) 100%);
        border-radius: 1rem;
        padding: 0.9rem 1rem;
        border: 1px solid rgba(203,213,225,0.68);
    }
    .calendar-widget-stat .label {
        display: block;
        color: var(--color-muted);
        font-size: 0.74rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        margin-bottom: 0.3rem;
    }
    .calendar-widget-stat strong {
        color: var(--color-heading);
        font-size: 1.2rem;
        font-weight: 800;
    }
    .calendar-widget-list {
        display: grid;
        gap: 0.7rem;
        margin-bottom: 1rem;
    }
    .calendar-widget-item {
        border: 1px solid rgba(226,232,240,0.86);
        border-radius: 1rem;
        padding: 0.85rem 0.95rem;
        background: #fff;
    }
    .calendar-widget-item .top {
        display: flex;
        justify-content: space-between;
        gap: 0.75rem;
        margin-bottom: 0.45rem;
        color: var(--color-heading);
        font-weight: 700;
    }
    .calendar-widget-bar {
        height: 0.55rem;
        border-radius: 999px;
        background: rgba(226,232,240,0.9);
        overflow: hidden;
    }
    .calendar-widget-bar span {
        display: block;
        height: 100%;
        border-radius: inherit;
        background: var(--gradient-brand);
    }
    .calendar-widget-actions {
        display: flex;
        gap: 0.65rem;
        flex-wrap: wrap;
    }
    @media (max-width: 767.98px) {
        .dash-header {
            padding: 2.4rem 0 4.4rem;
        }
        .dash-header h1 {
            font-size: 1.45rem;
            line-height: 1.35;
        }
        .dash-header .subtitle {
            font-size: 0.86rem;
            line-height: 1.6;
        }
        .dash-date-chip {
            display: inline-flex !important;
            margin-top: 0.75rem;
            font-size: 0.76rem;
            padding: 0.38rem 0.8rem;
        }
        .dash-body {
            margin-top: -2.65rem;
            padding-bottom: 2rem;
        }
        .stat-grid,
        .booking-stat-grid {
            grid-template-columns: 1fr;
            gap: 0.7rem;
        }
        .revenue-card {
            padding: 1.2rem;
            border-radius: 1rem;
        }
        .s-card {
            padding: 1rem 0.9rem;
            border-radius: 0.9rem;
        }
        .s-icon {
            width: 40px;
            height: 40px;
            font-size: 1rem;
            border-radius: 0.75rem;
        }
        .s-num {
            font-size: 1.35rem;
        }
        .s-label {
            font-size: 0.78rem;
        }
        .rev-chip {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 0.8rem;
            font-size: 1rem;
        }
        .rev-amount {
            font-size: 1.28rem;
        }
        .rev-label {
            font-size: 0.74rem;
        }
        .sec-title {
            font-size: 1.02rem;
            margin-bottom: 0.85rem;
        }
        .qa-btn {
            padding: 0.95rem 0.9rem;
            border-radius: 0.85rem;
        }
        .qa-icon {
            width: 40px;
            height: 40px;
            font-size: 1rem;
        }
        .qa-text strong {
            font-size: 0.84rem;
        }
        .tbl-wrap {
            overflow-x: auto;
        }
        .tbl-wrap table {
            min-width: 700px;
        }
        .tbl-wrap thead th {
            padding: 0.72rem 0.8rem;
        }
        .tbl-wrap tbody td {
            padding: 0.78rem 0.8rem;
            font-size: 0.82rem;
        }
        .calendar-widget-card {
            border-radius: 1rem;
            padding: 1rem;
        }
        .calendar-widget-head {
            flex-direction: column;
            align-items: stretch;
            gap: 0.75rem;
        }
        .calendar-widget-head h3 {
            font-size: 1rem;
        }
        .calendar-widget-head .btn {
            width: 100%;
            justify-content: center;
        }
        .calendar-widget-grid {
            grid-template-columns: 1fr;
            gap: 0.65rem;
        }
        .calendar-widget-item {
            border-radius: 0.85rem;
            padding: 0.75rem 0.8rem;
        }
        .calendar-widget-actions {
            display: grid;
            grid-template-columns: 1fr;
            gap: 0.55rem;
        }
        .calendar-widget-actions .btn {
            width: 100%;
            justify-content: center;
        }
    }
    @media (max-width: 420px) {
        .dash-header h1 {
            font-size: 1.3rem;
        }
        .s-num {
            font-size: 1.2rem;
        }
        .rev-amount {
            font-size: 1.16rem;
        }
    }
</style>

<!-- Header -->
<div class="dash-header">
    <div class="container position-relative" style="z-index:2">
        <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap">
            <div>
                <h1 class="mb-2">Admin Dashboard</h1>
                <p class="subtitle mb-0">Pantau kendaraan, booking, dan pendapatan rental dari sini.</p>
            </div>
            <span class="dash-date-chip d-none d-md-inline-flex align-items-center gap-2">
                <i class="bi bi-calendar3"></i> {{ now()->format('d M Y') }}
            </span>
        </div>
    </div>
</div>

<div class="container dash-body">
    <!-- Vehicle Stats -->
    <div class="stat-grid mb-4">
        <div class="s-card blue">
            <div class="s-icon blue"><i class="bi bi-car-front"></i></div>
            <div>
                <div class="s-num">{{ $totalVehicles }}</div>
                <div class="s-label">Total Kendaraan</div>
            </div>
        </div>
        <div class="s-card green">
            <div class="s-icon green"><i class="bi bi-check-circle"></i></div>
            <div>
                <div class="s-num">{{ $availableVehicles }}</div>
                <div class="s-label">Tersedia</div>
            </div>
        </div>
        <div class="s-card orange">
            <div class="s-icon orange"><i class="bi bi-key"></i></div>
            <div>
                <div class="s-num">{{ $rentedVehicles }}</div>
                <div class="s-label">Sedang Disewa</div>
            </div>
        </div>
        <div class="s-card red">
            <div class="s-icon red"><i class="bi bi-tools"></i></div>
            <div>
                <div class="s-num">{{ $maintenanceVehicles }}</div>
                <div class="s-label">Maintenance</div>
            </div>
        </div>
    </div>

    <!-- Booking Stats & Revenue -->
    <div class="row g-3 mb-5">
        <div class="col-lg-8">
            <div class="booking-stat-grid">
                <div class="s-card cyan">
                    <div class="s-icon cyan"><i class="bi bi-calendar-check"></i></div>
                    <div>
                        <div class="s-num">{{ $totalBookings }}</div>
                        <div class="s-label">Total Booking</div>
                    </div>
                </div>
                <div class="s-card orange">
                    <div class="s-icon orange"><i class="bi bi-clock"></i></div>
                    <div>
                        <div class="s-num">{{ $pendingBookings }}</div>
                        <div class="s-label">Pending</div>
                    </div>
                </div>
                <div class="s-card red">
                    <div class="s-icon red"><i class="bi bi-list-ol"></i></div>
                    <div>
                        <div class="s-num">{{ $waitingListBookings }}</div>
                        <div class="s-label">Antrean</div>
                    </div>
                </div>
                <div class="s-card blue">
                    <div class="s-icon blue"><i class="bi bi-check2-circle"></i></div>
                    <div>
                        <div class="s-num">{{ $confirmedBookings }}</div>
                        <div class="s-label">Confirmed</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="revenue-card h-100">
                <div class="rev-chip"><i class="bi bi-wallet2"></i></div>
                <div>
                    <div class="rev-label">Total Pendapatan</div>
                    <div class="rev-amount">Rp{{ number_format($totalRevenue, 0, ',', '.') }}</div>
                    <div class="rev-label mt-1">Dari booking selesai</div>
                </div>
            </div>
        </div>
    </div>

    <div class="mb-5">
        @include('admin.dashboard.calendar-widget')
    </div>

    <!-- Quick Actions -->
    <div class="mb-5">
        <h4 class="sec-title"><i class="bi bi-lightning-charge-fill"></i> Aksi Cepat</h4>
        <div class="qa-grid">
            <a href="{{ route('admin.vehicles.create') }}" class="qa-btn">
                <div class="qa-icon blue"><i class="bi bi-plus-lg"></i></div>
                <div class="qa-text">
                    <strong>Tambah Kendaraan</strong>
                    <small>Mobil atau motor baru</small>
                </div>
            </a>
            <a href="{{ route('admin.vehicles.index') }}" class="qa-btn">
                <div class="qa-icon green"><i class="bi bi-gear"></i></div>
                <div class="qa-text">
                    <strong>Kelola Kendaraan</strong>
                    <small>Edit, maintenance, hapus</small>
                </div>
            </a>
            <a href="{{ route('admin.bookings.index') }}" class="qa-btn">
                <div class="qa-icon cyan"><i class="bi bi-calendar3"></i></div>
                <div class="qa-text">
                    <strong>Kelola Booking</strong>
                    <small>Verifikasi & selesaikan</small>
                </div>
            </a>
            <a href="{{ route('admin.calendar.index') }}" class="qa-btn">
                <div class="qa-icon cyan"><i class="bi bi-grid-3x3-gap"></i></div>
                <div class="qa-text">
                    <strong>Kalender Armada</strong>
                    <small>Maintenance & pricing harian</small>
                </div>
            </a>
            <a href="{{ route('admin.settings.index') }}" class="qa-btn">
                <div class="qa-icon orange"><i class="bi bi-sliders"></i></div>
                <div class="qa-text">
                    <strong>Pengaturan</strong>
                    <small>Atur jadwal default</small>
                </div>
            </a>
        </div>
    </div>

    <!-- Recent Bookings Table -->
    <div>
        <h4 class="sec-title"><i class="bi bi-list-check"></i> Booking Terbaru</h4>

        <div class="tbl-wrap">
            <div class="table-responsive">
                <table class="mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Customer</th>
                            <th>Kendaraan</th>
                            <th>Tanggal</th>
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
                                <td><strong>#{{ $booking->id }}</strong></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="tbl-avatar">{{ strtoupper(substr($booking->user->name, 0, 1)) }}</span>
                                        {{ $booking->user->name }}
                                    </div>
                                </td>
                                <td>
                                    {{ $booking->vehicle->getTypeIcon() }}
                                    {{ $booking->vehicle->name }}
                                </td>
                                <td>
                                    <div style="font-size:0.82rem">{{ $booking->start_date->format('d/m/Y') }} {{ $booking->pickup_time_label }}</div>
                                    <div style="font-size:0.78rem" class="text-muted">s/d {{ $booking->end_date->format('d/m/Y') }} {{ $booking->return_time_label }}</div>
                                </td>
                                <td><strong style="color:var(--color-success)">Rp{{ number_format($booking->total_price, 0, ',', '.') }}</strong></td>
                                <td>
                                    @if($booking->isActive())
                                        <span class="badge badge-status bg-success">🚗 Rented</span>
                                    @elseif($booking->isAwaitingVehicleReturn())
                                        <span class="badge badge-status bg-warning text-dark">⏳ Menunggu Unit</span>
                                    @else
                                        @switch($booking->status)
                                            @case('pending')
                                                <span class="badge badge-status bg-warning text-dark">⏳ Pending</span>
                                                @break
                                            @case('waiting_list')
                                                <span class="badge badge-status bg-warning text-dark">🕒 Antrean</span>
                                                @break
                                            @case('confirmed')
                                                @if($booking->hasNotStartedYet())
                                                    <span class="badge badge-status bg-info">📅 Terjadwal</span>
                                                @else
                                                    <span class="badge badge-status bg-info">✓ Confirmed</span>
                                                @endif
                                                @break
                                            @case('completed')
                                                <span class="badge badge-status bg-success">✅ Completed</span>
                                                @break
                                            @case('cancelled')
                                                <span class="badge badge-status bg-danger">❌ Cancelled</span>
                                                @break
                                        @endswitch
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('bookings.show', $booking) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3" style="font-size:0.78rem">
                                        <i class="bi bi-eye me-1"></i>Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="tbl-empty">
                                        <i class="bi bi-inbox d-block"></i>
                                        <p class="mb-0">Belum ada booking</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const widget = document.getElementById('dashboardCalendarWidget');
        if (!widget) return;

        const apiUrl = widget.dataset.apiUrl;
        if (!apiUrl) return;

        fetch(apiUrl)
            .then(response => response.json())
            .then(payload => {
                const occEl = document.getElementById('dashboardOccupancyValue');
                const revEl = document.getElementById('dashboardRevenueValue');
                if (occEl && payload.average_occupancy !== undefined) {
                    occEl.textContent = payload.average_occupancy + '%';
                }
                if (revEl && payload.forecast_revenue !== undefined) {
                    revEl.textContent = 'Rp' + Number(payload.forecast_revenue).toLocaleString('id-ID');
                }

                const snapshot = payload.vehicles ? payload.vehicles.slice(0, 4).map((vehicle) => `
                    <div class="calendar-widget-item">
                        <div class="top">
                            <span>${vehicle.vehicle_name}</span>
                            <span>${vehicle.occupancy_rate}%</span>
                        </div>
                        <div class="calendar-widget-bar"><span style="width:${vehicle.occupancy_rate}%"></span></div>
                        <small class="text-muted d-block mt-2">Kosong lagi: ${vehicle.next_available_date ? new Date(`${vehicle.next_available_date}T00:00:00`).toLocaleDateString('id-ID') : 'Belum ada saran'}</small>
                    </div>
                `).join('') : '';

                document.getElementById('dashboardFleetSnapshot').innerHTML = snapshot || '<div class="text-muted small">Belum ada data armada untuk periode ini.</div>';
            })
            .catch((error) => {
                document.getElementById('dashboardFleetSnapshot').innerHTML = `<div class="text-danger small">${error.message}</div>`;
            });
    });
</script>
@endsection
