@extends('layouts.admin')

@section('title', 'Detail KTP - ' . $user->name)
@section('page-title', 'Detail KTP')

@section('content')
<style>
    .ktp-header {
        background: radial-gradient(circle at top right, rgba(255,255,255,0.14), transparent 34%), var(--gradient-brand);
        color: white;
        padding: 3rem 0 2.45rem;
        margin-bottom: 2rem;
        border-radius: 0 0 2rem 2rem;
    }
    .ktp-header h1 {
        font-weight: 700;
        font-size: clamp(2.05rem, 4.2vw, 3rem);
        margin-bottom: 0.5rem;
    }
    .back-link {
        color: rgba(255,255,255,0.8);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
    }
    .back-link:hover {
        color: white;
    }
    .ktp-container {
        max-width: 980px;
        margin: 0 auto;
        padding-bottom: 3rem;
    }
    .section-card {
        background: white;
        border-radius: 1.5rem;
        padding: 2rem;
        box-shadow: var(--shadow-card);
        margin-bottom: 1.5rem;
        border: 1px solid rgba(203,213,225,0.65);
    }
    .section-title {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #f1f5f9;
    }
    .section-title .icon {
        width: 40px;
        height: 40px;
        background: var(--gradient-brand);
        border-radius: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
    }
    .section-title h5 {
        margin: 0;
        font-weight: 700;
        color: #1a202c;
    }
    .user-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        flex-wrap: wrap;
        margin-bottom: 1.5rem;
    }
    .user-summary {
        display: flex;
        align-items: center;
        gap: 1.25rem;
        flex-wrap: wrap;
    }
    .user-avatar {
        width: 80px;
        height: 80px;
        background: var(--gradient-brand);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
        font-size: 2rem;
        box-shadow: 0 18px 32px rgba(31, 41, 55, 0.2);
    }
    .user-info h3 {
        margin: 0 0 0.5rem;
        font-weight: 700;
    }
    .user-info .meta {
        color: #718096;
        font-size: 0.9rem;
    }
    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));
        gap: 1rem;
    }
    .info-item {
        display: flex;
        flex-direction: column;
        background: #f8fafc;
        border: 1px solid rgba(203,213,225,0.78);
        border-radius: 1rem;
        padding: 1rem;
    }
    .info-item .label {
        font-size: 0.75rem;
        color: #718096;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.25rem;
    }
    .info-item .value {
        font-weight: 600;
        color: #1a202c;
    }
    .ktp-image-container {
        text-align: center;
        margin-bottom: 1.5rem;
        background: #f8fafc;
        border: 1px solid rgba(203,213,225,0.78);
        border-radius: 1.25rem;
        padding: 0.75rem;
    }
    .ktp-image {
        width: 100%;
        max-height: 520px;
        object-fit: contain;
        border-radius: 1rem;
        background: white;
        box-shadow: 0 14px 28px rgba(15, 23, 42, 0.12);
    }
    .status-chip {
        padding: 0.6rem 1.25rem;
        border-radius: 2rem;
        font-weight: 600;
        font-size: 0.9rem;
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
    .action-card {
        background: radial-gradient(circle at top right, rgba(255,255,255,0.14), transparent 34%), var(--gradient-brand);
        border-radius: 1.5rem;
        padding: 2rem;
        color: white;
        box-shadow: var(--shadow-card-hover);
    }
    .action-card h5 {
        color: white;
        font-weight: 600;
        margin-bottom: 0.6rem;
    }
    .action-card .helper-text {
        color: rgba(255,255,255,0.8);
        margin-bottom: 1.25rem;
    }
    .review-note {
        background: rgba(255,255,255,0.1);
        border: 1px solid rgba(255,255,255,0.14);
        border-radius: 1rem;
        padding: 1rem 1.15rem;
        margin-bottom: 1.25rem;
    }
    .review-note h6 {
        color: white;
        font-weight: 600;
        margin-bottom: 0.35rem;
    }
    .review-note p {
        margin: 0;
        color: rgba(255,255,255,0.78);
        font-size: 0.92rem;
    }
    .action-card textarea {
        background: rgba(255,255,255,0.1);
        border: 1px solid rgba(255,255,255,0.2);
        color: white;
        border-radius: 0.75rem;
    }
    .action-card textarea::placeholder {
        color: rgba(255,255,255,0.5);
    }
    .action-card textarea:focus {
        background: rgba(255,255,255,0.15);
        border-color: rgba(255,255,255,0.4);
        color: white;
        box-shadow: none;
    }
    .btn-verify {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        border: none;
        padding: 1rem 2rem;
        font-size: 1rem;
        font-weight: 600;
        border-radius: 1rem;
        color: white;
        transition: all 0.3s ease;
    }
    .btn-verify:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(16, 185, 129, 0.4);
        color: white;
    }
    .btn-reject {
        background: transparent;
        border: 2px solid #ef4444;
        padding: 1rem 2rem;
        font-size: 1rem;
        font-weight: 600;
        border-radius: 1rem;
        color: #ef4444;
        transition: all 0.3s ease;
    }
    .btn-reject:hover {
        background: #ef4444;
        color: white;
        transform: translateY(-3px);
    }
    .action-buttons {
        display: flex;
        gap: 0.85rem;
        flex-wrap: wrap;
    }
    .verified-badge {
        background: linear-gradient(135deg, #dcfce7 0%, #d1fae5 100%);
        border-radius: 1rem;
        padding: 1.75rem;
        text-align: center;
    }
    .verified-badge i {
        font-size: 3rem;
        color: #10b981;
        margin-bottom: 1rem;
    }
    .verified-badge h4 {
        color: #059669;
        margin-bottom: 0.5rem;
    }
    .verified-badge p {
        color: #065f46;
        margin: 0;
    }
    .rejected-badge {
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        border-radius: 1rem;
        padding: 1.75rem;
    }
    .rejected-badge h6 {
        color: #991b1b;
        margin-bottom: 0.5rem;
    }
    .rejected-badge p {
        color: #b91c1c;
        margin: 0;
    }
    @media (max-width: 768px) {
        .ktp-container {
            padding-inline: 1rem;
            padding-bottom: 2rem;
        }
        .ktp-header {
            padding: 2.6rem 0 2.15rem;
            margin-bottom: 1.25rem;
            border-radius: 0 0 1.35rem 1.35rem;
        }
        .ktp-header h1 {
            font-size: 1.45rem;
            line-height: 1.35;
        }
        .back-link {
            margin-bottom: 0.85rem;
            font-size: 0.9rem;
            gap: 0.4rem;
        }
        .info-grid {
            grid-template-columns: 1fr;
            gap: 0.75rem;
        }
        .section-card,
        .action-card {
            padding: 1rem;
            border-radius: 1rem;
        }
        .section-title {
            gap: 0.6rem;
            margin-bottom: 1rem;
            padding-bottom: 0.75rem;
        }
        .section-title .icon {
            width: 34px;
            height: 34px;
            border-radius: 0.65rem;
        }
        .section-title h5 {
            font-size: 1rem;
        }
        .user-header {
            align-items: flex-start;
            gap: 0.75rem;
            margin-bottom: 1rem;
        }
        .user-summary {
            align-items: flex-start;
            gap: 0.8rem;
        }
        .user-avatar {
            width: 54px;
            height: 54px;
            font-size: 1.3rem;
        }
        .user-info h3 {
            font-size: 1.1rem;
            margin-bottom: 0.25rem;
        }
        .user-info .meta {
            font-size: 0.82rem;
            line-height: 1.5;
            word-break: break-word;
        }
        .status-chip {
            font-size: 0.78rem;
            padding: 0.5rem 0.85rem;
        }
        .info-item {
            border-radius: 0.85rem;
            padding: 0.85rem;
        }
        .info-item .label {
            font-size: 0.7rem;
        }
        .info-item .value {
            font-size: 0.92rem;
        }
        .ktp-image-container {
            margin-bottom: 1rem;
            border-radius: 1rem;
            padding: 0.55rem;
        }
        .ktp-image {
            max-height: 320px;
            border-radius: 0.8rem;
        }
        .action-card h5 {
            font-size: 1.05rem;
        }
        .action-card .helper-text {
            font-size: 0.86rem;
            line-height: 1.55;
            margin-bottom: 1rem;
        }
        .review-note {
            border-radius: 0.85rem;
            padding: 0.8rem 0.9rem;
            margin-bottom: 1rem;
        }
        .review-note h6 {
            font-size: 0.92rem;
        }
        .review-note p {
            font-size: 0.84rem;
            line-height: 1.5;
        }
        .action-card textarea {
            min-height: 92px;
        }
        .action-card .form-label {
            font-size: 0.84rem;
            margin-bottom: 0.45rem;
        }
        .action-buttons {
            flex-direction: column;
            gap: 0.65rem;
        }
        .action-buttons .btn {
            width: 100%;
            min-height: 44px;
            padding: 0.72rem 1rem;
            border-radius: 0.85rem;
            font-size: 0.9rem;
        }
        .verified-badge,
        .rejected-badge {
            border-radius: 0.9rem;
            padding: 1.2rem;
        }
        .verified-badge i {
            font-size: 2.3rem;
            margin-bottom: 0.75rem;
        }
        .verified-badge h4 {
            font-size: 1.05rem;
        }
        .verified-badge p,
        .rejected-badge p {
            font-size: 0.88rem;
            line-height: 1.5;
        }
    }
    @media (max-width: 420px) {
        .ktp-header h1 {
            font-size: 1.28rem;
        }
        .user-avatar {
            width: 48px;
            height: 48px;
            font-size: 1.15rem;
        }
        .status-chip {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<!-- KTP Header -->
<div class="ktp-header">
    <div class="container ktp-container">
        <a href="{{ route('admin.ktp.index') }}" class="back-link">
            <i class="bi bi-arrow-left"></i> Kembali ke Daftar
        </a>
        <h1><i class="bi bi-person-badge me-2"></i>Detail KTP</h1>
        <p class="mb-0 opacity-75">Verifikasi identitas customer</p>
    </div>
</div>

<div class="container ktp-container">
    <!-- User Info -->
    <div class="section-card">
        <div class="user-header">
            <div class="user-summary">
                <div class="user-avatar">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div class="user-info">
                    <h3>{{ $user->name }}</h3>
                    <div class="meta">
                        <i class="bi bi-envelope me-1"></i>{{ $user->email }}
                    </div>
                </div>
            </div>
            <div>
                @if($user->ktp_status === 'verified')
                    <span class="status-chip verified"><i class="bi bi-check-circle-fill"></i> Terverifikasi</span>
                @elseif($user->ktp_status === 'rejected')
                    <span class="status-chip rejected"><i class="bi bi-x-circle-fill"></i> Ditolak</span>
                @else
                    <span class="status-chip pending"><i class="bi bi-hourglass-split"></i> Menunggu Verifikasi</span>
                @endif
            </div>
        </div>

        <div class="info-grid">
            <div class="info-item">
                <span class="label">Nomor KTP (NIK)</span>
                <span class="value">{{ $user->ktp_number }}</span>
            </div>
            <div class="info-item">
                <span class="label">Terdaftar Sejak</span>
                <span class="value">{{ $user->created_at->format('d M Y') }}</span>
            </div>
            <div class="info-item">
                <span class="label">Total Booking</span>
                <span class="value">{{ $user->bookings->count() }} booking</span>
            </div>
            @if($user->ktp_verified_at)
                <div class="info-item">
                    <span class="label">Tanggal Verifikasi</span>
                    <span class="value">{{ $user->ktp_verified_at->format('d M Y H:i') }}</span>
                </div>
            @endif
        </div>
    </div>

    <!-- KTP Image -->
    <div class="section-card">
        <div class="section-title">
            <div class="icon"><i class="bi bi-image"></i></div>
            <h5>Foto KTP</h5>
        </div>
        <div class="ktp-image-container">
            <img src="{{ $user->ktp_image_url }}" class="ktp-image" alt="KTP {{ $user->name }}">
        </div>
    </div>

    <!-- Verified Status -->
    @if($user->ktp_status === 'verified')
        <div class="section-card">
            <div class="verified-badge">
                <i class="bi bi-check-circle-fill"></i>
                <h4>KTP Sudah Terverifikasi</h4>
                <p>Diverifikasi pada {{ $user->ktp_verified_at->format('d M Y H:i') }}</p>
            </div>
        </div>
    @endif

    <!-- Rejected Status -->
    @if($user->ktp_status === 'rejected')
        <div class="section-card">
            <div class="rejected-badge">
                <h6><i class="bi bi-x-circle me-1"></i> Alasan Penolakan</h6>
                <p>{{ $user->ktp_rejection_reason ?? 'Tidak ada alasan yang dicatat' }}</p>
            </div>
        </div>
    @endif

    <!-- Action Section -->
    @if($user->ktp_status === 'pending')
        <div class="action-card">
            <h5><i class="bi bi-shield-check me-2"></i>Verifikasi KTP</h5>
            <p class="helper-text">Periksa apakah foto jelas, NIK terbaca, dan data yang diupload sesuai dengan identitas customer.</p>
            <div class="review-note">
                <h6>Checklist singkat</h6>
                <p>Setujui jika dokumen jelas dan valid. Tolak jika foto blur, data terpotong, atau identitas tidak sesuai.</p>
            </div>
            
            <form method="POST" action="{{ route('admin.ktp.verify', $user) }}" id="verifyForm">
                @csrf
                <input type="hidden" name="action" id="actionInput" value="">
                
                <div class="mb-4">
                    <label class="form-label" style="color: rgba(255,255,255,0.8);">Alasan Penolakan (wajib jika ditolak)</label>
                    <textarea class="form-control" name="rejection_reason" id="rejectionReason" rows="3" placeholder="Masukkan alasan penolakan..."></textarea>
                </div>
                
                <div class="action-buttons">
                    <button type="button" class="btn btn-verify" onclick="verifyKtp()">
                        <i class="bi bi-check-circle me-2"></i>Verifikasi KTP
                    </button>
                    <button type="button" class="btn btn-reject" onclick="rejectKtp()">
                        <i class="bi bi-x-circle me-2"></i>Tolak KTP
                    </button>
                </div>
            </form>
        </div>
    @endif
</div>

@endsection

@section('js')
<script>
    function verifyKtp() {
        if (confirm('Verifikasi KTP customer ini?')) {
            document.getElementById('actionInput').value = 'verify';
            document.getElementById('verifyForm').submit();
        }
    }

    function rejectKtp() {
        const reason = document.getElementById('rejectionReason').value.trim();
        if (!reason) {
            alert('Masukkan alasan penolakan!');
            document.getElementById('rejectionReason').focus();
            return;
        }
        if (confirm('Tolak KTP customer ini?')) {
            document.getElementById('actionInput').value = 'reject';
            document.getElementById('verifyForm').submit();
        }
    }
</script>
@endsection
