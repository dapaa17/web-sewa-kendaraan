<x-guest-layout>
    <h2>Reset Password</h2>
    <p class="auth-subtitle">Masukkan Email/Username Anda dan password baru untuk mereset.</p>

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

        <div style="margin-bottom: 1.1rem;">
            <label for="email" class="form-label">Email / Username</label>
            <div class="input-icon-wrap">
                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autofocus placeholder="nama@email.com">
                <i class="bi bi-envelope input-icon"></i>
            </div>
        </div>

        <div style="margin-bottom: 1.5rem;">
            <label for="password" class="form-label">Password Baru</label>
            <div class="input-icon-wrap">
                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required placeholder="Minimal 8 karakter">
                <i class="bi bi-lock input-icon"></i>
                <button type="button" class="pw-toggle" onclick="togglePw('password', this)" tabindex="-1"><i class="bi bi-eye-slash"></i></button>
            </div>
        </div>

        <button type="submit" class="btn-auth" style="margin-bottom:0.65rem;">
            <i class="bi bi-check-circle" style="margin-right:0.4rem;"></i> Reset Password
        </button>
        <a href="{{ route('login') }}" class="btn-auth-secondary">
            <i class="bi bi-arrow-left" style="margin-right:0.3rem;"></i> Kembali ke Login
        </a>
    </form>

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
