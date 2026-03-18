@extends('layouts.admin')

@section('title', 'Moderasi Ulasan')
@section('page-title', 'Ulasan')

@section('content')
<style>
    .review-admin-header {
        background: radial-gradient(circle at top right, rgba(255,255,255,0.16), transparent 34%), var(--gradient-brand);
        color: white;
        padding: 2.8rem 0 2.35rem;
        margin-bottom: 2rem;
        border-radius: 0 0 2rem 2rem;
    }
    .review-admin-shell {
        max-width: 1180px;
        margin: 0 auto;
        padding-bottom: 3rem;
    }
    .review-admin-kpis {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    .review-admin-kpi,
    .review-filter-card,
    .admin-review-card {
        background: rgba(255,255,255,0.96);
        border: 1px solid rgba(203,213,225,0.75);
        box-shadow: var(--shadow-card);
    }
    .review-admin-kpi {
        border-radius: 1.25rem;
        padding: 1.25rem;
    }
    .review-admin-kpi .label {
        display: block;
        color: #64748b;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        margin-bottom: 0.45rem;
    }
    .review-admin-kpi .value {
        font-family: var(--font-display);
        font-size: 1.9rem;
        font-weight: 700;
        color: #0f172a;
    }
    .review-filter-card,
    .admin-review-card {
        border-radius: 1.5rem;
        padding: 1.5rem;
    }
    .review-filter-tabs {
        display: flex;
        gap: 0.7rem;
        flex-wrap: wrap;
        margin-top: 1rem;
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
    .admin-review-card {
        margin-bottom: 1rem;
    }
    .admin-review-head,
    .admin-review-meta,
    .admin-review-actions,
    .moderation-grid {
        display: flex;
        gap: 0.9rem;
        flex-wrap: wrap;
    }
    .admin-review-head {
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1rem;
    }
    .admin-review-meta {
        color: #64748b;
        font-size: 0.92rem;
        margin-bottom: 1rem;
    }
    .admin-review-note {
        margin-top: 1rem;
        padding: 1rem;
        border-radius: 1rem;
        background: #f8fafc;
        border: 1px solid rgba(203,213,225,0.9);
    }
    .moderation-grid {
        margin-top: 1rem;
        align-items: stretch;
    }
    .moderation-card {
        flex: 1 1 320px;
        background: #f8fafc;
        border: 1px solid rgba(203,213,225,0.85);
        border-radius: 1rem;
        padding: 1rem;
    }
    .moderation-card textarea,
    .review-filter-card .form-control,
    .review-filter-card .form-select {
        border-radius: 0.9rem;
        border: 1px solid rgba(148,163,184,0.45);
    }
    .moderation-card textarea:focus,
    .review-filter-card .form-control:focus,
    .review-filter-card .form-select:focus {
        border-color: var(--color-secondary);
        box-shadow: 0 0 0 0.25rem rgba(var(--color-secondary-rgb), 0.16);
    }
    .admin-review-actions {
        margin-top: 1rem;
        align-items: center;
    }
    .admin-review-empty {
        text-align: center;
        padding: 3rem 1.5rem;
        color: #64748b;
    }
    .admin-review-empty i {
        font-size: 3.6rem;
        color: #cbd5e1;
        margin-bottom: 1rem;
    }
    @media (max-width: 991.98px) {
        .review-admin-kpis {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }
    @media (max-width: 767.98px) {
        .review-admin-kpis {
            grid-template-columns: 1fr;
        }
        .review-filter-card .btn {
            width: 100%;
        }
    }
</style>

<div class="review-admin-header">
    <div class="container review-admin-shell">
        <h1 class="mb-2"><i class="bi bi-star-half me-2"></i>Moderasi Ulasan</h1>
        <p class="mb-0 opacity-75">Kelola review yang masuk, setujui yang layak tampil, dan tolak yang perlu diperbaiki customer.</p>
    </div>
</div>

<div class="container review-admin-shell">
    <div class="review-admin-kpis">
        <div class="review-admin-kpi"><span class="label">Total Review</span><span class="value">{{ $counts['all'] }}</span></div>
        <div class="review-admin-kpi"><span class="label">Pending</span><span class="value">{{ $counts['pending'] }}</span></div>
        <div class="review-admin-kpi"><span class="label">Disetujui</span><span class="value">{{ $counts['approved'] }}</span></div>
        <div class="review-admin-kpi"><span class="label">Ditolak</span><span class="value">{{ $counts['rejected'] }}</span></div>
    </div>

    <div class="review-filter-card mb-4">
        <form method="GET" action="{{ route('admin.reviews.index') }}" class="row g-3 align-items-end">
            <div class="col-lg-6">
                <label class="form-label fw-semibold" for="search">Cari review, customer, atau kendaraan</label>
                <input type="text" id="search" name="search" class="form-control" value="{{ $search }}" placeholder="Contoh: Avanza, Bagus, Fajar">
            </div>
            <div class="col-lg-3">
                <label class="form-label fw-semibold" for="rating">Filter rating</label>
                <select id="rating" name="rating" class="form-select">
                    <option value="">Semua rating</option>
                    @for($star = 5; $star >= 1; $star--)
                        <option value="{{ $star }}" @selected((string) $rating === (string) $star)>{{ $star }} bintang</option>
                    @endfor
                </select>
            </div>
            <div class="col-lg-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1"><i class="bi bi-search me-2"></i>Filter</button>
                <a href="{{ route('admin.reviews.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-clockwise"></i></a>
            </div>
        </form>

        <div class="review-filter-tabs">
            <a href="{{ route('admin.reviews.index', array_filter(['search' => $search, 'rating' => $rating])) }}" class="review-filter-tab {{ $status === 'all' ? 'active' : '' }}">Semua <span>{{ $counts['all'] }}</span></a>
            <a href="{{ route('admin.reviews.index', array_filter(['status' => 'pending', 'search' => $search, 'rating' => $rating])) }}" class="review-filter-tab {{ $status === 'pending' ? 'active' : '' }}">Pending <span>{{ $counts['pending'] }}</span></a>
            <a href="{{ route('admin.reviews.index', array_filter(['status' => 'approved', 'search' => $search, 'rating' => $rating])) }}" class="review-filter-tab {{ $status === 'approved' ? 'active' : '' }}">Disetujui <span>{{ $counts['approved'] }}</span></a>
            <a href="{{ route('admin.reviews.index', array_filter(['status' => 'rejected', 'search' => $search, 'rating' => $rating])) }}" class="review-filter-tab {{ $status === 'rejected' ? 'active' : '' }}">Ditolak <span>{{ $counts['rejected'] }}</span></a>
        </div>
    </div>

    @forelse($reviews as $review)
        <div class="admin-review-card">
            <div class="admin-review-head">
                <div>
                    <span class="badge {{ $review->getStatusBadgeClass() }} rounded-pill px-3 py-2">{{ $review->getStatusLabel() }}</span>
                    <div class="mt-3 fw-semibold" style="font-size:1.1rem">{{ $review->getStarsLabel() }}</div>
                    <h4 class="mt-2 mb-1">{{ $review->title }}</h4>
                </div>
                <div class="text-muted small text-end">
                    <div>Dikirim {{ $review->created_at->format('d M Y H:i') }}</div>
                    @if($review->moderated_at)
                        <div>Dimoderasi {{ $review->moderated_at->format('d M Y H:i') }}</div>
                    @endif
                </div>
            </div>

            <div class="admin-review-meta">
                <span><i class="bi bi-person me-1"></i>{{ $review->user->name }}</span>
                <span><i class="bi bi-car-front me-1"></i>{{ $review->vehicle->name }}</span>
                <span><i class="bi bi-receipt me-1"></i>Booking #{{ $review->booking->id }}</span>
                <span><i class="bi bi-hand-thumbs-up me-1"></i>Helpful {{ $review->helpful_count }}</span>
            </div>

            <p class="mb-0 text-muted">{{ $review->review_text }}</p>

            @if($review->admin_note)
                <div class="admin-review-note">
                    <strong class="d-block mb-1">Catatan Moderasi</strong>
                    <div>{{ $review->admin_note }}</div>
                    @if($review->moderator)
                        <div class="small text-muted mt-2">Oleh {{ $review->moderator->name }}</div>
                    @endif
                </div>
            @endif

            @if($review->isPending())
                <div class="moderation-grid">
                    <div class="moderation-card">
                        <h6 class="fw-bold mb-3">Setujui Review</h6>
                        <form method="POST" action="{{ route('admin.reviews.approve', $review) }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Catatan Admin Opsional</label>
                                <textarea name="admin_note" rows="3" class="form-control" placeholder="Opsional, misalnya: detail pengalaman sudah jelas."></textarea>
                            </div>
                            <button type="submit" class="btn btn-success w-100">
                                <i class="bi bi-check2-circle me-2"></i>Setujui & Tampilkan
                            </button>
                        </form>
                    </div>
                    <div class="moderation-card">
                        <h6 class="fw-bold mb-3">Tolak Review</h6>
                        <form method="POST" action="{{ route('admin.reviews.reject', $review) }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Alasan Penolakan</label>
                                <textarea name="admin_note" rows="3" class="form-control" placeholder="Jelaskan bagian yang perlu diperbaiki customer." required></textarea>
                            </div>
                            <button type="submit" class="btn btn-outline-danger w-100">
                                <i class="bi bi-x-octagon me-2"></i>Tolak Review
                            </button>
                        </form>
                    </div>
                </div>
            @endif

            <div class="admin-review-actions">
                <a href="{{ route('vehicles.show', $review->vehicle) }}" class="btn btn-outline-secondary">
                    <i class="bi bi-car-front me-2"></i>Lihat Kendaraan
                </a>
                <a href="{{ route('bookings.show', $review->booking) }}" class="btn btn-outline-secondary">
                    <i class="bi bi-eye me-2"></i>Detail Booking
                </a>
                <form method="POST" action="{{ route('admin.reviews.destroy', $review) }}" onsubmit="return confirm('Hapus review ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger">
                        <i class="bi bi-trash3 me-2"></i>Hapus Review
                    </button>
                </form>
            </div>
        </div>
    @empty
        <div class="review-filter-card admin-review-empty">
            <i class="bi bi-inboxes-fill"></i>
            <h5 class="text-dark mb-2">Belum ada review untuk ditampilkan</h5>
            <p class="mb-0">Coba ubah filter atau tunggu customer mengirim review baru.</p>
        </div>
    @endforelse

    @if($reviews->hasPages())
        <div class="mt-4 d-flex justify-content-center">
            {{ $reviews->links() }}
        </div>
    @endif
</div>
@endsection