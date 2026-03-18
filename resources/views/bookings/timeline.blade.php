@extends('layouts.admin')

@section('title', 'Timeline Booking Mingguan')
@section('page-title', 'Timeline Booking')

@section('content')
@php
    $timelineQuery = array_filter([
        'vehicle_type' => $vehicleType,
        'search' => $search,
        'problem_only' => $problemOnly ? 1 : null,
    ], fn ($value) => $value !== null && $value !== '');
@endphp

<style>
    .timeline-header {
        background: radial-gradient(circle at top right, rgba(255,255,255,0.16), transparent 34%), var(--gradient-brand);
        color: white;
        padding: 3rem 0 2.5rem;
        margin-bottom: 1.5rem;
        border-radius: 0 0 2rem 2rem;
    }
    .timeline-header-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 1.25rem;
        flex-wrap: wrap;
    }
    .timeline-header .eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        text-transform: uppercase;
        font-size: 0.78rem;
        letter-spacing: 0.14em;
        font-weight: 700;
        margin-bottom: 0.6rem;
        opacity: 0.84;
    }
    .timeline-header h1 {
        margin: 0 0 0.55rem;
        font-weight: 700;
        letter-spacing: -0.05em;
    }
    .timeline-header p {
        max-width: 44rem;
        margin: 0;
        opacity: 0.82;
        line-height: 1.7;
    }
    .timeline-header-actions {
        display: flex;
        gap: 0.7rem;
        flex-wrap: wrap;
    }
    .timeline-week-nav {
        margin-top: 1.35rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        flex-wrap: wrap;
    }
    .timeline-week-nav .range {
        display: inline-flex;
        align-items: center;
        gap: 0.65rem;
        padding: 0.8rem 1rem;
        border-radius: 999px;
        background: rgba(255,255,255,0.12);
        border: 1px solid rgba(255,255,255,0.18);
        font-weight: 600;
    }
    .timeline-filters,
    .timeline-legend,
    .timeline-board,
    .timeline-empty,
    .timeline-stats .stat-card {
        background: white;
        border: 1px solid rgba(203,213,225,0.72);
        box-shadow: var(--shadow-soft);
    }
    .timeline-filters {
        display: grid;
        grid-template-columns: 1.5fr 1fr;
        gap: 1rem;
        padding: 1.25rem;
        border-radius: 1.2rem;
        margin-bottom: 1rem;
    }
    .timeline-filters > .timeline-field:first-of-type {
        grid-column: 1;
    }
    .timeline-field label {
        display: block;
        font-weight: 600;
        color: #0f172a;
        margin-bottom: 0.45rem;
        font-size: 0.92rem;
    }
    .timeline-field .helper {
        display: block;
        margin-top: 0.45rem;
        font-size: 0.78rem;
        color: #64748b;
    }
    .timeline-filters .form-control,
    .timeline-filters .form-select {
        border-radius: 0.9rem;
        border: 1px solid rgba(203,213,225,0.92);
        min-height: 48px;
    }
    .timeline-problem-toggle {
        display: flex;
        align-items: center;
        gap: 0.7rem;
        align-self: end;
        padding: 0.9rem 1rem;
        border-radius: 1rem;
        border: 1px solid rgba(226,232,240,0.95);
        background: #f8fafc;
        color: #0f172a;
        font-weight: 600;
        min-height: 48px;
        white-space: nowrap;
        font-size: 0.9rem;
    }
    .timeline-filter-actions {
        display: flex;
        gap: 0.65rem;
        align-items: end;
        justify-content: flex-end;
    }
    .timeline-filter-actions .btn {
        min-height: 48px;
        border-radius: 999px;
        padding-inline: 1.2rem;
        font-weight: 600;
    }
    .timeline-stats {
        display: grid;
        gap: 0.8rem;
        grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
        margin-bottom: 1rem;
    }
    .timeline-stats .stat-card {
        border-radius: 1rem;
        padding: 1rem;
    }
    .timeline-stats .eyebrow {
        color: #64748b;
        font-size: 0.78rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        margin-bottom: 0.35rem;
    }
    .timeline-stats .value {
        color: #0f172a;
        font-size: 1.7rem;
        line-height: 1;
        font-weight: 700;
        letter-spacing: -0.05em;
    }
    .timeline-stats .helper {
        color: #64748b;
        margin-top: 0.3rem;
        font-size: 0.85rem;
    }
    .timeline-legend {
        padding: 0.95rem 1rem;
        border-radius: 1rem;
        margin-bottom: 1rem;
        display: flex;
        justify-content: space-between;
        gap: 1rem;
        align-items: flex-start;
        flex-wrap: wrap;
    }
    .timeline-legend h2 {
        margin: 0;
        font-size: 1rem;
        font-weight: 700;
        color: #0f172a;
    }
    .timeline-legend p {
        margin: 0.25rem 0 0;
        color: #64748b;
        font-size: 0.88rem;
        line-height: 1.6;
    }
    .legend-items {
        display: flex;
        flex-wrap: wrap;
        gap: 0.55rem;
    }
    .legend-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.52rem 0.78rem;
        border-radius: 999px;
        font-weight: 600;
        font-size: 0.78rem;
    }
    .legend-pill.active {
        background: #dcfce7;
        color: #166534;
    }
    .legend-pill.scheduled {
        background: #dbeafe;
        color: #1d4ed8;
    }
    .legend-pill.maintenance-hold {
        background: #fee2e2;
        color: #991b1b;
    }
    .legend-pill.awaiting-return {
        background: #fef3c7;
        color: #92400e;
    }
    .legend-pill.waiting-list {
        background: #ffedd5;
        color: #9a3412;
    }
    .legend-pill.awaiting-proof {
        background: #ede9fe;
        color: #6d28d9;
    }
    .legend-pill.overdue-payment {
        background: #fee2e2;
        color: #b91c1c;
    }
    .legend-dot {
        width: 0.75rem;
        height: 0.75rem;
        border-radius: 999px;
        background: currentColor;
    }
    .timeline-board {
        border-radius: 1.25rem;
        padding: 1rem;
        overflow-x: hidden;
    }
    .timeline-grid {
        width: 100%;
        min-width: 0;
        display: grid;
        gap: 0.85rem;
    }
    .timeline-grid-header,
    .timeline-row {
        display: grid;
        grid-template-columns: clamp(205px, 19vw, 245px) minmax(0, 1fr);
        gap: 0.85rem;
    }
    .timeline-grid-header {
        position: sticky;
        top: 0;
        z-index: 2;
    }
    .timeline-grid-header .vehicle-col,
    .timeline-grid-header .day-grid,
    .timeline-row .vehicle-col,
    .timeline-row .track-col {
        border-radius: 1rem;
        border: 1px solid rgba(226,232,240,0.92);
    }
    .timeline-grid-header .vehicle-col {
        padding: 1rem;
        background: #f8fafc;
        color: #334155;
        font-weight: 700;
    }
    .day-grid {
        display: grid;
        grid-template-columns: repeat(7, minmax(0, 1fr));
        gap: 0.45rem;
        padding: 0.6rem;
        background: #f8fafc;
    }
    .day-head {
        min-height: 68px;
        padding: 0.4rem 0.3rem;
        border-radius: 0.9rem;
        background: white;
        border: 1px solid rgba(226,232,240,0.95);
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        text-align: center;
    }
    .day-head .name {
        font-size: 0.72rem;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
    }
    .day-head .date {
        font-size: 1.1rem;
        font-weight: 700;
        color: #0f172a;
        letter-spacing: -0.04em;
    }
    .day-head.is-today {
        background: linear-gradient(135deg, rgba(224,242,254,0.96) 0%, rgba(240,249,255,0.98) 100%);
        border-color: rgba(14,165,233,0.35);
    }
    .timeline-row .vehicle-col {
        background: linear-gradient(135deg, rgba(255,255,255,0.98) 0%, rgba(248,250,252,0.98) 100%);
        padding: 0.95rem;
    }
    .timeline-row.has-attention .vehicle-col,
    .timeline-row.has-attention .track-col {
        border-color: rgba(245, 158, 11, 0.42);
    }
    .vehicle-title {
        font-weight: 700;
        color: #0f172a;
        font-size: 0.98rem;
        line-height: 1.45;
        overflow-wrap: anywhere;
    }
    .vehicle-meta {
        margin-top: 0.3rem;
        color: #64748b;
        font-size: 0.8rem;
        overflow-wrap: anywhere;
    }
    .vehicle-pills {
        display: flex;
        flex-wrap: wrap;
        gap: 0.4rem;
        margin-top: 0.8rem;
    }
    .vehicle-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.4rem 0.62rem;
        border-radius: 999px;
        font-size: 0.72rem;
        font-weight: 700;
        border: 1px solid transparent;
    }
    .vehicle-pill.normal {
        background: rgba(14,165,233,0.1);
        color: #0f766e;
        border-color: rgba(14,165,233,0.18);
    }
    .vehicle-pill.attention {
        background: rgba(245,158,11,0.14);
        color: #92400e;
        border-color: rgba(245,158,11,0.22);
    }
    .vehicle-pill.maintenance {
        background: rgba(239,68,68,0.12);
        color: #b91c1c;
        border-color: rgba(239,68,68,0.22);
    }
    .track-col {
        padding: 0.6rem;
        background:
            repeating-linear-gradient(
                to right,
                rgba(248,250,252,0.9) 0,
                rgba(248,250,252,0.9) calc(14.285% - 1px),
                rgba(226,232,240,0.8) calc(14.285% - 1px),
                rgba(226,232,240,0.8) 14.285%
            );
    }
    .track-lane {
        display: grid;
        grid-template-columns: repeat(7, minmax(0, 1fr));
        gap: 0.45rem;
        min-height: 82px;
    }
    .track-lane + .track-lane {
        margin-top: 0.5rem;
    }
    .track-empty {
        min-height: 82px;
        border: 1px dashed rgba(148,163,184,0.5);
        border-radius: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.55rem;
        color: #94a3b8;
        background: rgba(255,255,255,0.76);
        font-weight: 600;
        text-align: center;
        padding: 0.75rem;
        font-size: 0.84rem;
    }
    .booking-chip {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-height: 82px;
        padding: 0.72rem 0.76rem;
        border-radius: 1rem;
        text-decoration: none;
        border: 1px solid transparent;
        box-shadow: 0 14px 26px rgba(15,23,42,0.08);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .booking-chip:hover {
        transform: translateY(-1px);
        box-shadow: 0 18px 30px rgba(15,23,42,0.14);
    }
    .booking-chip .chip-status {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        font-size: 0.65rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        margin-bottom: 0.32rem;
    }
    .booking-chip .chip-title {
        font-size: 0.86rem;
        font-weight: 700;
        line-height: 1.35;
        margin-bottom: 0.25rem;
        overflow-wrap: anywhere;
    }
    .booking-chip .chip-meta {
        font-size: 0.7rem;
        line-height: 1.4;
        opacity: 0.9;
    }
    .booking-chip.active {
        background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
        color: #166534;
        border-color: rgba(34,197,94,0.2);
    }
    .booking-chip.scheduled {
        background: linear-gradient(135deg, #e0f2fe 0%, #bfdbfe 100%);
        color: #1d4ed8;
        border-color: rgba(59,130,246,0.2);
    }
    .booking-chip.maintenance-hold {
        background: linear-gradient(135deg, #fee2e2 0%, #fca5a5 100%);
        color: #991b1b;
        border-color: rgba(239,68,68,0.26);
    }
    .booking-chip.awaiting-return {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        color: #92400e;
        border-color: rgba(245,158,11,0.25);
    }
    .booking-chip.waiting-list {
        background: linear-gradient(135deg, #ffedd5 0%, #fdba74 100%);
        color: #9a3412;
        border-color: rgba(249,115,22,0.24);
    }
    .booking-chip.awaiting-proof {
        background: linear-gradient(135deg, #ede9fe 0%, #ddd6fe 100%);
        color: #6d28d9;
        border-color: rgba(139,92,246,0.22);
    }
    .booking-chip.overdue-payment {
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        color: #b91c1c;
        border-color: rgba(239,68,68,0.24);
    }
    .booking-chip.pending {
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        color: #334155;
        border-color: rgba(148,163,184,0.24);
    }
    .timeline-empty {
        border-radius: 1.2rem;
        padding: 2.2rem;
        text-align: center;
        color: #475569;
    }
    .timeline-empty i {
        font-size: 2.2rem;
        color: #94a3b8;
        display: inline-flex;
        margin-bottom: 0.75rem;
    }
    .timeline-empty h3 {
        font-size: 1.2rem;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 0.45rem;
    }
    .timeline-empty p {
        max-width: 34rem;
        margin: 0 auto 1rem;
        line-height: 1.7;
        color: #64748b;
    }
    .timeline-modal[hidden] {
        display: none !important;
    }
    .timeline-modal {
        position: fixed;
        inset: 0;
        z-index: 1055;
        background: rgba(15, 23, 42, 0.55);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1.25rem;
        backdrop-filter: blur(6px);
    }
    .timeline-modal-panel {
        width: min(720px, 100%);
        max-height: calc(100vh - 2.5rem);
        overflow: auto;
        background: rgba(255,255,255,0.98);
        border: 1px solid rgba(203,213,225,0.82);
        border-radius: 1.4rem;
        box-shadow: 0 24px 60px rgba(15,23,42,0.26);
    }
    .timeline-modal-header {
        display: flex;
        justify-content: space-between;
        gap: 1rem;
        align-items: flex-start;
        padding: 1.25rem 1.25rem 1rem;
        border-bottom: 1px solid rgba(226,232,240,0.92);
    }
    .timeline-modal-header h3 {
        margin: 0 0 0.3rem;
        font-size: 1.2rem;
        font-weight: 700;
        color: #0f172a;
    }
    .timeline-modal-subtitle {
        color: #64748b;
        font-size: 0.9rem;
        line-height: 1.6;
    }
    .timeline-modal-close {
        flex-shrink: 0;
        width: 42px;
        height: 42px;
        border-radius: 999px;
        border: 1px solid rgba(203,213,225,0.92);
        background: #f8fafc;
        color: #0f172a;
    }
    .timeline-modal-body {
        padding: 1.25rem;
    }
    .timeline-modal-status {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.52rem 0.82rem;
        border-radius: 999px;
        font-weight: 700;
        font-size: 0.78rem;
        margin-bottom: 0.95rem;
    }
    .timeline-modal-status.active { background: #dcfce7; color: #166534; }
    .timeline-modal-status.scheduled { background: #dbeafe; color: #1d4ed8; }
    .timeline-modal-status.maintenance-hold { background: #fee2e2; color: #991b1b; }
    .timeline-modal-status.awaiting-return { background: #fef3c7; color: #92400e; }
    .timeline-modal-status.waiting-list { background: #ffedd5; color: #9a3412; }
    .timeline-modal-status.awaiting-proof { background: #ede9fe; color: #6d28d9; }
    .timeline-modal-status.overdue-payment { background: #fee2e2; color: #b91c1c; }
    .timeline-modal-status.pending { background: #e2e8f0; color: #334155; }
    .timeline-modal-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 0.8rem;
        margin-bottom: 1rem;
    }
    .timeline-modal-card {
        padding: 0.95rem 1rem;
        border-radius: 1rem;
        background: #f8fafc;
        border: 1px solid rgba(226,232,240,0.95);
    }
    .timeline-modal-card .label {
        color: #64748b;
        font-size: 0.78rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        margin-bottom: 0.3rem;
    }
    .timeline-modal-card .value {
        color: #0f172a;
        font-weight: 700;
        line-height: 1.5;
    }
    .timeline-modal-note {
        padding: 1rem;
        border-radius: 1rem;
        background: linear-gradient(135deg, rgba(248,250,252,0.98) 0%, rgba(241,245,249,0.98) 100%);
        border: 1px solid rgba(226,232,240,0.92);
        color: #334155;
        line-height: 1.7;
        white-space: pre-line;
    }
    .timeline-modal-actions {
        display: flex;
        justify-content: flex-end;
        gap: 0.7rem;
        flex-wrap: wrap;
        padding: 1rem 1.25rem 1.25rem;
        border-top: 1px solid rgba(226,232,240,0.92);
    }
    .timeline-modal-actions .btn {
        border-radius: 999px;
        font-weight: 600;
    }
    @media (max-width: 1199.98px) {
        .timeline-grid-header,
        .timeline-row {
            grid-template-columns: 1fr;
        }
        .timeline-grid-header .vehicle-col {
            display: none;
        }
        .timeline-filters {
            grid-template-columns: 1fr;
        }
        .timeline-filter-actions {
            justify-content: flex-start;
        }
        .timeline-modal-grid {
            grid-template-columns: 1fr;
        }
    }
    @media (max-width: 767.98px) {
        .timeline-header {
            padding: 2.35rem 0 2rem;
        }
        .timeline-week-nav {
            flex-direction: column;
            align-items: stretch;
        }
        .timeline-week-nav .range,
        .timeline-week-nav .btn {
            width: 100%;
            justify-content: center;
        }
        .timeline-filter-actions {
            width: 100%;
        }
        .timeline-filter-actions .btn {
            flex: 1 1 100%;
        }
        .day-grid,
        .track-lane {
            gap: 0.3rem;
        }
        .day-head {
            min-height: 60px;
            padding: 0.35rem 0.2rem;
        }
        .day-head .name {
            font-size: 0.66rem;
        }
        .day-head .date {
            font-size: 0.96rem;
        }
        .booking-chip {
            min-height: 76px;
            padding: 0.65rem;
        }
    }
</style>

<div class="timeline-header">
    <div class="container">
        <div class="timeline-header-top">
            <div>
                <span class="eyebrow"><i class="bi bi-kanban"></i>Planner Operasional</span>
                <h1><i class="bi bi-calendar3 me-2"></i>Timeline Booking Mingguan</h1>
                <p>Pantau kendaraan per minggu, lihat booking aktif, terjadwal, antrean, dan unit yang ketahan tanpa harus bongkar tabel satu-satu.</p>
            </div>
            <div class="timeline-header-actions">
                <a href="{{ route('admin.bookings.index') }}" class="btn btn-light rounded-pill px-4">
                    <i class="bi bi-list-ul me-2"></i>Daftar Booking
                </a>
            </div>
        </div>

        <div class="timeline-week-nav">
            <a href="{{ route('admin.bookings.timeline', array_merge($timelineQuery, ['week' => $previousWeek->toDateString()])) }}" class="btn btn-outline-light rounded-pill px-4">
                <i class="bi bi-arrow-left me-2"></i>Minggu Sebelumnya
            </a>
            <div class="range">
                <i class="bi bi-calendar-week"></i>
                {{ $weekStart->format('d M') }} - {{ $weekEnd->format('d M Y') }}
            </div>
            <a href="{{ route('admin.bookings.timeline', array_merge($timelineQuery, ['week' => $nextWeek->toDateString()])) }}" class="btn btn-outline-light rounded-pill px-4">
                Minggu Berikutnya<i class="bi bi-arrow-right ms-2"></i>
            </a>
        </div>
    </div>
</div>

<div class="container pb-5">
    <form method="GET" action="{{ route('admin.bookings.timeline') }}" class="timeline-filters">
        <input type="hidden" name="week" value="{{ $weekStart->toDateString() }}">

        <div class="timeline-field">
            <label for="timeline-search">Cari kendaraan atau customer</label>
            <input type="text" id="timeline-search" name="search" class="form-control" value="{{ $search }}" placeholder="Contoh: Avanza atau Budi">
            <span class="helper">Search akan mencocokkan nama kendaraan, plat nomor, dan nama customer pada booking minggu ini.</span>
        </div>

        <div class="timeline-field">
            <label for="timeline-vehicle-type">Jenis kendaraan</label>
            <select id="timeline-vehicle-type" name="vehicle_type" class="form-select">
                <option value="">Semua kendaraan</option>
                <option value="mobil" @selected($vehicleType === 'mobil')>Mobil</option>
                <option value="motor" @selected($vehicleType === 'motor')>Motor</option>
            </select>
        </div>

        <label class="timeline-problem-toggle">
            <input type="checkbox" name="problem_only" value="1" @checked($problemOnly)>
            <span>Hanya tampilkan yang perlu perhatian</span>
        </label>

        <div class="timeline-filter-actions">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-funnel me-2"></i>Terapkan
            </button>
            <a href="{{ route('admin.bookings.timeline', ['week' => $weekStart->toDateString()]) }}" class="btn btn-outline-secondary">
                Reset
            </a>
        </div>
    </form>

    <div class="timeline-stats">
        <div class="stat-card">
            <div class="eyebrow">Kendaraan Tampil</div>
            <div class="value">{{ $summary['vehicles'] }}</div>
            <div class="helper">Unit yang masuk board sesuai filter aktif.</div>
        </div>
        <div class="stat-card">
            <div class="eyebrow">Booking Minggu Ini</div>
            <div class="value">{{ $summary['bookings'] }}</div>
            <div class="helper">Semua event booking yang terlihat di timeline.</div>
        </div>
        <div class="stat-card">
            <div class="eyebrow">Perlu Perhatian</div>
            <div class="value">{{ $summary['attention'] }}</div>
            <div class="helper">Unit dengan antrean, unit tertahan, atau status bermasalah.</div>
        </div>
        <div class="stat-card">
            <div class="eyebrow">Kosong Minggu Ini</div>
            <div class="value">{{ $summary['free'] }}</div>
            <div class="helper">Bisa dipakai untuk planning unit yang masih longgar.</div>
        </div>
    </div>

    <div class="timeline-legend">
        <div>
            <h2>Arti Warna Timeline</h2>
            <p>Warna blok membantu admin bedakan booking aman dengan unit yang butuh tindakan operasional lebih cepat.</p>
        </div>
        <div class="legend-items">
            @foreach($legend as $item)
                <span class="legend-pill {{ $item['class'] }}">
                    <span class="legend-dot"></span>{{ $item['label'] }}
                </span>
            @endforeach
        </div>
    </div>

    @if($timelineVehicles->isNotEmpty())
        <div class="timeline-board">
            <div class="timeline-grid">
                <div class="timeline-grid-header">
                    <div class="vehicle-col">Kendaraan</div>
                    <div class="day-grid">
                        @foreach($weekDays as $day)
                            <div class="day-head {{ $day->isToday() ? 'is-today' : '' }}">
                                <span class="name">{{ $day->translatedFormat('D') }}</span>
                                <span class="date">{{ $day->format('d') }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                @foreach($timelineVehicles as $row)
                    <div class="timeline-row {{ $row['has_attention'] ? 'has-attention' : '' }}">
                        <div class="vehicle-col">
                            <div class="vehicle-title">{{ $row['vehicle']->getTypeIcon() }} {{ $row['vehicle']->name }}</div>
                            <div class="vehicle-meta">{{ $row['vehicle']->plat_number }} · {{ $row['vehicle']->transmission }} · {{ $row['vehicle']->year }}</div>
                            <div class="vehicle-pills">
                                <span class="vehicle-pill {{ $row['state_class'] }}">{{ $row['state_label'] }}</span>
                                <span class="vehicle-pill normal">{{ $row['booking_count'] }} booking</span>
                                @if($row['problem_count'] > 0)
                                    <span class="vehicle-pill attention">{{ $row['problem_count'] }} perlu tindakan</span>
                                @endif
                            </div>
                        </div>

                        <div class="track-col">
                            @forelse($row['lanes'] as $lane)
                                <div class="track-lane">
                                    @foreach($lane as $event)
                                        <a
                                            href="{{ $event['detail_url'] }}"
                                            class="booking-chip {{ $event['class'] }} js-timeline-chip"
                                            style="grid-column: {{ $event['grid_start'] }} / span {{ $event['span'] }};"
                                            data-booking-class="{{ $event['class'] }}"
                                            data-booking-payload="{{ base64_encode(json_encode($event['modal_payload'], JSON_UNESCAPED_UNICODE)) }}"
                                        >
                                            <span>
                                                <span class="chip-status">{{ $event['label'] }}</span>
                                                <span class="chip-title">{{ $event['customer_name'] }}</span>
                                            </span>
                                            <span class="chip-meta">
                                                Booking #{{ $event['booking']->id }} · {{ $event['time_label'] }}<br>
                                                {{ $event['starts_before_week'] ? '← ' : '' }}{{ $event['date_label'] }}{{ $event['ends_after_week'] ? ' →' : '' }}
                                            </span>
                                        </a>
                                    @endforeach
                                </div>
                            @empty
                                <div class="track-empty">
                                    <i class="bi bi-plus-circle-dotted"></i>Tidak ada booking di minggu ini
                                </div>
                            @endforelse
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <div class="timeline-empty">
            <i class="bi bi-calendar-x"></i>
            <h3>Tidak ada data timeline yang cocok</h3>
            <p>Filter aktif terlalu ketat atau belum ada booking pada minggu ini. Coba ganti minggu, hapus filter, atau tampilkan semua kendaraan.</p>
            <a href="{{ route('admin.bookings.timeline', ['week' => $weekStart->toDateString()]) }}" class="btn btn-outline-primary rounded-pill px-4">
                Lihat Semua Timeline
            </a>
        </div>
    @endif

    <div class="timeline-modal" id="timelineQuickView" hidden>
        <div class="timeline-modal-panel" role="dialog" aria-modal="true" aria-labelledby="timelineQuickViewTitle">
            <div class="timeline-modal-header">
                <div>
                    <h3 id="timelineQuickViewTitle">Timeline Ringkas Booking</h3>
                    <div class="timeline-modal-subtitle" id="timelineQuickViewSubtitle">Lihat ringkasan booking tanpa keluar dari board.</div>
                </div>
                <button type="button" class="timeline-modal-close" data-modal-close aria-label="Tutup modal">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <div class="timeline-modal-body">
                <div class="timeline-modal-status pending" id="timelineQuickViewStatus">Status</div>
                <p class="timeline-modal-subtitle mb-3" id="timelineQuickViewStatusDescription"></p>

                <div class="timeline-modal-grid">
                    <div class="timeline-modal-card">
                        <div class="label">Pelanggan</div>
                        <div class="value" id="timelineQuickViewCustomer">-</div>
                    </div>
                    <div class="timeline-modal-card">
                        <div class="label">Kendaraan</div>
                        <div class="value" id="timelineQuickViewVehicle">-</div>
                    </div>
                    <div class="timeline-modal-card">
                        <div class="label">Jadwal</div>
                        <div class="value" id="timelineQuickViewSchedule">-</div>
                    </div>
                    <div class="timeline-modal-card">
                        <div class="label">Pembayaran</div>
                        <div class="value" id="timelineQuickViewPayment">-</div>
                    </div>
                    <div class="timeline-modal-card">
                        <div class="label">Durasi</div>
                        <div class="value" id="timelineQuickViewDuration">-</div>
                    </div>
                    <div class="timeline-modal-card">
                        <div class="label">Total</div>
                        <div class="value" id="timelineQuickViewTotal">-</div>
                    </div>
                </div>

                <div class="timeline-modal-note" id="timelineQuickViewNotes">Tidak ada catatan tambahan.</div>
            </div>

            <div class="timeline-modal-actions">
                <a href="#" class="btn btn-outline-secondary" id="timelineQuickViewDetail">Buka Detail Booking</a>
                <a href="#" class="btn btn-primary" id="timelineQuickViewAction" hidden>
                    <i class="bi bi-arrow-right-circle me-2"></i><span id="timelineQuickViewActionLabel">Lanjut</span>
                </a>
            </div>
        </div>
    </div>
</div>

@section('js')
<script>
    (() => {
        const modal = document.getElementById('timelineQuickView');
        const panel = modal?.querySelector('.timeline-modal-panel');
        const detailButton = document.getElementById('timelineQuickViewDetail');
        const actionButton = document.getElementById('timelineQuickViewAction');
        const actionLabel = document.getElementById('timelineQuickViewActionLabel');
        const statusBadge = document.getElementById('timelineQuickViewStatus');
        const statusDescription = document.getElementById('timelineQuickViewStatusDescription');
        const customer = document.getElementById('timelineQuickViewCustomer');
        const vehicle = document.getElementById('timelineQuickViewVehicle');
        const schedule = document.getElementById('timelineQuickViewSchedule');
        const payment = document.getElementById('timelineQuickViewPayment');
        const duration = document.getElementById('timelineQuickViewDuration');
        const total = document.getElementById('timelineQuickViewTotal');
        const notes = document.getElementById('timelineQuickViewNotes');

        if (!modal || !panel) {
            return;
        }

        const openModal = (payload, variantClass) => {
            statusBadge.className = 'timeline-modal-status ' + variantClass;
            statusBadge.textContent = payload.status_label;
            statusDescription.textContent = payload.status_description;
            customer.textContent = payload.customer_name + ' · ' + payload.customer_email;
            vehicle.textContent = payload.vehicle_name + ' · ' + payload.vehicle_plate + ' · ' + payload.vehicle_type + ' · ' + payload.vehicle_state;
            schedule.textContent = payload.schedule_label;
            payment.textContent = payload.payment_method + ' · ' + payload.payment_status;
            duration.textContent = payload.duration_label;
            total.textContent = payload.total_label;
            notes.textContent = payload.notes && payload.notes.trim() !== '' ? payload.notes : 'Tidak ada catatan tambahan.';
            detailButton.href = payload.detail_url;

            if (payload.quick_action_label && payload.quick_action_url) {
                actionButton.hidden = false;
                actionButton.href = payload.quick_action_url;
                actionLabel.textContent = payload.quick_action_label;
            } else {
                actionButton.hidden = true;
                actionButton.href = '#';
                actionLabel.textContent = 'Lanjut';
            }

            modal.hidden = false;
            document.body.style.overflow = 'hidden';
        };

        const closeModal = () => {
            modal.hidden = true;
            document.body.style.overflow = '';
        };

        document.querySelectorAll('.js-timeline-chip').forEach((chip) => {
            chip.addEventListener('click', (event) => {
                if (event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
                    return;
                }

                const encoded = chip.dataset.bookingPayload;
                if (!encoded) {
                    return;
                }

                event.preventDefault();

                try {
                    const payload = JSON.parse(atob(encoded));
                    openModal(payload, chip.dataset.bookingClass || 'pending');
                } catch (error) {
                    window.location.href = chip.href;
                }
            });
        });

        modal.addEventListener('click', (event) => {
            if (event.target === modal) {
                closeModal();
            }
        });

        modal.querySelectorAll('[data-modal-close]').forEach((button) => {
            button.addEventListener('click', closeModal);
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && !modal.hidden) {
                closeModal();
            }
        });
    })();
</script>
@endsection
@endsection