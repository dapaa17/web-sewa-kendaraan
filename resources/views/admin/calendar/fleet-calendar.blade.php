@extends('layouts.admin')

@section('title', 'Kalender Armada')
@section('page-title', 'Kalender Armada')

@section('content')
<style>
    .fleet-header {
        background: var(--gradient-brand);
        color: #fff;
        border-radius: 1.5rem;
        padding: 2rem;
        position: relative;
        overflow: hidden;
        margin-bottom: 1.25rem;
    }
    .fleet-header::before {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at 82% 20%, rgba(6,182,212,0.22), transparent 52%), radial-gradient(circle at 10% 88%, rgba(255,255,255,0.08), transparent 40%);
        pointer-events: none;
    }
    .fleet-header h1 {
        font-size: 1.85rem;
        font-weight: 800;
        margin-bottom: 0.45rem;
    }
    .fleet-header p {
        margin: 0;
        max-width: 42rem;
        color: rgba(255,255,255,0.78);
    }
    .fleet-toolbar {
        display: flex;
        justify-content: space-between;
        gap: 1rem;
        flex-wrap: wrap;
        margin-top: 1.25rem;
        position: relative;
        z-index: 1;
    }
    .fleet-toolbar-group {
        display: flex;
        gap: 0.6rem;
        flex-wrap: wrap;
    }
    .fleet-toolbar .btn {
        border-radius: 999px;
    }
    .fleet-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 1rem;
        margin-bottom: 1rem;
    }
    .fleet-stat-card {
        background: rgba(255,255,255,0.96);
        border: 1px solid rgba(203,213,225,0.72);
        border-radius: 1.2rem;
        box-shadow: var(--shadow-card);
        padding: 1.15rem 1.2rem;
    }
    .fleet-stat-card .label {
        display: block;
        color: #64748b;
        font-size: 0.76rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        margin-bottom: 0.45rem;
    }
    .fleet-stat-card strong {
        display: block;
        color: #0f172a;
        font-size: 1.5rem;
        font-weight: 800;
    }
    .fleet-stat-card small {
        color: #64748b;
    }
    .fleet-panel {
        background: rgba(255,255,255,0.96);
        border: 1px solid rgba(203,213,225,0.72);
        border-radius: 1.3rem;
        box-shadow: var(--shadow-card);
        padding: 1.2rem;
        margin-bottom: 1rem;
    }
    .fleet-panel-head {
        display: flex;
        justify-content: space-between;
        gap: 1rem;
        align-items: center;
        margin-bottom: 0.9rem;
    }
    .fleet-panel-head h2 {
        margin: 0;
        font-size: 1.12rem;
        font-weight: 800;
        color: #0f172a;
    }
    .fleet-panel-head p {
        margin: 0.2rem 0 0;
        color: #64748b;
        font-size: 0.9rem;
    }
    .fleet-legend {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
    }
    .fleet-legend span {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        color: #475569;
        font-size: 0.86rem;
        font-weight: 600;
    }
    .fleet-dot {
        width: 0.8rem;
        height: 0.8rem;
        border-radius: 999px;
        display: inline-block;
    }
    .fleet-dot.available { background: #bbf7d0; }
    .fleet-dot.booked { background: #fecaca; }
    .fleet-dot.maintenance { background: #cbd5e1; }
    .fleet-dot.selection { background: #22d3ee; }
    .fleet-selection-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.55rem 0.8rem;
        border-radius: 999px;
        background: rgba(6,182,212,0.1);
        color: var(--color-secondary-strong);
        font-weight: 700;
        font-size: 0.86rem;
    }
    .fleet-board-wrap {
        overflow-x: auto;
        border-radius: 1rem;
        border: 1px solid rgba(226,232,240,0.9);
    }
    .fleet-board {
        min-width: calc(248px + (var(--fleet-days, 31) * 72px));
    }
    .fleet-board-header,
    .fleet-row {
        display: grid;
        grid-template-columns: 248px repeat(var(--fleet-days), minmax(72px, 1fr));
    }
    .fleet-board-header {
        position: sticky;
        top: 0;
        z-index: 2;
        background: #f8fafc;
        border-bottom: 1px solid rgba(226,232,240,0.9);
    }
    .fleet-board-header .vehicle-col,
    .fleet-row .vehicle-col {
        position: sticky;
        left: 0;
        z-index: 1;
        background: #fff;
        border-right: 1px solid rgba(226,232,240,0.9);
    }
    .fleet-board-header .vehicle-col {
        z-index: 3;
        background: #f8fafc;
    }
    .fleet-board-header .vehicle-col,
    .fleet-board-header .day-col,
    .fleet-row .vehicle-col,
    .fleet-cell {
        padding: 0.85rem 0.75rem;
    }
    .fleet-board-header .day-col {
        text-align: center;
        font-size: 0.78rem;
        color: #64748b;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        border-left: 1px solid rgba(241,245,249,0.9);
    }
    .fleet-board-header .day-col.weekend {
        background: rgba(248,250,252,0.88);
    }
    .fleet-board-header .day-col strong {
        display: block;
        color: #0f172a;
        font-size: 1rem;
        letter-spacing: -0.03em;
    }
    .fleet-row {
        border-bottom: 1px solid rgba(241,245,249,0.92);
        background: #fff;
    }
    .fleet-row:last-child {
        border-bottom: none;
    }
    .fleet-row .vehicle-col {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: flex-start;
        gap: 0.5rem;
    }
    .fleet-row .vehicle-col strong {
        color: #0f172a;
    }
    .fleet-row .vehicle-col span {
        color: #64748b;
        font-size: 0.84rem;
    }
    .fleet-vehicle-badges {
        display: flex;
        flex-wrap: wrap;
        gap: 0.35rem;
    }
    .fleet-vehicle-pill {
        display: inline-flex;
        align-items: center;
        padding: 0.18rem 0.5rem;
        border-radius: 999px;
        font-size: 0.68rem;
        font-weight: 700;
        letter-spacing: 0.03em;
        white-space: nowrap;
    }
    .fleet-vehicle-pill.type {
        background: rgba(15,23,42,0.06);
        color: #334155;
    }
    .fleet-vehicle-pill.available {
        background: rgba(16,185,129,0.12);
        color: #047857;
    }
    .fleet-vehicle-pill.booked {
        background: rgba(239,68,68,0.1);
        color: #b91c1c;
    }
    .fleet-vehicle-pill.maintenance {
        background: rgba(100,116,139,0.12);
        color: #475569;
    }
    .fleet-cell {
        min-height: 84px;
        border-left: 1px solid rgba(241,245,249,0.9);
        cursor: pointer;
        transition: box-shadow 0.16s ease, background-color 0.16s ease;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        justify-content: space-between;
        gap: 0.55rem;
        user-select: none;
        position: relative;
        text-align: left;
        overflow: hidden;
    }
    .fleet-cell:hover {
        box-shadow: inset 0 0 0 1px rgba(6,182,212,0.26);
    }
    .fleet-cell.available { background: linear-gradient(180deg, rgba(220,252,231,0.72) 0%, rgba(255,255,255,0.98) 58%); }
    .fleet-cell.booked { background: linear-gradient(180deg, rgba(254,226,226,0.82) 0%, rgba(255,255,255,0.98) 58%); }
    .fleet-cell.maintenance { background: linear-gradient(180deg, rgba(226,232,240,0.9) 0%, rgba(255,255,255,0.98) 58%); }
    .fleet-cell.selected,
    .fleet-cell.selected-start,
    .fleet-cell.selected-end {
        box-shadow: inset 0 0 0 2px rgba(6,182,212,0.32);
    }
    .fleet-cell-top {
        width: 100%;
        display: flex;
        align-items: center;
        gap: 0.35rem;
        min-height: 1rem;
    }
    .fleet-status-dot {
        width: 0.55rem;
        height: 0.55rem;
        border-radius: 999px;
        flex-shrink: 0;
    }
    .fleet-status-dot.available { background: #10b981; }
    .fleet-status-dot.booked { background: #ef4444; }
    .fleet-status-dot.maintenance { background: #94a3b8; }
    .fleet-status-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.14rem 0.34rem;
        border-radius: 999px;
        font-size: 0.58rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        white-space: nowrap;
    }
    .fleet-status-badge.booked {
        background: rgba(239,68,68,0.12);
        color: #b91c1c;
    }
    .fleet-status-badge.maintenance {
        background: rgba(100,116,139,0.16);
        color: #475569;
    }
    .fleet-cell .price {
        font-size: 0.84rem;
        font-weight: 800;
        color: #0f172a;
        letter-spacing: -0.02em;
        line-height: 1.1;
    }
    .fleet-cell .status {
        font-size: 0.66rem;
        color: #475569;
        line-height: 1.2;
    }
    .calendar-form-stack {
        display: grid;
        gap: 1rem;
    }
    .calendar-form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 0.85rem;
    }
    .calendar-form-field label {
        font-weight: 700;
        color: #334155;
        margin-bottom: 0.35rem;
    }
    .calendar-inline-alert {
        border-radius: 1rem;
        padding: 0.85rem 1rem;
        font-size: 0.88rem;
    }
    .calendar-inline-alert.success {
        background: rgba(16,185,129,0.1);
        color: #065f46;
        border: 1px solid rgba(16,185,129,0.18);
    }
    .calendar-inline-alert.error {
        background: rgba(239,68,68,0.08);
        color: #991b1b;
        border: 1px solid rgba(239,68,68,0.18);
    }
    .calendar-preview-card,
    .calendar-table-card {
        border: 1px solid rgba(226,232,240,0.9);
        border-radius: 1rem;
        background: #f8fafc;
    }
    .calendar-preview-card {
        padding: 0.95rem 1rem;
    }
    .calendar-preview-card .label {
        display: block;
        color: #64748b;
        font-size: 0.76rem;
        margin-bottom: 0.3rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
    }
    .calendar-table-head {
        padding: 1rem 1rem 0.8rem;
        border-bottom: 1px solid rgba(226,232,240,0.9);
        background: #fff;
        border-radius: 1rem 1rem 0 0;
    }
    .fleet-empty {
        padding: 2.5rem 1.5rem;
        text-align: center;
        color: #64748b;
    }
    .fleet-detail-meta {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 0.85rem;
        margin-top: 1rem;
    }
    .fleet-detail-meta .item {
        border-radius: 1rem;
        border: 1px solid rgba(226,232,240,0.9);
        padding: 0.85rem 0.95rem;
        background: #fff;
    }
    .fleet-detail-meta .item .label {
        display: block;
        color: #64748b;
        font-size: 0.74rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        margin-bottom: 0.25rem;
    }
    @media (max-width: 1199.98px) {
        .fleet-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }
    @media (max-width: 767.98px) {
        .fleet-header {
            padding: 1.4rem;
        }
        .fleet-grid,
        .calendar-form-grid,
        .fleet-detail-meta {
            grid-template-columns: 1fr;
        }
        .fleet-board {
            min-width: calc(220px + (var(--fleet-days, 31) * 64px));
        }
        .fleet-board-header,
        .fleet-row {
            grid-template-columns: 220px repeat(var(--fleet-days), minmax(64px, 1fr));
        }
    }
</style>

<div class="fleet-header">
    <h1><i class="bi bi-calendar3 me-2"></i>Kalender Armada Real-Time</h1>
    <p>Lihat kepadatan booking seluruh kendaraan, blok tanggal maintenance dengan drag, dan atur dynamic pricing harian dari satu board operasional.</p>
    <div class="fleet-toolbar">
        <div class="fleet-toolbar-group">
            <button type="button" class="btn btn-light btn-sm" id="fleetPrevBtn">
                <i class="bi bi-chevron-left"></i>
            </button>
            <button type="button" class="btn btn-light btn-sm" id="fleetTodayBtn">Hari Ini</button>
            <button type="button" class="btn btn-light btn-sm" id="fleetNextBtn">
                <i class="bi bi-chevron-right"></i>
            </button>
        </div>
        <div class="fleet-toolbar-group">
            <div class="btn-group btn-group-sm" role="group" aria-label="Pilih tampilan kalender">
                <button type="button" class="btn btn-light active" data-view-mode="month">Bulan</button>
                <button type="button" class="btn btn-light" data-view-mode="week">Minggu</button>
            </div>
            <button type="button" class="btn btn-info btn-sm" id="openBlockDatesBtn" disabled>
                <i class="bi bi-wrench-adjustable-circle me-1"></i> Blok Maintenance
            </button>
            <button type="button" class="btn btn-outline-light btn-sm" id="openPricingRulesBtn">
                <i class="bi bi-stars me-1"></i> Atur Pricing
            </button>
        </div>
    </div>
</div>

<div class="fleet-grid" id="fleetSummaryGrid">
    <div class="fleet-stat-card"><span class="label">Periode</span><strong id="fleetPeriodLabel">-</strong><small>Disesuaikan dengan mode bulan/minggu yang aktif.</small></div>
    <div class="fleet-stat-card"><span class="label">Rata-rata okupansi</span><strong id="fleetAverageOccupancy">-</strong><small>Persentase hari booked dibanding total hari pada periode.</small></div>
    <div class="fleet-stat-card"><span class="label">Forecast revenue</span><strong id="fleetForecastRevenue">-</strong><small>Akumulasi booking aktif pada bulan referensi.</small></div>
    <div class="fleet-stat-card"><span class="label">Tanggal padat</span><strong id="fleetPeakDates">-</strong><small>Tiga tanggal dengan booking density paling tinggi.</small></div>
</div>

<div class="fleet-panel">
    <div class="fleet-panel-head">
        <div>
            <h2>Board Ketersediaan Armada</h2>
            <p>Klik sel merah atau abu-abu untuk membuka detail. Drag rentang hijau atau abu-abu untuk memilih tanggal, lalu simpan lewat tombol Blok Maintenance agar alurnya lebih jelas.</p>
        </div>
        <div class="d-flex flex-wrap gap-2 align-items-center">
            <div class="fleet-selection-chip" id="fleetSelectionChip">Pilih atau drag rentang tanggal terlebih dulu</div>
        </div>
    </div>
    <div class="fleet-legend mb-3">
        <span><i class="fleet-dot available"></i> Tersedia</span>
        <span><i class="fleet-dot booked"></i> Dibooking</span>
        <span><i class="fleet-dot maintenance"></i> Maintenance</span>
        <span><i class="fleet-dot selection"></i> Rentang drag</span>
    </div>

    <div class="fleet-board-wrap">
        <div class="fleet-board" id="fleetBoard">
            <div class="fleet-empty">Memuat data armada...</div>
        </div>
    </div>
</div>

@include('admin.calendar.block-dates')
@include('admin.calendar.pricing-rules')

<div class="modal fade" id="fleetDetailModal" tabindex="-1" aria-labelledby="fleetDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <div>
                    <h5 class="modal-title" id="fleetDetailModalLabel">Detail Tanggal Armada</h5>
                    <p class="text-muted small mb-0">Rincian status tanggal yang diklik.</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <div class="fleet-detail-meta">
                    <div class="item"><span class="label">Kendaraan</span><strong id="fleetDetailVehicle">-</strong></div>
                    <div class="item"><span class="label">Tanggal</span><strong id="fleetDetailDate">-</strong></div>
                    <div class="item"><span class="label">Status</span><strong id="fleetDetailStatus">-</strong></div>
                    <div class="item"><span class="label">Tarif</span><strong id="fleetDetailPrice">-</strong></div>
                </div>
                <div class="calendar-inline-alert mt-3" id="fleetDetailReason"></div>
                <div class="d-flex justify-content-end gap-2 mt-4">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-danger d-none" id="fleetDetailUnblockBtn">
                        <i class="bi bi-unlock me-1"></i> Buka Blok Maintenance
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    const fleetApiUrl = @json(route('admin.api.fleet-availability'));
    const blockDatesUrl = @json(route('admin.calendar.block-dates'));
    const unblockDatesUrl = @json(route('admin.calendar.unblock-dates'));
    const pricingRulesUrl = @json(route('admin.calendar.pricing-rules'));
    const csrfToken = @json(csrf_token());
    const fleetVehicles = @json($fleetVehicles);
    const fleetBoard = document.getElementById('fleetBoard');
    const fleetSelectionChip = document.getElementById('fleetSelectionChip');
    const blockDatesModal = new bootstrap.Modal(document.getElementById('blockDatesModal'));
    const pricingRulesModal = new bootstrap.Modal(document.getElementById('pricingRulesModal'));
    const fleetDetailModal = new bootstrap.Modal(document.getElementById('fleetDetailModal'));
    const blockDatesAlert = document.getElementById('blockDatesAlert');
    const pricingRulesAlert = document.getElementById('pricingRulesAlert');
    const blockDatesForm = document.getElementById('blockDatesForm');
    const pricingRuleForm = document.getElementById('pricingRuleForm');
    const pricingRuleResetBtn = document.getElementById('pricingRuleResetBtn');
    const pricingPreviewText = document.getElementById('pricingPreviewText');
    const openBlockDatesBtn = document.getElementById('openBlockDatesBtn');
    const detailVehicle = document.getElementById('fleetDetailVehicle');
    const detailDate = document.getElementById('fleetDetailDate');
    const detailStatus = document.getElementById('fleetDetailStatus');
    const detailPrice = document.getElementById('fleetDetailPrice');
    const detailReason = document.getElementById('fleetDetailReason');
    const detailUnblockBtn = document.getElementById('fleetDetailUnblockBtn');

    const monthCache = new Map();
    const state = {
        viewMode: 'month',
        referenceDate: new Date(@json($referenceDate->toDateString()) + 'T00:00:00'),
        drag: null,
        selectedRange: null,
        detailPayload: null,
        suppressNextCellClick: false,
    };

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

    function toDateString(date) {
        return date.toISOString().split('T')[0];
    }

    function parseDate(dateString) {
        return new Date(`${dateString}T00:00:00`);
    }

    function formatDateLabel(dateString) {
        return new Intl.DateTimeFormat('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }).format(parseDate(dateString));
    }

    function monthKey(date) {
        return `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}`;
    }

    function cloneDate(date) {
        return new Date(date.getFullYear(), date.getMonth(), date.getDate());
    }

    function startOfWeek(date) {
        const value = cloneDate(date);
        const day = value.getDay();
        const diff = day === 0 ? -6 : 1 - day;
        value.setDate(value.getDate() + diff);
        return value;
    }

    function getVisibleDates() {
        const dates = [];

        if (state.viewMode === 'month') {
            const cursor = new Date(state.referenceDate.getFullYear(), state.referenceDate.getMonth(), 1);
            const month = cursor.getMonth();

            while (cursor.getMonth() === month) {
                dates.push(cloneDate(cursor));
                cursor.setDate(cursor.getDate() + 1);
            }

            return dates;
        }

        const cursor = startOfWeek(state.referenceDate);

        for (let index = 0; index < 7; index += 1) {
            dates.push(cloneDate(cursor));
            cursor.setDate(cursor.getDate() + 1);
        }

        return dates;
    }

    async function ensureMonthPayload(date) {
        const key = monthKey(date);

        if (monthCache.has(key)) {
            return monthCache.get(key);
        }

        const response = await fetch(`${fleetApiUrl}?month=${date.getMonth() + 1}&year=${date.getFullYear()}`, {
            headers: { 'Accept': 'application/json' },
        });

        if (!response.ok) {
            throw new Error('Gagal memuat data armada.');
        }

        const payload = await response.json();
        monthCache.set(key, payload);

        return payload;
    }

    async function ensureVisibleRangeData() {
        const visibleDates = getVisibleDates();
        const uniqueMonths = new Map();

        visibleDates.forEach((date) => {
            uniqueMonths.set(monthKey(date), date);
        });

        await Promise.all(Array.from(uniqueMonths.values()).map((date) => ensureMonthPayload(date)));
    }

    function getVehicleMonthPayload(vehicleId, date) {
        const payload = monthCache.get(monthKey(date));

        return payload?.vehicles?.find((vehicle) => vehicle.vehicle_id === Number(vehicleId)) || null;
    }

    function getDateEntry(vehicleId, dateString) {
        const date = parseDate(dateString);
        const vehiclePayload = getVehicleMonthPayload(vehicleId, date);

        return vehiclePayload?.dates?.find((entry) => entry.date === dateString) || {
            date: dateString,
            status: 'available',
            reason: null,
            final_price: 0,
        };
    }

    function currentMonthPayload() {
        return monthCache.get(monthKey(new Date(state.referenceDate.getFullYear(), state.referenceDate.getMonth(), 1)));
    }

    function getStatusMeta(status) {
        return {
            available: { label: 'Tersedia', shortLabel: '' },
            booked: { label: 'Dibooking', shortLabel: 'Booking' },
            maintenance: { label: 'Maintenance', shortLabel: 'Servis' },
        }[status] || { label: 'Tersedia', shortLabel: '' };
    }

    function renderSummaryCards() {
        const payload = currentMonthPayload();
        const visibleDates = getVisibleDates();
        const periodLabel = state.viewMode === 'month'
            ? new Intl.DateTimeFormat('id-ID', { month: 'long', year: 'numeric' }).format(new Date(state.referenceDate.getFullYear(), state.referenceDate.getMonth(), 1))
            : `${formatDateLabel(toDateString(visibleDates[0]))} - ${formatDateLabel(toDateString(visibleDates[visibleDates.length - 1]))}`;

        document.getElementById('fleetPeriodLabel').textContent = periodLabel;
        document.getElementById('fleetAverageOccupancy').textContent = `${payload?.summary?.average_occupancy ?? 0}%`;
        document.getElementById('fleetForecastRevenue').textContent = formatMoney(payload?.summary?.forecast_revenue ?? 0);
        document.getElementById('fleetPeakDates').textContent = (payload?.summary?.peak_dates || []).slice(0, 3).map(formatDateLabel).join(', ') || '-';
    }

    function renderBoard() {
        const visibleDates = getVisibleDates();
        fleetBoard.style.setProperty('--fleet-days', String(visibleDates.length));

        if (fleetVehicles.length === 0) {
            fleetBoard.innerHTML = '<div class="fleet-empty">Belum ada kendaraan yang bisa ditampilkan.</div>';
            return;
        }

        const header = `
            <div class="fleet-board-header">
                <div class="vehicle-col">
                    <strong>Armada</strong>
                </div>
                ${visibleDates.map((date) => `
                    <div class="day-col ${[0, 6].includes(date.getDay()) ? 'weekend' : ''}">
                        ${new Intl.DateTimeFormat('id-ID', { weekday: 'short' }).format(date)}
                        <strong>${String(date.getDate()).padStart(2, '0')}</strong>
                    </div>
                `).join('')}
            </div>
        `;

        const rows = fleetVehicles.map((vehicle) => {
            const dateEntries = visibleDates.map((date) => getDateEntry(vehicle.id, toDateString(date)));
            const bookedCount = dateEntries.filter((entry) => entry.status === 'booked').length;
            const maintenanceCount = dateEntries.filter((entry) => entry.status === 'maintenance').length;
            const availableCount = dateEntries.length - bookedCount - maintenanceCount;

            return `
                <div class="fleet-row" data-vehicle-id="${vehicle.id}">
                    <div class="vehicle-col">
                        <strong>${vehicle.name}</strong>
                        <span>${vehicle.plate}</span>
                        <div class="fleet-vehicle-badges">
                            <span class="fleet-vehicle-pill type">${vehicle.type === 'motor' ? 'Motor' : 'Mobil'}</span>
                            <span class="fleet-vehicle-pill available">${availableCount} kosong</span>
                            ${bookedCount > 0 ? `<span class="fleet-vehicle-pill booked">${bookedCount} booking</span>` : ''}
                            ${maintenanceCount > 0 ? `<span class="fleet-vehicle-pill maintenance">${maintenanceCount} servis</span>` : ''}
                        </div>
                    </div>
                    ${visibleDates.map((date) => {
                        const dateString = toDateString(date);
                        const entry = getDateEntry(vehicle.id, dateString);
                        const statusMeta = getStatusMeta(entry.status);
                        const isSelected = state.selectedRange
                            && state.selectedRange.vehicleId === vehicle.id
                            && dateString >= state.selectedRange.startDate
                            && dateString <= state.selectedRange.endDate;
                        const classes = [
                            'fleet-cell',
                            entry.status,
                            isSelected ? 'selected' : '',
                            state.selectedRange?.vehicleId === vehicle.id && state.selectedRange?.startDate === dateString ? 'selected-start' : '',
                            state.selectedRange?.vehicleId === vehicle.id && state.selectedRange?.endDate === dateString ? 'selected-end' : '',
                        ].filter(Boolean).join(' ');

                        return `
                            <button
                                type="button"
                                class="${classes}"
                                data-vehicle-id="${vehicle.id}"
                                data-vehicle-name="${vehicle.name}"
                                data-date="${dateString}"
                                data-status="${entry.status}"
                                data-price="${entry.final_price ?? 0}"
                                data-reason="${entry.reason ?? ''}"
                            >
                                <span class="fleet-cell-top">
                                    <span class="fleet-status-dot ${entry.status}" aria-hidden="true"></span>
                                    ${statusMeta.shortLabel ? `<span class="fleet-status-badge ${entry.status}">${statusMeta.shortLabel}</span>` : ''}
                                </span>
                                <span class="price">${formatCompactMoney(entry.final_price || vehicle.base_price)}</span>
                                ${entry.status === 'available' ? '' : `<span class="status">${statusMeta.label}</span>`}
                            </button>
                        `;
                    }).join('')}
                </div>
            `;
        }).join('');

        fleetBoard.innerHTML = header + rows;
        updateSelectionChip();
    }

    function updateSelectionChip() {
        if (!state.selectedRange) {
            fleetSelectionChip.textContent = 'Pilih atau drag rentang tanggal terlebih dulu';
            openBlockDatesBtn.disabled = true;
            return;
        }

        fleetSelectionChip.textContent = `${state.selectedRange.vehicleName} • ${formatDateLabel(state.selectedRange.startDate)} - ${formatDateLabel(state.selectedRange.endDate)} • klik Blok Maintenance`;
        openBlockDatesBtn.disabled = false;
    }

    function showAlert(element, message, type) {
        element.className = `calendar-inline-alert ${type}`;
        element.textContent = message;
        element.classList.remove('d-none');
    }

    function clearAlert(element) {
        element.className = 'calendar-inline-alert d-none';
        element.textContent = '';
    }

    function fillBlockForm(selection) {
        document.getElementById('block_vehicle_id').value = selection.vehicleId;
        document.getElementById('block_start_date').value = selection.startDate;
        document.getElementById('block_end_date').value = selection.endDate;
        if (!document.getElementById('block_reason').value) {
            document.getElementById('block_reason').value = 'Maintenance armada';
        }
    }

    function resetPricingForm() {
        pricingRuleForm.reset();
        document.getElementById('pricing_rule_id').value = '';
        document.getElementById('pricing_rule_action').value = 'save';
        document.getElementById('pricing_active').checked = true;
        clearAlert(pricingRulesAlert);
        updatePricingPreview();
    }

    function updatePricingPreview() {
        const vehicleSelect = document.getElementById('pricing_vehicle_id');
        const selectedOption = vehicleSelect.options[vehicleSelect.selectedIndex];
        const basePrice = Number(selectedOption?.dataset.basePrice || 0);
        const discount = Number(document.getElementById('pricing_discount_percentage').value || 0);

        if (!basePrice) {
            pricingPreviewText.textContent = 'Pilih kendaraan dan isi diskon untuk melihat estimasi harga setelah rule aktif.';
            return;
        }

        const discountedPrice = Math.round(basePrice * ((100 - discount) / 100));
        pricingPreviewText.textContent = `${formatMoney(basePrice)} menjadi sekitar ${formatMoney(discountedPrice)} per hari selama rule ini aktif.`;
    }

    function populatePricingForm(row) {
        document.getElementById('pricing_rule_id').value = row.dataset.pricingRuleId;
        document.getElementById('pricing_rule_action').value = 'save';
        document.getElementById('pricing_vehicle_id').value = row.dataset.vehicleId;
        document.getElementById('pricing_start_date').value = row.dataset.startDate;
        document.getElementById('pricing_end_date').value = row.dataset.endDate;
        document.getElementById('pricing_type').value = row.dataset.type;
        document.getElementById('pricing_discount_percentage').value = row.dataset.discount;
        document.getElementById('pricing_description').value = row.dataset.description || '';
        document.getElementById('pricing_active').checked = row.dataset.active === '1';
        updatePricingPreview();
    }

    async function postForm(formElement, url, successAlertElement) {
        clearAlert(successAlertElement);

        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: new FormData(formElement),
        });

        const payload = await response.json();

        if (!response.ok) {
            throw new Error(payload.message || 'Permintaan tidak dapat diproses.');
        }

        showAlert(successAlertElement, payload.message || 'Perubahan berhasil disimpan.', 'success');
        monthCache.clear();
        await refreshFleetBoard();
        return payload;
    }

    function openDetailModal(payload) {
        state.detailPayload = payload;
        const statusMeta = getStatusMeta(payload.status);
        detailVehicle.textContent = payload.vehicleName;
        detailDate.textContent = formatDateLabel(payload.date);
        detailStatus.textContent = statusMeta.label;
        detailPrice.textContent = formatMoney(payload.price || 0);
        detailReason.className = `calendar-inline-alert ${payload.status === 'booked' ? 'error' : 'success'}`;
        detailReason.textContent = payload.reason || 'Tidak ada catatan tambahan untuk tanggal ini.';

        if (payload.status === 'maintenance') {
            detailUnblockBtn.classList.remove('d-none');
        } else {
            detailUnblockBtn.classList.add('d-none');
        }

        fleetDetailModal.show();
    }

    async function refreshFleetBoard() {
        await ensureVisibleRangeData();
        renderSummaryCards();
        renderBoard();
    }

    document.querySelectorAll('[data-view-mode]').forEach((button) => {
        button.addEventListener('click', async () => {
            document.querySelectorAll('[data-view-mode]').forEach((viewButton) => viewButton.classList.remove('active'));
            button.classList.add('active');
            state.viewMode = button.dataset.viewMode;
            await refreshFleetBoard();
        });
    });

    document.getElementById('fleetPrevBtn').addEventListener('click', async () => {
        state.referenceDate = new Date(
            state.referenceDate.getFullYear(),
            state.referenceDate.getMonth() + (state.viewMode === 'month' ? -1 : 0),
            state.referenceDate.getDate() + (state.viewMode === 'week' ? -7 : 0)
        );
        await refreshFleetBoard();
    });

    document.getElementById('fleetNextBtn').addEventListener('click', async () => {
        state.referenceDate = new Date(
            state.referenceDate.getFullYear(),
            state.referenceDate.getMonth() + (state.viewMode === 'month' ? 1 : 0),
            state.referenceDate.getDate() + (state.viewMode === 'week' ? 7 : 0)
        );
        await refreshFleetBoard();
    });

    document.getElementById('fleetTodayBtn').addEventListener('click', async () => {
        state.referenceDate = new Date();
        await refreshFleetBoard();
    });

    openBlockDatesBtn.addEventListener('click', () => {
        if (!state.selectedRange) {
            return;
        }

        fillBlockForm(state.selectedRange);
        clearAlert(blockDatesAlert);
        blockDatesModal.show();
    });

    document.getElementById('openPricingRulesBtn').addEventListener('click', () => {
        resetPricingForm();
        pricingRulesModal.show();
    });

    fleetBoard.addEventListener('mousedown', (event) => {
        const cell = event.target.closest('.fleet-cell');

        if (!cell || cell.dataset.status === 'booked') {
            return;
        }

        state.drag = {
            vehicleId: Number(cell.dataset.vehicleId),
            vehicleName: cell.dataset.vehicleName,
            status: cell.dataset.status,
            previousSelection: state.selectedRange ? { ...state.selectedRange } : null,
            startDate: cell.dataset.date,
            endDate: cell.dataset.date,
            hasMoved: false,
        };
        state.selectedRange = {
            vehicleId: state.drag.vehicleId,
            vehicleName: state.drag.vehicleName,
            startDate: state.drag.startDate,
            endDate: state.drag.endDate,
        };
        renderBoard();
        event.preventDefault();
    });

    fleetBoard.addEventListener('mouseover', (event) => {
        const cell = event.target.closest('.fleet-cell');

        if (!state.drag || !cell || Number(cell.dataset.vehicleId) !== state.drag.vehicleId || cell.dataset.status === 'booked') {
            return;
        }

        state.drag.hasMoved = true;
        if (cell.dataset.date >= state.drag.startDate) {
            state.drag.endDate = cell.dataset.date;
            state.selectedRange = { ...state.drag };
            renderBoard();
        }
    });

    document.addEventListener('mouseup', () => {
        if (!state.drag) {
            return;
        }

        const finalizedRange = { ...state.drag };
        const shouldKeepSelection = finalizedRange.hasMoved || finalizedRange.status === 'available';
        state.drag = null;

        if (shouldKeepSelection) {
            state.selectedRange = {
                vehicleId: finalizedRange.vehicleId,
                vehicleName: finalizedRange.vehicleName,
                startDate: finalizedRange.startDate,
                endDate: finalizedRange.endDate,
            };
            fillBlockForm(finalizedRange);
        } else {
            state.selectedRange = finalizedRange.previousSelection ?? null;
        }

        if (finalizedRange.hasMoved) {
            state.suppressNextCellClick = true;
        }

        renderBoard();
    });

    fleetBoard.addEventListener('click', (event) => {
        const cell = event.target.closest('.fleet-cell');

        if (!cell || state.drag) {
            return;
        }

        if (state.suppressNextCellClick) {
            state.suppressNextCellClick = false;
            return;
        }

        if (cell.dataset.status === 'booked' || cell.dataset.status === 'maintenance') {
            openDetailModal({
                vehicleId: Number(cell.dataset.vehicleId),
                vehicleName: cell.dataset.vehicleName,
                date: cell.dataset.date,
                status: cell.dataset.status,
                price: Number(cell.dataset.price || 0),
                reason: cell.dataset.reason,
            });
        }
    });

    blockDatesForm.addEventListener('submit', async (event) => {
        event.preventDefault();

        try {
            await postForm(blockDatesForm, blockDatesUrl, blockDatesAlert);
            setTimeout(() => blockDatesModal.hide(), 700);
        } catch (error) {
            showAlert(blockDatesAlert, error.message, 'error');
        }
    });

    pricingRuleForm.addEventListener('submit', async (event) => {
        event.preventDefault();

        try {
            await postForm(pricingRuleForm, pricingRulesUrl, pricingRulesAlert);
            setTimeout(() => window.location.reload(), 700);
        } catch (error) {
            showAlert(pricingRulesAlert, error.message, 'error');
        }
    });

    pricingRuleResetBtn.addEventListener('click', resetPricingForm);
    document.getElementById('pricing_vehicle_id').addEventListener('change', updatePricingPreview);
    document.getElementById('pricing_discount_percentage').addEventListener('input', updatePricingPreview);

    document.getElementById('pricingRulesTable').addEventListener('click', async (event) => {
        const row = event.target.closest('tr[data-pricing-rule-id]');

        if (!row) {
            return;
        }

        if (event.target.classList.contains('pricing-edit-btn')) {
            populatePricingForm(row);
            return;
        }

        if (event.target.classList.contains('pricing-delete-btn')) {
            if (!confirm('Hapus aturan harga ini?')) {
                return;
            }

            const deleteForm = new FormData();
            deleteForm.append('pricing_rule_id', row.dataset.pricingRuleId);
            deleteForm.append('action', 'delete');

            try {
                const response = await fetch(pricingRulesUrl, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: deleteForm,
                });
                const payload = await response.json();

                if (!response.ok) {
                    throw new Error(payload.message || 'Rule gagal dihapus.');
                }

                window.location.reload();
            } catch (error) {
                showAlert(pricingRulesAlert, error.message, 'error');
            }
        }
    });

    detailUnblockBtn.addEventListener('click', async () => {
        if (!state.detailPayload) {
            return;
        }

        const formData = new FormData();
        formData.append('vehicle_id', state.detailPayload.vehicleId);
        formData.append('start_date', state.detailPayload.date);
        formData.append('end_date', state.detailPayload.date);

        try {
            const response = await fetch(unblockDatesUrl, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: formData,
            });
            const payload = await response.json();

            if (!response.ok) {
                throw new Error(payload.message || 'Blok maintenance gagal dibuka.');
            }

            fleetDetailModal.hide();
            monthCache.clear();
            await refreshFleetBoard();
        } catch (error) {
            detailReason.className = 'calendar-inline-alert error';
            detailReason.textContent = error.message;
        }
    });

    refreshFleetBoard();
    updatePricingPreview();
</script>
@endsection