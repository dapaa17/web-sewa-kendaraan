<x-guest-layout>
    <div style="text-align:center; margin-bottom:1.5rem;">
        <div style="width:3.5rem; height:3.5rem; border-radius:1rem; display:inline-flex; align-items:center; justify-content:center; background:rgba(6,182,212,0.12); color:var(--color-secondary-strong); font-size:1.5rem; margin-bottom:0.75rem;">
            <i class="bi bi-envelope-check"></i>
        </div>
        <h2 style="margin-bottom:0.3rem;">Verifikasi Email</h2>
    </div>
    <p class="auth-subtitle" style="text-align:center;">Terima kasih sudah mendaftar! Silakan verifikasi email Anda dengan klik link yang kami kirimkan.</p>

    @if (session('status') == 'verification-link-sent')
        <div class="auth-alert success">Link verifikasi baru telah dikirim ke email yang Anda daftarkan.</div>
    @endif

    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit" class="btn-auth" style="margin-bottom:0.65rem;">
            <i class="bi bi-arrow-clockwise" style="margin-right:0.4rem;"></i> Kirim Ulang Email
        </button>
    </form>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="btn-auth-secondary">
            <i class="bi bi-box-arrow-right" style="margin-right:0.3rem;"></i> Logout
        </button>
    </form>
</x-guest-layout>
