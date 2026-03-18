@extends('layouts.admin')

@section('title', 'Verifikasi KTP Customer')
@section('page-title', 'Verifikasi KTP')

@section('content')
<style>
    .ktp-header {
        background: radial-gradient(circle at top right, rgba(255,255,255,0.14), transparent 34%), var(--gradient-brand);
        color: white;
        padding: 3rem 0 2.5rem;
        margin-bottom: 2rem;
        border-radius: 0 0 2rem 2rem;
    }
    .ktp-header h1 {
        font-weight: 700;
        font-size: clamp(2.05rem, 4vw, 2.9rem);
        margin-bottom: 0.5rem;
    }
    .ktp-container {
        max-width: 1280px;
        margin: 0 auto;
        padding-bottom: 3rem;
    }
    .ktp-toolbar {
        display: grid;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    .stats-row {
        display: flex;
        gap: 0.9rem;
        flex-wrap: wrap;
    }
    .stat-pill {
        background: white;
        border-radius: 1.35rem;
        padding: 1rem 1.15rem;
        display: flex;
        align-items: center;
        gap: 0.9rem;
        box-shadow: var(--shadow-card);
        font-weight: 500;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.3s ease;
        border: 1px solid rgba(203,213,225,0.72);
        flex: 1 1 220px;
    }
    .stat-pill:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-card-hover);
    }
    .stat-pill.active {
        background: var(--gradient-brand);
        color: white;
        border-color: transparent;
    }
    .stat-pill .icon {
        width: 46px;
        height: 46px;
        border-radius: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
        flex-shrink: 0;
    }
    .stat-pill.pending .icon {
        background: rgba(245,158,11,0.14);
        color: #d97706;
    }
    .stat-pill.verified .icon {
        background: rgba(16,185,129,0.14);
        color: #059669;
    }
    .stat-pill.rejected .icon {
        background: rgba(239,68,68,0.14);
        color: #dc2626;
    }
    .stat-pill.active .icon {
        background: rgba(255,255,255,0.18);
        color: white;
    }
    .stat-pill .copy {
        display: flex;
        flex-direction: column;
        gap: 0.12rem;
    }
    .stat-pill .number {
        font-size: 1.25rem;
        font-weight: 700;
    }
    .stat-pill .label {
        font-size: 0.85rem;
    }
    .stat-pill.pending { border-left: 4px solid #f59e0b; }
    .stat-pill.verified { border-left: 4px solid #10b981; }
    .stat-pill.rejected { border-left: 4px solid #ef4444; }
    .stat-pill.active.pending,
    .stat-pill.active.verified,
    .stat-pill.active.rejected { border-left: none; }
    .ktp-filter-panel {
        padding: 1rem;
        border-radius: 1.15rem;
        background: linear-gradient(135deg, rgba(255,255,255,0.98) 0%, rgba(248,250,252,0.98) 100%);
        border: 1px solid rgba(203,213,225,0.75);
        box-shadow: var(--shadow-soft);
    }
    .ktp-filter-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 1rem;
        flex-wrap: wrap;
        margin-bottom: 0.9rem;
    }
    .ktp-filter-kicker {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        font-size: 0.74rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.12em;
        color: #0f766e;
        margin-bottom: 0.35rem;
    }
    .ktp-filter-header h2 {
        margin: 0;
        font-size: 1.05rem;
        font-weight: 700;
        color: #0f172a;
    }
    .ktp-filter-header p {
        margin: 0.28rem 0 0;
        color: #64748b;
        line-height: 1.6;
    }
    .ktp-filter-meta {
        display: flex;
        align-items: center;
        gap: 0.55rem;
        flex-wrap: wrap;
    }
    .ktp-filter-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.55rem 0.85rem;
        border-radius: 999px;
        background: white;
        border: 1px solid rgba(203,213,225,0.78);
        color: #334155;
        font-size: 0.82rem;
        font-weight: 700;
        box-shadow: 0 10px 20px rgba(15,23,42,0.05);
    }
    .ktp-filter-chip.muted {
        background: rgba(6,182,212,0.08);
        border-color: rgba(6,182,212,0.16);
        color: #0e7490;
    }
    .ktp-filter-form {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto auto;
        gap: 0.85rem;
        align-items: end;
    }
    .ktp-search-field label {
        display: block;
        font-size: 0.82rem;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 0.4rem;
    }
    .ktp-search-field .form-control {
        min-height: 48px;
        border-radius: 0.9rem;
        border: 1px solid rgba(203,213,225,0.92);
    }
    .ktp-filter-form .btn,
    .ktp-filter-reset {
        min-height: 48px;
        border-radius: 999px;
        padding-inline: 1rem;
        font-weight: 600;
    }
    .ktp-filter-reset {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        border: 1px solid rgba(203,213,225,0.95);
        background: white;
        color: #334155;
        text-decoration: none;
    }
    .ktp-filter-reset:hover {
        color: var(--color-primary);
        border-color: rgba(var(--color-secondary-rgb), 0.35);
    }
    .user-card {
        background: white;
        border-radius: 1.25rem;
        padding: 1.4rem;
        margin-bottom: 1rem;
        box-shadow: var(--shadow-card);
        transition: all 0.3s ease;
        border-left: 4px solid #f59e0b;
        border-top: 1px solid rgba(203,213,225,0.72);
        border-right: 1px solid rgba(203,213,225,0.72);
        border-bottom: 1px solid rgba(203,213,225,0.72);
    }
    .user-card:hover {
        box-shadow: var(--shadow-card-hover);
        transform: translateY(-2px);
    }
    .user-card.verified { border-left-color: #10b981; }
    .user-card.rejected { border-left-color: #ef4444; }
    .user-card .header-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
        gap: 1rem;
        flex-wrap: wrap;
    }
    .user-card .user-info {
        display: flex;
        align-items: center;
        gap: 1rem;
        min-width: 0;
    }
    .user-card .avatar {
        width: 50px;
        height: 50px;
        background: var(--gradient-brand);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        font-size: 1.25rem;
        box-shadow: 0 14px 26px rgba(31, 41, 55, 0.18);
    }
    .user-card .user-details h5 {
        font-weight: 600;
        margin-bottom: 0.25rem;
        font-size: 1.08rem;
    }
    .user-card .user-details .meta {
        color: #718096;
        font-size: 0.85rem;
        word-break: break-word;
    }
    .user-card .details-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
        gap: 1rem;
        margin-bottom: 1rem;
        padding: 1rem;
        background: #f8fafc;
        border-radius: 0.75rem;
        border: 1px solid rgba(203,213,225,0.78);
    }
    .user-card .detail-item {
        display: flex;
        flex-direction: column;
    }
    .user-card .detail-item .label {
        color: #718096;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.25rem;
    }
    .user-card .detail-item .value {
        color: #1a202c;
        font-weight: 600;
        font-size: 0.95rem;
    }
    .status-chip {
        padding: 0.58rem 1rem;
        border-radius: 2rem;
        font-weight: 600;
        font-size: 0.8rem;
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
    }
    .status-chip.verified {
        background: #dcfce7;
        color: #166534;
    }
    .status-chip.rejected {
        background: #fee2e2;
        color: #991b1b;
    }
    .status-chip.pending {
        background: #fef3c7;
        color: #92400e;
    }
    .ktp-thumb {
        width: 132px;
        border-radius: 0.85rem;
        overflow: hidden;
        border: 1px solid rgba(203,213,225,0.85);
        background: white;
        box-shadow: 0 10px 20px rgba(15, 23, 42, 0.08);
    }
    .ktp-thumb .ktp-preview {
        width: 100%;
        height: 84px;
        object-fit: cover;
        display: block;
        border: none;
    }
    .btn-action {
        border-radius: 0.75rem;
        padding: 0.7rem 1.1rem;
        font-size: 0.85rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    .btn-action:hover {
        transform: translateY(-2px);
    }
    .btn-view {
        background: var(--color-primary);
        border: none;
        color: white;
    }
    .btn-view:hover {
        background: var(--color-secondary);
        box-shadow: 0 5px 15px rgba(var(--color-secondary-rgb), 0.24);
        color: var(--color-primary);
    }
    .btn-approve {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        border: none;
        color: white;
    }
    .btn-approve:hover {
        box-shadow: 0 5px 15px rgba(16, 185, 129, 0.28);
        color: white;
    }
    .card-actions {
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
        flex-wrap: wrap;
    }
    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        background: white;
        border-radius: 1.25rem;
        box-shadow: var(--shadow-card);
        border: 1px solid rgba(203,213,225,0.72);
    }
    .empty-state i {
        font-size: 4rem;
        color: #cbd5e0;
        margin-bottom: 1rem;
    }
    .empty-state h4 {
        color: #4a5568;
        margin-bottom: 0.5rem;
    }
    .empty-state p {
        color: #718096;
    }
    @media (max-width: 767.98px) {
        .ktp-header {
            padding: 2.6rem 0 2.15rem;
        }
        .stat-pill {
            width: 100%;
            flex: none;
        }
        .ktp-filter-form {
            grid-template-columns: 1fr;
        }
        .ktp-filter-form .btn,
        .ktp-filter-reset {
            width: 100%;
            justify-content: center;
        }
        .user-card {
            padding: 1.2rem;
        }
        .card-actions,
        .btn-view,
        .btn-approve {
            width: 100%;
        }
        .btn-view,
        .btn-approve {
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
    }
</style>

@php($hasKtpSearch = filled($search ?? ''))

<!-- KTP Header -->
<div class="ktp-header">
    <div class="container ktp-container">
        <h1><i class="bi bi-person-badge me-2"></i>Verifikasi KTP Customer</h1>
        <p class="mb-0 opacity-75">Kelola verifikasi identitas customer</p>
    </div>
</div>

<div class="container ktp-container">
    <div class="ktp-toolbar">
        <!-- Stats Pills -->
        <div class="stats-row">
            <a href="{{ route('admin.ktp.index', ['status' => 'pending']) }}" class="stat-pill pending {{ $status === 'pending' ? 'active' : '' }}">
                <span class="icon"><i class="bi bi-hourglass-split"></i></span>
                <div class="copy">
                    <div class="number">{{ $counts['pending'] }}</div>
                    <div class="label">Pending</div>
                </div>
            </a>
            <a href="{{ route('admin.ktp.index', ['status' => 'verified']) }}" class="stat-pill verified {{ $status === 'verified' ? 'active' : '' }}">
                <span class="icon"><i class="bi bi-patch-check-fill"></i></span>
                <div class="copy">
                    <div class="number">{{ $counts['verified'] }}</div>
                    <div class="label">Terverifikasi</div>
                </div>
            </a>
            <a href="{{ route('admin.ktp.index', ['status' => 'rejected']) }}" class="stat-pill rejected {{ $status === 'rejected' ? 'active' : '' }}">
                <span class="icon"><i class="bi bi-x-octagon-fill"></i></span>
                <div class="copy">
                    <div class="number">{{ $counts['rejected'] }}</div>
                    <div class="label">Ditolak</div>
                </div>
            </a>
        </div>

        <form method="GET" action="{{ route('admin.ktp.index') }}" class="ktp-filter-panel">
            <input type="hidden" name="status" value="{{ $status }}">

            <div class="ktp-filter-header">
                <div>
                    <span class="ktp-filter-kicker"><i class="bi bi-search"></i>Pencarian</span>
                    <h2>Cari customer yang perlu dicek</h2>
                    <p class="mb-0">Search ini akan mencocokkan nama customer, email, dan NIK tanpa keluar dari status verifikasi yang sedang aktif.</p>
                </div>
                <div class="ktp-filter-meta">
                    <span class="ktp-filter-chip"><i class="bi bi-list-ul"></i>{{ $users->total() }} hasil</span>
                    @if($hasKtpSearch)
                        <span class="ktp-filter-chip muted"><i class="bi bi-sliders"></i>Pencarian aktif</span>
                    @endif
                </div>
            </div>

            <div class="ktp-filter-form">
                <div class="ktp-search-field">
                    <label for="ktp-search">Cari customer</label>
                    <input type="text" id="ktp-search" name="search" class="form-control" value="{{ $search ?? '' }}" placeholder="Nama, email, atau NIK">
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-funnel me-2"></i>Terapkan
                </button>
                <a href="{{ route('admin.ktp.index', ['status' => $status]) }}" class="ktp-filter-reset">
                    <i class="bi bi-arrow-counterclockwise"></i>Reset
                </a>
            </div>
        </form>
    </div>

    @if($users->count() > 0)
        @foreach($users as $user)
            <div class="user-card {{ $user->ktp_status }}">
                <div class="header-row">
                    <div class="user-info">
                        <div class="avatar">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <div class="user-details">
                            <h5>{{ $user->name }}</h5>
                            <div class="meta">
                                <i class="bi bi-envelope me-1"></i>{{ $user->email }}
                            </div>
                        </div>
                    </div>
                    <div class="status-wrap">
                        @if($user->ktp_status === 'verified')
                            <span class="status-chip verified"><i class="bi bi-check-circle-fill"></i> Terverifikasi</span>
                        @elseif($user->ktp_status === 'rejected')
                            <span class="status-chip rejected"><i class="bi bi-x-circle-fill"></i> Ditolak</span>
                        @else
                            <span class="status-chip pending"><i class="bi bi-hourglass-split"></i> Menunggu</span>
                        @endif
                    </div>
                </div>

                <div class="details-row">
                    <div class="detail-item">
                        <span class="label">NIK</span>
                        <span class="value">{{ $user->ktp_number }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="label">Tanggal Upload</span>
                        <span class="value">{{ $user->updated_at->format('d M Y H:i') }}</span>
                    </div>
                    @if($user->ktp_verified_at)
                        <div class="detail-item">
                            <span class="label">Tanggal Verifikasi</span>
                            <span class="value">{{ $user->ktp_verified_at->format('d M Y H:i') }}</span>
                        </div>
                    @endif
                    <div class="detail-item preview">
                        <span class="label">Preview KTP</span>
                        <div class="ktp-thumb mt-1">
                            <img src="{{ asset('storage/' . $user->ktp_image) }}" class="ktp-preview" alt="KTP {{ $user->name }}">
                        </div>
                    </div>
                </div>

                <div class="card-actions">
                    @if($user->ktp_status === 'pending')
                        <form method="POST" action="{{ route('admin.ktp.verify', $user) }}" onsubmit="return confirm('Verifikasi KTP {{ addslashes($user->name) }} sekarang?');">
                            @csrf
                            <input type="hidden" name="action" value="verify">
                            <button type="submit" class="btn btn-action btn-approve">
                                <i class="bi bi-patch-check me-1"></i>Verifikasi Cepat
                            </button>
                        </form>
                    @endif
                    <a href="{{ route('admin.ktp.show', $user) }}" class="btn btn-action btn-view">
                        <i class="bi bi-eye me-1"></i>Lihat Detail
                    </a>
                </div>
            </div>
        @endforeach

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $users->links() }}
        </div>
    @else
        <div class="empty-state">
            <i class="bi bi-person-badge"></i>
            @if($hasKtpSearch)
                <h4>Data Tidak Ditemukan</h4>
                <p>Tidak ada KTP dengan status {{ $status === 'pending' ? 'menunggu verifikasi' : ($status === 'verified' ? 'terverifikasi' : 'ditolak') }} yang cocok dengan kata kunci "{{ $search }}".</p>
            @else
                <h4>Tidak Ada Data</h4>
                <p>Tidak ada KTP dengan status {{ $status === 'pending' ? 'menunggu verifikasi' : ($status === 'verified' ? 'terverifikasi' : 'ditolak') }}</p>
            @endif
        </div>
    @endif
</div>
@endsection
