@extends('layouts.app')

@section('title', 'Tulis Review')

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
    .review-note-card {
        background: rgba(255,255,255,0.96);
        border: 1px solid rgba(203,213,225,0.78);
        border-radius: 1.5rem;
        box-shadow: var(--shadow-card);
        padding: 1.6rem;
        margin-bottom: 1.5rem;
    }
    .review-summary-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 1rem;
        margin-top: 1.2rem;
    }
    .review-summary-item {
        background: #f8fafc;
        border: 1px solid rgba(203,213,225,0.8);
        border-radius: 1rem;
        padding: 1rem;
    }
    .review-summary-item .label {
        display: block;
        font-size: 0.78rem;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        margin-bottom: 0.35rem;
    }
    .review-summary-item .value {
        color: #0f172a;
        font-weight: 700;
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
        .review-summary-grid {
            grid-template-columns: 1fr;
        }
        .review-form-actions .btn {
            width: 100%;
        }
    }
</style>

<div class="review-page-header">
    <div class="container review-page-shell">
        <a href="{{ route('bookings.show', $booking) }}" class="review-back-link">
            <i class="bi bi-arrow-left"></i> Kembali ke detail booking
        </a>
        <h1 class="mb-2"><i class="bi bi-star-fill me-2"></i>Tulis Review Kendaraan</h1>
        <p class="mb-0 opacity-75">Bagikan pengalaman Anda setelah rental selesai. Review akan tampil setelah dicek admin.</p>
    </div>
</div>

<div class="container review-page-shell">
    <div class="review-summary-card">
        <div class="d-flex align-items-start justify-content-between gap-3 flex-wrap">
            <div>
                <div class="text-uppercase small text-muted mb-2">Booking #{{ $booking->id }}</div>
                <h3 class="mb-2">{{ $booking->vehicle->getTypeIcon() }} {{ $booking->vehicle->name }}</h3>
                <p class="mb-0 text-muted">Booking sudah selesai dan pembayaran sudah lunas, jadi Anda bisa memberi ulasan untuk kendaraan ini.</p>
            </div>
            <span class="badge rounded-pill text-bg-success px-3 py-2">Selesai & Lunas</span>
        </div>
        <div class="review-summary-grid">
            <div class="review-summary-item">
                <span class="label">Tanggal Rental</span>
                <span class="value">{{ $booking->start_date->format('d M Y') }} - {{ $booking->end_date->format('d M Y') }}</span>
            </div>
            <div class="review-summary-item">
                <span class="label">Jadwal</span>
                <span class="value">{{ $booking->pickup_time_label }} - {{ $booking->return_time_label }}</span>
            </div>
            <div class="review-summary-item">
                <span class="label">Total Pembayaran</span>
                <span class="value">Rp{{ number_format($booking->total_price, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    <div class="review-note-card">
        <div class="d-flex align-items-start gap-3">
            <i class="bi bi-shield-check fs-4 text-info"></i>
            <div>
                <strong class="d-block mb-1">Moderasi review aktif</strong>
                <p class="mb-0 text-muted">Review baru akan berstatus pending terlebih dahulu. Selama belum disetujui admin, Anda masih bisa edit atau hapus review sendiri.</p>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('reviews.store', $booking) }}">
        @csrf
        @include('reviews.partials.form')

        <div class="review-form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-send-fill me-2"></i>Kirim Review
            </button>
            <a href="{{ route('bookings.show', $booking) }}" class="btn btn-outline-secondary">
                <i class="bi bi-x-lg me-2"></i>Batal
            </a>
        </div>
    </form>
</div>
@endsection