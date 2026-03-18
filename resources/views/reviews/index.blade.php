@extends('layouts.app')

@section('title', 'Ulasan Saya')

@section('content')
<style>
    .review-index-header {
        background: radial-gradient(circle at top right, rgba(255,255,255,0.16), transparent 34%), var(--gradient-brand);
        color: white;
        padding: 3rem 0 2.5rem;
        margin-bottom: 2rem;
        border-radius: 0 0 2rem 2rem;
    }
    .review-index-shell {
        max-width: 1120px;
        margin: 0 auto;
        padding-bottom: 3rem;
    }
    .review-kpis {
        display: grid;
        grid-template-columns: repeat(5, minmax(0, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    .review-kpi {
        background: rgba(255,255,255,0.96);
        border: 1px solid rgba(203,213,225,0.75);
        border-radius: 1.25rem;
        padding: 1.25rem;
        box-shadow: var(--shadow-soft);
    }
    .review-kpi .label {
        display: block;
        color: #64748b;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        margin-bottom: 0.45rem;
    }
    .review-kpi .value {
        font-family: var(--font-display);
        font-size: 1.9rem;
        font-weight: 700;
        color: #0f172a;
        line-height: 1;
    }
    .review-section-card,
    .my-review-card,
    .eligible-review-card {
        background: rgba(255,255,255,0.96);
        border: 1px solid rgba(203,213,225,0.75);
        border-radius: 1.5rem;
        box-shadow: var(--shadow-card);
    }
    .review-section-card {
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }
    .review-filter-tabs {
        display: flex;
        gap: 0.7rem;
        flex-wrap: wrap;
        margin-bottom: 1.4rem;
    }
    .review-filter-tab {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.75rem 1rem;
        border-radius: 999px;
        text-decoration: none;
        border: 1px solid rgba(203,213,225,0.78);
        color: #475569;
        background: white;
        font-weight: 600;
    }
    .review-filter-tab.active {
        background: var(--gradient-brand);
        border-color: transparent;
        color: white;
    }
    .eligible-review-card {
        padding: 1.35rem;
        height: 100%;
    }
    .eligible-review-card .meta {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.42rem 0.8rem;
        border-radius: 999px;
        background: rgba(var(--color-secondary-rgb), 0.12);
        color: var(--color-primary);
        font-weight: 600;
        font-size: 0.86rem;
        margin-bottom: 0.85rem;
    }
    .eligible-review-actions,
    .my-review-actions {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
        margin-top: 1rem;
    }
    .eligible-review-actions .btn,
    .my-review-actions .btn {
        border-radius: 0.9rem;
    }
    .my-review-card {
        padding: 1.5rem;
        margin-bottom: 1rem;
    }
    .my-review-head {
        display: flex;
        justify-content: space-between;
        gap: 1rem;
        align-items: flex-start;
        flex-wrap: wrap;
        margin-bottom: 1rem;
    }
    .my-review-title {
        font-size: 1.2rem;
        font-weight: 700;
        color: #0f172a;
        margin: 0.4rem 0 0.25rem;
    }
    .my-review-meta {
        display: flex;
        gap: 0.65rem;
        flex-wrap: wrap;
        color: #64748b;
        font-size: 0.92rem;
    }
    .my-review-note {
        margin-top: 1rem;
        padding: 1rem;
        border-radius: 1rem;
        background: #fff7ed;
        border: 1px solid #fdba74;
        color: #9a3412;
    }
    .review-empty-state {
        text-align: center;
        padding: 3rem 1.5rem;
        color: #64748b;
    }
    .review-empty-state i {
        font-size: 3.5rem;
        color: #cbd5e1;
        margin-bottom: 1rem;
    }
    @media (max-width: 991.98px) {
        .review-kpis {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }
    @media (max-width: 767.98px) {
        .review-kpis {
            grid-template-columns: 1fr;
        }
        .eligible-review-actions .btn,
        .my-review-actions .btn {
            width: 100%;
        }
    }
</style>

<div class="review-index-header">
    <div class="container review-index-shell">
        <h1 class="mb-2"><i class="bi bi-stars me-2"></i>Ulasan Saya</h1>
        <p class="mb-0 opacity-75">Lihat review yang sudah Anda kirim, status moderasinya, dan booking selesai yang siap diulas.</p>
    </div>
</div>

<div class="container review-index-shell">
    <div class="review-kpis">
        <div class="review-kpi"><span class="label">Total Review</span><span class="value">{{ $counts['all'] }}</span></div>
        <div class="review-kpi"><span class="label">Pending</span><span class="value">{{ $counts['pending'] }}</span></div>
        <div class="review-kpi"><span class="label">Disetujui</span><span class="value">{{ $counts['approved'] }}</span></div>
        <div class="review-kpi"><span class="label">Ditolak</span><span class="value">{{ $counts['rejected'] }}</span></div>
        <div class="review-kpi"><span class="label">Siap Diulas</span><span class="value">{{ $counts['eligible'] }}</span></div>
    </div>

    <div class="review-section-card">
        <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap mb-3">
            <div>
                <h4 class="mb-1">Booking Siap Diulas</h4>
                <p class="mb-0 text-muted">Hanya booking selesai dan lunas yang bisa diberi review.</p>
            </div>
            <a href="{{ route('bookings.index', ['status' => 'completed']) }}" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="bi bi-receipt me-2"></i>Lihat Booking Selesai
            </a>
        </div>

        @if($eligibleBookings->isNotEmpty())
            <div class="row g-4">
                @foreach($eligibleBookings as $booking)
                    <div class="col-lg-4 col-md-6">
                        <div class="eligible-review-card">
                            <span class="meta"><i class="bi bi-check2-circle"></i>Selesai & Lunas</span>
                            <h5 class="fw-bold mb-2">{{ $booking->vehicle->getTypeIcon() }} {{ $booking->vehicle->name }}</h5>
                            <p class="text-muted mb-2">Booking #{{ $booking->id }}</p>
                            <p class="mb-0 text-muted">{{ $booking->start_date->format('d M Y') }} {{ $booking->pickup_time_label }} - {{ $booking->end_date->format('d M Y') }} {{ $booking->return_time_label }}</p>

                            <div class="eligible-review-actions">
                                <a href="{{ route('reviews.create', $booking) }}" class="btn btn-primary">
                                    <i class="bi bi-star-fill me-2"></i>Tulis Review
                                </a>
                                <a href="{{ route('bookings.show', $booking) }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-eye me-2"></i>Detail Booking
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="review-empty-state">
                <i class="bi bi-chat-square-heart"></i>
                <h5 class="text-dark mb-2">Belum ada booking yang siap diulas</h5>
                <p class="mb-0">Setelah rental selesai dan pembayaran lunas, booking akan muncul di sini.</p>
            </div>
        @endif
    </div>

    <div class="review-section-card">
        <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap mb-3">
            <div>
                <h4 class="mb-1">Riwayat Review</h4>
                <p class="mb-0 text-muted">Anda bisa edit atau hapus review sendiri selama belum disetujui admin.</p>
            </div>
        </div>

        <div class="review-filter-tabs">
            <a href="{{ route('reviews.index') }}" class="review-filter-tab {{ $status === 'all' ? 'active' : '' }}">Semua <span>{{ $counts['all'] }}</span></a>
            <a href="{{ route('reviews.index', ['status' => 'pending']) }}" class="review-filter-tab {{ $status === 'pending' ? 'active' : '' }}">Pending <span>{{ $counts['pending'] }}</span></a>
            <a href="{{ route('reviews.index', ['status' => 'approved']) }}" class="review-filter-tab {{ $status === 'approved' ? 'active' : '' }}">Disetujui <span>{{ $counts['approved'] }}</span></a>
            <a href="{{ route('reviews.index', ['status' => 'rejected']) }}" class="review-filter-tab {{ $status === 'rejected' ? 'active' : '' }}">Ditolak <span>{{ $counts['rejected'] }}</span></a>
        </div>

        @forelse($reviews as $review)
            <div class="my-review-card">
                <div class="my-review-head">
                    <div>
                        <span class="badge {{ $review->getStatusBadgeClass() }} rounded-pill px-3 py-2">{{ $review->getStatusLabel() }}</span>
                        <div class="mt-3 fw-semibold" style="font-size:1.1rem">{{ $review->getStarsLabel() }}</div>
                        <h5 class="my-review-title">{{ $review->title }}</h5>
                    </div>
                    <div class="text-muted small">{{ $review->created_at->format('d M Y H:i') }}</div>
                </div>

                <div class="my-review-meta mb-3">
                    <span><i class="bi bi-car-front me-1"></i>{{ $review->vehicle->name }}</span>
                    <span><i class="bi bi-receipt me-1"></i>Booking #{{ $review->booking->id }}</span>
                    <span><i class="bi bi-hand-thumbs-up me-1"></i>Helpful {{ $review->helpful_count }}</span>
                </div>

                <p class="mb-0 text-muted">{{ $review->review_text }}</p>

                @if($review->admin_note)
                    <div class="my-review-note">
                        <strong class="d-block mb-1">Catatan Admin</strong>
                        <div>{{ $review->admin_note }}</div>
                    </div>
                @endif

                <div class="my-review-actions">
                    <a href="{{ route('vehicles.show', $review->vehicle) }}" class="btn btn-outline-secondary">
                        <i class="bi bi-car-front me-2"></i>Lihat Kendaraan
                    </a>
                    <a href="{{ route('bookings.show', $review->booking) }}" class="btn btn-outline-secondary">
                        <i class="bi bi-eye me-2"></i>Detail Booking
                    </a>
                    @if($review->canBeManagedByOwner())
                        <a href="{{ route('reviews.edit', $review) }}" class="btn btn-primary">
                            <i class="bi bi-pencil-square me-2"></i>Edit
                        </a>
                        <form method="POST" action="{{ route('reviews.destroy', $review) }}" onsubmit="return confirm('Hapus review ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger">
                                <i class="bi bi-trash3 me-2"></i>Hapus
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <div class="review-empty-state">
                <i class="bi bi-chat-square-text"></i>
                <h5 class="text-dark mb-2">Belum ada review pada filter ini</h5>
                <p class="mb-0">Mulai dari booking yang sudah selesai untuk menulis review pertama Anda.</p>
            </div>
        @endforelse

        @if($reviews->hasPages())
            <div class="mt-4 d-flex justify-content-center">
                {{ $reviews->links() }}
            </div>
        @endif
    </div>
</div>
@endsection