@extends('layouts.app')

@section('title', 'Verifikasi KTP')

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
        font-size: clamp(2rem, 4.6vw, 3rem);
        margin-bottom: 0.5rem;
    }
    .ktp-container {
        max-width: 940px;
        margin: 0 auto;
        padding-bottom: 3rem;
    }
    .status-card {
        background: linear-gradient(135deg, rgba(255,255,255,0.98) 0%, rgba(248,250,252,0.96) 100%);
        border-radius: 1.5rem;
        padding: 2rem;
        box-shadow: var(--shadow-card);
        margin-bottom: 1.5rem;
        text-align: center;
        border: 1px solid rgba(203,213,225,0.65);
    }
    .status-icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        font-size: 2.5rem;
        box-shadow: 0 20px 34px rgba(15, 23, 42, 0.1);
    }
    .status-icon.verified {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
    }
    .status-icon.pending {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
    }
    .status-icon.rejected {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: white;
    }
    .status-icon.unverified {
        background: linear-gradient(135deg, #94a3b8 0%, #64748b 100%);
        color: white;
    }
    .status-icon.admin {
        background: linear-gradient(135deg, var(--color-primary) 0%, #334155 100%);
        color: white;
    }
    .status-card h3 {
        font-weight: 700;
        margin-bottom: 0.5rem;
    }
    .status-card p {
        color: #718096;
        margin-bottom: 0;
    }
    .status-meta {
        display: flex;
        justify-content: center;
        gap: 0.75rem;
        flex-wrap: wrap;
        margin-top: 1.2rem;
    }
    .status-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.7rem 1rem;
        border-radius: 999px;
        font-weight: 600;
        font-size: 0.88rem;
        background: rgba(var(--color-secondary-rgb), 0.12);
        color: var(--color-primary);
        border: 1px solid rgba(var(--color-secondary-rgb), 0.18);
    }
    .form-card {
        background: white;
        border-radius: 1.5rem;
        padding: 2rem;
        box-shadow: var(--shadow-card);
        margin-bottom: 1.5rem;
        border: 1px solid rgba(203,213,225,0.65);
    }
    .form-card h5 {
        font-weight: 700;
        margin-bottom: 0.6rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    .form-card h5 .icon {
        width: 40px;
        height: 40px;
        background: var(--gradient-brand);
        border-radius: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
    }
    .form-subtitle {
        color: #64748b;
        margin-bottom: 1.4rem;
    }
    .upload-area {
        border: 2px dashed #e2e8f0;
        border-radius: 1rem;
        padding: 2rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        background: #f8fafc;
    }
    .upload-area:hover {
        border-color: var(--color-secondary);
        background: rgba(var(--color-secondary-rgb), 0.08);
    }
    .upload-area.dragover {
        border-color: var(--color-secondary);
        background: rgba(var(--color-secondary-rgb), 0.14);
    }
    .upload-area i {
        font-size: 3rem;
        color: var(--color-secondary-strong);
        margin-bottom: 1rem;
    }
    .upload-area p {
        color: #718096;
        margin-bottom: 0.5rem;
    }
    .upload-area small {
        color: #94a3b8;
    }
    .preview-image {
        max-width: 100%;
        max-height: 300px;
        border-radius: 1rem;
        margin-top: 1rem;
        display: none;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.12);
    }
    .current-ktp {
        border-radius: 1rem;
        overflow: hidden;
        margin-bottom: 1.5rem;
        border: 1px solid rgba(203,213,225,0.72);
        background: #f8fafc;
        padding: 0.75rem;
    }
    .current-ktp img {
        width: 100%;
        max-height: 360px;
        object-fit: contain;
        background: #f8fafc;
        border-radius: 0.75rem;
    }
    .info-card {
        background: linear-gradient(135deg, rgba(255,255,255,0.96) 0%, rgba(240,249,255,0.94) 100%);
        border-radius: 1.25rem;
        padding: 1.4rem 1.5rem;
        margin-bottom: 1.5rem;
        border: 1px solid rgba(var(--color-secondary-rgb), 0.14);
        box-shadow: var(--shadow-soft);
    }
    .info-card h6 {
        color: var(--color-primary);
        font-weight: 700;
        margin-bottom: 1rem;
    }
    .info-card ul {
        margin: 0;
        padding-left: 1.25rem;
        color: var(--color-primary);
    }
    .info-card ul li {
        margin-bottom: 0.5rem;
    }
    .rejection-card {
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        border-radius: 1rem;
        padding: 1.25rem 1.5rem;
        margin-bottom: 1.5rem;
    }
    .rejection-card h6 {
        color: #991b1b;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }
    .rejection-card p {
        color: #b91c1c;
        margin: 0;
    }
    .detail-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
    }
    .detail-card {
        background: #f8fafc;
        border: 1px solid rgba(203,213,225,0.8);
        border-radius: 1rem;
        padding: 1rem;
    }
    .detail-card .label {
        display: block;
        color: #64748b;
        font-size: 0.76rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        margin-bottom: 0.35rem;
    }
    .detail-card .value {
        color: #0f172a;
        font-weight: 700;
    }
    .upload-tips {
        background: #f8fafc;
        border: 1px solid rgba(203,213,225,0.82);
        border-radius: 1rem;
        padding: 1rem 1.1rem;
        margin-bottom: 1.4rem;
    }
    .upload-tips h6 {
        margin-bottom: 0.5rem;
        color: #0f172a;
        font-weight: 700;
    }
    .upload-tips p {
        margin: 0;
        color: #64748b;
        font-size: 0.92rem;
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
        box-shadow: 0 10px 30px rgba(var(--color-secondary-rgb), 0.24);
        color: var(--color-primary);
    }
    .btn-submit:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }
    @media (max-width: 767.98px) {
        .ktp-header {
            padding: 2.6rem 0 2.15rem;
        }
        .status-card,
        .form-card {
            padding: 1.4rem;
        }
        .status-meta {
            flex-direction: column;
            align-items: stretch;
        }
    }
