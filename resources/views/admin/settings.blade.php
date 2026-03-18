@extends('layouts.admin')

@section('title', 'Pengaturan Admin')
@section('page-title', 'Pengaturan')

@section('content')
<style>
    .settings-hero {
        background:
            radial-gradient(circle at top right, rgba(255,255,255,0.14), transparent 34%),
            radial-gradient(circle at bottom left, rgba(6,182,212,0.22), transparent 26%),
            var(--gradient-brand);
        color: white;
        padding: 3.15rem 0 2.7rem;
        margin-bottom: 2rem;
        border-radius: 0 0 2rem 2rem;
        position: relative;
        overflow: hidden;
    }
    .settings-hero h1 {
        font-weight: 700;
        font-size: clamp(2rem, 4vw, 3rem);
        margin-bottom: 0.65rem;
        letter-spacing: -0.055em;
    }
    .settings-shell {
        max-width: 1280px;
        margin: 0 auto;
        padding-bottom: 3rem;
    }
    .settings-kicker {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.45rem 0.8rem;
        border-radius: 999px;
        background: rgba(255,255,255,0.12);
        border: 1px solid rgba(255,255,255,0.14);
        font-size: 0.82rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        margin-bottom: 1rem;
    }
    .settings-grid {
        display: grid;
        grid-template-columns: minmax(0, 2fr) minmax(280px, 1fr);
        gap: 1.5rem;
        align-items: start;
    }
    .settings-card {
        background: white;
        border-radius: 1.5rem;
        padding: 1.75rem;
        box-shadow: var(--shadow-card);
        border: 1px solid rgba(203,213,225,0.68);
    }
    .settings-card.primary {
        background: linear-gradient(180deg, rgba(255,255,255,0.98) 0%, rgba(248,250,252,0.98) 100%);
    }
    .settings-card h4 {
        font-weight: 700;
        margin-bottom: 0.4rem;
        color: #1a202c;
    }
    .settings-card p {
        color: #64748b;
    }
    .settings-card .form-label {
        font-weight: 600;
        color: #334155;
    }
    .settings-card .form-control {
        border-radius: 0.95rem;
        padding: 0.85rem 1rem;
        border-color: rgba(148,163,184,0.45);
    }
    .settings-card .form-control:focus {
        border-color: rgba(37,99,235,0.35);
        box-shadow: 0 0 0 0.22rem rgba(59,130,246,0.12);
    }
    .settings-card .form-text {
        color: #64748b;
        font-size: 0.85rem;
    }
    .settings-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.55rem 0.9rem;
        border-radius: 999px;
        background: rgba(15,23,42,0.06);
        color: #334155;
        font-weight: 600;
        font-size: 0.85rem;
    }
    .settings-tab-shell {
        padding: 0;
        overflow: hidden;
    }
    .settings-shell-header {
        padding: 1.75rem 1.75rem 1.25rem;
        border-bottom: 1px solid rgba(203,213,225,0.72);
        background: linear-gradient(180deg, rgba(255,255,255,0.98) 0%, rgba(248,250,252,0.98) 100%);
    }
    .settings-tabs {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 0.85rem;
        padding: 1.1rem 1.75rem 0;
        border-bottom: 1px solid rgba(203,213,225,0.72);
        background: linear-gradient(180deg, rgba(248,250,252,0.92) 0%, rgba(255,255,255,0.98) 100%);
    }
    .settings-tabs .nav-link {
        border: 0;
        border-radius: 1.15rem 1.15rem 0 0;
        padding: 1rem 1rem 1.05rem;
        text-align: left;
        display: flex;
        align-items: flex-start;
        gap: 0.8rem;
        background: rgba(255,255,255,0.82);
        color: #334155;
        box-shadow: inset 0 0 0 1px rgba(203,213,225,0.78);
    }
    .settings-tabs .nav-link .icon {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 0.95rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(6,182,212,0.12);
        color: var(--color-secondary-strong);
        flex-shrink: 0;
    }
    .settings-tabs .nav-link strong {
        display: block;
        margin-bottom: 0.18rem;
        color: inherit;
    }
    .settings-tabs .nav-link span {
        color: #64748b;
        font-size: 0.88rem;
        line-height: 1.45;
    }
    .settings-tabs .nav-link.active {
        background: var(--gradient-brand);
        color: white;
        box-shadow: 0 16px 36px rgba(var(--color-primary-rgb), 0.16);
    }
    .settings-tabs .nav-link.active .icon {
        background: rgba(255,255,255,0.12);
        color: white;
    }
    .settings-tabs .nav-link.active span {
        color: rgba(255,255,255,0.78);
    }
    .settings-pane {
        padding: 1.75rem;
    }
    .settings-summary-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 1rem;
        margin-top: 1.5rem;
    }
    .time-stat {
        background: #f8fafc;
        border: 1px solid rgba(203,213,225,0.78);
        border-radius: 1.2rem;
        padding: 1.1rem 1.2rem;
        height: 100%;
    }
    .time-stat.highlight {
        background: linear-gradient(135deg, rgba(17,24,39,0.04) 0%, rgba(6,182,212,0.1) 100%);
    }
    .time-stat .label {
        color: #64748b;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        margin-bottom: 0.45rem;
    }
    .time-stat .value {
        font-size: clamp(1.6rem, 2.5vw, 2rem);
        font-weight: 700;
        color: #0f172a;
    }
    .preview-band {
        display: flex;
        align-items: center;
        gap: 0.85rem;
        padding: 1rem 1.1rem;
        margin-top: 1.5rem;
        border-radius: 1.2rem;
        background: linear-gradient(135deg, rgba(17,24,39,0.92) 0%, rgba(8,145,178,0.92) 100%);
        color: white;
    }
    .preview-icon {
        width: 48px;
        height: 48px;
        border-radius: 1rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(255,255,255,0.12);
        border: 1px solid rgba(255,255,255,0.14);
        flex-shrink: 0;
    }
    .preview-copy {
        min-width: 0;
    }
    .preview-copy strong {
        display: block;
        font-size: 1rem;
        margin-bottom: 0.18rem;
    }
    .preview-copy span {
        color: rgba(255,255,255,0.76);
        font-size: 0.92rem;
    }
    .pane-section + .pane-section {
        margin-top: 1.5rem;
    }
    .helper-list {
        padding-left: 1.1rem;
        margin-bottom: 0;
        color: #475569;
    }
    .helper-list li + li {
        margin-top: 0.65rem;
    }
    .insight-list {
        display: grid;
        gap: 0.85rem;
    }
    .insight-item {
        display: flex;
        gap: 0.85rem;
        align-items: flex-start;
        padding: 1rem;
        border-radius: 1rem;
        background: #f8fafc;
        border: 1px solid rgba(203,213,225,0.72);
    }
    .insight-item .icon {
        width: 2.3rem;
        height: 2.3rem;
        border-radius: 0.85rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(6,182,212,0.12);
        color: var(--color-secondary-strong);
        flex-shrink: 0;
    }
    .insight-item strong {
        display: block;
        margin-bottom: 0.18rem;
        color: #0f172a;
    }
    .insight-item span {
        color: #64748b;
        font-size: 0.92rem;
    }
    .aside-stack {
        display: grid;
        gap: 1.5rem;
        position: sticky;
        top: 1.25rem;
    }
    .quick-link {
        display: inline-flex;
        align-items: center;
        gap: 0.55rem;
        text-decoration: none;
        font-weight: 600;
    }
    .quick-link + .quick-link {
        margin-top: 0.85rem;
    }
    .route-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 0.95rem;
        border-radius: 1rem;
        background: #f8fafc;
        border: 1px solid rgba(203,213,225,0.72);
        color: #334155;
        font-weight: 600;
    }
    .placeholder-card {
        background: linear-gradient(180deg, rgba(248,250,252,0.98) 0%, rgba(255,255,255,0.98) 100%);
        border: 1px dashed rgba(148,163,184,0.82);
        border-radius: 1.35rem;
        padding: 1.35rem;
    }
    .placeholder-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 1rem;
        margin-top: 1.25rem;
    }
    .placeholder-item {
        background: white;
        border: 1px solid rgba(203,213,225,0.72);
        border-radius: 1.1rem;
        padding: 1rem;
    }
    .placeholder-item strong {
        display: block;
        color: #0f172a;
        margin-bottom: 0.2rem;
    }
    .placeholder-item span {
        color: #64748b;
        font-size: 0.92rem;
    }
    .placeholder-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.5rem 0.8rem;
        border-radius: 999px;
        background: rgba(6,182,212,0.12);
        color: var(--color-secondary-strong);
        font-weight: 700;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.06em;
    }
    @media (max-width: 1279.98px) {
        .settings-grid {
            grid-template-columns: 1fr;
        }
        .settings-tabs {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
        .aside-stack {
            position: static;
        }
    }
    @media (max-width: 991.98px) {
        .settings-grid {
            grid-template-columns: 1fr;
        }
        .settings-tabs {
            grid-template-columns: 1fr;
        }
        .settings-summary-grid {
            grid-template-columns: 1fr;
        }
        .placeholder-grid {
            grid-template-columns: 1fr;
        }
        .aside-stack {
            position: static;
        }
    }
    @media (max-width: 767.98px) {
        .settings-shell-header,
        .settings-pane {
            padding: 1.25rem;
        }
        .settings-tabs {
            padding: 1rem 1.25rem 0;
            grid-template-columns: 1fr;
        }
        .settings-tabs .nav-link {
            padding: 0.95rem;
        }
        .preview-band {
            align-items: flex-start;
        }
    }
</style>

<div class="settings-hero">
    <div class="container settings-shell">
        <a href="{{ route('admin.dashboard') }}" class="text-white text-decoration-none d-inline-flex align-items-center gap-2 mb-3 opacity-75">
            <i class="bi bi-arrow-left"></i>
            <span>Kembali ke dashboard admin</span>
        </a>
        <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap">
            <div>
                <span class="settings-kicker">
                    <i class="bi bi-sliders"></i>
                    Area Admin
                </span>
                <h1>Pengaturan Admin</h1>
                <p class="mb-0 opacity-75">Pusat pengaturan operasional yang memengaruhi cara customer melihat dan mengisi jadwal booking.</p>
            </div>
            <span class="settings-badge">
                <i class="bi bi-sliders"></i>
                Jadwal booking default
            </span>
        </div>
    </div>
</div>

<div class="container settings-shell">
    <div class="settings-grid">
        <div>
            <div class="settings-card settings-tab-shell">
                <div class="settings-shell-header d-flex justify-content-between align-items-start gap-3 flex-wrap">
                    <div>
                        <h4 class="mb-2">Kategori Pengaturan</h4>
                        <p class="mb-0">Pengaturan admin sekarang dipisah per kategori supaya nantinya gampang nambah aturan baru tanpa bikin halaman ini berantakan.</p>
                    </div>
                    <span class="settings-badge">
                        <i class="bi bi-layout-text-sidebar-reverse"></i>
                        3 tab siap dipakai
                    </span>
                </div>

                <ul class="nav settings-tabs" id="adminSettingsTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="booking-settings-tab" data-bs-toggle="tab" data-bs-target="#booking-settings-pane" type="button" role="tab" aria-controls="booking-settings-pane" aria-selected="true">
                            <span class="icon"><i class="bi bi-clock-history"></i></span>
                            <span>
                                <strong>Jadwal Booking</strong>
                                <span>Atur jam default pickup dan pengembalian.</span>
                            </span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="operations-settings-tab" data-bs-toggle="tab" data-bs-target="#operations-settings-pane" type="button" role="tab" aria-controls="operations-settings-pane" aria-selected="false">
                            <span class="icon"><i class="bi bi-gear-wide-connected"></i></span>
                            <span>
                                <strong>Operasional</strong>
                                <span>Slot untuk jam kerja, biaya telat, dan aturan lapangan.</span>
                            </span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="communication-settings-tab" data-bs-toggle="tab" data-bs-target="#communication-settings-pane" type="button" role="tab" aria-controls="communication-settings-pane" aria-selected="false">
                            <span class="icon"><i class="bi bi-chat-dots"></i></span>
                            <span>
                                <strong>Komunikasi</strong>
                                <span>Siapkan area template WhatsApp dan notifikasi customer.</span>
                            </span>
                        </button>
                    </li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane fade show active" id="booking-settings-pane" role="tabpanel" aria-labelledby="booking-settings-tab" tabindex="0">
                        <div class="settings-pane">
                            <div class="pane-section d-flex justify-content-between align-items-start gap-3 flex-wrap mb-4">
                                <div>
                                    <h4>Default Jadwal Booking</h4>
                                    <p class="mb-0">Jam ini akan terisi otomatis saat customer membuka form booking baru. Cocok untuk menjaga jam operasional tetap konsisten tanpa mengunci customer sepenuhnya.</p>
                                </div>
                                <span class="settings-badge">
                                    <i class="bi bi-clock-history"></i>
                                    Aktif untuk booking baru
                                </span>
                            </div>

                            <form method="POST" action="{{ route('admin.settings.booking-schedule') }}" class="row g-3 align-items-start pane-section">
                                @csrf
                                <div class="col-md-5">
                                    <label for="pickup_time" class="form-label">Jam Ambil Default</label>
                                    <input
                                        type="time"
                                        class="form-control @error('pickup_time') is-invalid @enderror"
                                        id="pickup_time"
                                        name="pickup_time"
                                        value="{{ old('pickup_time', $bookingScheduleDefaults['pickup_time']) }}"
                                        required
                                    >
                                    <div class="form-text">Dipakai sebagai nilai awal ketika customer memilih tanggal mulai.</div>
                                    @error('pickup_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-5">
                                    <label for="return_time" class="form-label">Jam Kembali Default</label>
                                    <input
                                        type="time"
                                        class="form-control @error('return_time') is-invalid @enderror"
                                        id="return_time"
                                        name="return_time"
                                        value="{{ old('return_time', $bookingScheduleDefaults['return_time']) }}"
                                        required
                                    >
                                    <div class="form-text">Dipakai sebagai nilai awal untuk pengembalian kendaraan.</div>
                                    @error('return_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-2 d-grid">
                                    <label class="form-label d-none d-md-block">&nbsp;</label>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-save me-1"></i>Simpan
                                    </button>
                                </div>
                            </form>

                            <div class="settings-summary-grid pane-section">
                                <div class="time-stat">
                                    <div class="label">Jam ambil aktif</div>
                                    <div class="value">{{ $bookingScheduleDefaults['pickup_time'] }}</div>
                                </div>
                                <div class="time-stat">
                                    <div class="label">Jam kembali aktif</div>
                                    <div class="value">{{ $bookingScheduleDefaults['return_time'] }}</div>
                                </div>
                                <div class="time-stat highlight">
                                    <div class="label">Rentang jadwal default</div>
                                    <div class="value">{{ $scheduleWindowLabel }}</div>
                                </div>
                            </div>

                            <div class="preview-band pane-section">
                                <span class="preview-icon">
                                    <i class="bi bi-eye"></i>
                                </span>
                                <div class="preview-copy">
                                    <strong>Preview ke customer: {{ $bookingScheduleDefaults['pickup_time'] }} sampai {{ $bookingScheduleDefaults['return_time'] }}</strong>
                                    <span>Nilai ini muncul sebagai isi awal form booking dan masih bisa diubah customer sebelum checkout.</span>
                                </div>
                            </div>

                            <div class="settings-card pane-section mt-4">
                                <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap mb-3">
                                    <div>
                                        <h4>Dampak Pengaturan Ini</h4>
                                        <p class="mb-0">Satu perubahan kecil di sini langsung memengaruhi pengalaman booking customer berikutnya.</p>
                                    </div>
                                    <span class="route-chip">
                                        <i class="bi bi-calendar-check"></i>
                                        Form booking baru
                                    </span>
                                </div>
                                <div class="insight-list">
                                    <div class="insight-item">
                                        <span class="icon"><i class="bi bi-lightning-charge"></i></span>
                                        <div>
                                            <strong>Lebih cepat untuk customer</strong>
                                            <span>Customer tidak mulai dari kolom kosong karena jam operasional sudah diisi otomatis.</span>
                                        </div>
                                    </div>
                                    <div class="insight-item">
                                        <span class="icon"><i class="bi bi-shield-check"></i></span>
                                        <div>
                                            <strong>Lebih konsisten untuk operasional</strong>
                                            <span>Admin bisa menjaga pola pickup dan return tanpa perlu edit setiap booking satu per satu.</span>
                                        </div>
                                    </div>
                                    <div class="insight-item">
                                        <span class="icon"><i class="bi bi-info-circle"></i></span>
                                        <div>
                                            <strong>Tetap fleksibel</strong>
                                            <span>Jam default bukan aturan kaku. Customer masih bisa menyesuaikan saat kebutuhan sewa berbeda.</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="operations-settings-pane" role="tabpanel" aria-labelledby="operations-settings-tab" tabindex="0">
                        <div class="settings-pane">
                            <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap mb-4">
                                <div>
                                    <h4>Operasional</h4>
                                    <p class="mb-0">Kategori ini disiapkan buat aturan kerja harian yang nanti paling sering disentuh admin operasional.</p>
                                </div>
                                <span class="placeholder-pill">
                                    <i class="bi bi-stars"></i>
                                    Slot siap diisi
                                </span>
                            </div>

                            <div class="placeholder-card">
                                <h4 class="mb-2">Siap untuk Pengaturan Operasional Berikutnya</h4>
                                <p class="mb-0">Begitu lo mau nambah fitur admin berikutnya, kategori ini sudah siap menampung pengaturan tanpa ngerombak layout halaman lagi.</p>

                                <div class="placeholder-grid">
                                    <div class="placeholder-item">
                                        <strong>Biaya telat</strong>
                                        <span>Taruh nominal denda per hari atau aturan grace period di sini.</span>
                                    </div>
                                    <div class="placeholder-item">
                                        <strong>Jam operasional</strong>
                                        <span>Bedakan jam buka, jam tutup, atau jam pickup khusus hari tertentu.</span>
                                    </div>
                                    <div class="placeholder-item">
                                        <strong>Aturan maintenance</strong>
                                        <span>Masukkan jeda buffer sebelum kendaraan kembali tersedia.</span>
                                    </div>
                                    <div class="placeholder-item">
                                        <strong>Batas booking minimum</strong>
                                        <span>Kalau nanti perlu, lo bisa atur minimal hari sewa langsung dari area ini.</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="communication-settings-pane" role="tabpanel" aria-labelledby="communication-settings-tab" tabindex="0">
                        <div class="settings-pane">
                            <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap mb-4">
                                <div>
                                    <h4>Komunikasi</h4>
                                    <p class="mb-0">Kategori ini cocok buat semua pengaturan yang berhubungan dengan isi pesan dan alur komunikasi ke customer.</p>
                                </div>
                                <span class="placeholder-pill">
                                    <i class="bi bi-chat-left-text"></i>
                                    Siap dipakai nanti
                                </span>
                            </div>

                            <div class="placeholder-card">
                                <h4 class="mb-2">Tempat Template Notifikasi</h4>
                                <p class="mb-0">Kalau nanti lo mau bikin admin bisa ngatur isi pesan otomatis, area ini sudah pas buat nampung semuanya.</p>

                                <div class="placeholder-grid">
                                    <div class="placeholder-item">
                                        <strong>Template WhatsApp</strong>
                                        <span>Atur format pesan konfirmasi pembayaran atau pengambilan unit.</span>
                                    </div>
                                    <div class="placeholder-item">
                                        <strong>Pesan pengingat</strong>
                                        <span>Simpan copy reminder H-1, keterlambatan, atau unit siap diambil.</span>
                                    </div>
                                    <div class="placeholder-item">
                                        <strong>Kontak admin</strong>
                                        <span>Masukkan nomor atau nama PIC yang ingin ditampilkan ke customer.</span>
                                    </div>
                                    <div class="placeholder-item">
                                        <strong>Preferensi notifikasi</strong>
                                        <span>Tentukan channel mana yang aktif saat nanti email dan WhatsApp makin lengkap.</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="aside-stack">
            <div class="settings-card">
                <h4>Aturan yang Tetap Berlaku</h4>
                <p class="mb-3">Batasan sistem ini tidak berubah walaupun default jam diubah.</p>
                <ul class="helper-list">
                    <li>Jam default hanya jadi nilai awal di form booking.</li>
                    <li>Harga rental tetap dihitung per hari, bukan per jam.</li>
                    <li>Untuk sewa satu hari, jam kembali harus setelah jam ambil.</li>
                </ul>
            </div>

            <div class="settings-card">
                <h4>Akses Cepat</h4>
                <p class="mb-3">Setelah simpan pengaturan, lo bisa cek dampaknya dari dua titik ini.</p>
                <a href="{{ route('admin.dashboard') }}" class="quick-link">
                    <i class="bi bi-speedometer2"></i>
                    <span>Kembali ke dashboard admin</span>
                </a>
                <a href="{{ route('vehicles.browse') }}" class="quick-link">
                    <i class="bi bi-car-front"></i>
                    <span>Lihat katalog dari sisi customer</span>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection