@php($usesAdminLayout = (bool) auth()->user()?->isAdmin())
@extends($usesAdminLayout ? 'layouts.admin' : 'layouts.app')

@section('title', 'Cari Kendaraan')
@if($usesAdminLayout)
@section('page-title', 'Cari Kendaraan')
@endif

@section('content')
<style>
    .browse-header {
        background: radial-gradient(circle at top right, rgba(255,255,255,0.14), transparent 34%), var(--gradient-brand);
        color: white;
        padding: 3.15rem 0 2.75rem;
        margin-bottom: 2rem;
        border-radius: 0 0 2rem 2rem;
    }
    .browse-header h1 {
        font-weight: 700;
        margin-bottom: 0.6rem;
        letter-spacing: -0.055em;
    }
    .browse-header p {
        max-width: 34rem;
        line-height: 1.8;
    }
    .filter-card {
        background: white;
        border-radius: 1rem;
        padding: 1.7rem;
        box-shadow: var(--shadow-card);
        margin-bottom: 2rem;
        border: 1px solid rgba(203,213,225,0.65);
    }
    .filter-card .form-label {
        font-weight: 500;
        color: #4a5568;
        font-size: 0.85rem;
        margin-bottom: 0.5rem;
    }
    .filter-card .form-control,
    .filter-card .form-select {
        border-radius: 0.75rem;
        border: 2px solid #e2e8f0;
        padding: 0.6rem 1rem;
        transition: all 0.3s ease;
    }
    .filter-card .form-control:focus,
    .filter-card .form-select:focus {
        border-color: var(--color-secondary);
        box-shadow: 0 0 0 3px rgba(var(--color-secondary-rgb), 0.16);
    }
    .btn-filter {
        background: var(--color-primary);
        border: none;
        border-radius: 0.75rem;
        padding: 0.6rem 1.5rem;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    .btn-filter:hover {
        background: var(--color-secondary);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(var(--color-secondary-rgb), 0.24);
        color: var(--color-primary);
    }
    .btn-reset {
        border-radius: 0.75rem;
        padding: 0.6rem 1rem;
        border: 2px solid #e2e8f0;
        background: white;
        color: #718096;
        transition: all 0.3s ease;
    }
    .btn-reset:hover {
        background: rgba(var(--color-secondary-rgb), 0.08);
        border-color: var(--color-secondary);
        color: var(--color-primary);
    }
    .vehicle-card {
        background: white;
        border-radius: 1rem;
        overflow: hidden;
        box-shadow: var(--shadow-soft);
        transition: all 0.3s ease;
        border: 1px solid rgba(203,213,225,0.6);
        height: 100%;
    }
    .vehicle-card:hover {
        transform: translateY(-8px);
        box-shadow: var(--shadow-card-hover);
    }
    .vehicle-card .card-img-top {
        height: 180px;
        object-fit: cover;
        position: relative;
    }
    .vehicle-card .type-badge {
        position: absolute;
        top: 1rem;
        left: 1rem;
        background: white;
        padding: 0.4rem 0.8rem;
        border-radius: 2rem;
        font-size: 0.85rem;
        font-weight: 500;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    .vehicle-card .status-badge {
        position: absolute;
        top: 1rem;
        right: 1rem;
        padding: 0.4rem 0.8rem;
        border-radius: 2rem;
        font-size: 0.75rem;
        font-weight: 600;
    }
    .vehicle-card .card-body {
        padding: 1.4rem;
    }
    .vehicle-card .vehicle-name {
        font-weight: 600;
        color: #1a202c;
        font-size: 1.18rem;
        margin-bottom: 0.85rem;
        letter-spacing: -0.04em;
    }
    .vehicle-card .vehicle-info {
        display: flex;
        gap: 1rem;
        margin-bottom: 1rem;
        flex-wrap: wrap;
    }
    .vehicle-card .info-item {
        display: flex;
        align-items: center;
        gap: 0.3rem;
        color: #718096;
        font-size: 0.88rem;
    }
    .vehicle-card .info-item i {
        color: var(--color-secondary-strong);
    }
    .vehicle-card .review-strip {
        margin-bottom: 1rem;
        padding: 0.95rem 1rem;
        border-radius: 1rem;
        background: linear-gradient(135deg, rgba(31, 41, 55, 0.04) 0%, rgba(6, 182, 212, 0.12) 100%);
        border: 1px solid rgba(var(--color-secondary-rgb), 0.14);
    }
    .vehicle-card .review-strip-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 0.75rem;
        flex-wrap: wrap;
        margin-bottom: 0.55rem;
    }
    .vehicle-card .review-score-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.4rem 0.75rem;
        border-radius: 999px;
        background: white;
        color: #0f172a;
        font-weight: 700;
    }
    .vehicle-card .review-count-text {
        color: #475569;
        font-size: 0.86rem;
        font-weight: 600;
    }
    .vehicle-card .review-preview-title {
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 0.35rem;
        font-size: 0.92rem;
    }
    .vehicle-card .review-preview-text,
    .vehicle-card .review-empty {
        color: #64748b;
        font-size: 0.88rem;
        line-height: 1.65;
        margin: 0;
    }
    .vehicle-card .review-preview-meta {
        margin-top: 0.55rem;
        color: #475569;
        font-size: 0.82rem;
        font-weight: 600;
    }
    .vehicle-card .price-section {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 1rem;
        border-top: 1px solid #f1f5f9;
    }
    .vehicle-card .price {
        font-size: 1.45rem;
        font-weight: 700;
        color: var(--color-primary);
        letter-spacing: -0.05em;
    }
    .vehicle-card .price span {
        font-size: 0.85rem;
        font-weight: 400;
        color: #718096;
    }
    .vehicle-card .btn-detail {
        background: var(--color-primary);
        border: none;
        border-radius: 0.75rem;
        padding: 0.5rem 1rem;
        font-size: 0.85rem;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    .vehicle-card .btn-detail:hover {
        background: var(--color-secondary);
        transform: scale(1.05);
        box-shadow: 0 5px 15px rgba(var(--color-secondary-rgb), 0.24);
        color: var(--color-primary);
    }
    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        background: white;
        border-radius: 1rem;
        box-shadow: var(--shadow-card);
        border: 1px solid rgba(203,213,225,0.6);
    }
    .empty-state i {
        font-size: 4rem;
        color: #cbd5e0;
        margin-bottom: 1rem;
    }
    .empty-state h4 {
        color: #4a5568;
        margin-bottom: 0.5rem;
    }
    .empty-state p {
        color: #718096;
    }
    .results-info {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        gap: 1rem;
        flex-wrap: wrap;
    }
    .results-info .count {
        color: #718096;
        font-size: 0.9rem;
    }
    .results-info .count strong {
        color: #1a202c;
    }
    .type-tabs {
        display: flex;
        gap: 0.7rem;
        margin-bottom: 1.8rem;
        flex-wrap: wrap;
    }
    .type-tab {
        padding: 0.75rem 1.2rem;
        border-radius: 2rem;
        border: 2px solid #e2e8f0;
        background: white;
        color: #718096;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
        font-size: 0.9rem;
        box-shadow: 0 10px 22px rgba(15, 23, 42, 0.05);
    }
    .type-tab:hover {
        border-color: var(--color-secondary);
        color: var(--color-primary);
    }
    .type-tab.active {
        background: var(--gradient-brand);
        border-color: transparent;
        color: white;
    }
    .vehicle-placeholder-icon {
        font-size: 4rem;
        color: white;
        filter: drop-shadow(0 10px 20px rgba(15, 23, 42, 0.25));
    }

    /* Pagination Styling */
    .pagination-wrapper {
        display: flex;
        justify-content: center;
        margin-top: 2.5rem;
    }

    @media (max-width: 767.98px) {
        .browse-header {
            padding: 2.8rem 0 2.35rem;
            margin-bottom: 1.2rem;
            border-radius: 0 0 1.35rem 1.35rem;
        }

        .browse-header h1 {
            font-size: 1.45rem;
            line-height: 1.35;
        }

        .browse-header p {
            font-size: 0.86rem;
            line-height: 1.6;
        }

        .browse-body {
            padding-inline: 1rem;
        }

        .filter-card {
            padding: 1rem;
            border-radius: 0.95rem;
        }

        .filter-card form .col-md-1 {
            width: 100%;
            display: grid !important;
            grid-template-columns: 1fr 1fr;
            gap: 0.55rem !important;
        }

        .filter-card .form-label {
            font-size: 0.79rem;
        }

        .filter-card .form-control,
        .filter-card .form-select {
            min-height: 44px;
        }

        .btn-filter,
        .btn-reset,
        .vehicle-card .btn-detail {
            width: 100%;
            justify-content: center;
            min-height: 44px;
            display: inline-flex;
            align-items: center;
        }

        .results-info {
            margin-bottom: 1rem;
        }

        .results-info .count {
            width: 100%;
            font-size: 0.84rem;
        }

        .results-info > .d-flex {
            width: 100%;
            justify-content: space-between;
        }

        .vehicle-card .price-section {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
        }

        .vehicle-card .card-body {
            padding: 1rem;
        }

        .vehicle-card .vehicle-name {
            font-size: 1.03rem;
            margin-bottom: 0.65rem;
        }

        .vehicle-card .info-item,
        .vehicle-card .review-preview-text,
        .vehicle-card .review-empty {
            font-size: 0.82rem;
        }

        .vehicle-card .price {
            font-size: 1.22rem;
        }

        .vehicle-card .price-section .btn-detail {
            width: 100%;
        }

        .type-tab {
            flex: 1 1 calc(50% - 0.7rem);
            text-align: center;
        }
    }
    @media (max-width: 420px) {
        .type-tab {
            flex: 1 1 100%;
        }

        .filter-card form .col-md-1 {
            grid-template-columns: 1fr;
        }
    }
