@extends('layouts.app')

@section('title', 'RentalHub - Sewa Kendaraan Mudah')

@section('content')
<style>
    .hero-section {
        background: radial-gradient(circle at top right, rgba(255,255,255,0.16), transparent 34%), var(--gradient-brand);
        color: white;
        padding: 6.25rem 0 5.75rem;
        margin-top: -1rem;
    }
    .hero-title {
        font-size: 3.5rem;
        font-weight: 800;
        margin-bottom: 1.35rem;
        line-height: 1.02;
        letter-spacing: -0.065em;
        max-width: 11ch;
    }
    .hero-subtitle {
        font-size: 1.12rem;
        opacity: 0.9;
        margin-bottom: 2.25rem;
        line-height: 1.85;
        max-width: 35rem;
    }
    .btn-hero-primary {
        background: var(--color-primary);
        color: white;
        border: none;
        padding: 1rem 2rem;
        border-radius: 50px;
        font-weight: 600;
        font-size: 1.1rem;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        box-shadow: 0 16px 28px rgba(15, 23, 42, 0.22);
    }
    .btn-hero-primary:hover {
        background: var(--color-secondary);
        color: var(--color-primary);
        transform: translateY(-3px);
        box-shadow: 0 14px 34px rgba(var(--color-secondary-rgb), 0.28);
    }
    .btn-hero-outline {
        background: rgba(255,255,255,0.06);
        color: white;
        border: 2px solid rgba(255,255,255,0.72);
        padding: 1rem 2rem;
        border-radius: 50px;
        font-weight: 600;
        font-size: 1.1rem;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    .btn-hero-outline:hover {
        background: rgba(255,255,255,0.95);
        color: var(--color-primary);
        transform: translateY(-3px);
        box-shadow: 0 14px 34px rgba(15, 23, 42, 0.18);
    }
    .hero-image {
        background: rgba(255,255,255,0.08);
        border-radius: 2rem;
        padding: 2.5rem;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.14);
        box-shadow: 0 24px 48px rgba(15, 23, 42, 0.2);
    }
    .hero-icon {
        font-size: 8rem;
        opacity: 0.9;
    }
    .hero-image .badge {
        border-radius: 999px;
        box-shadow: 0 12px 24px rgba(15, 23, 42, 0.12);
    }
    .hero-image .badge i {
        color: var(--color-secondary-strong) !important;
    }
    .features-section {
        padding: 5rem 0;
        background: linear-gradient(180deg, rgba(255,255,255,0.4) 0%, rgba(238,242,247,0.75) 100%);
    }
    .section-title {
        font-size: 2.8rem;
        font-weight: 700;
        color: var(--color-primary);
        margin-bottom: 0.9rem;
        line-height: 1.02;
        letter-spacing: -0.055em;
    }
    .section-subtitle {
        font-size: 1.03rem;
        color: var(--color-muted);
        margin-bottom: 3rem;
        max-width: 40rem;
        margin-left: auto;
        margin-right: auto;
        line-height: 1.8;
    }
    .feature-card {
        background: white;
        border-radius: 1.5rem;
        padding: 2.85rem 2.2rem;
        text-align: center;
        box-shadow: var(--shadow-soft);
        transition: all 0.3s ease;
        height: 100%;
        border: 1px solid rgba(203,213,225,0.55);
    }
    .feature-card:hover {
        transform: translateY(-10px);
        box-shadow: var(--shadow-card-hover);
    }
    .feature-icon {
        width: 80px;
        height: 80px;
        background: var(--gradient-brand);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
    }
    .feature-icon i {
        font-size: 2rem;
        color: white;
    }
    .feature-title {
        font-size: 1.35rem;
        font-weight: 700;
        color: var(--color-primary);
        margin-bottom: 0.75rem;
        letter-spacing: -0.04em;
    }
    .feature-text {
        color: var(--color-muted);
        line-height: 1.75;
        font-size: 0.98rem;
    }
    .cta-section {
        padding: 5rem 0;
        background: white;
    }
    .cta-card {
        background: radial-gradient(circle at top right, rgba(255,255,255,0.18), transparent 34%), var(--gradient-brand);
        border-radius: 2rem;
        padding: 4.3rem 3.5rem;
        text-align: center;
        color: white;
        box-shadow: 0 24px 50px rgba(15, 23, 42, 0.22);
    }
    .cta-title {
        font-size: 2.45rem;
        font-weight: 700;
        margin-bottom: 0.9rem;
        line-height: 1.02;
    }
    .cta-text {
        font-size: 1.05rem;
        opacity: 0.9;
        margin-bottom: 2rem;
        max-width: 34rem;
        margin-left: auto;
        margin-right: auto;
        line-height: 1.8;
    }
    .vehicle-types {
        padding: 5rem 0;
        background: white;
    }
    .type-card {
        background: linear-gradient(135deg, rgba(255,255,255,0.82) 0%, rgba(6,182,212,0.12) 100%);
        border-radius: 1.5rem;
        padding: 3.35rem 2.35rem;
        text-align: center;
        transition: all 0.3s ease;
        border: 1px solid rgba(203,213,225,0.55);
        box-shadow: var(--shadow-soft);
    }
    .type-card:hover {
        background: var(--gradient-brand);
        transform: translateY(-6px) scale(1.01);
        box-shadow: var(--shadow-card-hover);
    }
    .type-card:hover .type-icon,
    .type-card:hover .type-title,
    .type-card:hover .type-text {
        color: white;
    }
    .type-icon {
        font-size: 4rem;
        color: var(--color-secondary);
        margin-bottom: 1rem;
        transition: all 0.3s ease;
    }
    .type-title {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--color-primary);
        margin-bottom: 0.5rem;
        transition: all 0.3s ease;
        letter-spacing: -0.045em;
    }
    .type-text {
        color: var(--color-muted);
        transition: all 0.3s ease;
        line-height: 1.75;
    }
    @media (max-width: 768px) {
        .hero-section,
        .features-section,
        .vehicle-types,
        .cta-section {
            padding: 4rem 0;
        }

        .hero-title {
            font-size: 2.65rem;
            max-width: none;
        }

        .hero-subtitle {
            font-size: 1rem;
        }

        .cta-card {
            padding: 2.5rem 2rem;
        }

        .section-title {
            font-size: 2.2rem;
        }
    }
