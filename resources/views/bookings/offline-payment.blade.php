@php($usesAdminLayout = (bool) auth()->user()?->isAdmin())
@extends($usesAdminLayout ? 'layouts.admin' : 'layouts.app')

@section('title', 'Transfer dan Upload Bukti')
@if($usesAdminLayout)
@section('page-title', 'Upload Bukti Bayar')
@endif

@section('css')
<style>
    .op-header{background:var(--gradient-brand);color:#fff;padding:3rem 0 5.5rem;position:relative;overflow:hidden}
    .op-header::before{content:'';position:absolute;inset:0;background:radial-gradient(circle at 80% 25%,rgba(6,182,212,.22),transparent 55%),radial-gradient(circle at 10% 75%,rgba(255,255,255,.05),transparent 45%);pointer-events:none}
    .op-header h1{font-weight:800;font-size:1.85rem;letter-spacing:-.06em;margin:0}
    .op-header .subtitle{opacity:.7;font-size:.92rem;margin-top:.35rem}
    .op-back{display:inline-flex;align-items:center;gap:.4rem;color:rgba(255,255,255,.7);text-decoration:none;font-size:.85rem;font-weight:500;margin-bottom:.75rem;transition:color .2s}
    .op-back:hover{color:#fff}
    .op-body{margin-top:-3.5rem;position:relative;z-index:2;padding-bottom:3rem}

    /* Card */
    .op-card{background:#fff;border-radius:1.15rem;box-shadow:0 4px 24px rgba(15,23,42,.07);border:1px solid rgba(203,213,225,.45);overflow:hidden;margin-bottom:1.25rem}
    .op-card-head{padding:1rem 1.5rem;border-bottom:1px solid rgba(203,213,225,.35);display:flex;align-items:center;gap:.6rem}
    .op-card-head i{color:var(--color-secondary);font-size:1rem}
    .op-card-head h2{font-size:.95rem;font-weight:700;margin:0;color:#0f172a;letter-spacing:-.03em}
    .op-card-body{padding:1.5rem}

    /* Instructions */
    .op-steps{list-style:none;padding:0;margin:0;counter-reset:step}
    .op-steps li{counter-increment:step;display:flex;align-items:flex-start;gap:.75rem;padding:.6rem 0;font-size:.86rem;color:#334155;border-bottom:1px solid rgba(203,213,225,.3)}
    .op-steps li:last-child{border-bottom:none}
    .op-steps li::before{content:counter(step);display:flex;align-items:center;justify-content:center;width:1.55rem;height:1.55rem;border-radius:50%;background:rgba(6,182,212,.1);color:#0e7490;font-size:.72rem;font-weight:700;flex-shrink:0;margin-top:1px}

    /* Bank info */
    .op-bank{background:linear-gradient(135deg,rgba(248,250,252,.9) 0%,rgba(6,182,212,.06) 100%);border-radius:.85rem;padding:1rem 1.25rem;margin-top:1rem}
    .op-bank-row{display:flex;justify-content:space-between;align-items:center;padding:.55rem 0;border-bottom:1px solid rgba(203,213,225,.35);font-size:.86rem}
    .op-bank-row:last-child{border-bottom:none}
    .op-bank-row span{color:#64748b}
    .op-bank-row strong{color:#0f172a}
    .op-bank-row .amount{font-size:1.05rem;color:var(--color-primary);font-weight:800}
    .op-copy{display:inline-flex;align-items:center;gap:.3rem;padding:.25rem .55rem;border:1px solid rgba(203,213,225,.65);border-radius:.5rem;background:#fff;color:#475569;font-size:.75rem;font-weight:600;cursor:pointer;transition:all .2s}
    .op-copy:hover{border-color:var(--color-secondary);color:var(--color-secondary)}

    /* Upload zone */
    .op-upload-zone{border:2px dashed rgba(203,213,225,.7);border-radius:.85rem;padding:2rem 1.5rem;text-align:center;transition:all .2s;cursor:pointer;background:rgba(248,250,252,.5)}
    .op-upload-zone:hover{border-color:var(--color-secondary);background:rgba(6,182,212,.03)}
    .op-upload-zone i{font-size:2rem;color:#94a3b8;display:block;margin-bottom:.5rem}
    .op-upload-zone span{font-size:.84rem;color:#64748b}
    .op-preview{margin-top:1rem;text-align:center;display:none}
    .op-preview img{max-height:200px;border-radius:.65rem;box-shadow:0 2px 12px rgba(0,0,0,.08)}

    /* Buttons */
    .op-submit{display:flex;align-items:center;justify-content:center;gap:.5rem;width:100%;padding:.75rem;border:none;border-radius:.85rem;background:var(--gradient-brand);color:#fff;font-weight:700;font-size:.92rem;cursor:pointer;transition:all .25s}
    .op-submit:hover{opacity:.92;transform:translateY(-1px);box-shadow:0 8px 24px rgba(15,23,42,.15)}
    .op-cancel{display:flex;align-items:center;justify-content:center;gap:.5rem;width:100%;padding:.7rem;border:1.5px solid rgba(203,213,225,.8);border-radius:.85rem;background:#fff;color:#475569;font-weight:600;font-size:.88rem;text-decoration:none;transition:all .2s;margin-top:.65rem}
    .op-cancel:hover{border-color:#94a3b8;color:#0f172a}

    /* Note */
    .op-note{border-radius:.85rem;background:rgba(6,182,212,.05);border:1px solid rgba(6,182,212,.15);padding:.85rem 1.15rem;font-size:.84rem;color:#0e7490;display:flex;align-items:flex-start;gap:.5rem;margin-top:1rem}
    .op-note i{flex-shrink:0;margin-top:2px}

    /* Help box */
    .op-help{background:#fff;border:1px solid rgba(203,213,225,.45);border-radius:1rem;padding:1.25rem 1.5rem;margin-top:1.5rem}
    .op-help h3{font-size:.9rem;font-weight:700;color:#0f172a;margin-bottom:.65rem;display:flex;align-items:center;gap:.4rem}
    .op-help h3 i{color:#94a3b8}
    .op-help p{font-size:.84rem;color:#64748b;margin-bottom:.5rem}
    .op-help ul{list-style:none;padding:0;margin:0}
    .op-help ul li{font-size:.84rem;color:#475569;padding:.3rem 0;display:flex;align-items:center;gap:.4rem}
    .op-help ul li i{color:#94a3b8;width:.9rem;text-align:center;flex-shrink:0}

    @media(max-width:767px){
        .op-header{padding:2rem 0 4.5rem}
        .op-header h1{font-size:1.4rem}
        .op-body{margin-top:-2.5rem}
    }
</style>
@endsection

@section('content')
{{-- ── Header ── --}}
<div class="op-header">
    <div class="container" style="max-width:700px">
        <a href="{{ route('bookings.payment', $booking) }}" class="op-back"><i class="bi bi-arrow-left"></i> Pilih Metode Lain</a>
        <h1><i class="bi bi-bank2 me-2" style="font-size:1.5rem"></i>Transfer & Upload Bukti</h1>
        <p class="subtitle mb-0">Booking #{{ $booking->id }} — Rp{{ number_format($booking->total_price, 0, ',', '.') }}</p>
    </div>
</div>

{{-- ── Body ── --}}
<div class="op-body">
    <div class="container" style="max-width:700px">

        {{-- Instructions --}}
        <div class="op-card">
            <div class="op-card-head">
                <i class="bi bi-list-check"></i>
                <h2>Cara Pembayaran</h2>
            </div>
            <div class="op-card-body">
                <ol class="op-steps">
                    <li>Transfer ke rekening yang tertera di bawah</li>
                    <li>Minimal transfer sesuai total pembayaran</li>
                    <li>Upload bukti transfer (screenshot/foto)</li>
                    <li>Upload sebelum batas waktu pembayaran berakhir</li>
                    <li>Booking aktif setelah pembayaran terverifikasi</li>
                </ol>
            </div>
        </div>

        {{-- Bank Info --}}
        <div class="op-card">
            <div class="op-card-head">
                <i class="bi bi-credit-card-2-front"></i>
                <h2>Rekening Tujuan Transfer</h2>
            </div>
            <div class="op-card-body">
                <div class="op-bank">
                    <div class="op-bank-row">
                        <span>Bank</span>
                        <strong>{{ config('services.whatsapp.bank_name') }}</strong>
                    </div>
                    <div class="op-bank-row">
                        <span>Nomor Rekening</span>
                        <div class="d-flex align-items-center gap-2">
                            <strong>{{ config('services.whatsapp.account_number') }}</strong>
                            <button type="button" class="op-copy" onclick="copyToClipboard('{{ config('services.whatsapp.account_number') }}')">
                                <i class="bi bi-files"></i> Copy
                            </button>
                        </div>
                    </div>
                    <div class="op-bank-row">
                        <span>Atas Nama</span>
                        <strong>{{ config('services.whatsapp.account_name') }}</strong>
                    </div>
                    <div class="op-bank-row">
                        <span>Jumlah</span>
                        <strong class="amount">Rp{{ number_format($booking->total_price, 0, ',', '.') }}</strong>
                    </div>
                </div>

                <div class="op-note">
                    <i class="bi bi-info-circle"></i>
                    <span>Sertakan nomor booking <strong>#{{ $booking->id }}</strong> di keterangan transfer untuk memudahkan verifikasi.</span>
                </div>
            </div>
        </div>

        {{-- Upload Form --}}
        <div class="op-card">
            <div class="op-card-head">
                <i class="bi bi-cloud-arrow-up"></i>
                <h2>Upload Bukti Transfer</h2>
            </div>
            <div class="op-card-body">
                <form method="POST" action="{{ route('bookings.upload-proof', $booking) }}" enctype="multipart/form-data" id="uploadForm">
                    @csrf

                    <label class="op-upload-zone" for="paymentProof">
                        <i class="bi bi-image"></i>
                        <span>Klik untuk pilih file bukti transfer — JPG, PNG maks 2MB</span>
                    </label>
                    <input type="file" class="d-none" id="paymentProof" name="payment_proof" accept="image/*" required>

                    <div class="op-preview" id="filePreview">
                        <p style="font-size:.82rem;color:#059669;margin-bottom:.5rem"><i class="bi bi-check-circle"></i> Preview:</p>
                        <img id="previewImage" src="" alt="Preview">
                    </div>

                    @error('payment_proof')
                        <div class="text-danger" style="font-size:.82rem;margin-top:.5rem">{{ $message }}</div>
                    @enderror

                    <div style="margin-top:1.5rem">
                        <button type="submit" class="op-submit" id="submitBtn">
                            <i class="bi bi-check-circle"></i> Kirim Bukti Transfer
                        </button>
                        <a href="{{ route('bookings.payment', $booking) }}" class="op-cancel">
                            <i class="bi bi-arrow-left"></i> Kembali Pilih Metode
                        </a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Help --}}
        <div class="op-help">
            <h3><i class="bi bi-question-circle"></i> Butuh Bantuan?</h3>
            <p>Jika ada pertanyaan atau masalah dengan pembayaran, hubungi kami:</p>
            <ul>
                <li><i class="bi bi-whatsapp"></i> WhatsApp: +{{ config('services.whatsapp.admin_number') }}</li>
                <li><i class="bi bi-envelope"></i> Email: support@rentalhub.com</li>
                <li><i class="bi bi-clock"></i> Jam operasional: 08:00 - 18:00 (Senin-Jumat)</li>
            </ul>
        </div>

    </div>
</div>
@endsection

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const fileInput = document.getElementById('paymentProof');
        const filePreview = document.getElementById('filePreview');
        const previewImage = document.getElementById('previewImage');

        fileInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const file = this.files[0];
                
                if (file.size > 2 * 1024 * 1024) {
                    alert('File terlalu besar! Maksimal 2MB');
                    this.value = '';
                    filePreview.style.display = 'none';
                    return;
                }
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    filePreview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                filePreview.style.display = 'none';
            }
        });
    });

    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(function() {
            alert('Nomor rekening berhasil dicopy!');
        });
    }
</script>
@endsection