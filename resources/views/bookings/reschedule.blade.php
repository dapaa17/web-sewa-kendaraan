@extends('layouts.admin')

@section('title', 'Jadwalkan Ulang Booking')
@section('page-title', 'Jadwalkan Ulang Booking')

@section('content')
<style>
    .reschedule-header {
        background: radial-gradient(circle at top right, rgba(255,255,255,0.14), transparent 34%), var(--gradient-brand);
        color: white;
        padding: 2.5rem 0;
        margin-bottom: 2rem;
        border-radius: 0 0 2rem 2rem;
    }
    .reschedule-header h1 {
        font-weight: 700;
        margin-bottom: 0.5rem;
    }
    .reschedule-container {
        max-width: 1080px;
        margin: 0 auto;
        padding: 0 1rem 3rem;
    }
    .back-link {
        color: rgba(255,255,255,0.82);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 1rem;
    }
    .back-link:hover {
        color: white;
    }
    .reschedule-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(320px, 380px);
        gap: 1.5rem;
    }
    .panel-card {
        background: white;
        border-radius: 1.5rem;
        padding: 1.75rem;
        border: 1px solid rgba(203,213,225,0.7);
        box-shadow: var(--shadow-card);
    }
    .panel-card h5 {
        margin-bottom: 1.2rem;
        font-weight: 700;
        color: #0f172a;
    }
    .info-list {
        display: grid;
        gap: 0.9rem;
    }
    .info-item {
        padding: 0.95rem 1rem;
        border-radius: 1rem;
        background: #f8fafc;
        border: 1px solid rgba(226,232,240,0.85);
    }
    .info-item .label {
        display: block;
        font-size: 0.78rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: #64748b;
        margin-bottom: 0.3rem;
    }
    .info-item .value {
        color: #0f172a;
        font-weight: 600;
    }
    .hold-box {
        border-radius: 1rem;
        padding: 1rem 1.1rem;
        background: linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%);
        border: 1px solid rgba(249, 115, 22, 0.18);
        color: #9a3412;
        margin-bottom: 1rem;
    }
    .hold-box strong {
        display: block;
        margin-bottom: 0.4rem;
    }
    .form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 1rem;
    }
    .field-block {
        display: grid;
        gap: 0.45rem;
    }
    .field-block label {
        font-weight: 600;
        color: #1f2937;
    }
    .helper-text {
        color: #64748b;
        font-size: 0.86rem;
        line-height: 1.55;
    }
    .form-control,
    .form-select {
        border-radius: 0.9rem;
        border: 2px solid #e5e7eb;
        padding: 0.8rem 0.95rem;
    }
    .form-control:focus,
    .form-select:focus {
        border-color: var(--color-secondary);
        box-shadow: 0 0 0 3px rgba(var(--color-secondary-rgb), 0.14);
    }
    .form-control.is-invalid,
    .form-select.is-invalid {
        border-color: #ef4444;
    }
    .note-box {
        margin-top: 1rem;
        padding: 1rem 1.1rem;
        border-radius: 1rem;
        background: linear-gradient(135deg, rgba(224,242,254,0.95) 0%, rgba(240,249,255,0.98) 100%);
        border: 1px solid rgba(125, 211, 252, 0.45);
        color: #0c4a6e;
    }
    .action-row {
        display: flex;
        gap: 0.9rem;
        flex-wrap: wrap;
        margin-top: 1.5rem;
    }
    .btn-submit {
        background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%);
        border: none;
        color: white;
        padding: 0.95rem 1.6rem;
        border-radius: 999px;
        font-weight: 600;
    }
    .btn-submit:hover {
        color: white;
        box-shadow: 0 10px 22px rgba(249, 115, 22, 0.24);
    }
    .btn-light-outline {
        background: white;
        color: var(--color-primary);
        border: 1px solid rgba(var(--color-primary-rgb), 0.16);
        padding: 0.95rem 1.6rem;
        border-radius: 999px;
        font-weight: 600;
    }
    .error-text {
        color: #dc2626;
        font-size: 0.84rem;
    }
    @media (max-width: 900px) {
        .reschedule-grid,
        .form-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="reschedule-header">
    <div class="reschedule-container">
        <a href="{{ route('bookings.show', $booking) }}" class="back-link">
            <i class="bi bi-arrow-left"></i> Kembali ke Detail Booking
        </a>
        <h1><i class="bi bi-calendar2-week me-2"></i>Jadwalkan Ulang Booking</h1>
        <p class="mb-0 opacity-75">Booking #{{ $booking->id }} - {{ $booking->vehicle->name }}</p>
    </div>
</div>

<div class="reschedule-container">
    @if($errors->any())
        <div class="alert alert-danger border-0 rounded-4 shadow-sm mb-4">
            <strong>Jadwal baru belum valid.</strong>
            <div class="small mt-2">Periksa kembali tanggal atau jam yang dipilih lalu kirim ulang.</div>
        </div>
    @endif

    <div class="reschedule-grid">
        <div class="panel-card">
            <h5><i class="bi bi-arrow-repeat me-2"></i>Atur Jadwal Baru</h5>

            <div class="hold-box">
                <strong>Booking ini sedang tertahan maintenance</strong>
                <div>{{ $booking->maintenance_hold_reason ?? 'Unit ditahan setelah inspeksi pengembalian dan perlu penyesuaian jadwal.' }}</div>
            </div>

            <form method="POST" action="{{ route('admin.bookings.reschedule', $booking) }}">
                @csrf

                <div class="form-grid">
                    <div class="field-block">
                        <label for="start_date">Tanggal mulai baru</label>
                        <input
                            type="date"
                            id="start_date"
                            name="start_date"
                            value="{{ old('start_date', $booking->start_date->toDateString()) }}"
                            class="form-control @error('start_date') is-invalid @enderror"
                            min="{{ now()->toDateString() }}"
                            required
                        >
                        @error('start_date')
                            <span class="error-text">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="field-block">
                        <label for="end_date">Tanggal selesai baru</label>
                        <input
                            type="date"
                            id="end_date"
                            name="end_date"
                            value="{{ old('end_date', $booking->end_date->toDateString()) }}"
                            class="form-control @error('end_date') is-invalid @enderror"
                            min="{{ old('start_date', now()->toDateString()) }}"
                            required
                        >
                        @error('end_date')
                            <span class="error-text">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="field-block">
                        <label for="pickup_time">Jam ambil</label>
                        <input
                            type="time"
                            id="pickup_time"
                            name="pickup_time"
                            value="{{ old('pickup_time', $booking->pickup_time_label) }}"
                            class="form-control @error('pickup_time') is-invalid @enderror"
                        >
                        @error('pickup_time')
                            <span class="error-text">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="field-block">
                        <label for="return_time">Jam kembali</label>
                        <input
                            type="time"
                            id="return_time"
                            name="return_time"
                            value="{{ old('return_time', $booking->return_time_label) }}"
                            class="form-control @error('return_time') is-invalid @enderror"
                        >
                        @error('return_time')
                            <span class="error-text">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="field-block mt-3">
                    <label for="admin_note">Catatan admin tambahan</label>
                    <textarea
                        id="admin_note"
                        name="admin_note"
                        rows="4"
                        class="form-control @error('admin_note') is-invalid @enderror"
                        placeholder="Opsional, misalnya hasil koordinasi dengan customer atau estimasi unit siap lagi."
                    >{{ old('admin_note') }}</textarea>
                    <div class="helper-text">Catatan ini akan ditambahkan ke histori booking agar follow up berikutnya tetap terbaca.</div>
                    @error('admin_note')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>

                <div class="note-box">
                    Setelah disimpan, booking akan keluar dari filter maintenance hold. Status kendaraan tetap maintenance sampai admin selesai menangani unit di halaman kendaraan.
                </div>

                <div class="action-row">
                    <button type="submit" class="btn btn-submit">
                        <i class="bi bi-check2-circle me-1"></i>Simpan Jadwal Baru
                    </button>
                    <a href="{{ route('bookings.show', $booking) }}" class="btn btn-light-outline">
                        <i class="bi bi-x-circle me-1"></i>Batal
                    </a>
                </div>
            </form>
        </div>

        <div class="panel-card">
            <h5><i class="bi bi-info-circle me-2"></i>Ringkasan Booking</h5>

            <div class="info-list">
                <div class="info-item">
                    <span class="label">Kendaraan</span>
                    <div class="value">{{ $booking->vehicle->name }} · {{ $booking->vehicle->plat_number }}</div>
                </div>
                <div class="info-item">
                    <span class="label">Customer</span>
                    <div class="value">{{ $booking->user->name }} · {{ $booking->user->email }}</div>
                </div>
                <div class="info-item">
                    <span class="label">Jadwal Saat Ini</span>
                    <div class="value">{{ $booking->start_date->format('d M Y') }} {{ $booking->pickup_time_label }} - {{ $booking->end_date->format('d M Y') }} {{ $booking->return_time_label }}</div>
                </div>
                <div class="info-item">
                    <span class="label">Durasi</span>
                    <div class="value">{{ $booking->duration_days }} hari</div>
                </div>
                <div class="info-item">
                    <span class="label">Status Pembayaran</span>
                    <div class="value">{{ $booking->getPaymentStatusLabel() }}</div>
                </div>
                <div class="info-item">
                    <span class="label">Total Booking</span>
                    <div class="value">Rp{{ number_format($booking->total_price, 0, ',', '.') }}</div>
                </div>
            </div>

            <div class="note-box mt-3">
                Pilih tanggal yang benar-benar kosong untuk kendaraan ini. Flow ini tidak mengubah pembayaran customer, hanya menyesuaikan jadwal booking yang tertahan maintenance.
            </div>
        </div>
    </div>
</div>
@endsection