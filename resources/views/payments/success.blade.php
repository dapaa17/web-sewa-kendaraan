@extends('layouts.app')

@section('title', 'Pembayaran Berhasil')

@section('content')
<style>
    .ps-header{background:var(--gradient-brand);color:#fff;padding:3rem 0 5.5rem;position:relative;overflow:hidden}
    .ps-header::before{content:'';position:absolute;inset:0;background:radial-gradient(circle at 80% 25%,rgba(6,182,212,.22),transparent 55%),radial-gradient(circle at 10% 75%,rgba(255,255,255,.05),transparent 45%);pointer-events:none}
    .ps-body{margin-top:-3.5rem;position:relative;z-index:2;padding-bottom:3rem}
    .ps-center{max-width:540px;margin:0 auto;text-align:center}
    .ps-icon{width:88px;height:88px;margin:0 auto 1.25rem;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:2.5rem;background:linear-gradient(135deg,rgba(16,185,129,.14),rgba(209,250,229,.22));box-shadow:0 14px 36px rgba(16,185,129,.14)}
    .ps-title{font-weight:800;font-size:1.6rem;color:#059669;letter-spacing:-.05em;margin-bottom:.5rem}
    .ps-desc{color:#64748b;font-size:.92rem;margin-bottom:1.5rem;line-height:1.6}
    .ps-card{background:linear-gradient(135deg,rgba(236,253,245,.96),rgba(255,255,255,.98));border:1px solid rgba(16,185,129,.2);border-radius:1.15rem;box-shadow:0 4px 24px rgba(15,23,42,.06);padding:1.25rem 1.5rem;margin-bottom:1.5rem}
    .ps-card h3{font-size:.88rem;font-weight:700;color:#0f172a;margin-bottom:.85rem;display:flex;align-items:center;gap:.45rem;justify-content:center}
    .ps-card h3 i{color:#10b981}
    .ps-detail{display:flex;justify-content:space-between;align-items:center;padding:.5rem 0;border-bottom:1px solid rgba(16,185,129,.1);font-size:.86rem}
    .ps-detail:last-child{border-bottom:none}
    .ps-detail span{color:#64748b}
    .ps-detail strong{color:#0f172a}
    .ps-badge{display:inline-flex;align-items:center;gap:.3rem;padding:.25rem .65rem;border-radius:2rem;font-size:.75rem;font-weight:600;background:rgba(16,185,129,.1);color:#059669}
    .ps-btn-primary{display:flex;align-items:center;justify-content:center;gap:.5rem;width:100%;padding:.75rem;border:none;border-radius:.85rem;background:var(--gradient-brand);color:#fff;font-weight:700;font-size:.92rem;text-decoration:none;transition:all .25s}
    .ps-btn-primary:hover{opacity:.92;transform:translateY(-1px);box-shadow:0 8px 24px rgba(15,23,42,.15);color:#fff}
    .ps-btn-secondary{display:flex;align-items:center;justify-content:center;gap:.5rem;width:100%;padding:.7rem;border:1.5px solid rgba(203,213,225,.8);border-radius:.85rem;background:#fff;color:#475569;font-weight:600;font-size:.88rem;text-decoration:none;transition:all .2s;margin-top:.65rem}
    .ps-btn-secondary:hover{border-color:#94a3b8;color:#0f172a}
    .ps-note{border-radius:.85rem;background:rgba(6,182,212,.05);border:1px solid rgba(6,182,212,.15);padding:.85rem 1.15rem;font-size:.84rem;color:#0e7490;display:flex;align-items:center;gap:.5rem;margin-top:1.25rem;justify-content:center}
    @media(max-width:767px){.ps-header{padding:2rem 0 4.5rem}.ps-body{margin-top:-2.5rem}.ps-title{font-size:1.3rem}}
</style>

<div class="ps-header"></div>

<div class="ps-body">
    <div class="container">
        <div class="ps-center">
            <div class="ps-icon"><i class="bi bi-check-circle-fill" style="color:#10b981"></i></div>
            <h1 class="ps-title">Pembayaran Berhasil!</h1>
            <p class="ps-desc">Terima kasih telah melakukan pembayaran. Booking Anda telah dikonfirmasi.</p>

            <div class="ps-card">
                <h3><i class="bi bi-check-circle"></i> Detail Pembayaran</h3>
                <div class="ps-detail">
                    <span>Status</span>
                    <span class="ps-badge"><i class="bi bi-check-circle-fill" style="font-size:.65rem"></i> Confirmed</span>
                </div>
                @if(request('order_id'))
                <div class="ps-detail">
                    <span>Order ID</span>
                    <strong style="font-family:monospace;font-size:.84rem">{{ request('order_id') }}</strong>
                </div>
                @endif
            </div>

            <a href="{{ route('bookings.index') }}" class="ps-btn-primary"><i class="bi bi-calendar-check"></i> Lihat Semua Booking</a>
            <a href="{{ route('dashboard') }}" class="ps-btn-secondary"><i class="bi bi-house"></i> Kembali ke Dashboard</a>

            <div class="ps-note"><i class="bi bi-envelope"></i> Invoice dan detail booking telah dikirim ke email Anda.</div>
        </div>
    </div>
</div>
@endsection
