@extends('layouts.app')

@section('title', 'Konfirmasi Pembayaran Gagal')

@section('content')
<style>
    .pe-header{background:var(--gradient-brand);color:#fff;padding:3rem 0 5.5rem;position:relative;overflow:hidden}
    .pe-header::before{content:'';position:absolute;inset:0;background:radial-gradient(circle at 80% 25%,rgba(6,182,212,.22),transparent 55%),radial-gradient(circle at 10% 75%,rgba(255,255,255,.05),transparent 45%);pointer-events:none}
    .pe-body{margin-top:-3.5rem;position:relative;z-index:2;padding-bottom:3rem}
    .pe-center{max-width:540px;margin:0 auto;text-align:center}
    .pe-icon{width:88px;height:88px;margin:0 auto 1.25rem;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:2.5rem;background:linear-gradient(135deg,rgba(239,68,68,.12),rgba(254,202,202,.2));box-shadow:0 14px 36px rgba(239,68,68,.14)}
    .pe-title{font-weight:800;font-size:1.6rem;color:#dc2626;letter-spacing:-.05em;margin-bottom:.5rem}
    .pe-desc{color:#64748b;font-size:.92rem;margin-bottom:1.5rem;line-height:1.6}
    .pe-card{background:linear-gradient(135deg,rgba(254,242,242,.96),rgba(255,255,255,.98));border:1px solid rgba(239,68,68,.18);border-radius:1.15rem;box-shadow:0 4px 24px rgba(15,23,42,.06);padding:1.25rem 1.5rem;margin-bottom:1.25rem}
    .pe-card h3{font-size:.88rem;font-weight:700;color:#0f172a;margin-bottom:.85rem;display:flex;align-items:center;gap:.45rem;justify-content:center}
    .pe-card h3 i{color:#dc2626}
    .pe-detail{display:flex;justify-content:space-between;align-items:center;padding:.5rem 0;border-bottom:1px solid rgba(239,68,68,.08);font-size:.86rem}
    .pe-detail:last-child{border-bottom:none}
    .pe-detail span{color:#64748b}
    .pe-detail strong{color:#0f172a}
    .pe-badge{display:inline-flex;align-items:center;gap:.3rem;padding:.25rem .65rem;border-radius:2rem;font-size:.75rem;font-weight:600;background:rgba(239,68,68,.08);color:#dc2626}
    .pe-tips{background:#fff;border:1px solid rgba(203,213,225,.45);border-radius:1rem;padding:1.15rem 1.35rem;margin-bottom:1.5rem;text-align:left}
    .pe-tips h4{font-size:.88rem;font-weight:700;color:#0f172a;margin-bottom:.65rem;display:flex;align-items:center;gap:.4rem}
    .pe-tips h4 i{color:#f59e0b}
    .pe-tips ul{list-style:none;padding:0;margin:0}
    .pe-tips ul li{font-size:.84rem;color:#475569;padding:.35rem 0;display:flex;align-items:flex-start;gap:.45rem}
    .pe-tips ul li::before{content:'';width:6px;height:6px;border-radius:50%;background:rgba(239,68,68,.3);flex-shrink:0;margin-top:6px}
    .pe-btn-primary{display:flex;align-items:center;justify-content:center;gap:.5rem;width:100%;padding:.75rem;border:none;border-radius:.85rem;background:var(--gradient-brand);color:#fff;font-weight:700;font-size:.92rem;text-decoration:none;transition:all .25s}
    .pe-btn-primary:hover{opacity:.92;transform:translateY(-1px);box-shadow:0 8px 24px rgba(15,23,42,.15);color:#fff}
    .pe-btn-secondary{display:flex;align-items:center;justify-content:center;gap:.5rem;width:100%;padding:.7rem;border:1.5px solid rgba(203,213,225,.8);border-radius:.85rem;background:#fff;color:#475569;font-weight:600;font-size:.88rem;text-decoration:none;transition:all .2s;margin-top:.65rem}
    .pe-btn-secondary:hover{border-color:#94a3b8;color:#0f172a}
    @media(max-width:767px){.pe-header{padding:2rem 0 4.5rem}.pe-body{margin-top:-2.5rem}.pe-title{font-size:1.3rem}}
</style>

<div class="pe-header"></div>

<div class="pe-body">
    <div class="container">
        <div class="pe-center">
            <div class="pe-icon"><i class="bi bi-x-circle-fill" style="color:#ef4444"></i></div>
            <h1 class="pe-title">Konfirmasi Pembayaran Gagal</h1>
            <p class="pe-desc">Maaf, pembayaran Anda belum berhasil diproses. Silakan cek detail di bawah lalu ulangi pembayaran atau kirim ulang bukti transfer yang benar.</p>

            <div class="pe-card">
                <h3><i class="bi bi-exclamation-circle"></i> Detail Error</h3>
                <div class="pe-detail">
                    <span>Status</span>
                    <span class="pe-badge"><i class="bi bi-x-circle-fill" style="font-size:.65rem"></i> Failed</span>
                </div>
                @if(request('order_id'))
                <div class="pe-detail">
                    <span>Order ID</span>
                    <strong style="font-family:monospace;font-size:.84rem">{{ request('order_id') }}</strong>
                </div>
                @endif
                <div class="pe-detail">
                    <span>Pesan</span>
                    <strong style="font-size:.84rem">{{ request('error_message') ?? 'Konfirmasi pembayaran ditolak' }}</strong>
                </div>
            </div>

            <div class="pe-tips">
                <h4><i class="bi bi-lightbulb"></i> Tips Troubleshooting</h4>
                <ul>
                    <li>Pastikan nominal transfer sesuai total pembayaran booking</li>
                    <li>Pastikan bukti transfer dapat dibaca dengan jelas</li>
                    <li>Ulangi pembayaran: upload bukti transfer atau konfirmasi via WhatsApp</li>
                    <li>Hubungi admin jika masalah berlanjut</li>
                </ul>
            </div>

            <a href="{{ route('bookings.index') }}" class="pe-btn-primary"><i class="bi bi-arrow-left"></i> Kembali ke Booking</a>
            <a href="{{ route('dashboard') }}" class="pe-btn-secondary"><i class="bi bi-house"></i> Kembali ke Dashboard</a>
        </div>
    </div>
</div>
@endsection
