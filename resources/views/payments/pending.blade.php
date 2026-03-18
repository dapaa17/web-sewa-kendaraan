@extends('layouts.app')

@section('title', 'Pembayaran Pending')

@section('content')
<style>
    .pp-header{background:var(--gradient-brand);color:#fff;padding:3rem 0 5.5rem;position:relative;overflow:hidden}
    .pp-header::before{content:'';position:absolute;inset:0;background:radial-gradient(circle at 80% 25%,rgba(6,182,212,.22),transparent 55%),radial-gradient(circle at 10% 75%,rgba(255,255,255,.05),transparent 45%);pointer-events:none}
    .pp-body{margin-top:-3.5rem;position:relative;z-index:2;padding-bottom:3rem}
    .pp-center{max-width:540px;margin:0 auto;text-align:center}
    .pp-icon{width:88px;height:88px;margin:0 auto 1.25rem;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:2.5rem;background:linear-gradient(135deg,rgba(245,158,11,.14),rgba(251,191,36,.2));box-shadow:0 14px 36px rgba(245,158,11,.14)}
    .pp-title{font-weight:800;font-size:1.6rem;color:#b45309;letter-spacing:-.05em;margin-bottom:.5rem}
    .pp-desc{color:#64748b;font-size:.92rem;margin-bottom:1.5rem;line-height:1.6}
    .pp-card{background:linear-gradient(135deg,rgba(255,251,235,.96),rgba(255,255,255,.98));border:1px solid rgba(245,158,11,.2);border-radius:1.15rem;box-shadow:0 4px 24px rgba(15,23,42,.06);padding:1.25rem 1.5rem;margin-bottom:1.25rem}
    .pp-card h3{font-size:.88rem;font-weight:700;color:#0f172a;margin-bottom:.85rem;display:flex;align-items:center;gap:.45rem;justify-content:center}
    .pp-card h3 i{color:#d97706}
    .pp-detail{display:flex;justify-content:space-between;align-items:center;padding:.5rem 0;border-bottom:1px solid rgba(245,158,11,.1);font-size:.86rem}
    .pp-detail:last-child{border-bottom:none}
    .pp-detail span{color:#64748b}
    .pp-detail strong{color:#0f172a}
    .pp-badge{display:inline-flex;align-items:center;gap:.3rem;padding:.25rem .65rem;border-radius:2rem;font-size:.75rem;font-weight:600;background:rgba(245,158,11,.1);color:#b45309}
    .pp-info{background:#fff;border:1px solid rgba(203,213,225,.45);border-radius:1rem;padding:1.15rem 1.35rem;margin-bottom:1.5rem;text-align:left}
    .pp-info h4{font-size:.88rem;font-weight:700;color:#0f172a;margin-bottom:.65rem;display:flex;align-items:center;gap:.4rem}
    .pp-info h4 i{color:#0ea5e9}
    .pp-info ul{list-style:none;padding:0;margin:0}
    .pp-info ul li{font-size:.84rem;color:#475569;padding:.35rem 0;display:flex;align-items:flex-start;gap:.45rem}
    .pp-info ul li::before{content:'';width:6px;height:6px;border-radius:50%;background:rgba(6,182,212,.4);flex-shrink:0;margin-top:6px}
    .pp-btn-primary{display:flex;align-items:center;justify-content:center;gap:.5rem;width:100%;padding:.75rem;border:none;border-radius:.85rem;background:var(--gradient-brand);color:#fff;font-weight:700;font-size:.92rem;cursor:pointer;transition:all .25s}
    .pp-btn-primary:hover{opacity:.92;transform:translateY(-1px);box-shadow:0 8px 24px rgba(15,23,42,.15);color:#fff}
    .pp-btn-secondary{display:flex;align-items:center;justify-content:center;gap:.5rem;width:100%;padding:.7rem;border:1.5px solid rgba(203,213,225,.8);border-radius:.85rem;background:#fff;color:#475569;font-weight:600;font-size:.88rem;text-decoration:none;transition:all .2s;margin-top:.65rem}
    .pp-btn-secondary:hover{border-color:#94a3b8;color:#0f172a}
    @media(max-width:767px){.pp-header{padding:2rem 0 4.5rem}.pp-body{margin-top:-2.5rem}.pp-title{font-size:1.3rem}}
</style>

<div class="pp-header"></div>

<div class="pp-body">
    <div class="container">
        <div class="pp-center">
            <div class="pp-icon"><i class="bi bi-hourglass-split" style="color:#d97706"></i></div>
            <h1 class="pp-title">Pembayaran Pending</h1>
            <p class="pp-desc">Pembayaran Anda masih dalam proses. Sistem kami akan mengupdate status pembayaran segera.</p>

            <div class="pp-card">
                <h3><i class="bi bi-hourglass-split"></i> Detail Status</h3>
                <div class="pp-detail">
                    <span>Status</span>
                    <span class="pp-badge"><i class="bi bi-clock-fill" style="font-size:.65rem"></i> Pending</span>
                </div>
                @if(request('order_id'))
                <div class="pp-detail">
                    <span>Order ID</span>
                    <strong style="font-family:monospace;font-size:.84rem">{{ request('order_id') }}</strong>
                </div>
                @endif
            </div>

            <div class="pp-info">
                <h4><i class="bi bi-info-circle"></i> Apa yang terjadi selanjutnya?</h4>
                <ul>
                    <li>Sistem kami akan memverifikasi pembayaran Anda</li>
                    <li>Notifikasi sukses akan dikirim via email dalam beberapa menit</li>
                    <li>Jika ada masalah, kami akan menghubungi Anda</li>
                    <li>Jangan lakukan pembayaran ulang</li>
                </ul>
            </div>

            <button type="button" class="pp-btn-primary" id="checkStatusBtn"><i class="bi bi-arrow-clockwise"></i> Cek Status Pembayaran</button>
            <a href="{{ route('bookings.index') }}" class="pp-btn-secondary"><i class="bi bi-calendar-check"></i> Lihat Booking</a>
            <a href="{{ route('dashboard') }}" class="pp-btn-secondary"><i class="bi bi-house"></i> Kembali ke Dashboard</a>
        </div>
    </div>
</div>

<script>
    document.getElementById('checkStatusBtn').addEventListener('click', function() {
        const orderId = '{{ request("order_id") ?? "" }}';
        if (orderId) {
            this.disabled = true;
            this.innerHTML = '<i class="bi bi-hourglass"></i> Memeriksa...';
            location.reload();
        }
    });
</script>
@endsection
