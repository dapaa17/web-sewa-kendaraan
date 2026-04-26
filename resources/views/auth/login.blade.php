<x-guest-layout>
    <h2>Masuk ke Akun</h2>
    <p class="auth-subtitle">Selamat datang kembali! Masukkan kredensial Anda.</p>

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

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="auth-form-row">
            <label for="email" class="form-label">Email</label>
            <div class="input-icon-wrap">
                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autofocus placeholder="nama@email.com">
                <i class="bi bi-envelope input-icon"></i>
            </div>
        </div>

        <div class="auth-form-row">
            <label for="password" class="form-label">Password</label>
            <div class="input-icon-wrap has-toggle">
                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required placeholder="••••••••">
                <i class="bi bi-lock input-icon"></i>
                <button type="button" class="pw-toggle" onclick="togglePw('password', this)" tabindex="-1"><i class="bi bi-eye-slash"></i></button>
            </div>
        </div>

        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem; font-size:0.84rem;">
            <label style="display:flex; align-items:center; gap:0.4rem; cursor:pointer; color:var(--color-muted);">
                <input type="checkbox" class="form-check-input" name="remember" style="margin:0;"> Ingat saya
            </label>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" style="color:var(--color-secondary-strong); font-weight:600; text-decoration:none;">Lupa password?</a>
            @endif
        </div>

        <button type="submit" class="btn-auth">
            <i class="bi bi-box-arrow-in-right" style="margin-right:0.4rem;"></i> Masuk
        </button>
    </form>

    <div class="auth-footer">
        Belum punya akun? <a href="{{ route('register') }}">Daftar sekarang</a>
    </div>

    <script>
    function togglePw(id, btn) {
        const input = document.getElementById(id);
        const icon = btn.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.className = 'bi bi-eye';
        } else {
            input.type = 'password';
            icon.className = 'bi bi-eye-slash';
        }
    }
    </script>
</x-guest-layout>
