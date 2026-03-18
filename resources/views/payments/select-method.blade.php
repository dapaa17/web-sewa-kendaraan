@php($usesAdminLayout = (bool) auth()->user()?->isAdmin())
@extends($usesAdminLayout ? 'layouts.admin' : 'layouts.app')

@section('title', 'Transfer dan Konfirmasi via WhatsApp')
@if($usesAdminLayout)
@section('page-title', 'Konfirmasi WhatsApp')
@endif

@section('content')
<style>
    .wa-header {
        background: radial-gradient(circle at top right, rgba(255,255,255,0.16), transparent 34%), var(--gradient-brand);
        color: white;
        padding: 3rem 0 2.45rem;
        margin-bottom: 2rem;
        border-radius: 0 0 2rem 2rem;
    }
    .wa-header h1 {
        font-weight: 700;
        font-size: clamp(2rem, 4.6vw, 3rem);
        margin-bottom: 0.5rem;
    }
    .wa-container {
        max-width: 940px;
        margin: 0 auto;
        padding-bottom: 3rem;
    }
    .hero-meta {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
        margin-top: 1.2rem;
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
    .booking-card {
        background: white;
        border-radius: 1.5rem;
        padding: 2rem;
        box-shadow: var(--shadow-card);
        margin-bottom: 1.5rem;
        border: 1px solid rgba(203,213,225,0.7);
    }
    .card-header-title {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    .card-header-title .icon {
        width: 45px;
        height: 45px;
        background: var(--gradient-brand);
        border-radius: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.25rem;
        box-shadow: 0 16px 26px rgba(31, 41, 55, 0.18);
    }
    .card-header-title h5 {
        margin: 0;
        font-weight: 700;
        color: #1a202c;
    }
    .detail-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }
    .detail-item {
        display: flex;
        flex-direction: column;
        background: #f8fafc;
        border: 1px solid rgba(203,213,225,0.82);
        border-radius: 1rem;
        padding: 1rem;
    }
    .detail-item .label {
        font-size: 0.8rem;
        color: #718096;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.25rem;
    }
    .detail-item .value {
        font-weight: 700;
        color: #1a202c;
        font-size: 1rem;
    }
    .detail-item .value.price {
        font-size: 1.5rem;
        color: var(--color-primary);
    }
    .info-banner {
        background: linear-gradient(135deg, rgba(255,255,255,0.98) 0%, rgba(240,249,255,0.96) 100%);
        border-radius: 1.25rem;
        padding: 1.25rem 1.5rem;
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        margin-bottom: 1.5rem;
        border: 1px solid rgba(var(--color-secondary-rgb), 0.14);
        box-shadow: var(--shadow-soft);
    }
    .info-banner i {
        font-size: 1.5rem;
        color: var(--color-secondary-strong);
    }
    .info-banner .content strong {
        color: var(--color-primary);
        display: block;
        margin-bottom: 0.25rem;
    }
    .info-banner .content p {
        color: #475569;
        font-size: 0.9rem;
        margin: 0;
    }
    .transfer-card {
        background: white;
        border: 1px solid rgba(203,213,225,0.72);
        border-radius: 1.5rem;
        overflow: hidden;
        margin-bottom: 1.5rem;
        box-shadow: var(--shadow-card);
    }
    .transfer-card .card-title-bar {
        background: linear-gradient(135deg, rgba(var(--color-secondary-rgb), 0.14) 0%, rgba(255,255,255,0.98) 100%);
        color: var(--color-primary);
        padding: 1rem 1.5rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        border-bottom: 1px solid rgba(203,213,225,0.72);
    }
    .transfer-card .card-body {
        padding: 1.5rem;
    }
    .bank-info {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
    }
    .bank-info-item {
        display: flex;
        flex-direction: column;
        background: #f8fafc;
        border: 1px solid rgba(203,213,225,0.82);
        border-radius: 1rem;
        padding: 1rem;
    }
    .bank-info-item .label {
        font-size: 0.75rem;
        color: #718096;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.25rem;
    }
    .bank-info-item .value {
        font-weight: 700;
        color: #1a202c;
        font-size: 1.1rem;
    }
    .bank-info-item .value.account {
        font-family: 'Courier New', monospace;
        font-size: 1.35rem;
        color: var(--color-primary);
        background: linear-gradient(135deg, rgba(var(--color-secondary-rgb), 0.12) 0%, rgba(255,255,255,0.96) 100%);
        border: 1px solid rgba(var(--color-secondary-rgb), 0.18);
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        display: inline-block;
    }
    .steps-card {
        background: white;
        border-radius: 1.5rem;
        padding: 2rem;
        box-shadow: var(--shadow-card);
        margin-bottom: 1.5rem;
        border: 1px solid rgba(203,213,225,0.72);
    }
    .steps-card h5 {
        font-weight: 700;
        color: #1a202c;
        margin-bottom: 0.55rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .steps-card .subtitle {
        color: #64748b;
        margin-bottom: 1.2rem;
    }
    .steps-list {
        list-style: none;
        padding: 0;
        margin: 0;
        counter-reset: steps;
    }
    .steps-list li {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        padding: 1rem 0;
        border-bottom: 1px solid #f1f5f9;
        counter-increment: steps;
    }
    .steps-list li:last-child {
        border-bottom: none;
    }
    .steps-list li::before {
        content: counter(steps);
        width: 32px;
        height: 32px;
        background: var(--gradient-brand);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        font-size: 0.9rem;
        flex-shrink: 0;
    }
    .steps-list li span {
        color: #4a5568;
        padding-top: 0.25rem;
    }
    .whatsapp-card {
        background: linear-gradient(135deg, rgba(37,211,102,0.1) 0%, rgba(255,255,255,0.98) 100%);
        border: 1px solid rgba(37,211,102,0.18);
        border-radius: 1.25rem;
        padding: 1.35rem 1.5rem;
        margin-bottom: 1.5rem;
    }
    .whatsapp-card h6 {
        color: #166534;
        font-weight: 700;
        margin-bottom: 0.45rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .whatsapp-card p {
        margin: 0;
        color: #475569;
    }
    .btn-whatsapp {
        background: linear-gradient(135deg, #25d366 0%, #128c7e 100%);
        border: none;
        padding: 1.25rem 2rem;
        font-size: 1.1rem;
        font-weight: 600;
        border-radius: 1rem;
        color: white;
        width: 100%;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
    }
    .btn-whatsapp:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(37, 211, 102, 0.4);
        color: white;
    }
    .btn-whatsapp i {
        font-size: 1.5rem;
    }
    .btn-back {
        background: white;
        border: 2px solid rgba(var(--color-primary-rgb), 0.12);
        padding: 1rem 2rem;
        font-size: 1rem;
        font-weight: 500;
        border-radius: 1rem;
        color: var(--color-primary);
        width: 100%;
        transition: all 0.3s ease;
        margin-top: 1rem;
        text-align: center;
        display: block;
    }
    .btn-back:hover {
        background: rgba(var(--color-secondary-rgb), 0.08);
        border-color: var(--color-secondary);
        color: var(--color-primary);
        text-decoration: none;
    }
    @media (max-width: 768px) {
        .wa-header {
            padding: 2.6rem 0 2.15rem;
        }
        .hero-meta {
            flex-direction: column;
            align-items: flex-start;
        }
        .detail-grid {
            grid-template-columns: 1fr;
        }
        .booking-card,
        .steps-card {
            padding: 1.4rem;
        }
    }
</style>

<!-- WhatsApp Header -->
<div class="wa-header">
    <div class="container wa-container">
        <h1><i class="bi bi-whatsapp me-2"></i>Transfer dan Konfirmasi via WhatsApp</h1>
        <p class="mb-0 opacity-75">Transfer bank lalu konfirmasi ke admin via WhatsApp</p>
        <div class="hero-meta">
            <span class="meta-chip"><i class="bi bi-receipt"></i>Booking #{{ $booking->id }}</span>
            <span class="meta-chip"><i class="bi bi-cash-stack"></i>Rp{{ number_format($booking->total_price, 0, ',', '.') }}</span>
            <span class="meta-chip"><i class="bi bi-calendar-range"></i>{{ $booking->duration_days }} hari</span>
        </div>
    </div>
</div>

<div class="container wa-container">
    <!-- Booking Summary -->
    <div class="booking-card">
        <div class="card-header-title">
            <div class="icon">{{ $booking->vehicle->getTypeIcon() }}</div>
            <h5>Detail Booking</h5>
        </div>
        <div class="detail-grid">
            <div class="detail-item">
                <span class="label">Kendaraan</span>
                <span class="value">{{ $booking->vehicle->name }}</span>
            </div>
            <div class="detail-item">
                <span class="label">Jadwal Sewa</span>
                <span class="value">{{ $booking->start_date->format('d M Y') }} {{ $booking->pickup_time_label }} - {{ $booking->end_date->format('d M Y') }} {{ $booking->return_time_label }}</span>
            </div>
            <div class="detail-item">
                <span class="label">Durasi</span>
                <span class="value">{{ $booking->duration_days }} hari</span>
            </div>
            <div class="detail-item">
                <span class="label">Total Pembayaran</span>
                <span class="value price">Rp{{ number_format($booking->total_price, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    <!-- Info Banner -->
    <div class="info-banner">
        <i class="bi bi-info-circle-fill"></i>
        <div class="content">
            <strong>Pembayaran tanpa payment gateway</strong>
            <p>Transfer sesuai nominal, simpan bukti, lalu kirim ke admin via WhatsApp untuk verifikasi manual.</p>
        </div>
    </div>

    <div class="whatsapp-card">
        <h6><i class="bi bi-shield-check"></i>Alur verifikasi manual</h6>
        <p>Metode ini bukan payment gateway otomatis. Setelah Anda mengirim bukti transfer via WhatsApp, admin akan memeriksa pembayaran dan memperbarui status booking secara manual.</p>
    </div>

    <!-- Transfer Instructions -->
    <div class="transfer-card">
        <div class="card-title-bar">
            <i class="bi bi-bank"></i>
            <span>Instruksi Transfer</span>
        </div>
        <div class="card-body">
            <div class="bank-info">
                <div class="bank-info-item">
                    <span class="label">Bank</span>
                    <span class="value">{{ $paymentContact['bank_name'] }}</span>
                </div>
                <div class="bank-info-item">
                    <span class="label">Atas Nama</span>
                    <span class="value">{{ $paymentContact['account_name'] }}</span>
                </div>
                <div class="bank-info-item">
                    <span class="label">Nomor Rekening</span>
                    <span class="value account">{{ $paymentContact['account_number'] }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Steps -->
    <div class="steps-card">
        <h5><i class="bi bi-list-ol"></i> Langkah Pembayaran</h5>
        <p class="subtitle">Ikuti urutan ini supaya pembayaran cepat diverifikasi dan booking segera aktif.</p>
        <ol class="steps-list">
            <li><span>Transfer sesuai total pembayaran ke rekening di atas</span></li>
            <li><span>Screenshot atau simpan bukti transfer</span></li>
            <li><span>Klik tombol WhatsApp di bawah</span></li>
            <li><span>Kirim bukti transfer ke admin melalui chat</span></li>
            <li><span>Kirim konfirmasi secepatnya agar admin bisa memverifikasi sebelum batas waktu booking berakhir</span></li>
        </ol>
    </div>

    <!-- Action Buttons -->
    <a href="{{ $whatsAppUrl }}" target="_blank" rel="noopener noreferrer" class="btn btn-whatsapp">
        <i class="bi bi-whatsapp"></i>
        Hubungi Admin via WhatsApp
    </a>
    
    <a href="{{ route('bookings.show', $booking) }}" class="btn btn-back">
        <i class="bi bi-arrow-left me-2"></i>Kembali ke Detail Booking
    </a>
</div>
@endsection
