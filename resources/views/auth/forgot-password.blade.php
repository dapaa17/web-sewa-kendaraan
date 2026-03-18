<x-guest-layout>
    <h2>Lupa Password?</h2>
    <p class="auth-subtitle">Tidak masalah. Masukkan email Anda dan kami akan kirimkan link reset password.</p>

    @if (session('status'))
        <div class="auth-alert success">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
        <div class="auth-alert danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div style="margin-bottom: 1.5rem;">
            <label for="email" class="form-label">Email</label>
            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autofocus placeholder="nama@email.com">
        </div>

        <button type="submit" class="btn-auth" style="margin-bottom:0.65rem;">
            <i class="bi bi-envelope" style="margin-right:0.4rem;"></i> Kirim Link Reset
        </button>
        <a href="{{ route('login') }}" class="btn-auth-secondary">
            <i class="bi bi-arrow-left" style="margin-right:0.3rem;"></i> Kembali ke Login
        </a>
    </form>
</x-guest-layout>