</style>

<!-- Browse Header -->
<div class="browse-header">
    <div class="container">
        <h1><i class="bi bi-search me-2"></i>Cari Kendaraan</h1>
        <p class="mb-0 opacity-75">Temukan mobil atau motor untuk perjalananmu 🚗🏍️</p>
    </div>
</div>

<div class="container pb-5 browse-body">
    <!-- Quick Type Tabs -->
    <div class="type-tabs">
        <a href="{{ route('vehicles.browse') }}" class="type-tab {{ !request('vehicle_type') ? 'active' : '' }}">
            📋 Semua
        </a>
        <a href="{{ route('vehicles.browse', ['vehicle_type' => 'mobil']) }}" class="type-tab {{ request('vehicle_type') == 'mobil' ? 'active' : '' }}">
            🚗 Mobil
        </a>
        <a href="{{ route('vehicles.browse', ['vehicle_type' => 'motor']) }}" class="type-tab {{ request('vehicle_type') == 'motor' ? 'active' : '' }}">
            🏍️ Motor
        </a>
    </div>

    <!-- Filter Section -->
    <div class="filter-card">
        <form method="GET" action="{{ route('vehicles.browse') }}" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label for="search" class="form-label">🔍 Cari Nama Kendaraan</label>
                <input type="text" class="form-control" id="search" name="search" 
                       value="{{ request('search') }}" placeholder="Honda Civic, Yamaha NMAX...">
            </div>

            <div class="col-md-2">
                <label for="vehicle_type" class="form-label">🚙 Tipe</label>
                <select class="form-select" id="vehicle_type" name="vehicle_type">
                    <option value="">Semua Tipe</option>
                    <option value="mobil" @if(request('vehicle_type') == 'mobil') selected @endif>🚗 Mobil</option>
                    <option value="motor" @if(request('vehicle_type') == 'motor') selected @endif>🏍️ Motor</option>
                </select>
            </div>

            <div class="col-md-2">
                <label for="transmission" class="form-label">⚙️ Transmisi</label>
                <select class="form-select" id="transmission" name="transmission">
                    <option value="">Semua</option>
                    <option value="Manual" @if(request('transmission') == 'Manual') selected @endif>Manual</option>
                    <option value="Otomatis" @if(request('transmission') == 'Otomatis') selected @endif>Otomatis</option>
                </select>
            </div>

            <div class="col-md-2">
                <label for="start_date" class="form-label">📅 Tanggal Mulai</label>
                <input type="date" class="form-control" id="start_date" name="start_date" 
                       value="{{ request('start_date') }}" min="{{ date('Y-m-d') }}">
            </div>

            <div class="col-md-2">
                <label for="end_date" class="form-label">📅 Tanggal Selesai</label>
                <input type="date" class="form-control" id="end_date" name="end_date" 
                       value="{{ request('end_date') }}" min="{{ date('Y-m-d') }}">
            </div>

            <div class="col-md-1 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-filter">
                    <i class="bi bi-search"></i>
                </button>
                @if(request('search') || request('transmission') || request('start_date') || request('end_date') || request('vehicle_type') || request('sort'))
                    <a href="{{ route('vehicles.browse') }}" class="btn btn-reset">
                        <i class="bi bi-x-lg"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Results Info -->
    @if($vehicles->count() > 0)
        <div class="results-info">
            <div class="count">
                Menampilkan <strong>{{ $vehicles->count() }}</strong> dari <strong>{{ $vehicles->total() }}</strong> kendaraan
                @if($hasAvailabilityFilter)
                    <div class="mt-2">
                        Bisa dibooking untuk tanggal <strong>{{ $selectedDateLabel }}</strong>
                    </div>
                @endif
            </div>
            <div class="d-flex align-items-center gap-2">
                <span style="color: #718096; font-size: 0.85rem; white-space: nowrap;">Urutkan:</span>
                <div class="dropdown">
                    <button class="btn btn-sm rounded-pill px-3 dropdown-toggle {{ request('sort') ? 'btn-primary' : 'btn-outline-secondary' }}" type="button" data-bs-toggle="dropdown" style="font-weight: 600; font-size: 0.85rem;">
                        @if(request('sort') === 'price_asc')
                            💰 Termurah
                        @elseif(request('sort') === 'price_desc')
                            💰 Termahal
                        @else
                            Harga
                        @endif
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item {{ request('sort') === 'price_asc' ? 'active' : '' }}" href="{{ route('vehicles.browse', array_merge(request()->except('sort', 'page'), ['sort' => 'price_asc'])) }}">
                                <i class="bi bi-sort-up me-2"></i>Harga Terendah
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item {{ request('sort') === 'price_desc' ? 'active' : '' }}" href="{{ route('vehicles.browse', array_merge(request()->except('sort', 'page'), ['sort' => 'price_desc'])) }}">
                                <i class="bi bi-sort-down me-2"></i>Harga Tertinggi
                            </a>
                        </li>
                        @if(request('sort'))
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="{{ route('vehicles.browse', request()->except('sort', 'page')) }}">
                                <i class="bi bi-x-circle me-2"></i>Reset Urutan
                            </a>
                        </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <!-- Vehicles Grid -->
    @if($vehicles->count() > 0)
        <div class="row g-4">
            @foreach($vehicles as $vehicle)
                <div class="col-md-6 col-lg-4">
                    <div class="vehicle-card">
                        <div class="position-relative">
                            @if($vehicle->display_image_url)
                                <img src="{{ $vehicle->display_image_url }}" 
                                     class="card-img-top" alt="{{ $vehicle->name }}">
                            @else
                                <div class="card-img-top d-flex align-items-center justify-content-center" style="height: 200px; background: var(--gradient-brand);">
                                    <i class="bi {{ $vehicle->vehicle_type === 'motor' ? 'bi-bicycle' : 'bi-car-front-fill' }} vehicle-placeholder-icon"></i>
                                </div>
                            @endif
                            
                            <span class="type-badge">
                                {{ $vehicle->getTypeIcon() }} {{ $vehicle->getTypeLabel() }}
                            </span>
                            
                            @if($hasAvailabilityFilter && !empty($vehicle->bookingAvailability['queue_available']))
                                <span class="status-badge bg-warning text-dark">⏳ Bisa Antre</span>
                            @elseif($hasAvailabilityFilter)
                                <span class="status-badge bg-success text-white">✓ Tersedia di tanggal ini</span>
                            @elseif($vehicle->current_rental_status == 'available')
                                <span class="status-badge bg-success text-white">✓ Tersedia</span>
                            @elseif($vehicle->current_rental_status == 'rented')
                                <span class="status-badge bg-warning text-dark">⏳ Disewa</span>
                            @else
                                <span class="status-badge bg-secondary text-white">🔧 Maintenance</span>
                            @endif
                        </div>
                        
                        <div class="card-body">
                            <h5 class="vehicle-name">{{ $vehicle->name }}</h5>
                            
                            <div class="vehicle-info">
                                <span class="info-item">
                                    <i class="bi bi-calendar3"></i> {{ $vehicle->year }}
                                </span>
                                <span class="info-item">
                                    <i class="bi bi-gear-fill"></i> {{ $vehicle->transmission }}
                                </span>
                                <span class="info-item">
                                    <i class="bi bi-card-text"></i> {{ $vehicle->plat_number }}
                                </span>
                            </div>

                            <div class="review-strip">
                                @if($vehicle->hasApprovedReviews())
                                    <div class="review-strip-head">
                                        <span class="review-score-pill">⭐ {{ number_format($vehicle->getAverageRatingValue(), 1, ',', '.') }}</span>
                                        <span class="review-count-text">{{ $vehicle->getApprovedReviewCount() }} ulasan</span>
                                    </div>

                                    @if($vehicle->topApprovedReview)
                                        <div class="review-preview-title">{{ $vehicle->topApprovedReview->title }}</div>
                                        <p class="review-preview-text">{{ \Illuminate\Support\Str::limit($vehicle->topApprovedReview->review_text, 90) }}</p>
                                        <div class="review-preview-meta">{{ $vehicle->topApprovedReview->getStarsLabel() }} · {{ $vehicle->topApprovedReview->user->name }}</div>
                                    @endif
                                @else
                                    <p class="review-empty">Belum ada ulasan pelanggan untuk kendaraan ini.</p>
                                @endif
                            </div>
                            
                            <div class="price-section">
                                <div class="price">
                                    Rp{{ number_format($vehicle->daily_price, 0, ',', '.') }}
                                    <span>/hari</span>
                                </div>
                                <a href="{{ route('vehicles.show', $vehicle) }}" class="btn btn-primary btn-detail">
                                    <i class="bi bi-eye me-1"></i> Detail
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="pagination-wrapper">
            {{ $vehicles->withQueryString()->links('vendor.pagination.custom') }}
        </div>
    @else
        <div class="empty-state">
            <i class="bi bi-emoji-frown"></i>
            <h4>Tidak Ada Kendaraan Ditemukan</h4>
            <p>
                @if($hasAvailabilityFilter)
                    Tidak ada kendaraan yang bisa dibooking atau masuk antrean untuk tanggal {{ $selectedDateLabel }}. Coba ubah rentang tanggal atau reset filter.
                @else
                    Coba ubah filter pencarian atau reset untuk melihat semua kendaraan.
                @endif
            </p>
            <a href="{{ route('vehicles.browse') }}" class="btn btn-primary btn-filter mt-3">
                <i class="bi bi-arrow-clockwise me-2"></i>Reset Filter
            </a>
        </div>
    @endif
</div>

<script>
    // Update end_date min value when start_date changes
    document.getElementById('start_date').addEventListener('change', function() {
        const endDate = document.getElementById('end_date');
        endDate.min = this.value;
        if (endDate.value && endDate.value < this.value) {
            endDate.value = this.value;
        }
    });
</script>
@endsection
