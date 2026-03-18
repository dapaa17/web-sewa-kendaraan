@php($usesAdminLayout = (bool) auth()->user()?->isAdmin())
@extends($usesAdminLayout ? 'layouts.admin' : 'layouts.app')

@section('title', $vehicle->name . ' - Kalender Ketersediaan')
@if($usesAdminLayout)
@section('page-title', 'Kalender Kendaraan')
@endif

@section('css')
<style>
    .vhc-hero {
        background: radial-gradient(circle at top right, rgba(255,255,255,0.16), transparent 36%), var(--gradient-brand);
        color: #fff;
        padding: 3rem 0 5.5rem;
        position: relative;
        overflow: hidden;
    }
    .vhc-shell {
        margin-top: -3.5rem;
        position: relative;
        z-index: 2;
        padding-bottom: 3rem;
    }
    .vhc-back {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        text-decoration: none;
        color: rgba(255,255,255,0.78);
        margin-bottom: 1rem;
    }
    .vhc-back:hover {
        color: #fff;
    }
    .vhc-hero h1 {
        font-weight: 800;
        font-size: clamp(2rem, 4vw, 3rem);
        margin-bottom: 0.85rem;
    }
    .vhc-hero-copy {
        max-width: 42rem;
        color: rgba(255,255,255,0.82);
    }
    .vhc-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 0.8rem;
        margin-top: 1.35rem;
    }
    .vhc-meta-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.55rem;
        padding: 0.75rem 1rem;
        border-radius: 999px;
        background: rgba(255,255,255,0.12);
        border: 1px solid rgba(255,255,255,0.16);
        font-weight: 600;
    }
    .vhc-panel {
        background: rgba(255,255,255,0.96);
        border: 1px solid rgba(203,213,225,0.72);
        border-radius: 1.45rem;
        box-shadow: var(--shadow-card);
        padding: 1.4rem;
    }
    .vhc-panel + .vhc-panel {
        margin-top: 1rem;
    }
    .vhc-panel-head {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 1rem;
        margin-bottom: 1rem;
    }
    .vhc-panel-head h2,
    .vhc-panel-head h3 {
        font-size: 1.15rem;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 0.2rem;
    }
    .vhc-panel-head p {
        margin: 0;
        color: #64748b;
        font-size: 0.9rem;
    }
    .vhc-calendar-toolbar {
        display: flex;
        gap: 0.6rem;
        flex-wrap: wrap;
    }
    .vhc-legend {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
        margin-bottom: 1rem;
    }
    .vhc-legend span {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        color: #475569;
        font-size: 0.86rem;
        font-weight: 600;
    }
    .vhc-dot {
        width: 0.8rem;
        height: 0.8rem;
        border-radius: 999px;
        display: inline-block;
    }
    .vhc-dot.available { background: #bbf7d0; }
    .vhc-dot.booked { background: #fecaca; }
    .vhc-dot.maintenance { background: #cbd5e1; }
    .vhc-dot.selected { background: var(--color-secondary); }
    .vhc-calendars {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    .vhc-calendar-card {
        border: 1px solid rgba(203,213,225,0.72);
        border-radius: 1.1rem;
        background: #fff;
        overflow: hidden;
    }
    .vhc-calendar-card-head {
        padding: 0.9rem 1rem;
        border-bottom: 1px solid rgba(226,232,240,0.9);
        background: linear-gradient(135deg, rgba(31, 41, 55, 0.04) 0%, rgba(6, 182, 212, 0.08) 100%);
        font-weight: 700;
        color: #0f172a;
    }
    .vhc-calendar {
        padding: 0.75rem;
        overflow: hidden;
    }
    .vhc-calendar .fc {
        --fc-border-color: rgba(226,232,240,0.72);
        --fc-page-bg-color: transparent;
        --fc-neutral-bg-color: #f8fafc;
        --fc-today-bg-color: rgba(6,182,212,0.08);
    }
    .vhc-calendar .fc .fc-toolbar { display: none; }
    .vhc-calendar .fc-scrollgrid,
    .vhc-calendar .fc-scrollgrid table {
        width: 100%;
        table-layout: fixed;
    }
    .vhc-calendar .fc-col-header-cell {
        background: #f8fafc;
    }
    .vhc-calendar .fc-col-header-cell-cushion {
        color: #64748b;
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        text-decoration: none;
        padding: 0.55rem 0.1rem;
        letter-spacing: 0.04em;
    }
    .vhc-calendar .fc-daygrid-day-number {
        color: #0f172a;
        font-weight: 700;
        font-size: 0.82rem;
        text-decoration: none;
    }
    .vhc-calendar .fc-daygrid-day-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding: 0.45rem 0.5rem 0;
    }
    .vhc-calendar .fc-daygrid-day-frame {
        min-height: 104px;
        position: relative;
        padding-bottom: 1.55rem;
    }
    .vhc-calendar .fc-day-other .fc-daygrid-day-number {
        opacity: 0.38;
    }
    .vhc-calendar .fc-daygrid-day.rh-day-available {
        background: linear-gradient(180deg, rgba(220,252,231,0.72) 0%, rgba(255,255,255,0.98) 56%);
    }
    .vhc-calendar .fc-daygrid-day.rh-day-booked {
        background: linear-gradient(180deg, rgba(254,226,226,0.82) 0%, rgba(255,255,255,0.98) 58%);
    }
    .vhc-calendar .fc-daygrid-day.rh-day-maintenance {
        background: linear-gradient(180deg, rgba(226,232,240,0.9) 0%, rgba(255,255,255,0.98) 58%);
    }
    .vhc-calendar .fc-daygrid-day.rh-day-selected,
    .vhc-calendar .fc-daygrid-day.rh-day-selected-start,
    .vhc-calendar .fc-daygrid-day.rh-day-selected-end {
        box-shadow: inset 0 0 0 2px rgba(6,182,212,0.2);
    }
    .vhc-calendar .fc-daygrid-day.rh-day-selected {
        background: linear-gradient(180deg, rgba(165,243,252,0.54) 0%, rgba(255,255,255,0.98) 56%);
    }
    .vhc-calendar .fc-daygrid-day.rh-day-selected-start,
    .vhc-calendar .fc-daygrid-day.rh-day-selected-end {
        background: linear-gradient(180deg, rgba(34,211,238,0.22) 0%, rgba(255,255,255,0.98) 56%);
    }
    .vhc-calendar .fc-daygrid-day.rh-day-selected-start .fc-daygrid-day-number,
    .vhc-calendar .fc-daygrid-day.rh-day-selected-end .fc-daygrid-day-number {
        color: var(--color-secondary-strong);
    }
    .vhc-price-badge {
        position: absolute;
        left: 0.5rem;
        right: 0.5rem;
        bottom: 0.45rem;
        display: block;
        overflow: hidden;
        text-overflow: ellipsis;
        font-size: 0.68rem;
        line-height: 1.1;
        font-weight: 600;
        color: #64748b;
        background: transparent;
        border: 0;
        border-radius: 0;
        padding: 0;
        white-space: nowrap;
        box-shadow: none;
        pointer-events: none;
        text-align: left;
    }
    .vhc-calendar .fc-daygrid-day.rh-day-booked .vhc-price-badge,
    .vhc-calendar .fc-daygrid-day.rh-day-maintenance .vhc-price-badge {
        display: none;
    }
    .vhc-calendar .fc-daygrid-day.fc-day-today .fc-daygrid-day-number {
        color: var(--color-secondary-strong);
    }
    .vhc-calendar .fc-daygrid-day.fc-day-today .vhc-price-badge {
        color: var(--color-secondary-strong);
    }
    .vhc-summary-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 0.9rem;
        margin-bottom: 1rem;
    }
    .vhc-summary-card {
        background: linear-gradient(135deg, rgba(31,41,55,0.04) 0%, rgba(6,182,212,0.12) 100%);
        border: 1px solid rgba(var(--color-secondary-rgb), 0.12);
        border-radius: 1rem;
        padding: 1rem;
    }
    .vhc-summary-card .label {
        display: block;
        color: #64748b;
        font-size: 0.74rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        margin-bottom: 0.4rem;
    }
    .vhc-summary-card strong {
        display: block;
        font-size: 1.1rem;
        color: #0f172a;
    }
    .vhc-price-total {
        font-size: 1.8rem;
        font-weight: 800;
        color: var(--color-primary);
        letter-spacing: -0.05em;
    }
    .vhc-status-banner {
        border-radius: 1rem;
        padding: 0.95rem 1rem;
        margin-bottom: 1rem;
        display: none;
        border: 1px solid transparent;
    }
    .vhc-status-banner strong {
        display: block;
        margin-bottom: 0.2rem;
    }
    .vhc-status-banner.available {
        display: block;
        background: rgba(16,185,129,0.1);
        color: #065f46;
        border-color: rgba(16,185,129,0.18);
    }
    .vhc-status-banner.queue {
        display: block;
        background: rgba(245,158,11,0.12);
        color: #92400e;
        border-color: rgba(245,158,11,0.18);
    }
    .vhc-status-banner.unavailable {
        display: block;
        background: rgba(239,68,68,0.1);
        color: #991b1b;
        border-color: rgba(239,68,68,0.18);
    }
    .vhc-breakdown-list {
        display: grid;
        gap: 0.7rem;
    }
    .vhc-breakdown-row {
        display: flex;
        justify-content: space-between;
        gap: 1rem;
        align-items: center;
        border: 1px solid rgba(226,232,240,0.86);
        border-radius: 1rem;
        padding: 0.85rem 1rem;
        background: #fff;
    }
    .vhc-breakdown-row .date {
        font-weight: 700;
        color: #0f172a;
    }
    .vhc-breakdown-row .meta {
        color: #64748b;
        font-size: 0.84rem;
    }
    .vhc-empty-state {
        border: 1px dashed rgba(148,163,184,0.5);
        border-radius: 1rem;
        padding: 1.1rem;
        color: #64748b;
        background: rgba(248,250,252,0.82);
    }
    .vhc-addon-list {
        display: grid;
        gap: 0.75rem;
        margin-bottom: 1rem;
    }
    .vhc-addon-item {
        display: flex;
        gap: 0.8rem;
        align-items: flex-start;
        border: 1px solid rgba(226,232,240,0.86);
        border-radius: 1rem;
        padding: 0.85rem 0.95rem;
        background: #fff;
    }
    .vhc-addon-item input {
        margin-top: 0.28rem;
    }
    .vhc-addon-item strong {
        display: block;
        color: #0f172a;
        margin-bottom: 0.2rem;
    }
    .vhc-addon-item span {
        display: block;
        color: #64748b;
        font-size: 0.85rem;
    }
    .vhc-info-card {
        display: flex;
        gap: 0.9rem;
        align-items: center;
        padding: 0.95rem 1rem;
        border: 1px solid rgba(226,232,240,0.86);
        border-radius: 1rem;
        background: #fff;
    }
    .vhc-info-card i {
        width: 2.6rem;
        height: 2.6rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 0.9rem;
        background: linear-gradient(135deg, rgba(31,41,55,0.08) 0%, rgba(6,182,212,0.16) 100%);
        color: var(--color-secondary-strong);
        font-size: 1.15rem;
    }
    .vhc-info-card strong {
        display: block;
        color: #0f172a;
    }
    .vhc-info-card span {
        color: #64748b;
        font-size: 0.85rem;
    }
    .vhc-vehicle-card {
        display: grid;
        grid-template-columns: 120px minmax(0, 1fr);
        gap: 1rem;
        align-items: center;
    }
    .vhc-vehicle-card img,
    .vhc-vehicle-placeholder {
        width: 120px;
        height: 96px;
        border-radius: 1rem;
        object-fit: cover;
    }
    .vhc-vehicle-placeholder {
        display: flex;
        align-items: center;
        justify-content: center;
        background: radial-gradient(circle at top, rgba(255,255,255,0.12), transparent 36%), var(--gradient-brand);
        color: #fff;
        font-size: 2.2rem;
    }
    .vhc-vehicle-facts {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 0.75rem;
        margin-top: 1rem;
    }
    .vhc-vehicle-facts .fact {
        border-radius: 1rem;
        border: 1px solid rgba(226,232,240,0.86);
        padding: 0.85rem 0.95rem;
        background: #fff;
    }
    .vhc-vehicle-facts .fact .label {
        display: block;
        color: #64748b;
        font-size: 0.74rem;
        margin-bottom: 0.25rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
    }
    .vhc-vehicle-facts .fact strong {
        color: #0f172a;
    }
    .vhc-sticky {
        position: sticky;
        top: 6rem;
    }
    @media (min-width: 1200px) {
        .vhc-calendars {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }
    @media (min-width: 1800px) {
        .vhc-calendars {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
    }
    @media (max-width: 1599.98px) {
        .vhc-main-column,
        .vhc-sidebar-column {
            width: 100%;
            flex: 0 0 100%;
        }
        .vhc-sticky {
            position: static;
        }
    }
    @media (max-width: 1199.98px) {
        .vhc-calendars {
            grid-template-columns: 1fr;
        }
        .vhc-sticky {
            position: static;
        }
    }
    @media (max-width: 767.98px) {
        .vhc-hero {
            padding: 2rem 0 4.5rem;
        }
        .vhc-shell {
            margin-top: -2.5rem;
        }
        .vhc-calendars {
            grid-template-columns: 1fr;
        }
        .vhc-summary-grid,
        .vhc-vehicle-facts,
        .vhc-vehicle-card {
            grid-template-columns: 1fr;
        }
        .vhc-vehicle-card img,
        .vhc-vehicle-placeholder {
            width: 100%;
            height: 180px;
        }
    }
</style>
@endsection

@section('content')
<div class="vhc-hero">
    <div class="container">
        <a href="{{ route('vehicles.show', $vehicle) }}" class="vhc-back">
            <i class="bi bi-arrow-left"></i> Kembali ke detail kendaraan
        </a>
        <h1>{{ $vehicle->name }}</h1>
        <p class="vhc-hero-copy mb-0">
            Pantau ketersediaan harian real-time, lihat tarif dinamis per tanggal, lalu booking langsung dari kalender tanpa menebak-nebak slot kosong.
        </p>
        <div class="vhc-meta">
            <span class="vhc-meta-chip"><i class="bi bi-cash-stack"></i> Mulai Rp{{ number_format($vehicle->base_price ?? $vehicle->daily_price, 0, ',', '.') }}/hari</span>
            <span class="vhc-meta-chip"><i class="bi bi-star-fill"></i> {{ number_format($vehicle->getAverageRatingValue(), 1, ',', '.') }} dari {{ $vehicle->getApprovedReviewCount() }} review</span>
            <span class="vhc-meta-chip"><i class="bi bi-calendar-event"></i> {{ $nextAvailableDate ? 'Kosong lagi mulai ' . \Carbon\Carbon::parse($nextAvailableDate)->format('d M Y') : 'Belum ada slot kosong terdekat' }}</span>
        </div>
    </div>
</div>

<div class="container vhc-shell">
    <div class="row g-4 align-items-start">
        <div class="col-xl-8 vhc-main-column">
            <div class="vhc-panel">
                <div class="vhc-panel-head">
                    <div>
                        <h2>Kalender Ketersediaan</h2>
                        <p>Klik tanggal mulai lalu klik lagi untuk menentukan tanggal selesai. Customer akan melihat 2 bulan sekaligus di desktop biasa, dan 3 bulan hanya di layar ekstra lebar.</p>
                    </div>
                    <div class="vhc-calendar-toolbar">
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="calendarPrevBtn">
                            <i class="bi bi-chevron-left"></i>
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="calendarNextBtn">
                            <i class="bi bi-chevron-right"></i>
                        </button>
                    </div>
                </div>

                <div class="vhc-legend">
                    <span><i class="vhc-dot available"></i> Tersedia</span>
                    <span><i class="vhc-dot booked"></i> Dibooking</span>
                    <span><i class="vhc-dot maintenance"></i> Maintenance</span>
                    <span><i class="vhc-dot selected"></i> Pilihan Anda</span>
                </div>

                <div class="vhc-calendars" id="vehicleCalendarGrid"></div>
            </div>

            <div class="vhc-panel">
                <div class="vhc-panel-head">
                    <div>
                        <h3>Breakdown Harga Harian</h3>
                        <p>Gunakan panel ini untuk memeriksa rincian tarif setiap hari yang masuk ke total booking.</p>
                    </div>
                </div>
                <div id="selectionDailyBreakdown" class="vhc-breakdown-list">
                    <div class="vhc-empty-state">Pilih rentang tanggal di kalender untuk melihat tarif per hari dan total booking.</div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 vhc-sidebar-column">
            <div class="vhc-sticky">
                <div class="vhc-panel">
                    <div class="vhc-panel-head">
                        <div>
                            <h3>Booking Langsung dari Kalender</h3>
                            <p>Flow ini tetap menyimpan booking ke sistem yang sama, hanya pilihan tanggal dan harga kini real-time per hari.</p>
                        </div>
                    </div>

                    <div class="vhc-summary-grid">
                        <div class="vhc-summary-card">
                            <span class="label">Rentang Pilihan</span>
                            <strong id="selectedRangeLabel">Belum dipilih</strong>
                        </div>
                        <div class="vhc-summary-card">
                            <span class="label">Durasi</span>
                            <strong id="selectedDurationLabel">-</strong>
                        </div>
                        <div class="vhc-summary-card">
                            <span class="label">Subtotal</span>
                            <strong id="selectedSubtotalLabel">-</strong>
                        </div>
                        <div class="vhc-summary-card">
                            <span class="label">Total Akhir</span>
                            <strong class="vhc-price-total" id="selectedTotalLabel">-</strong>
                        </div>
                    </div>

                    <div class="vhc-status-banner" id="selectionStatusBanner"></div>

                    <form method="POST" action="{{ route('bookings.store') }}" id="calendarBookingForm">
                        @csrf
                        <input type="hidden" name="vehicle_id" value="{{ $vehicle->id }}">
                        <input type="hidden" name="start_date" id="bookingStartDateInput">
                        <input type="hidden" name="end_date" id="bookingEndDateInput">
                        <input type="hidden" name="notes" id="bookingNotesInput">

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0 fw-bold">Add-ons & Catatan</h6>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="selectionResetBtn">Reset Pilihan</button>
                        </div>

                        <div class="vhc-addon-list">
                            @foreach($calendarAddOns as $addOn)
                                <label class="vhc-addon-item">
                                    <input type="checkbox" class="form-check-input calendar-addon-checkbox" data-label="{{ $addOn['label'] }}">
                                    <span>
                                        <strong>{{ $addOn['label'] }}</strong>
                                        <span>{{ $addOn['description'] }}</span>
                                    </span>
                                </label>
                            @endforeach
                        </div>

                        <div class="mb-3">
                            <label for="calendarExtraNote" class="form-label fw-semibold">Catatan Tambahan</label>
                            <textarea id="calendarExtraNote" class="form-control" rows="3" placeholder="Contoh: butuh charger, kursi anak, atau titip request khusus lainnya"></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary w-100" id="calendarBookingSubmit" disabled>
                            <i class="bi bi-calendar-check me-1"></i> Booking untuk Tanggal Ini
                        </button>
                    </form>
                </div>

                <div class="vhc-panel">
                    <div class="vhc-vehicle-card">
                        @if($vehicle->image)
                            <img src="{{ Storage::url($vehicle->image) }}" alt="{{ $vehicle->name }}">
                        @else
                            <div class="vhc-vehicle-placeholder">
                                <i class="bi {{ $vehicle->vehicle_type === 'motor' ? 'bi-bicycle' : 'bi-car-front-fill' }}"></i>
                            </div>
                        @endif
                        <div>
                            <h3 class="mb-2">{{ $vehicle->name }}</h3>
                            <p class="text-muted mb-2">{{ $vehicle->description ?: 'Belum ada deskripsi tambahan untuk kendaraan ini.' }}</p>
                            <span class="badge text-bg-light">{{ ucfirst($vehicle->vehicle_type) }}</span>
                            <span class="badge text-bg-light">{{ $vehicle->transmission }}</span>
                        </div>
                    </div>

                    <div class="vhc-vehicle-facts">
                        <div class="fact">
                            <span class="label">Nomor Plat</span>
                            <strong>{{ $vehicle->plat_number }}</strong>
                        </div>
                        <div class="fact">
                            <span class="label">Tahun</span>
                            <strong>{{ $vehicle->year }}</strong>
                        </div>
                        <div class="fact">
                            <span class="label">Weekend</span>
                            <strong>{{ number_format((($vehicle->weekend_multiplier ?? 1.2) - 1) * 100, 0, ',', '.') }}% dari harga dasar</strong>
                        </div>
                        <div class="fact">
                            <span class="label">Peak / Low Season</span>
                            <strong>{{ number_format((($vehicle->peak_season_multiplier ?? 1.4) - 1) * 100, 0, ',', '.') }}% / {{ number_format((1 - ($vehicle->low_season_multiplier ?? 0.8)) * 100, 0, ',', '.') }}%</strong>
                        </div>
                    </div>
                </div>

                <div class="vhc-panel">
                    <div class="vhc-info-card">
                        <i class="bi bi-lightbulb"></i>
                        <div>
                            <strong>Saran slot kosong berikutnya</strong>
                            <span>{{ $nextAvailableDate ? \Carbon\Carbon::parse($nextAvailableDate)->format('d M Y') : 'Belum ada tanggal kosong yang terdeteksi dalam horizon kalender saat ini.' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.17/index.global.min.js"></script>
<script>
    const vehicleId = {{ $vehicle->id }};
    const availabilityApiUrl = @json(route('api.vehicle.availability', $vehicle));
    const priceApiUrl = @json(route('api.vehicle.price', $vehicle));
    const initialMonths = @json($calendarMonths->values()->all());
    const todayDateString = new Date().toISOString().split('T')[0];
    const calendarGrid = document.getElementById('vehicleCalendarGrid');
    const rangeLabel = document.getElementById('selectedRangeLabel');
    const durationLabel = document.getElementById('selectedDurationLabel');
    const subtotalLabel = document.getElementById('selectedSubtotalLabel');
    const totalLabel = document.getElementById('selectedTotalLabel');
    const breakdownContainer = document.getElementById('selectionDailyBreakdown');
    const statusBanner = document.getElementById('selectionStatusBanner');
    const startDateInput = document.getElementById('bookingStartDateInput');
    const endDateInput = document.getElementById('bookingEndDateInput');
    const notesInput = document.getElementById('bookingNotesInput');
    const extraNoteInput = document.getElementById('calendarExtraNote');
    const submitButton = document.getElementById('calendarBookingSubmit');
    const addOnCheckboxes = Array.from(document.querySelectorAll('.calendar-addon-checkbox'));
    const resetButton = document.getElementById('selectionResetBtn');
    const prevButton = document.getElementById('calendarPrevBtn');
    const nextButton = document.getElementById('calendarNextBtn');

    const monthCache = new Map();
    let calendars = [];
    let currentWindowStart = { month: initialMonths[0].month, year: initialMonths[0].year };
    let visibleMonthCount = getVisibleMonthCount();
    let selectedStart = null;
    let selectedEnd = null;
    let currentPricing = null;
    let currentAvailability = null;

    function monthKey(month, year) {
        return `${year}-${String(month).padStart(2, '0')}`;
    }

    function parseDate(dateString) {
        return new Date(`${dateString}T00:00:00`);
    }

    function formatDateLabel(dateString) {
        return new Intl.DateTimeFormat('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }).format(parseDate(dateString));
    }

    function formatMonthLabel(month, year) {
        return new Intl.DateTimeFormat('id-ID', { month: 'long', year: 'numeric' }).format(new Date(year, month - 1, 1));
    }

    function formatMoney(amount) {
        return `Rp${Number(amount || 0).toLocaleString('id-ID')}`;
    }

    function formatCompactMoney(amount) {
        const value = Number(amount || 0);

        if (value >= 1000000) {
            const millions = value / 1000000;
            const formatted = Number.isInteger(millions)
                ? String(millions)
                : millions.toFixed(1).replace('.', ',');

            return `${formatted}jt`;
        }

        return `${Math.round(value / 1000)}rb`;
    }

    function getVisibleMonthCount() {
        if (window.innerWidth >= 1800) {
            return 3;
        }

        if (window.innerWidth >= 1200) {
            return 2;
        }

        return 1;
    }

    function addMonths(month, year, delta) {
        const value = new Date(year, month - 1 + delta, 1);

        return {
            month: value.getMonth() + 1,
            year: value.getFullYear(),
        };
    }

    async function ensureMonthData(month, year) {
        const key = monthKey(month, year);

        if (monthCache.has(key)) {
            return monthCache.get(key);
        }

        const response = await fetch(`${availabilityApiUrl}?month=${month}&year=${year}`, {
            headers: { 'Accept': 'application/json' },
        });

        if (!response.ok) {
            throw new Error('Gagal memuat kalender kendaraan.');
        }

        const payload = await response.json();
        monthCache.set(key, payload);

        return payload;
    }

    function destroyCalendars() {
        calendars.forEach(({ calendar }) => calendar.destroy());
        calendars = [];
        calendarGrid.innerHTML = '';
    }

    async function renderCalendarWindow() {
        destroyCalendars();

        for (let offset = 0; offset < visibleMonthCount; offset += 1) {
            const { month, year } = addMonths(currentWindowStart.month, currentWindowStart.year, offset);
            const card = document.createElement('div');
            card.className = 'vhc-calendar-card';
            card.innerHTML = `
                <div class="vhc-calendar-card-head">${formatMonthLabel(month, year)}</div>
                <div class="vhc-calendar" id="vehicle-calendar-${year}-${month}"></div>
            `;
            calendarGrid.appendChild(card);

            const calendarElement = card.querySelector('.vhc-calendar');
            const calendar = new FullCalendar.Calendar(calendarElement, {
                initialView: 'dayGridMonth',
                initialDate: `${year}-${String(month).padStart(2, '0')}-01`,
                fixedWeekCount: false,
                headerToolbar: false,
                height: 'auto',
                dayHeaderContent(args) {
                    return ['Mg', 'Sn', 'Sl', 'Rb', 'Km', 'Jm', 'Sb'][args.date.getDay()];
                },
                dateClick(info) {
                    handleDateClick(info.dateStr);
                },
            });

            calendar.render();
            calendars.push({ calendar, month, year });

            const payload = await ensureMonthData(month, year);
            decorateCalendar(calendar, payload);
        }

        updateSelectionVisuals();
    }

    function decorateCalendar(calendar, payload) {
        const dateMap = new Map(payload.dates.map((entry) => [entry.date, entry]));

        calendar.el.querySelectorAll('.fc-daygrid-day').forEach((dayElement) => {
            const dateString = dayElement.getAttribute('data-date');
            const entry = dateMap.get(dateString);
            const existingBadge = dayElement.querySelector('.vhc-price-badge');

            dayElement.classList.remove('rh-day-available', 'rh-day-booked', 'rh-day-maintenance', 'rh-day-selected', 'rh-day-selected-start', 'rh-day-selected-end');
            dayElement.removeAttribute('title');

            if (!entry) {
                if (existingBadge) {
                    existingBadge.remove();
                }
                return;
            }

            dayElement.classList.add(`rh-day-${entry.status}`);

            if (entry.status === 'available' && !dayElement.classList.contains('fc-day-other')) {
                const badge = existingBadge || document.createElement('span');
                badge.className = 'vhc-price-badge';
                badge.textContent = formatCompactMoney(entry.final_price);

                if (!existingBadge) {
                    dayElement.querySelector('.fc-daygrid-day-frame')?.appendChild(badge);
                }
            } else if (existingBadge) {
                existingBadge.remove();
            }

            const tooltip = [
                formatDateLabel(entry.date),
                `Status: ${entry.status === 'available' ? 'Tersedia' : (entry.status === 'booked' ? 'Dibooking' : 'Maintenance')}`,
                `Harga: ${formatMoney(entry.final_price)}`,
            ];

            if (entry.reason) {
                tooltip.push(`Info: ${entry.reason}`);
            }

            dayElement.title = tooltip.join('\n');
        });
    }

    function updateSelectionVisuals() {
        calendars.forEach(({ calendar }) => {
            calendar.el.querySelectorAll('.fc-daygrid-day').forEach((dayElement) => {
                const dateString = dayElement.getAttribute('data-date');

                dayElement.classList.remove('rh-day-selected', 'rh-day-selected-start', 'rh-day-selected-end');

                if (!selectedStart || !selectedEnd) {
                    return;
                }

                if (dateString === selectedStart) {
                    dayElement.classList.add('rh-day-selected-start');
                }

                if (dateString === selectedEnd) {
                    dayElement.classList.add('rh-day-selected-end');
                }

                if (dateString >= selectedStart && dateString <= selectedEnd) {
                    dayElement.classList.add('rh-day-selected');
                }
            });
        });
    }

    function resetSelection() {
        selectedStart = null;
        selectedEnd = null;
        currentPricing = null;
        currentAvailability = null;
        startDateInput.value = '';
        endDateInput.value = '';
        rangeLabel.textContent = 'Belum dipilih';
        durationLabel.textContent = '-';
        subtotalLabel.textContent = '-';
        totalLabel.textContent = '-';
        breakdownContainer.innerHTML = '<div class="vhc-empty-state">Pilih rentang tanggal di kalender untuk melihat tarif per hari dan total booking.</div>';
        statusBanner.className = 'vhc-status-banner';
        statusBanner.textContent = '';
        submitButton.disabled = true;
        updateSelectionVisuals();
        syncNotes();
    }

    function handleDateClick(dateString) {
        if (dateString < todayDateString) {
            return;
        }

        if (!selectedStart || (selectedStart && selectedEnd && dateString < selectedStart)) {
            selectedStart = dateString;
            selectedEnd = dateString;
        } else {
            selectedEnd = dateString >= selectedStart ? dateString : selectedStart;
        }

        startDateInput.value = selectedStart;
        endDateInput.value = selectedEnd;
        updateSelectionVisuals();
        loadSelectionData();
    }

    async function loadSelectionData() {
        if (!selectedStart || !selectedEnd) {
            return;
        }

        rangeLabel.textContent = `${formatDateLabel(selectedStart)} - ${formatDateLabel(selectedEnd)}`;

        try {
            const [availabilityResponse, priceResponse] = await Promise.all([
                fetch(`${availabilityApiUrl}?start_date=${selectedStart}&end_date=${selectedEnd}`, { headers: { 'Accept': 'application/json' } }),
                fetch(`${priceApiUrl}?start_date=${selectedStart}&end_date=${selectedEnd}`, { headers: { 'Accept': 'application/json' } }),
            ]);

            const availabilityData = await availabilityResponse.json();
            const priceData = await priceResponse.json();

            if (!availabilityResponse.ok) {
                throw new Error(availabilityData.error || availabilityData.message || 'Gagal memuat ketersediaan.');
            }

            if (!priceResponse.ok) {
                throw new Error(priceData.error || priceData.message || 'Gagal memuat harga.');
            }

            currentAvailability = availabilityData;
            currentPricing = priceData;
            renderSelectionSummary();
            renderSelectionStatus();
            renderDailyBreakdown();
            syncNotes();
        } catch (error) {
            statusBanner.className = 'vhc-status-banner unavailable';
            statusBanner.innerHTML = `<strong>Data kalender belum bisa dimuat.</strong><span>${error.message}</span>`;
            submitButton.disabled = true;
        }
    }

    function renderSelectionSummary() {
        if (!currentPricing) {
            return;
        }

        durationLabel.textContent = `${currentPricing.duration_days} hari`;
        subtotalLabel.textContent = formatMoney(currentPricing.subtotal);
        totalLabel.textContent = formatMoney(currentPricing.total);
    }

    function renderSelectionStatus() {
        if (!currentAvailability) {
            return;
        }

        const nextSuggestion = @json($nextAvailableDate ? \Carbon\Carbon::parse($nextAvailableDate)->format('d M Y') : null);

        if (currentAvailability.available) {
            statusBanner.className = 'vhc-status-banner available';
            statusBanner.innerHTML = '<strong>Tanggal tersedia untuk direct booking.</strong><span>Semua hari di rentang ini masih kosong dan bisa langsung diproses.</span>';
            submitButton.disabled = false;
            return;
        }

        if (currentAvailability.queue_available) {
            statusBanner.className = 'vhc-status-banner queue';
            statusBanner.innerHTML = '<strong>Rentang ini masih bisa diajukan ke antrean.</strong><span>Pembayaran akan diproses seperti biasa, tetapi aktivasi booking menunggu unit benar-benar kosong.</span>';
            submitButton.disabled = false;
            return;
        }

        const unavailableDates = (currentPricing?.unavailable_dates || []).map(formatDateLabel).join(', ');
        statusBanner.className = 'vhc-status-banner unavailable';
        statusBanner.innerHTML = `<strong>Ada tanggal yang tidak tersedia.</strong><span>${unavailableDates || 'Rentang ini bentrok dengan booking atau maintenance.'}${nextSuggestion ? ` Coba mulai lagi dari ${nextSuggestion}.` : ''}</span>`;
        submitButton.disabled = true;
    }

    function renderDailyBreakdown() {
        if (!currentPricing || !Array.isArray(currentPricing.daily_prices) || currentPricing.daily_prices.length === 0) {
            breakdownContainer.innerHTML = '<div class="vhc-empty-state">Belum ada rincian harga untuk ditampilkan.</div>';
            return;
        }

        breakdownContainer.innerHTML = currentPricing.daily_prices.map((entry) => {
            const statusLabel = entry.status === 'available' ? 'Tersedia' : (entry.status === 'booked' ? 'Dibooking' : 'Maintenance');

            return `
                <div class="vhc-breakdown-row">
                    <div>
                        <div class="date">${formatDateLabel(entry.date)}</div>
                        <div class="meta">${statusLabel}</div>
                    </div>
                    <strong>${formatMoney(entry.price)}</strong>
                </div>
            `;
        }).join('');
    }

    function syncNotes() {
        const selectedAddOns = addOnCheckboxes
            .filter((checkbox) => checkbox.checked)
            .map((checkbox) => checkbox.dataset.label);
        const lines = [];

        if (selectedAddOns.length > 0) {
            lines.push(`Add-on pilihan: ${selectedAddOns.join(', ')}`);
        }

        if (extraNoteInput.value.trim() !== '') {
            lines.push(`Catatan customer: ${extraNoteInput.value.trim()}`);
        }

        notesInput.value = lines.join('\n');
    }

    prevButton.addEventListener('click', async () => {
        currentWindowStart = addMonths(currentWindowStart.month, currentWindowStart.year, -visibleMonthCount);
        await renderCalendarWindow();
    });

    nextButton.addEventListener('click', async () => {
        currentWindowStart = addMonths(currentWindowStart.month, currentWindowStart.year, visibleMonthCount);
        await renderCalendarWindow();
    });

    let resizeDebounce = null;

    window.addEventListener('resize', () => {
        window.clearTimeout(resizeDebounce);
        resizeDebounce = window.setTimeout(async () => {
            const nextVisibleMonthCount = getVisibleMonthCount();

            if (nextVisibleMonthCount === visibleMonthCount) {
                return;
            }

            visibleMonthCount = nextVisibleMonthCount;
            await renderCalendarWindow();
        }, 140);
    });

    resetButton.addEventListener('click', resetSelection);
    addOnCheckboxes.forEach((checkbox) => checkbox.addEventListener('change', syncNotes));
    extraNoteInput.addEventListener('input', syncNotes);

    document.getElementById('calendarBookingForm').addEventListener('submit', (event) => {
        syncNotes();

        if (!selectedStart || !selectedEnd || submitButton.disabled) {
            event.preventDefault();
        }
    });

    renderCalendarWindow();
    resetSelection();
</script>
@endsection