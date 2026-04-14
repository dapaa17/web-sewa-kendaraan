@php($usesAdminLayout = (bool) auth()->user()?->isAdmin())
@extends($usesAdminLayout ? 'layouts.admin' : 'layouts.app')

@section('title', $vehicle->name)
@if($usesAdminLayout)
@section('page-title', 'Detail Kendaraan')
@endif

@section('content')
<style>
    .vehicle-hero {
        background: radial-gradient(circle at top right, rgba(255,255,255,0.16), transparent 34%), var(--gradient-brand);
        color: white;
        padding: 3rem 0 2.6rem;
        margin-bottom: 2rem;
        border-radius: 0 0 2rem 2rem;
    }
    .vehicle-shell {
        max-width: 1120px;
        margin: 0 auto;
        padding-bottom: 3rem;
    }
    .back-link {
        color: rgba(255,255,255,0.82);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.55rem;
        margin-bottom: 1rem;
        transition: color 0.3s ease;
    }
    .back-link:hover {
        color: white;
    }
    .vehicle-hero h1 {
        font-weight: 700;
        font-size: clamp(2.2rem, 5vw, 3.25rem);
        margin-bottom: 0.75rem;
    }
    .hero-meta {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
    }
    .meta-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.7rem 1rem;
        border-radius: 999px;
        background: rgba(255,255,255,0.12);
        border: 1px solid rgba(255,255,255,0.18);
        color: white;
        font-weight: 600;
        backdrop-filter: blur(10px);
    }
    .media-card,
    .panel-card,
    .description-card {
        background: white;
        border: 1px solid rgba(203,213,225,0.72);
        border-radius: 1.6rem;
        box-shadow: var(--shadow-card);
    }
    .media-card {
        padding: 0.75rem;
        overflow: hidden;
    }
    .vehicle-media img,
    .vehicle-placeholder {
        width: 100%;
        min-height: 480px;
        border-radius: 1.2rem;
        display: block;
    }
    .vehicle-media img {
        height: 480px;
        object-fit: cover;
    }
    .vehicle-placeholder {
        background: radial-gradient(circle at top, rgba(255,255,255,0.14), transparent 38%), var(--gradient-brand);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .vehicle-placeholder i {
        font-size: clamp(5rem, 18vw, 8rem);
        color: white;
        filter: drop-shadow(0 16px 28px rgba(15, 23, 42, 0.28));
    }
    .description-card {
        margin-top: 1.5rem;
        padding: 1.75rem;
    }
    .section-heading {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 1rem;
    }
    .section-heading .icon {
        width: 42px;
        height: 42px;
        border-radius: 0.9rem;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--gradient-brand);
        color: white;
        box-shadow: 0 14px 24px rgba(31, 41, 55, 0.18);
    }
    .section-heading h5 {
        margin: 0;
        font-weight: 700;
        color: #0f172a;
    }
    .description-copy {
        color: #475569;
        line-height: 1.8;
        white-space: pre-line;
        margin: 0;
    }
    .panel-card {
        padding: 1.75rem;
        position: sticky;
        top: 6rem;
    }
    .vehicle-badges {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
        margin-bottom: 1.2rem;
    }
    .vehicle-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.65rem 1rem;
        border-radius: 999px;
        font-weight: 600;
    }
    .vehicle-badge.type {
        background: rgba(var(--color-secondary-rgb), 0.14);
        color: var(--color-primary);
    }
    .vehicle-badge.available {
        background: #dcfce7;
        color: #166534;
    }
    .vehicle-badge.rented {
        background: #fef3c7;
        color: #92400e;
    }
    .vehicle-badge.maintenance {
        background: #e2e8f0;
        color: #334155;
    }
    .vehicle-title {
        font-weight: 700;
        color: #0f172a;
        font-size: 2rem;
        margin-bottom: 1.25rem;
    }
    .price-box {
        background: linear-gradient(135deg, rgba(31, 41, 55, 0.04) 0%, rgba(6, 182, 212, 0.14) 100%);
        border: 1px solid rgba(var(--color-secondary-rgb), 0.16);
        border-radius: 1.25rem;
        padding: 1.25rem 1.35rem;
        margin-bottom: 1.5rem;
    }
    .price-box .label {
        display: block;
        color: #64748b;
        font-size: 0.82rem;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        margin-bottom: 0.5rem;
    }
    .price-box .price {
        display: block;
        color: var(--color-primary);
        font-size: clamp(2.2rem, 5vw, 2.9rem);
        font-weight: 700;
        line-height: 1;
    }
    .price-box .caption {
        margin: 0.5rem 0 0;
        color: #475569;
    }
    .spec-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 0.9rem;
        margin-bottom: 1.5rem;
    }
    .spec-card {
        background: #f8fafc;
        border: 1px solid rgba(203,213,225,0.8);
        border-radius: 1rem;
        padding: 1rem;
    }
    .spec-card .label {
        display: block;
        color: #64748b;
        font-size: 0.76rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        margin-bottom: 0.35rem;
    }
    .spec-card .value {
        color: #0f172a;
        font-weight: 700;
    }
    .notice-card {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        border-radius: 1rem;
        padding: 1rem 1.1rem;
        margin-bottom: 1.25rem;
    }
    .notice-card i {
        font-size: 1.25rem;
        margin-top: 0.1rem;
    }
    .notice-card strong {
        display: block;
        margin-bottom: 0.2rem;
        color: #0f172a;
    }
    .notice-card p {
        margin: 0;
        color: #475569;
        font-size: 0.95rem;
    }
    .notice-card.warning {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
    }
    .notice-card.warning i {
        color: #b45309;
    }
    .notice-card.danger {
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
    }
    .notice-card.danger i {
        color: #dc2626;
    }
    .action-stack {
        display: grid;
        gap: 0.8rem;
    }
    .btn-booking,
    .btn-back {
        width: 100%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.65rem;
        padding: 1rem 1.25rem;
        border-radius: 1rem;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
    }
    .btn-booking {
        background: var(--color-primary);
        color: white;
        border: none;
    }
    .btn-booking:hover {
        background: var(--color-secondary);
        color: var(--color-primary);
        transform: translateY(-2px);
        box-shadow: 0 12px 26px rgba(var(--color-secondary-rgb), 0.26);
    }
    .btn-booking.disabled,
    .btn-booking:disabled {
        background: #cbd5e1;
        color: #475569;
        pointer-events: none;
    }
    .btn-back {
        background: white;
        border: 1px solid rgba(148,163,184,0.45);
        color: var(--color-primary);
    }
    .btn-back:hover {
        background: rgba(var(--color-secondary-rgb), 0.08);
        border-color: var(--color-secondary);
        color: var(--color-primary);
    }
    .review-score-card {
        background: linear-gradient(135deg, rgba(31, 41, 55, 0.04) 0%, rgba(6, 182, 212, 0.14) 100%);
        border: 1px solid rgba(var(--color-secondary-rgb), 0.16);
        border-radius: 1.25rem;
        padding: 1.2rem 1.3rem;
        margin-bottom: 1.5rem;
    }
    .review-score-card .label {
        display: block;
        color: #64748b;
        font-size: 0.82rem;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        margin-bottom: 0.45rem;
    }
    .review-score-head {
        display: flex;
        justify-content: space-between;
        gap: 1rem;
        align-items: flex-end;
        flex-wrap: wrap;
    }
    .review-score-value {
        font-size: clamp(2rem, 5vw, 2.75rem);
        font-weight: 700;
        color: #0f172a;
        line-height: 1;
    }
    .review-score-stars {
        margin-top: 0.45rem;
        font-size: 1rem;
    }
    .review-score-caption {
        color: #475569;
        max-width: 14rem;
        text-align: right;
    }
    .vehicle-review-section {
        margin-top: 1.75rem;
    }
    .review-insights-grid {
        display: grid;
        grid-template-columns: minmax(250px, 320px) minmax(0, 1fr);
        gap: 1.25rem;
    }
    .review-breakdown-card,
    .review-list-card {
        background: #f8fafc;
        border: 1px solid rgba(203,213,225,0.8);
        border-radius: 1.15rem;
        padding: 1.2rem;
    }
    .review-breakdown-card h6,
    .review-list-card h6 {
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 1rem;
    }
    .review-breakdown-row {
        display: grid;
        grid-template-columns: 52px minmax(0, 1fr) 34px;
        gap: 0.75rem;
        align-items: center;
        margin-bottom: 0.75rem;
        color: #475569;
    }
    .review-bar {
        height: 10px;
        border-radius: 999px;
        background: rgba(203,213,225,0.7);
        overflow: hidden;
    }
    .review-bar-fill {
        height: 100%;
        border-radius: inherit;
        background: var(--gradient-cyan);
    }
    .review-list-stack {
        display: grid;
        gap: 1rem;
    }
    .review-card {
        background: white;
        border: 1px solid rgba(203,213,225,0.72);
        border-radius: 1rem;
        padding: 1.1rem;
    }
    .review-card-head {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 1rem;
        flex-wrap: wrap;
        margin-bottom: 0.85rem;
    }
    .review-card-stars {
        font-size: 1rem;
        margin-bottom: 0.45rem;
    }
    .review-card-head h5 {
        margin: 0;
        font-size: 1.05rem;
        font-weight: 700;
        color: #0f172a;
    }
    .review-card-date,
    .review-card-author {
        color: #64748b;
        font-size: 0.9rem;
    }
    .review-card-author {
        margin-bottom: 0.85rem;
        font-weight: 600;
    }
    .review-card-body {
        color: #475569;
        line-height: 1.75;
        margin-bottom: 1rem;
    }
    .review-card-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 0.85rem;
        flex-wrap: wrap;
    }
    .review-helpful-static {
        color: #64748b;
        font-size: 0.92rem;
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
    }
    @media (max-width: 991.98px) {
        .panel-card {
            position: static;
        }
        .review-insights-grid {
            grid-template-columns: 1fr;
        }
    }
    @media (max-width: 767.98px) {
        .vehicle-hero {
            padding: 2.6rem 0 2.2rem;
            margin-bottom: 1.2rem;
            border-radius: 0 0 1.35rem 1.35rem;
        }
        .vehicle-shell {
            padding-inline: 1rem;
            padding-bottom: 2rem;
        }
        .vehicle-hero h1 {
            font-size: 1.45rem;
            line-height: 1.35;
            margin-bottom: 0.55rem;
        }
        .back-link {
            font-size: 0.84rem;
            margin-bottom: 0.7rem;
        }
        .meta-chip {
            width: 100%;
            justify-content: flex-start;
            font-size: 0.8rem;
            padding: 0.55rem 0.8rem;
        }
        .vehicle-media img,
        .vehicle-placeholder {
            min-height: 280px;
            height: 280px;
        }
        .hero-meta {
            flex-direction: column;
            align-items: flex-start;
        }
        .spec-grid {
            grid-template-columns: 1fr;
        }
        .media-card,
        .panel-card,
        .description-card {
            border-radius: 1rem;
        }
        .panel-card,
        .description-card {
            padding: 1rem;
        }
        .section-heading {
            gap: 0.6rem;
            margin-bottom: 0.75rem;
        }
        .section-heading .icon {
            width: 34px;
            height: 34px;
            border-radius: 0.65rem;
        }
        .section-heading h5 {
            font-size: 0.98rem;
        }
        .description-copy {
            font-size: 0.86rem;
            line-height: 1.65;
        }
        .vehicle-badge {
            font-size: 0.78rem;
            padding: 0.48rem 0.72rem;
        }
        .vehicle-title {
            font-size: 1.2rem;
            margin-bottom: 0.85rem;
        }
        .price-box {
            border-radius: 0.9rem;
            padding: 0.9rem;
            margin-bottom: 1rem;
        }
        .price-box .label {
            font-size: 0.72rem;
            margin-bottom: 0.4rem;
        }
        .price-box .price {
            font-size: 1.45rem;
        }
        .price-box .caption {
            font-size: 0.8rem;
        }
        .review-score-card,
        .spec-card,
        .notice-card {
            border-radius: 0.85rem;
            padding: 0.8rem;
        }
        .review-score-caption,
        .notice-card p {
            font-size: 0.82rem;
        }
        .btn-booking,
        .btn-back {
            min-height: 44px;
            border-radius: 0.85rem;
            font-size: 0.88rem;
            padding: 0.72rem 0.9rem;
        }
        .review-card-footer {
            flex-direction: column;
            align-items: flex-start;
        }
    }
    @media (max-width: 420px) {
        .vehicle-hero h1 {
            font-size: 1.22rem;
        }
        .vehicle-media img,
        .vehicle-placeholder {
            min-height: 230px;
            height: 230px;
        }
    }