</style>

<!-- Hero Section -->
<div class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <h1 class="hero-title">
                    Sewa Kendaraan<br>Mudah & Terpercaya
                </h1>
                <p class="hero-subtitle">
                    Kami menyediakan layanan penyewaan kendaraan berkualitas dengan harga terjangkau. 
                    Proses booking yang cepat dan mudah, kendaraan berkondisi prima, dan layanan 24/7.
                </p>
                <div class="d-flex gap-3 flex-wrap">
                    @auth
                        <a href="{{ route('vehicles.browse') }}" class="btn-hero-primary">
                            <i class="bi bi-search"></i> Cari Kendaraan
                        </a>
                        <a href="{{ route('bookings.index') }}" class="btn-hero-outline">
                            <i class="bi bi-journal-text"></i> Booking Saya
                        </a>
                    @else
                        <a href="{{ route('vehicles.browse') }}" class="btn-hero-primary">
                            <i class="bi bi-search"></i> Lihat Kendaraan
                        </a>
                        <a href="{{ route('register') }}" class="btn-hero-outline">
                            <i class="bi bi-person-plus"></i> Daftar Sekarang
                        </a>
                    @endauth
                </div>
            </div>
            <div class="col-lg-6 text-center">
                <div class="hero-image">
                    <i class="bi bi-car-front-fill hero-icon"></i>
                    <div class="mt-3">
                        <span class="badge bg-white text-dark px-3 py-2 me-2">
                            <i class="bi bi-car-front text-primary"></i> Mobil
                        </span>
                        <span class="badge bg-white text-dark px-3 py-2">
                            <i class="bi bi-bicycle text-primary"></i> Motor
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Features Section -->
<div class="features-section">
    <div class="container">
        <div class="text-center">
            <h2 class="section-title">Mengapa Pilih RentalHub?</h2>
            <p class="section-subtitle">Kami memberikan layanan terbaik untuk kenyamanan Anda</p>
        </div>
        
        <div class="row g-4">
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-lightning-fill"></i>
                    </div>
                    <h5 class="feature-title">Booking Cepat</h5>
                    <p class="feature-text">Proses booking hanya 5 menit. Pilih kendaraan, tentukan tanggal, dan langsung konfirmasi.</p>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <h5 class="feature-title">Aman & Terpercaya</h5>
                    <p class="feature-text">Kendaraan terseleksi dengan perawatan rutin. Keamanan Anda adalah prioritas kami.</p>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-wallet2"></i>
                    </div>
                    <h5 class="feature-title">Harga Terjangkau</h5>
                    <p class="feature-text">Tarif kompetitif dan transparan. Tanpa biaya tersembunyi, harga sudah termasuk semua.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Vehicle Types Section -->
<div class="vehicle-types">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">Pilihan Kendaraan</h2>
            <p class="section-subtitle">Tersedia berbagai jenis kendaraan sesuai kebutuhan Anda</p>
        </div>
        
        <div class="row g-4 justify-content-center">
            <div class="col-md-5">
                <div class="type-card">
                    <i class="bi bi-car-front-fill type-icon"></i>
                    <h4 class="type-title">Mobil</h4>
                    <p class="type-text">City car, SUV, MPV, dan sedan untuk berbagai keperluan</p>
                </div>
            </div>
            <div class="col-md-5">
                <div class="type-card">
                    <i class="bi bi-bicycle type-icon"></i>
                    <h4 class="type-title">Motor</h4>
                    <p class="type-text">Matic, sport, dan bebek untuk mobilitas praktis</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CTA Section -->
@guest
<div class="cta-section">
    <div class="container">
        <div class="cta-card">
            <h3 class="cta-title">Siap Untuk Memulai?</h3>
            <p class="cta-text">Daftar sekarang dan nikmati kemudahan menyewa kendaraan dengan RentalHub</p>
            <a href="{{ route('register') }}" class="btn-hero-primary">
                <i class="bi bi-person-plus"></i> Daftar Gratis Sekarang
            </a>
        </div>
    </div>
</div>
@endguest

@endsection