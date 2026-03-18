@extends('layouts.app')

@section('title', 'Tentang Kami - RentalHub')

@section('content')
<style>
    .ab-header {
        background: radial-gradient(circle at top right, rgba(255,255,255,0.16), transparent 34%), var(--gradient-brand);
        color: white;
        padding: 5rem 0 6.5rem;
        margin-top: -1rem;
    }
    .ab-header-title {
        font-size: 3rem;
        font-weight: 800;
        letter-spacing: -0.06em;
        line-height: 1.02;
        margin-bottom: 0.75rem;
    }
    .ab-header-sub {
        font-size: 1.1rem;
        opacity: 0.88;
        max-width: 38rem;
        line-height: 1.8;
    }
    .ab-body {
        margin-top: -3.5rem;
        padding-bottom: 4rem;
    }

    /* Story Card */
    .ab-story {
        background: var(--color-card);
        border-radius: 1.25rem;
        padding: 3rem 2.75rem;
        border: 1px solid rgba(203,213,225,0.55);
        box-shadow: var(--shadow-card);
        margin-bottom: 2rem;
    }
    .ab-story-title {
        font-size: 1.6rem;
        font-weight: 700;
        color: var(--color-primary);
        margin-bottom: 1rem;
        letter-spacing: -0.04em;
    }
    .ab-story p {
        color: var(--color-muted);
        line-height: 1.85;
        font-size: 1rem;
        margin-bottom: 1rem;
    }
    .ab-story p:last-child { margin-bottom: 0; }

    /* Mission / Vision */
    .ab-mv-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    .ab-mv-card {
        background: var(--color-card);
        border-radius: 1.25rem;
        padding: 2.5rem 2.25rem;
        border: 1px solid rgba(203,213,225,0.55);
        box-shadow: var(--shadow-card);
        transition: all 0.3s ease;
    }
    .ab-mv-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-card-hover);
    }
    .ab-mv-icon {
        width: 56px;
        height: 56px;
        border-radius: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
        margin-bottom: 1.25rem;
    }
    .ab-mv-icon.visi { background: var(--gradient-brand); }
    .ab-mv-icon.misi { background: var(--gradient-cyan); }
    .ab-mv-title {
        font-size: 1.35rem;
        font-weight: 700;
        color: var(--color-primary);
        margin-bottom: 0.6rem;
        letter-spacing: -0.04em;
    }
    .ab-mv-text {
        color: var(--color-muted);
        line-height: 1.8;
        font-size: 0.97rem;
        margin: 0;
    }

    /* Values */
    .ab-values {
        background: var(--color-card);
        border-radius: 1.25rem;
        padding: 2.5rem 2.75rem;
        border: 1px solid rgba(203,213,225,0.55);
        box-shadow: var(--shadow-card);
        margin-bottom: 2rem;
    }
    .ab-values-title {
        font-size: 1.6rem;
        font-weight: 700;
        color: var(--color-primary);
        margin-bottom: 1.5rem;
        letter-spacing: -0.04em;
    }
    .ab-val-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1.25rem;
    }
    .ab-val-item {
        text-align: center;
        padding: 1.75rem 1rem;
        border-radius: 1rem;
        background: var(--gradient-soft);
        border: 1px solid rgba(203,213,225,0.35);
        transition: all 0.3s ease;
    }
    .ab-val-item:hover {
        transform: translateY(-3px);
        box-shadow: var(--shadow-soft);
    }
    .ab-val-icon {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: var(--gradient-brand);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 0.85rem;
        font-size: 1.15rem;
        color: white;
    }
    .ab-val-label {
        font-family: var(--font-display);
        font-weight: 700;
        font-size: 0.98rem;
        color: var(--color-primary);
        margin-bottom: 0.25rem;
        letter-spacing: -0.03em;
    }
    .ab-val-desc {
        font-size: 0.88rem;
        color: var(--color-muted);
        line-height: 1.6;
        margin: 0;
    }

    /* Stats */
    .ab-stats {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1.25rem;
        margin-bottom: 2rem;
    }
    .ab-stat {
        background: var(--color-card);
        border-radius: 1.25rem;
        padding: 2rem 1.5rem;
        text-align: center;
        border: 1px solid rgba(203,213,225,0.55);
        box-shadow: var(--shadow-card);
        transition: all 0.3s ease;
    }
    .ab-stat:hover {
        transform: translateY(-3px);
        box-shadow: var(--shadow-card-hover);
    }
    .ab-stat-num {
        font-family: var(--font-display);
        font-size: 2.2rem;
        font-weight: 800;
        background: var(--gradient-brand);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        letter-spacing: -0.05em;
        line-height: 1.1;
        margin-bottom: 0.3rem;
    }
    .ab-stat-label {
        font-size: 0.9rem;
        color: var(--color-muted);
        font-weight: 500;
    }

    /* CTA */
    .ab-cta {
        background: radial-gradient(circle at top right, rgba(255,255,255,0.18), transparent 34%), var(--gradient-brand);
        border-radius: 1.5rem;
        padding: 3.5rem 3rem;
        text-align: center;
        color: white;
        box-shadow: 0 24px 50px rgba(15, 23, 42, 0.22);
    }
    .ab-cta-title {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 0.6rem;
        letter-spacing: -0.04em;
    }
    .ab-cta-text {
        opacity: 0.9;
        margin-bottom: 1.75rem;
        max-width: 30rem;
        margin-left: auto;
        margin-right: auto;
        line-height: 1.8;
    }
    .ab-cta-btn {
        background: white;
        color: var(--color-primary);
        border: none;
        padding: 0.85rem 2rem;
        border-radius: 50px;
        font-weight: 700;
        font-size: 1rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.18);
    }
    .ab-cta-btn:hover {
        background: var(--color-secondary);
        color: white;
        transform: translateY(-3px);
        box-shadow: 0 14px 34px rgba(var(--color-secondary-rgb), 0.28);
    }

    @media (max-width: 768px) {
        .ab-header { padding: 4rem 0 5.5rem; }
        .ab-header-title { font-size: 2.3rem; }
        .ab-mv-grid { grid-template-columns: 1fr; }
        .ab-val-grid { grid-template-columns: 1fr; }
        .ab-stats { grid-template-columns: repeat(2, 1fr); }
        .ab-story, .ab-values { padding: 2rem 1.5rem; }
        .ab-cta { padding: 2.5rem 1.5rem; }
    }