</style>

<!-- KTP Header -->
<div class="ktp-header">
    <div class="container ktp-container">
        <h1><i class="bi bi-person-badge me-2"></i>Verifikasi KTP</h1>
        <p class="mb-0 opacity-75">Upload KTP untuk verifikasi identitas Anda</p>
    </div>
</div>

<div class="container ktp-container">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Status Card -->
    <div class="status-card">
        @if($user->isAdmin())
            <div class="status-icon admin">
                <i class="bi bi-shield-lock"></i>
            </div>
            <h3>Akun Admin Tidak Perlu Verifikasi KTP</h3>
            <p>Akun admin otomatis dianggap valid untuk mengakses fitur internal tanpa upload dokumen KTP.</p>
        @elseif($user->ktp_status === 'verified')
            <div class="status-icon verified">
                <i class="bi bi-check-lg"></i>
            </div>
            <h3 class="text-success">KTP Terverifikasi</h3>
            <p>Identitas Anda telah terverifikasi pada {{ $user->ktp_verified_at->format('d M Y H:i') }}</p>
        @elseif($user->ktp_status === 'rejected')
            <div class="status-icon rejected">
                <i class="bi bi-x-lg"></i>
            </div>
            <h3 class="text-danger">KTP Ditolak</h3>
            <p>Silakan upload ulang KTP Anda</p>
        @elseif($user->hasUploadedKtp())
            <div class="status-icon pending">
                <i class="bi bi-hourglass-split"></i>
            </div>
            <h3 class="text-warning">Menunggu Verifikasi</h3>
            <p>KTP Anda sedang dalam proses verifikasi oleh admin</p>
        @else
            <div class="status-icon unverified">
                <i class="bi bi-person-x"></i>
            </div>
            <h3>Belum Verifikasi</h3>
            <p>Upload KTP untuk memverifikasi identitas Anda</p>
        @endif

        <div class="status-meta">
            <span class="status-chip"><i class="bi bi-shield-check"></i>Status: {{ $user->getKtpStatusLabel() }}</span>
            @if($user->ktp_number)
                <span class="status-chip"><i class="bi bi-credit-card-2-front"></i>NIK: {{ $user->ktp_number }}</span>
            @endif
        </div>
    </div>

    <!-- Rejection Reason -->
    @if(!$user->isAdmin() && $user->ktp_status === 'rejected' && $user->ktp_rejection_reason)
        <div class="rejection-card">
            <h6><i class="bi bi-exclamation-triangle me-1"></i> Alasan Penolakan</h6>
            <p>{{ $user->ktp_rejection_reason }}</p>
        </div>
    @endif

    <!-- Info Card -->
    <div class="info-card">
        @if($user->isAdmin())
            <h6><i class="bi bi-info-circle me-1"></i> Status Verifikasi untuk Admin</h6>
            <ul>
                <li>Admin tidak wajib upload KTP untuk menggunakan sistem</li>
                <li>Akses pembayaran dan fitur internal tidak akan terblokir oleh status KTP</li>
                <li>Halaman ini hanya informatif bila dibuka dari URL langsung</li>
            </ul>
        @else
            <h6><i class="bi bi-info-circle me-1"></i> Ketentuan Upload KTP</h6>
            <ul>
                <li>Foto KTP harus jelas dan tidak blur</li>
                <li>Pastikan semua informasi pada KTP terbaca</li>
                <li>Format file: JPEG, PNG, atau JPG</li>
                <li>Ukuran maksimal: 5MB</li>
                <li>KTP harus asli dan masih berlaku</li>
            </ul>
        @endif
    </div>

    <!-- Current KTP -->
    @if(!$user->isAdmin() && $user->ktp_image && $user->ktp_status !== 'rejected')
        <div class="form-card">
            <h5>
                <span class="icon"><i class="bi bi-image"></i></span>
                KTP Saat Ini
            </h5>
            <p class="form-subtitle">Dokumen yang saat ini tersimpan dan dipakai untuk proses verifikasi identitas Anda.</p>
            <div class="current-ktp">
                <img src="{{ asset('storage/' . $user->ktp_image) }}" alt="KTP">
            </div>
            <div class="detail-grid">
                <div class="detail-card">
                    <span class="label">Nomor KTP</span>
                    <span class="value">{{ $user->ktp_number }}</span>
                </div>
                <div class="detail-card">
                    <span class="label">Status Verifikasi</span>
                    <span class="value">{{ $user->getKtpStatusLabel() }}</span>
                </div>
            </div>
        </div>
    @endif

    <!-- Upload Form -->
    @if(!$user->isAdmin() && ($user->ktp_status !== 'verified' || $user->ktp_status === 'rejected'))
        <div class="form-card">
            <h5>
                <span class="icon"><i class="bi bi-upload"></i></span>
                {{ $user->hasUploadedKtp() ? 'Upload Ulang KTP' : 'Upload KTP' }}
            </h5>
            <p class="form-subtitle">Pastikan foto terang, utuh, dan semua data dapat dibaca jelas sebelum dikirim.</p>
            
            <form method="POST" action="{{ route('profile.ktp.upload') }}" enctype="multipart/form-data">
                @csrf

                <div class="upload-tips">
                    <h6>Tips cepat sebelum upload</h6>
                    <p>Gunakan pencahayaan terang, hindari pantulan, dan isi NIK persis seperti yang tertulis pada KTP.</p>
                </div>
                
                <div class="mb-4">
                    <label for="ktp_number" class="form-label fw-semibold">Nomor KTP (NIK)</label>
                    <input type="text" 
                           class="form-control form-control-lg @error('ktp_number') is-invalid @enderror" 
                           id="ktp_number" 
                           name="ktp_number" 
                           value="{{ old('ktp_number', $user->ktp_number) }}"
                           placeholder="Masukkan 16 digit NIK"
                           maxlength="16"
                           pattern="[0-9]{16}"
                           required>
                    @error('ktp_number')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">Masukkan 16 digit Nomor Induk Kependudukan</small>
                </div>
                
                <div class="mb-4">
                    <label class="form-label fw-semibold">Foto KTP</label>
                    <div class="upload-area" id="uploadArea" onclick="document.getElementById('ktp_image').click()">
                        <i class="bi bi-cloud-arrow-up"></i>
                        <p>Klik atau drag & drop foto KTP di sini</p>
                        <small>JPEG, PNG, JPG - Maksimal 5MB</small>
                        <img id="previewImage" class="preview-image" alt="Preview">
                    </div>
                    <input type="file" 
                           id="ktp_image" 
                           name="ktp_image" 
                           class="d-none @error('ktp_image') is-invalid @enderror"
                           accept="image/jpeg,image/png,image/jpg"
                           required>
                    @error('ktp_image')
                        <div class="text-danger small mt-2">{{ $message }}</div>
                    @enderror
                </div>
                
                <button type="submit" class="btn btn-submit" id="submitBtn">
                    <i class="bi bi-upload me-2"></i>Upload KTP
                </button>
            </form>
        </div>
    @endif
