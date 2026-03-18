<div class="calendar-widget-card" id="dashboardCalendarWidget" data-api-url="{{ route('admin.api.fleet-availability', ['month' => now()->month, 'year' => now()->year]) }}" data-calendar-url="{{ route('admin.calendar.index') }}">
    <div class="calendar-widget-head">
        <div>
            <p class="eyebrow mb-1">Kalender Armada</p>
            <h3>Occupancy & Forecast Bulan Ini</h3>
        </div>
        <a href="{{ route('admin.calendar.index') }}" class="btn btn-sm btn-outline-primary">
            <i class="bi bi-calendar3 me-1"></i> Buka Kalender
        </a>
    </div>
    <div class="calendar-widget-grid">
        <div class="calendar-widget-stat">
            <span class="label">Rata-rata okupansi</span>
            <strong id="dashboardOccupancyValue">-</strong>
        </div>
        <div class="calendar-widget-stat">
            <span class="label">Forecast revenue</span>
            <strong id="dashboardRevenueValue">-</strong>
        </div>
    </div>
    <div class="calendar-widget-list" id="dashboardFleetSnapshot">
        <div class="text-muted small">Memuat ringkasan armada...</div>
    </div>
    <div class="calendar-widget-actions">
        <a href="{{ route('admin.calendar.index') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-wrench-adjustable me-1"></i> Kelola Maintenance
        </a>
        <a href="{{ route('admin.vehicles.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-car-front me-1"></i> Lihat Armada
        </a>
    </div>
</div>