</style>

<!-- Header -->
<div class="ab-header">
    <div class="container">
        <h1 class="ab-header-title">Tentang Kami</h1>
        <p class="ab-header-sub">Mengenal lebih dekat RentalHub — platform penyewaan kendaraan yang mengutamakan kemudahan, keamanan, dan kenyamanan pelanggan.</p>
    </div>
</div>

<!-- Body -->
<div class="ab-body">
    <div class="container">

        <!-- Story -->
        <div class="ab-story">
            <h2 class="ab-story-title"><i class="bi bi-book me-2" style="color: var(--color-secondary);"></i>Cerita Kami</h2>
            <p>RentalHub hadir sebagai solusi penyewaan kendaraan yang modern dan terpercaya. Berawal dari kebutuhan akan platform rental yang mudah diakses, kami membangun sistem yang mempertemukan penyedia kendaraan dengan pelanggan secara efisien.</p>
            <p>Dengan teknologi terkini dan komitmen terhadap kualitas layanan, RentalHub menyediakan pengalaman booking yang cepat, transparan, dan aman — mulai dari pemilihan kendaraan hingga pengembalian.</p>
        </div>

        <!-- Stats -->
        <div class="ab-stats">
            <div class="ab-stat">
                <div class="ab-stat-num">{{ \App\Models\Vehicle::where('status', 'available')->count() }}+</div>
                <div class="ab-stat-label">Kendaraan Tersedia</div>
            </div>
            <div class="ab-stat">
                <div class="ab-stat-num">{{ \App\Models\User::where('role', 'customer')->count() }}+</div>
                <div class="ab-stat-label">Pelanggan Terdaftar</div>
            </div>
            <div class="ab-stat">
                <div class="ab-stat-num">{{ \App\Models\Booking::where('status', 'completed')->count() }}+</div>
                <div class="ab-stat-label">Booking Selesai</div>
            </div>
            <div class="ab-stat">
                <div class="ab-stat-num">24/7</div>
                <div class="ab-stat-label">Layanan Support</div>
            </div>
        </div>

        <!-- Mission / Vision -->
        <div class="ab-mv-grid">
            <div class="ab-mv-card">
                <div class="ab-mv-icon visi"><i class="bi bi-eye"></i></div>
                <h3 class="ab-mv-title">Visi</h3>
                <p class="ab-mv-text">Menjadi platform penyewaan kendaraan terdepan yang memberikan pengalaman terbaik dan terpercaya bagi seluruh pelanggan di Indonesia.</p>
            </div>
            <div class="ab-mv-card">
                <div class="ab-mv-icon misi"><i class="bi bi-rocket-takeoff"></i></div>
                <h3 class="ab-mv-title">Misi</h3>
                <p class="ab-mv-text">Menyediakan layanan penyewaan kendaraan yang mudah, aman, dan terjangkau melalui inovasi teknologi serta pelayanan yang prima kepada setiap pelanggan.</p>
            </div>
        </div>

        <!-- Values -->
        <div class="ab-values">
            <h2 class="ab-values-title"><i class="bi bi-stars me-2" style="color: var(--color-secondary);"></i>Nilai-Nilai Kami</h2>
            <div class="ab-val-grid">
                <div class="ab-val-item">
                    <div class="ab-val-icon"><i class="bi bi-shield-check"></i></div>
                    <div class="ab-val-label">Kepercayaan</div>
                    <p class="ab-val-desc">Kendaraan terawat, proses transparan, dan data pelanggan terjaga aman.</p>
                </div>
                <div class="ab-val-item">
                    <div class="ab-val-icon"><i class="bi bi-lightning-fill"></i></div>
                    <div class="ab-val-label">Kecepatan</div>
                    <p class="ab-val-desc">Booking cepat dalam hitungan menit, tanpa proses berbelit-belit.</p>
                </div>
                <div class="ab-val-item">
                    <div class="ab-val-icon"><i class="bi bi-heart-fill"></i></div>
                    <div class="ab-val-label">Pelayanan</div>
                    <p class="ab-val-desc">Layanan ramah dan responsif untuk memastikan kepuasan pelanggan.</p>
                </div>
                <div class="ab-val-item">
                    <div class="ab-val-icon"><i class="bi bi-graph-up-arrow"></i></div>
                    <div class="ab-val-label">Inovasi</div>
                    <p class="ab-val-desc">Terus berinovasi menghadirkan fitur dan teknologi terbaru.</p>
                </div>
                <div class="ab-val-item">
                    <div class="ab-val-icon"><i class="bi bi-wallet2"></i></div>
                    <div class="ab-val-label">Transparansi</div>
                    <p class="ab-val-desc">Harga jelas tanpa biaya tersembunyi, semua informasi terbuka.</p>
                </div>
                <div class="ab-val-item">
                    <div class="ab-val-icon"><i class="bi bi-people-fill"></i></div>
                    <div class="ab-val-label">Komunitas</div>
                    <p class="ab-val-desc">Membangun hubungan jangka panjang dengan pelanggan dan mitra.</p>
                </div>
            </div>
        </div>

        <!-- CTA -->
        <div class="ab-cta">
            <h3 class="ab-cta-title">Mulai Perjalanan Anda</h3>
            <p class="ab-cta-text">Bergabung bersama kami dan nikmati pengalaman menyewa kendaraan yang mudah dan menyenangkan.</p>
            @auth
                <a href="{{ route('vehicles.browse') }}" class="ab-cta-btn">
                    <i class="bi bi-search"></i> Cari Kendaraan
                </a>
            @else
                <a href="{{ route('register') }}" class="ab-cta-btn">
                    <i class="bi bi-person-plus"></i> Daftar Sekarang
                </a>
            @endauth
        </div>

    </div>
</div>
@endsection