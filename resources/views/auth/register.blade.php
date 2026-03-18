<x-guest-layout>
    <h2>Buat Akun Baru</h2>
    <p class="auth-subtitle">Daftar untuk mulai rental kendaraan dengan mudah.</p>

    @if ($errors->any())
        <div class="auth-alert danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div style="margin-bottom: 1.1rem;">
            <label for="name" class="form-label">Nama Lengkap</label>
            <div class="input-icon-wrap">
                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autofocus placeholder="Nama lengkap Anda">
                <i class="bi bi-person input-icon"></i>
            </div>
        </div>

        <div style="margin-bottom: 1.1rem;">
            <label for="email" class="form-label">Email</label>
            <div class="input-icon-wrap">
                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required placeholder="nama@email.com">
                <i class="bi bi-envelope input-icon"></i>
            </div>
        </div>

        <div style="margin-bottom: 1.1rem;">
            <label for="password" class="form-label">Password</label>
            <div class="input-icon-wrap">
                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required placeholder="Minimal 8 karakter">
                <i class="bi bi-lock input-icon"></i>
                <button type="button" class="pw-toggle" onclick="togglePw('password', this)" tabindex="-1"><i class="bi bi-eye-slash"></i></button>
            </div>
        </div>

        <div style="margin-bottom: 1.5rem;">
            <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
            <div class="input-icon-wrap">
                <input id="password_confirmation" type="password" class="form-control" name="password_confirmation" required placeholder="Ulangi password">
                <i class="bi bi-lock input-icon"></i>
                <button type="button" class="pw-toggle" onclick="togglePw('password_confirmation', this)" tabindex="-1"><i class="bi bi-eye-slash"></i></button>
            </div>
        </div>

        <button type="submit" class="btn-auth">
            <i class="bi bi-person-plus" style="margin-right:0.4rem;"></i> Daftar
        </button>
    </form>

    <div class="auth-footer">
        Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a>
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
