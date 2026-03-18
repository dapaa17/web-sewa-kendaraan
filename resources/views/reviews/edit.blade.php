@extends('layouts.app')

@section('title', 'Edit Review')

@section('content')
<style>
    .review-page-header {
        background: radial-gradient(circle at top right, rgba(255,255,255,0.16), transparent 34%), var(--gradient-brand);
        color: white;
        padding: 3rem 0 2.4rem;
        margin-bottom: 2rem;
        border-radius: 0 0 2rem 2rem;
    }
    .review-page-shell {
        max-width: 980px;
        margin: 0 auto;
        padding-bottom: 3rem;
    }
    .review-back-link {
        color: rgba(255,255,255,0.82);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.55rem;
        margin-bottom: 1rem;
    }
    .review-back-link:hover {
        color: white;
    }
    .review-summary-card,
    .review-state-card {
        background: rgba(255,255,255,0.96);
        border: 1px solid rgba(203,213,225,0.78);
        border-radius: 1.5rem;
        box-shadow: var(--shadow-card);
        padding: 1.6rem;
        margin-bottom: 1.5rem;
    }
    .review-form-actions {
        display: flex;
        gap: 0.85rem;
        flex-wrap: wrap;
        margin-top: 1.5rem;
    }
    .review-form-actions .btn {
        min-width: 180px;
        border-radius: 1rem;
        padding: 0.9rem 1.2rem;
    }
    @media (max-width: 767.98px) {
        .review-form-actions .btn {
            width: 100%;
        }
    }
</style>

<div class="review-page-header">
    <div class="container review-page-shell">
        <a href="{{ route('reviews.index') }}" class="review-back-link">
            <i class="bi bi-arrow-left"></i> Kembali ke ulasan saya
        </a>
        <h1 class="mb-2"><i class="bi bi-pencil-square me-2"></i>Edit Review</h1>
        <p class="mb-0 opacity-75">Perbarui review Anda. Setelah disimpan, review akan kembali masuk antrean moderasi admin.</p>
    </div>
</div>

<div class="container review-page-shell">
    <div class="review-summary-card">
        <div class="d-flex align-items-start justify-content-between gap-3 flex-wrap">
            <div>
                <div class="text-uppercase small text-muted mb-2">Booking #{{ $review->booking->id }}</div>
                <h3 class="mb-2">{{ $review->vehicle->getTypeIcon() }} {{ $review->vehicle->name }}</h3>
                <p class="mb-0 text-muted">Review saat ini berstatus <strong>{{ strtolower($review->getStatusLabel()) }}</strong>.</p>
            </div>
            <span class="badge {{ $review->getStatusBadgeClass() }} rounded-pill px-3 py-2">{{ $review->getStatusLabel() }}</span>
        </div>
    </div>

    @if($review->admin_note)
        <div class="review-state-card">
            <div class="d-flex align-items-start gap-3">
                <i class="bi {{ $review->isRejected() ? 'bi-exclamation-octagon-fill text-danger' : 'bi-info-circle-fill text-info' }} fs-4"></i>
                <div>
                    <strong class="d-block mb-1">Catatan Admin</strong>
                    <p class="mb-0 text-muted">{{ $review->admin_note }}</p>
                </div>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('reviews.update', $review) }}">
        @csrf
        @method('PUT')
        @include('reviews.partials.form', ['review' => $review])

        <div class="review-form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save2-fill me-2"></i>Simpan Perubahan
            </button>
            <a href="{{ route('reviews.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-x-lg me-2"></i>Batal
            </a>
        </div>
    </form>
</div>
@endsection