</style>

<div class="vehicle-hero">
    <div class="container vehicle-shell">
        <a href="{{ route('vehicles.browse') }}" class="back-link">
            <i class="bi bi-arrow-left"></i> Kembali ke katalog kendaraan
        </a>
        <h1>{{ $vehicle->name }}</h1>
        <div class="hero-meta">
            <span class="meta-chip">
                <i class="bi bi-tag"></i>{{ $vehicle->plat_number }}
            </span>
            <span class="meta-chip">
                <i class="bi bi-gear"></i>{{ $vehicle->transmission }}
            </span>
            <span class="meta-chip">
                <i class="bi bi-calendar4"></i>{{ $vehicle->year }}
            </span>
        </div>
    </div>
</div>

<div class="container vehicle-shell">
    <div class="row g-4 align-items-start">
        <div class="col-lg-7">
            <div class="media-card">
                @if($vehicle->display_image_url)
                    <div class="vehicle-media">
                        <img src="{{ $vehicle->display_image_url }}" alt="{{ $vehicle->name }}">
                    </div>
                @else
                    <div class="vehicle-placeholder">
                        <i class="bi {{ $vehicle->vehicle_type === 'motor' ? 'bi-bicycle' : 'bi-car-front-fill' }}"></i>
                    </div>
                @endif
            </div>

            @if($vehicle->description)
                <div class="description-card">
                    <div class="section-heading">
                        <span class="icon"><i class="bi bi-card-text"></i></span>
                        <h5>Deskripsi Kendaraan</h5>
                    </div>
                    <p class="description-copy">{{ $vehicle->description }}</p>
                </div>
            @endif
        </div>

        <div class="col-lg-5">
            <div class="panel-card">
                <div class="vehicle-badges">
                    <span class="vehicle-badge type">
                        <i class="bi bi-grid"></i>{{ $vehicle->getTypeLabel() }}
                    </span>
                    @if($vehicle->current_rental_status === 'available')
                        <span class="vehicle-badge available">
                            <i class="bi bi-check-circle-fill"></i>Tersedia
                        </span>
                    @elseif($vehicle->current_rental_status === 'rented')
                        <span class="vehicle-badge rented">
                            <i class="bi bi-hourglass-split"></i>Sedang Disewa
                        </span>
                    @else
                        <span class="vehicle-badge maintenance">
                            <i class="bi bi-tools"></i>Maintenance
                        </span>
                    @endif
                </div>

                <h2 class="vehicle-title">{{ $vehicle->getTypeIcon() }} {{ $vehicle->name }}</h2>

                <div class="price-box">
                    <span class="label">Harga Sewa per Hari</span>
                    <span class="price">Rp{{ number_format($vehicle->daily_price, 0, ',', '.') }}</span>
                    <p class="caption">Tarif sudah siap dipakai untuk simulasi booking Anda.</p>
                </div>

                <div class="review-score-card">
                    <span class="label">Rating Pelanggan</span>
                    @if($vehicle->hasApprovedReviews())
                        <div class="review-score-head">
                            <div>
                                <div class="review-score-value">{{ number_format($vehicle->getAverageRatingValue(), 1, ',', '.') }}</div>
                                <div class="review-score-stars">{{ str_repeat('⭐', max(1, (int) round($vehicle->getAverageRatingValue()))) }}</div>
                            </div>
                            <div class="review-score-caption">
                                {{ $vehicle->getApprovedReviewCount() }} ulasan disetujui dari booking yang sudah selesai.
                            </div>
                        </div>
                    @else
                        <div class="review-score-head">
                            <div>
                                <div class="review-score-value">-</div>
                                <div class="review-score-stars">Belum ada rating</div>
                            </div>
                            <div class="review-score-caption">Jadi pelanggan pertama yang membagikan pengalaman sewa untuk unit ini.</div>
                        </div>
                    @endif
                </div>

                <div class="spec-grid">
                    <div class="spec-card">
                        <span class="label">Tipe Kendaraan</span>
                        <span class="value">{{ $vehicle->getTypeLabel() }}</span>
                    </div>
                    <div class="spec-card">
                        <span class="label">Nomor Plat</span>
                        <span class="value">{{ $vehicle->plat_number }}</span>
                    </div>
                    <div class="spec-card">
                        <span class="label">Tahun</span>
                        <span class="value">{{ $vehicle->year }}</span>
                    </div>
                    <div class="spec-card">
                        <span class="label">Transmisi</span>
                        <span class="value">{{ $vehicle->transmission }}</span>
                    </div>
                </div>

                @auth
                    @if($vehicle->current_rental_status === 'rented')
                        <div class="notice-card warning" role="alert">
                            <i class="bi bi-info-circle-fill"></i>
                            <div>
                                <strong>Kendaraan sedang dipakai pada jadwal lain</strong>
                                <p>Anda tetap bisa lanjut booking. Jika pembayaran diverifikasi saat kendaraan masih dipakai, booking Anda akan masuk antrean sampai kendaraan tersedia.</p>
                            </div>
                        </div>
                    @elseif($vehicle->status === 'maintenance')
                        <div class="notice-card danger" role="alert">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            <div>
                                <strong>Kendaraan belum bisa dipesan</strong>
                                <p>Status maintenance aktif sampai admin menandainya tersedia kembali.</p>
                            </div>
                        </div>
                    @endif

                    <div class="action-stack">
                        @if($vehicle->current_rental_status !== 'maintenance')
                            <a href="{{ route('vehicles.calendar', $vehicle) }}" class="btn-booking">
                                <i class="bi bi-calendar-check"></i> Lihat Kalender & Booking
                            </a>
                        @else
                            <button class="btn-booking disabled" disabled>
                                <i class="bi bi-x-circle"></i> Tidak Dapat Di-booking
                            </button>
                        @endif
                        <a href="{{ route('vehicles.browse') }}" class="btn-back">
                            <i class="bi bi-arrow-left"></i> Kembali ke Daftar
                        </a>
                    </div>
                @else
                    <div class="action-stack">
                        <a href="{{ route('login') }}" class="btn-booking">
                            <i class="bi bi-key"></i> Login untuk Booking
                        </a>
                        <a href="{{ route('vehicles.browse') }}" class="btn-back">
                            <i class="bi bi-arrow-left"></i> Kembali ke Daftar
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </div>

    <div class="vehicle-review-section">
        <div class="description-card">
            <div class="section-heading">
                <span class="icon"><i class="bi bi-chat-square-quote"></i></span>
                <h5>Ulasan Pelanggan</h5>
            </div>

            @if($vehicle->hasApprovedReviews())
                @php($totalApprovedReviews = max($vehicle->getApprovedReviewCount(), 1))

                <div class="review-insights-grid">
                    <div class="review-breakdown-card">
                        <h6>Distribusi Rating</h6>
                        @foreach($ratingBreakdown as $rating => $count)
                            @php($percentage = $totalApprovedReviews > 0 ? ($count / $totalApprovedReviews) * 100 : 0)
                            <div class="review-breakdown-row">
                                <span>{{ $rating }} ⭐</span>
                                <div class="review-bar">
                                    <div class="review-bar-fill" style="width: {{ number_format($percentage, 2, '.', '') }}%"></div>
                                </div>
                                <span>{{ $count }}</span>
                            </div>
                        @endforeach
                    </div>

                    <div class="review-list-card">
                        <h6>Review Terbaru</h6>
                        <div class="review-list-stack">
                            @foreach($approvedReviews as $review)
                                <article class="review-card">
                                    <div class="review-card-head">
                                        <div>
                                            <div class="review-card-stars">{{ $review->getStarsLabel() }}</div>
                                            <h5>{{ $review->title }}</h5>
                                        </div>
                                        <div class="review-card-date">{{ $review->created_at->format('d M Y') }}</div>
                                    </div>

                                    <div class="review-card-author">{{ $review->user->name }}</div>
                                    <p class="review-card-body">{{ $review->review_text }}</p>

                                    <div class="review-card-footer">
                                        <span class="review-helpful-static">
                                            <i class="bi bi-hand-thumbs-up"></i> Helpful {{ $review->helpful_count }}
                                        </span>

                                        @can('markHelpful', $review)
                                            <form method="POST" action="{{ route('reviews.helpful', $review) }}">
                                                @csrf
                                                <button type="submit" class="btn btn-sm {{ $helpfulReviewIds->contains($review->id) ? 'btn-primary' : 'btn-outline-primary' }} rounded-pill px-3">
                                                    <i class="bi {{ $helpfulReviewIds->contains($review->id) ? 'bi-hand-thumbs-up-fill' : 'bi-hand-thumbs-up' }} me-1"></i>
                                                    {{ $helpfulReviewIds->contains($review->id) ? 'Helpful dipilih' : 'Tandai Helpful' }}
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </div>
                </div>

                @if($approvedReviews->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $approvedReviews->links() }}
                    </div>
                @endif
            @else
                <p class="description-copy mb-0">Belum ada review yang disetujui untuk kendaraan ini. Review pertama akan muncul setelah customer menyelesaikan booking dan lolos moderasi admin.</p>
            @endif
        </div>
    </div>
</div>
@endsection