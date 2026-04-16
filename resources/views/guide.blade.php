@php($usesAdminLayout = (bool) auth()->user()?->isAdmin())
@extends($usesAdminLayout ? 'layouts.admin' : 'layouts.app')

@section('title', 'Panduan Penyewaan - RentalHub')
@if($usesAdminLayout)
@section('page-title', 'Panduan')
@endif

@section('content')
<style>
    .gd-header {
        background: radial-gradient(circle at top right, rgba(255,255,255,0.16), transparent 34%), var(--gradient-brand);
        color: white;
        padding: 5rem 0 6.5rem;
        margin-top: -1rem;
    }
    .gd-header-title {
        font-size: 3rem;
        font-weight: 800;
        letter-spacing: -0.06em;
        line-height: 1.02;
        margin-bottom: 0.75rem;
    }
    .gd-header-sub {
        font-size: 1.1rem;
        opacity: 0.88;
        max-width: 38rem;
        line-height: 1.8;
    }
    .gd-body {
        margin-top: -3.5rem;
        padding-bottom: 4rem;
    }

    /* Phase Card */
    .gd-phase {
        background: var(--color-card);
        border-radius: 1.25rem;
        padding: 2.5rem 2.5rem 2rem;
        border: 1px solid rgba(203,213,225,0.55);
        box-shadow: var(--shadow-card);
        margin-bottom: 1.5rem;
    }
    .gd-phase-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1.75rem;
    }
    .gd-phase-badge {
        width: 44px;
        height: 44px;
        border-radius: 0.85rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: var(--font-display);
        font-weight: 800;
        font-size: 1.1rem;
        color: white;
        flex-shrink: 0;
    }
    .gd-phase-badge.p1 { background: var(--gradient-brand); }
    .gd-phase-badge.p2 { background: var(--gradient-cyan); }
    .gd-phase-badge.p3 { background: var(--gradient-warning); }
    .gd-phase-badge.p4 { background: var(--gradient-success); }
    .gd-phase-badge.p5 { background: var(--gradient-danger); }
    .gd-phase-title {
        font-size: 1.4rem;
        font-weight: 700;
        color: var(--color-primary);
        letter-spacing: -0.04em;
        margin: 0;
    }
    .gd-phase-desc {
        font-size: 0.95rem;
        color: var(--color-muted);
        margin: 0;
    }

    /* Step */
    .gd-steps {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        position: relative;
    }
    .gd-step {
        display: flex;
        gap: 1.15rem;
        align-items: flex-start;
        position: relative;
    }
    .gd-step-line {
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: center;
        flex-shrink: 0;
        width: 40px;
    }
    .gd-step-num {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: var(--gradient-soft);
        border: 2px solid rgba(6,182,212,0.35);
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: var(--font-display);
        font-weight: 700;
        font-size: 0.9rem;
        color: var(--color-secondary-strong);
        flex-shrink: 0;
        z-index: 1;
    }
    .gd-step:not(:last-child) .gd-step-line::after {
        content: '';
        position: absolute;
        top: 42px;
        width: 2px;
        height: calc(100% + 1rem - 42px);
        background: linear-gradient(180deg, rgba(6,182,212,0.35), rgba(6,182,212,0.08));
        z-index: 0;
    }
    .gd-step-content {
        flex: 1;
        padding-bottom: 0.25rem;
    }
    .gd-step-title {
        font-family: var(--font-display);
        font-weight: 700;
        font-size: 1.05rem;
        color: var(--color-heading);
        margin-bottom: 0.3rem;
        letter-spacing: -0.03em;
    }
    .gd-step-text {
        font-size: 0.93rem;
        color: var(--color-muted);
        line-height: 1.75;
        margin: 0;
    }
    .gd-step-icon {
        color: var(--color-secondary);
        margin-right: 0.35rem;
    }

    /* Tip Box */
    .gd-tip {
        background: linear-gradient(135deg, rgba(6,182,212,0.08) 0%, rgba(6,182,212,0.16) 100%);
        border: 1px solid rgba(6,182,212,0.25);
        border-radius: 0.85rem;
        padding: 1rem 1.25rem;
        margin-top: 0.6rem;
        display: flex;
        align-items: flex-start;
        gap: 0.65rem;
    }
    .gd-tip-icon {
        color: var(--color-secondary-strong);
        font-size: 1.1rem;
        margin-top: 0.1rem;
        flex-shrink: 0;
    }
    .gd-tip-text {
        font-size: 0.88rem;
        color: var(--color-secondary-strong);
        line-height: 1.65;
        margin: 0;
        font-weight: 500;
    }

    /* Warning box */
    .gd-warn {
        background: linear-gradient(135deg, rgba(245,158,11,0.08) 0%, rgba(245,158,11,0.16) 100%);
        border: 1px solid rgba(245,158,11,0.3);
        border-radius: 0.85rem;
        padding: 1rem 1.25rem;
        margin-top: 0.6rem;
        display: flex;
        align-items: flex-start;
        gap: 0.65rem;
    }
    .gd-warn-icon {
        color: #D97706;
        font-size: 1.1rem;
        margin-top: 0.1rem;
        flex-shrink: 0;
    }
    .gd-warn-text {
        font-size: 0.88rem;
        color: #92400E;
        line-height: 1.65;
        margin: 0;
        font-weight: 500;
    }

    /* Quick nav */
    .gd-quicknav {
        background: var(--color-card);
        border-radius: 1.25rem;
        padding: 2rem 2.5rem;
        border: 1px solid rgba(203,213,225,0.55);
        box-shadow: var(--shadow-card);
        margin-bottom: 1.5rem;
    }
    .gd-quicknav-title {
        font-size: 1rem;
        font-weight: 700;
        color: var(--color-primary);
        margin-bottom: 1rem;
        letter-spacing: -0.03em;
    }
    .gd-quicknav-items {
        display: flex;
        flex-wrap: wrap;
        gap: 0.65rem;
    }
    .gd-quicknav-btn {
        background: var(--gradient-soft);
        border: 1px solid rgba(203,213,225,0.4);
        border-radius: 50px;
        padding: 0.5rem 1.15rem;
        font-size: 0.88rem;
        font-weight: 600;
        color: var(--color-primary);
        text-decoration: none;
        transition: all 0.25s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
    }
    .gd-quicknav-btn:hover {
        background: var(--gradient-brand);
        color: white;
        transform: translateY(-2px);
        box-shadow: var(--shadow-soft);
    }
    .gd-quicknav-btn i { font-size: 0.9rem; }

    /* Flow diagram */
    .gd-flow {
        background: var(--color-card);
        border-radius: 1.25rem;
        padding: 2.5rem;
        border: 1px solid rgba(203,213,225,0.55);
        box-shadow: var(--shadow-card);
        margin-bottom: 1.5rem;
        text-align: center;
    }
    .gd-flow-title {
        font-size: 1.15rem;
        font-weight: 700;
        color: var(--color-primary);
        margin-bottom: 1.5rem;
        letter-spacing: -0.03em;
    }
    .gd-flow-row {
        display: flex;
        align-items: center;
        justify-content: center;
        flex-wrap: wrap;
        gap: 0.5rem;
    }
    .gd-flow-item {
        background: var(--gradient-soft);
        border: 1px solid rgba(203,213,225,0.4);
        border-radius: 0.75rem;
        padding: 0.65rem 1rem;
        font-size: 0.82rem;
        font-weight: 600;
        color: var(--color-primary);
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
    }
    .gd-flow-item i { color: var(--color-secondary); }
    .gd-flow-arrow {
        color: var(--color-secondary);
        font-size: 1.1rem;
    }

    /* CTA */
    .gd-cta {
        background: radial-gradient(circle at top right, rgba(255,255,255,0.18), transparent 34%), var(--gradient-brand);
        border-radius: 1.5rem;
        padding: 3.5rem 3rem;
        text-align: center;
        color: white;
        box-shadow: 0 24px 50px rgba(15, 23, 42, 0.22);
    }
    .gd-cta-title {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 0.6rem;
        letter-spacing: -0.04em;
    }
    .gd-cta-text {
        opacity: 0.9;
        margin-bottom: 1.75rem;
        max-width: 30rem;
        margin-left: auto;
        margin-right: auto;
        line-height: 1.8;
    }
    .gd-cta-btn {
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
    .gd-cta-btn:hover {
        background: var(--color-secondary);
        color: white;
        transform: translateY(-3px);
        box-shadow: 0 14px 34px rgba(var(--color-secondary-rgb), 0.28);
    }

    @media (max-width: 768px) {
        .gd-header { padding: 4rem 0 5.5rem; }
        .gd-header-title { font-size: 2.3rem; }
        .gd-phase { padding: 2rem 1.5rem 1.5rem; }
        .gd-quicknav { padding: 1.5rem; }
        .gd-flow { padding: 1.5rem; }
        .gd-flow-row { flex-direction: column; }
        .gd-flow-arrow { transform: rotate(90deg); }
        .gd-cta { padding: 2.5rem 1.5rem; }
    }
</style>

<!-- Header -->
<div class="gd-header">
    <div class="container">
        <h1 class="gd-header-title"><i class="bi bi-book me-2"></i>Panduan Penyewaan</h1>
        <p class="gd-header-sub">Ikuti langkah-langkah berikut untuk menyewa kendaraan di RentalHub dengan mudah dan cepat.</p>
    </div>
</div>

<!-- Body -->
<div class="gd-body">
    <div class="container">

        <!-- Flow Overview -->
        <div class="gd-flow">
            <div class="gd-flow-title">Alur Penyewaan Kendaraan</div>
            <div class="gd-flow-row">
                <div class="gd-flow-item"><i class="bi bi-person-plus"></i> Daftar</div>
                <i class="bi bi-chevron-right gd-flow-arrow"></i>
                <div class="gd-flow-item"><i class="bi bi-person-badge"></i> Verifikasi KTP</div>
                <i class="bi bi-chevron-right gd-flow-arrow"></i>
                <div class="gd-flow-item"><i class="bi bi-search"></i> Pilih Kendaraan</div>
                <i class="bi bi-chevron-right gd-flow-arrow"></i>
                <div class="gd-flow-item"><i class="bi bi-calendar-check"></i> Booking</div>
                <i class="bi bi-chevron-right gd-flow-arrow"></i>
                <div class="gd-flow-item"><i class="bi bi-credit-card"></i> Bayar</div>
                <i class="bi bi-chevron-right gd-flow-arrow"></i>
                <div class="gd-flow-item"><i class="bi bi-car-front"></i> Ambil</div>
                <i class="bi bi-chevron-right gd-flow-arrow"></i>
                <div class="gd-flow-item"><i class="bi bi-check-circle"></i> Selesai</div>
            </div>
        </div>

        <!-- Quick Nav -->
        <div class="gd-quicknav">
            <div class="gd-quicknav-title"><i class="bi bi-signpost-2 me-2" style="color: var(--color-secondary);"></i>Langsung ke Tahap</div>
            <div class="gd-quicknav-items">
                <a href="#phase1" class="gd-quicknav-btn"><i class="bi bi-1-circle"></i> Buat Akun</a>
                <a href="#phase2" class="gd-quicknav-btn"><i class="bi bi-2-circle"></i> Pilih & Booking</a>
                <a href="#phase3" class="gd-quicknav-btn"><i class="bi bi-3-circle"></i> Pembayaran</a>
                <a href="#phase4" class="gd-quicknav-btn"><i class="bi bi-4-circle"></i> Pengambilan</a>
                <a href="#phase5" class="gd-quicknav-btn"><i class="bi bi-5-circle"></i> Pengembalian</a>
            </div>
        </div>

        <!-- Phase 1: Registration & KTP -->
        <div class="gd-phase" id="phase1">
            <div class="gd-phase-header">
                <div class="gd-phase-badge p1">1</div>
                <div>
                    <h2 class="gd-phase-title">Buat Akun & Verifikasi</h2>
                    <p class="gd-phase-desc">Siapkan akun dan identitas Anda sebelum menyewa</p>
                </div>
            </div>
            <div class="gd-steps">
                <div class="gd-step">
                    <div class="gd-step-line">
                        <div class="gd-step-num">1</div>
                    </div>
                    <div class="gd-step-content">
                        <div class="gd-step-title"><i class="bi bi-person-plus gd-step-icon"></i>Daftar Akun</div>
                        <p class="gd-step-text">
                            Klik tombol <strong>Daftar</strong> di halaman utama. Isi nama lengkap, alamat email, dan buat password. Setelah berhasil, Anda akan langsung masuk ke dashboard.
                        </p>
                    </div>
                </div>
                <div class="gd-step">
                    <div class="gd-step-line">
                        <div class="gd-step-num">2</div>
                    </div>
                    <div class="gd-step-content">
                        <div class="gd-step-title"><i class="bi bi-person-badge gd-step-icon"></i>Upload KTP</div>
                        <p class="gd-step-text">
                            Buka menu <strong>Profile → Verifikasi KTP</strong>. Masukkan nomor KTP (16 digit) dan upload foto KTP yang jelas. Format yang diterima: JPG atau PNG, maksimal 5MB.
                        </p>
                        <div class="gd-warn">
                            <i class="bi bi-exclamation-triangle-fill gd-warn-icon"></i>
                            <p class="gd-warn-text">Verifikasi KTP <strong>wajib dilakukan</strong> sebelum bisa melakukan pembayaran. Pastikan foto KTP terlihat jelas dan tidak buram.</p>
                        </div>
                    </div>
                </div>
                <div class="gd-step">
                    <div class="gd-step-line">
                        <div class="gd-step-num">3</div>
                    </div>
                    <div class="gd-step-content">
                        <div class="gd-step-title"><i class="bi bi-hourglass-split gd-step-icon"></i>Tunggu Verifikasi Admin</div>
                        <p class="gd-step-text">
                            Tim admin akan memeriksa data KTP Anda. Proses ini biasanya memakan waktu beberapa saat. Status verifikasi bisa dilihat di halaman profil.
                        </p>
                        <div class="gd-tip">
                            <i class="bi bi-lightbulb-fill gd-tip-icon"></i>
                            <p class="gd-tip-text">Sambil menunggu verifikasi, Anda sudah bisa browsing dan memilih kendaraan yang diinginkan!</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Phase 2: Browse & Book -->
        <div class="gd-phase" id="phase2">
            <div class="gd-phase-header">
                <div class="gd-phase-badge p2">2</div>
                <div>
                    <h2 class="gd-phase-title">Pilih Kendaraan & Booking</h2>
                    <p class="gd-phase-desc">Temukan kendaraan yang sesuai dan buat booking</p>
                </div>
            </div>
            <div class="gd-steps">
                <div class="gd-step">
                    <div class="gd-step-line">
                        <div class="gd-step-num">4</div>
                    </div>
                    <div class="gd-step-content">
                        <div class="gd-step-title"><i class="bi bi-search gd-step-icon"></i>Cari Kendaraan</div>
                        <p class="gd-step-text">
                            Buka menu <strong>Cari Kendaraan</strong> di navigasi. Anda bisa memfilter berdasarkan jenis kendaraan (mobil/motor), rentang tanggal, dan harga. Setiap kendaraan menampilkan foto, spesifikasi, dan harga per hari.
                        </p>
                    </div>
                </div>
                <div class="gd-step">
                    <div class="gd-step-line">
                        <div class="gd-step-num">5</div>
                    </div>
                    <div class="gd-step-content">
                        <div class="gd-step-title"><i class="bi bi-eye gd-step-icon"></i>Lihat Detail Kendaraan</div>
                        <p class="gd-step-text">
                            Klik kendaraan untuk melihat detail lengkap termasuk spesifikasi, fitur, foto, dan ketersediaan. Cek kalender ketersediaan untuk memastikan kendaraan tersedia di tanggal yang diinginkan.
                        </p>
                    </div>
                </div>
                <div class="gd-step">
                    <div class="gd-step-line">
                        <div class="gd-step-num">6</div>
                    </div>
                    <div class="gd-step-content">
                        <div class="gd-step-title"><i class="bi bi-calendar-plus gd-step-icon"></i>Buat Booking</div>
                        <p class="gd-step-text">
                            Klik tombol <strong>Booking Sekarang</strong>, lalu tentukan tanggal mulai, tanggal selesai, jam pengambilan, dan jam pengembalian. Sistem akan otomatis menghitung durasi dan total harga sewa.
                        </p>
                        <div class="gd-tip">
                            <i class="bi bi-lightbulb-fill gd-tip-icon"></i>
                            <p class="gd-tip-text">Jika kendaraan sedang disewa orang lain pada tanggal tersebut, Anda akan otomatis masuk <strong>waiting list</strong> dan dihubungi saat kendaraan tersedia.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Phase 3: Payment -->
        <div class="gd-phase" id="phase3">
            <div class="gd-phase-header">
                <div class="gd-phase-badge p3">3</div>
                <div>
                    <h2 class="gd-phase-title">Pembayaran</h2>
                    <p class="gd-phase-desc">Lakukan pembayaran untuk mengkonfirmasi booking</p>
                </div>
            </div>
            <div class="gd-steps">
                <div class="gd-step">
                    <div class="gd-step-line">
                        <div class="gd-step-num">7</div>
                    </div>
                    <div class="gd-step-content">
                        <div class="gd-step-title"><i class="bi bi-credit-card gd-step-icon"></i>Pilih Metode Pembayaran</div>
                        <p class="gd-step-text">
                            Setelah booking dibuat, lanjutkan ke konfirmasi pembayaran via WhatsApp admin:
                        </p>
                        <div style="margin-top: 0.5rem; display: flex; flex-direction: column; gap: 0.35rem;">
                            <span style="font-size: 0.9rem; color: var(--color-muted);"><i class="bi bi-whatsapp gd-step-icon"></i><strong>Konfirmasi WhatsApp</strong> — Transfer ke rekening RentalHub lalu kirim bukti transfer melalui chat WhatsApp</span>
                        </div>
                    </div>
                </div>
                <div class="gd-step">
                    <div class="gd-step-line">
                        <div class="gd-step-num">8</div>
                    </div>
                    <div class="gd-step-content">
                        <div class="gd-step-title"><i class="bi bi-whatsapp gd-step-icon"></i>Kirim Bukti Transfer via WhatsApp</div>
                        <p class="gd-step-text">
                            Transfer sesuai nominal ke rekening yang tertera, lalu kirim screenshot bukti transfer ke admin lewat tombol WhatsApp pada halaman pembayaran.
                        </p>
                        <div class="gd-warn">
                            <i class="bi bi-exclamation-triangle-fill gd-warn-icon"></i>
                            <p class="gd-warn-text">Pastikan nominal transfer <strong>sesuai persis</strong> dengan jumlah yang tertera agar verifikasi berjalan lancar.</p>
                        </div>
                    </div>
                </div>
                <div class="gd-step">
                    <div class="gd-step-line">
                        <div class="gd-step-num">9</div>
                    </div>
                    <div class="gd-step-content">
                        <div class="gd-step-title"><i class="bi bi-check2-circle gd-step-icon"></i>Verifikasi Pembayaran</div>
                        <p class="gd-step-text">
                            Admin akan memverifikasi pembayaran Anda. Setelah terverifikasi, status booking berubah menjadi <strong>Terkonfirmasi</strong> dan Anda akan menerima notifikasi email.
                        </p>
                        <div class="gd-tip">
                            <i class="bi bi-lightbulb-fill gd-tip-icon"></i>
                            <p class="gd-tip-text">Cek status pembayaran kapan saja di halaman <strong>Booking Saya</strong>. Jika ditolak, lakukan transfer ulang dan kirim bukti terbaru via WhatsApp.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Phase 4: Pickup -->
        <div class="gd-phase" id="phase4">
            <div class="gd-phase-header">
                <div class="gd-phase-badge p4">4</div>
                <div>
                    <h2 class="gd-phase-title">Pengambilan Kendaraan</h2>
                    <p class="gd-phase-desc">Ambil kendaraan sesuai jadwal yang ditentukan</p>
                </div>
            </div>
            <div class="gd-steps">
                <div class="gd-step">
                    <div class="gd-step-line">
                        <div class="gd-step-num">10</div>
                    </div>
                    <div class="gd-step-content">
                        <div class="gd-step-title"><i class="bi bi-envelope-check gd-step-icon"></i>Terima Konfirmasi</div>
                        <p class="gd-step-text">
                            Setelah pembayaran diverifikasi, Anda akan menerima email konfirmasi berisi detail booking: info kendaraan, tanggal, dan jam pengambilan.
                        </p>
                    </div>
                </div>
                <div class="gd-step">
                    <div class="gd-step-line">
                        <div class="gd-step-num">11</div>
                    </div>
                    <div class="gd-step-content">
                        <div class="gd-step-title"><i class="bi bi-car-front gd-step-icon"></i>Ambil Kendaraan</div>
                        <p class="gd-step-text">
                            Datang sesuai jadwal pengambilan. Bawa KTP asli untuk verifikasi identitas. Periksa kondisi kendaraan bersama petugas sebelum membawa kendaraan.
                        </p>
                        <div class="gd-tip">
                            <i class="bi bi-lightbulb-fill gd-tip-icon"></i>
                            <p class="gd-tip-text">Foto kondisi kendaraan saat pengambilan sebagai dokumentasi bersama untuk menghindari perselisihan saat pengembalian.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Phase 5: Return -->
        <div class="gd-phase" id="phase5">
            <div class="gd-phase-header">
                <div class="gd-phase-badge p5">5</div>
                <div>
                    <h2 class="gd-phase-title">Pengembalian Kendaraan</h2>
                    <p class="gd-phase-desc">Kembalikan kendaraan tepat waktu untuk menghindari denda</p>
                </div>
            </div>
            <div class="gd-steps">
                <div class="gd-step">
                    <div class="gd-step-line">
                        <div class="gd-step-num">12</div>
                    </div>
                    <div class="gd-step-content">
                        <div class="gd-step-title"><i class="bi bi-arrow-return-left gd-step-icon"></i>Kembalikan Tepat Waktu</div>
                        <p class="gd-step-text">
                            Kembalikan kendaraan sesuai tanggal dan jam yang disepakati. Pastikan kendaraan dalam kondisi baik, bersih, dan bahan bakar sesuai ketentuan.
                        </p>
                        <div class="gd-warn">
                            <i class="bi bi-exclamation-triangle-fill gd-warn-icon"></i>
                            <p class="gd-warn-text">Keterlambatan pengembalian akan dikenakan <strong>denda per hari</strong> sesuai tarif harian kendaraan. Pastikan Anda mengembalikan tepat waktu!</p>
                        </div>
                    </div>
                </div>
                <div class="gd-step">
                    <div class="gd-step-line">
                        <div class="gd-step-num">13</div>
                    </div>
                    <div class="gd-step-content">
                        <div class="gd-step-title"><i class="bi bi-clipboard-check gd-step-icon"></i>Inspeksi Pengembalian</div>
                        <p class="gd-step-text">
                            Petugas akan memeriksa kondisi kendaraan saat pengembalian, termasuk kondisi fisik, level bahan bakar, dan kelengkapan dokumen/aksesoris. Jika ada kerusakan, biaya tambahan akan diinformasikan.
                        </p>
                    </div>
                </div>
                <div class="gd-step">
                    <div class="gd-step-line">
                        <div class="gd-step-num">14</div>
                    </div>
                    <div class="gd-step-content">
                        <div class="gd-step-title"><i class="bi bi-trophy gd-step-icon"></i>Booking Selesai!</div>
                        <p class="gd-step-text">
                            Setelah inspeksi selesai, status booking berubah menjadi <strong>Selesai</strong>. Riwayat booking bisa dilihat kapan saja di halaman <strong>Booking Saya</strong>. Terima kasih sudah menggunakan RentalHub!
                        </p>
                        <div class="gd-tip">
                            <i class="bi bi-lightbulb-fill gd-tip-icon"></i>
                            <p class="gd-tip-text">Punya pengalaman menyenangkan? Booking lagi kapan saja — kendaraan favorit Anda selalu tersedia!</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- CTA -->
        <div class="gd-cta">
            <h3 class="gd-cta-title">Siap Menyewa?</h3>
            <p class="gd-cta-text">Ikuti langkah-langkah di atas dan mulai perjalanan Anda bersama RentalHub sekarang!</p>
            @auth
                <a href="{{ route('vehicles.browse') }}" class="gd-cta-btn">
                    <i class="bi bi-search"></i> Cari Kendaraan
                </a>
            @else
                <a href="{{ route('register') }}" class="gd-cta-btn">
                    <i class="bi bi-person-plus"></i> Daftar Sekarang
                </a>
            @endauth
        </div>

    </div>
</div>
@endsection