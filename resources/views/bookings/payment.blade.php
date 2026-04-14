@php($usesAdminLayout = (bool) auth()->user()?->isAdmin())
@extends($usesAdminLayout ? 'layouts.admin' : 'layouts.app')

@section('title', 'Pilih Metode Pembayaran')
@if($usesAdminLayout)
@section('page-title', 'Metode Pembayaran')
@endif

@section('content')
<style>
    .payment-header {
        background: radial-gradient(circle at top right, rgba(255,255,255,0.14), transparent 36%), var(--gradient-brand);
        color: white;
        padding: 2.5rem 0;
        margin-bottom: 2rem;
        border-radius: 0 0 2rem 2rem;
    }
    .payment-header h1 {
        font-weight: 700;
        margin-bottom: 0.5rem;
    }
    .payment-container {
        max-width: 900px;
        margin: 0 auto;
        padding-bottom: 3rem;
    }
    .booking-summary-card {
        background: white;
        border-radius: 1.5rem;
        padding: 2rem;
        box-shadow: var(--shadow-card);
        margin-bottom: 2rem;
        border: 1px solid rgba(203,213,225,0.65);
    }
    .summary-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid rgba(203,213,225,0.45);
    }
    .summary-header .icon {
        width: 50px;
        height: 50px;
        background: var(--gradient-brand);
        border-radius: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.5rem;
    }
    .summary-header h5 {
        margin: 0;
        font-weight: 600;
        color: #1a202c;
    }
    .summary-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
    }
    .summary-item {
        display: flex;
        flex-direction: column;
    }
    .summary-item .label {
        font-size: 0.8rem;
        color: #718096;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.25rem;
    }
    .summary-item .value {
        font-weight: 600;
        color: #1a202c;
        font-size: 1rem;
    }
    .summary-total {
        margin-top: 1.5rem;
        padding-top: 1.5rem;
        border-top: 2px dashed #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .summary-total .label {
        font-size: 1rem;
        font-weight: 600;
        color: #4a5568;
    }
    .summary-total .amount {
        font-size: 1.75rem;
        font-weight: 700;
        background: var(--gradient-brand);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    .method-title {
        text-align: center;
        margin: 2rem 0;
    }
    .method-title h4 {
        font-weight: 600;
        color: var(--color-primary);
        margin-bottom: 0.5rem;
    }
    .method-title p {
        color: var(--color-muted);
        font-size: 0.9rem;
    }
    .payment-methods {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    .payment-card {
        background: white;
        border: 2px solid rgba(203,213,225,0.65);
        border-radius: 1.5rem;
        padding: 2rem;
        cursor: pointer;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        text-align: center;
        position: relative;
        overflow: hidden;
    }
    .payment-card input[type="radio"] {
        display: none;
    }
    .payment-card:hover {
        border-color: var(--color-secondary);
        transform: translateY(-5px);
        box-shadow: var(--shadow-card-hover);
    }
    .payment-card.active {
        border-color: var(--color-secondary);
        background: linear-gradient(135deg, rgba(31,41,55,0.04) 0%, rgba(6,182,212,0.14) 100%);
        box-shadow: var(--shadow-card-hover);
    }
    .payment-card.active::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: var(--gradient-cyan);
    }
    .payment-card .check-badge {
        position: absolute;
        top: 1rem;
        right: 1rem;
        width: 30px;
        height: 30px;
        background: var(--gradient-brand);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        opacity: 0;
        transform: scale(0);
        transition: all 0.3s ease;
    }
    .payment-card.active .check-badge {
        opacity: 1;
        transform: scale(1);
    }
    .payment-card .icon-wrapper {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, rgba(31,41,55,0.08) 0%, rgba(6,182,212,0.18) 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        font-size: 2.5rem;
        transition: all 0.3s ease;
    }
    .payment-card:hover .icon-wrapper,
    .payment-card.active .icon-wrapper {
        background: var(--gradient-brand);
        transform: scale(1.1);
    }
    .payment-card:hover .icon-wrapper span,
    .payment-card.active .icon-wrapper span {
        filter: brightness(0) invert(1);
    }
    .payment-card h4 {
        font-weight: 700;
        color: #1a202c;
        margin-bottom: 0.5rem;
    }
    .payment-card .desc {
        color: var(--color-muted);
        font-size: 0.9rem;
        margin-bottom: 1.5rem;
    }
    .payment-card .features {
        list-style: none;
        padding: 0;
        margin: 0;
        text-align: left;
    }
    .payment-card .features li {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: var(--color-muted);
        font-size: 0.85rem;
        margin-bottom: 0.5rem;
    }
    .payment-card .features li i {
        color: #10b981;
    }
    .btn-submit {
        background: var(--color-primary);
        border: none;
        padding: 1rem 2rem;
        font-size: 1.1rem;
        font-weight: 600;
        border-radius: 1rem;
        color: white;
        width: 100%;
        transition: all 0.3s ease;
    }
    .btn-submit:hover {
        background: var(--color-secondary);
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(var(--color-secondary-rgb), 0.28);
        color: var(--color-primary);
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
    }
    .btn-back:hover {
        background: rgba(var(--color-secondary-rgb), 0.08);
        border-color: var(--color-secondary);
        color: var(--color-primary);
    }
    .info-alert {
        background: var(--gradient-info-soft);
        border: 1px solid rgba(var(--color-secondary-rgb), 0.2);
        border-radius: 1rem;
        padding: 1.25rem;
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        margin-top: 1.5rem;
    }
    .info-alert i {
        font-size: 1.25rem;
        color: var(--color-secondary-strong);
    }
    .info-alert p {
        margin: 0;
        color: var(--color-primary);
        font-size: 0.9rem;
    }
    @media (max-width: 768px) {
        .payment-header {
            padding: 2.2rem 0 1.9rem;
            margin-bottom: 1.2rem;
            border-radius: 0 0 1.35rem 1.35rem;
        }
        .payment-header h1 {
            font-size: 1.35rem;
            line-height: 1.35;
        }
        .payment-header p {
            font-size: 0.84rem;
            line-height: 1.55;
        }
        .payment-container {
            padding-inline: 1rem;
            padding-bottom: 2rem;
        }
        .booking-summary-card {
            border-radius: 1rem;
            padding: 1rem;
            margin-bottom: 1.2rem;
        }
        .summary-header {
            margin-bottom: 1rem;
            padding-bottom: 0.75rem;
        }
        .summary-header .icon {
            width: 42px;
            height: 42px;
            border-radius: 0.8rem;
            font-size: 1.15rem;
        }
        .summary-header h5 {
            font-size: 0.98rem;
        }
        .payment-methods {
            grid-template-columns: 1fr;
            gap: 0.85rem;
            margin-bottom: 1.2rem;
        }
        .summary-grid {
            grid-template-columns: 1fr;
            gap: 0.7rem;
        }
        .summary-item .label {
            font-size: 0.72rem;
        }
        .summary-item .value {
            font-size: 0.9rem;
        }
        .summary-total {
            margin-top: 1rem;
            padding-top: 1rem;
            gap: 0.75rem;
        }
        .summary-total .amount {
            font-size: 1.24rem;
        }
        .method-title {
            margin: 1rem 0;
        }
        .method-title h4 {
            font-size: 1.05rem;
        }
        .method-title p {
            font-size: 0.82rem;
        }
        .payment-card {
            border-radius: 1rem;
            padding: 1.1rem;
        }
        .payment-card .icon-wrapper {
            width: 56px;
            height: 56px;
            margin-bottom: 1rem;
            font-size: 1.8rem;
        }
        .payment-card h4 {
            font-size: 1rem;
        }
        .payment-card .desc {
            font-size: 0.82rem;
            margin-bottom: 1rem;
        }
        .payment-card .features li {
            font-size: 0.8rem;
        }
        .btn-submit,
        .btn-back {
            min-height: 44px;
            border-radius: 0.85rem;
            font-size: 0.9rem;
            padding: 0.72rem 1rem;
        }
        .info-alert {
            border-radius: 0.85rem;
            padding: 0.9rem;
            gap: 0.65rem;
            margin-top: 1rem;
        }
        .info-alert p {
            font-size: 0.82rem;
            line-height: 1.5;
        }
    }
    @media (max-width: 420px) {
        .payment-header h1 {
            font-size: 1.2rem;
        }
    }
</style>

<!-- Payment Header -->
<div class="payment-header">
    <div class="container">
        <h1><i class="bi bi-credit-card-2-front me-2"></i>Pilih Metode Pembayaran</h1>
        <p class="mb-0 opacity-75">Pilih jalur konfirmasi pembayaran yang paling sesuai untuk Anda.</p>
    </div>
</div>

<div class="container payment-container">
    <!-- Booking Summary Card -->
    <div class="booking-summary-card">
        <div class="summary-header">
            <div class="icon">
                {{ $booking->vehicle->getTypeIcon() }}
            </div>
            <div>
                <h5>{{ $booking->vehicle->name }}</h5>
                <span class="text-muted">Booking #{{ $booking->id }}</span>
            </div>
        </div>
        
        <div class="summary-grid">
            <div class="summary-item">
                <span class="label">Tanggal Sewa</span>
                <span class="value">{{ $booking->start_date->format('d M Y') }}</span>
            </div>
            <div class="summary-item">
                <span class="label">Jam Ambil</span>
                <span class="value">{{ $booking->pickup_time_label }}</span>
            </div>
            <div class="summary-item">
                <span class="label">Tanggal Kembali</span>
                <span class="value">{{ $booking->end_date->format('d M Y') }}</span>
            </div>
            <div class="summary-item">
                <span class="label">Jam Kembali</span>
                <span class="value">{{ $booking->return_time_label }}</span>
            </div>
            <div class="summary-item">
                <span class="label">Durasi</span>
                <span class="value">{{ $booking->duration_days }} hari</span>
            </div>
            <div class="summary-item">
                <span class="label">Harga per Hari</span>
                <span class="value">Rp{{ number_format($booking->daily_price, 0, ',', '.') }}</span>
            </div>
        </div>
        
        <div class="summary-total">
            <span class="label">💰 Total Pembayaran</span>
            <span class="amount">Rp{{ number_format($booking->total_price, 0, ',', '.') }}</span>
        </div>
    </div>

    <!-- Method Selection -->
    <div class="method-title">
        <h4>Pilih Metode Pembayaran</h4>
        <p>Klik salah satu opsi di bawah untuk melanjutkan</p>
    </div>

    <form method="POST" action="{{ route('bookings.process-payment', $booking) }}" id="paymentForm">
        @csrf
        
        <div class="payment-methods">
            <!-- WhatsApp Confirmation -->
            <label class="payment-card" id="card-whatsapp" for="radio-whatsapp">
                <input type="radio" id="radio-whatsapp" name="payment_method" value="whatsapp" checked>
                <div class="check-badge"><i class="bi bi-check"></i></div>
                <div class="icon-wrapper">
                    <span>💳</span>
                </div>
                <h4>Transfer + Konfirmasi WhatsApp</h4>
                <p class="desc">Transfer manual lalu kirim konfirmasi langsung ke admin</p>
                <ul class="features">
                    <li><i class="bi bi-check-circle-fill"></i> Transfer ke rekening tujuan</li>
                    <li><i class="bi bi-check-circle-fill"></i> Kirim bukti via WhatsApp</li>
                    <li><i class="bi bi-check-circle-fill"></i> Chat langsung dengan admin</li>
                    <li><i class="bi bi-check-circle-fill"></i> Konfirmasi via WhatsApp</li>
                </ul>
            </label>

            <!-- Proof Upload -->
            <label class="payment-card" id="card-transfer-proof" for="radio-transfer-proof">
                <input type="radio" id="radio-transfer-proof" name="payment_method" value="transfer_proof">
                <div class="check-badge"><i class="bi bi-check"></i></div>
                <div class="icon-wrapper">
                    <span>🏦</span>
                </div>
                <h4>Transfer + Upload Bukti</h4>
                <p class="desc">Transfer manual lalu upload bukti pembayaran di website</p>
                <ul class="features">
                    <li><i class="bi bi-check-circle-fill"></i> Transfer ke Rekening</li>
                    <li><i class="bi bi-check-circle-fill"></i> Upload bukti transfer di website</li>
                    <li><i class="bi bi-check-circle-fill"></i> Status menunggu verifikasi admin</li>
                    <li><i class="bi bi-check-circle-fill"></i> Verifikasi oleh Admin</li>
                </ul>
            </label>
        </div>

        <button type="submit" class="btn btn-submit" id="submitBtn">
            <i class="bi bi-arrow-right-circle me-2"></i>Lanjut ke Pembayaran
        </button>
        
        <a href="{{ route('bookings.show', $booking) }}" class="btn btn-back">
            <i class="bi bi-arrow-left me-2"></i>Kembali ke Detail Booking
        </a>
    </form>

    <div class="info-alert">
        <i class="bi bi-info-circle-fill"></i>
        <p><strong>Informasi Penting:</strong> Kedua metode di atas tetap memakai verifikasi manual admin. Selesaikan pembayaran sebelum {{ $booking->getPaymentDeadline()->format('d M Y H:i') }} untuk mengamankan booking Anda.</p>
    </div>
</div>
@endsection

@section('js')
<script>
    const allRadios = document.querySelectorAll('input[name="payment_method"]');
    const cardWhatsApp = document.getElementById('card-whatsapp');
    const cardTransferProof = document.getElementById('card-transfer-proof');

    function updateCardVisuals() {
        const checkedValue = document.querySelector('input[name="payment_method"]:checked').value;
        
        if (checkedValue === 'whatsapp') {
            cardWhatsApp.classList.add('active');
            cardTransferProof.classList.remove('active');
        } else {
            cardTransferProof.classList.add('active');
            cardWhatsApp.classList.remove('active');
        }
    }

    allRadios.forEach(radio => {
        radio.addEventListener('change', updateCardVisuals);
    });

    document.addEventListener('DOMContentLoaded', updateCardVisuals);
</script>
@endsection