</div>

@endsection

@section('js')
<script>
    const uploadArea = document.getElementById('uploadArea');
    const fileInput = document.getElementById('ktp_image');
    const previewImage = document.getElementById('previewImage');
    const ktpNumberInput = document.getElementById('ktp_number');

    function showPreview(file) {
        if (file && file.type.startsWith('image/') && previewImage) {
            const reader = new FileReader();
            reader.onload = (e) => {
                previewImage.src = e.target.result;
                previewImage.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    }

    if (uploadArea && fileInput) {
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach((eventName) => {
            uploadArea.addEventListener(eventName, (e) => {
                e.preventDefault();
                e.stopPropagation();
            }, false);
        });

        ['dragenter', 'dragover'].forEach((eventName) => {
            uploadArea.addEventListener(eventName, () => {
                uploadArea.classList.add('dragover');
            }, false);
        });

        ['dragleave', 'drop'].forEach((eventName) => {
            uploadArea.addEventListener(eventName, () => {
                uploadArea.classList.remove('dragover');
            }, false);
        });

        uploadArea.addEventListener('drop', (e) => {
            const files = e.dataTransfer.files;
            if (files.length) {
                fileInput.files = files;
                showPreview(files[0]);
            }
        });

        fileInput.addEventListener('change', (e) => {
            if (e.target.files.length) {
                showPreview(e.target.files[0]);
            }
        });
    }

    if (ktpNumberInput) {
        ktpNumberInput.addEventListener('input', function () {
            this.value = this.value.replace(/[^0-9]/g, '').slice(0, 16);
        });
    }
</script>
@endsection
