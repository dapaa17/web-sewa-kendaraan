<x-guest-layout>
    <h2>Konfirmasi Password</h2>
    <p class="auth-subtitle">Ini adalah area aman. Silakan konfirmasi password Anda sebelum melanjutkan.</p>

    @if ($errors->any())
        <div class="auth-alert danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <div style="margin-bottom: 1.5rem;">
            <label for="password" class="form-label">Password</label>
            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required placeholder="Masukkan password Anda">
        </div>

        <button type="submit" class="btn-auth">
            <i class="bi bi-shield-lock" style="margin-right:0.4rem;"></i> Konfirmasi
        </button>
    </form>
</x-guest-layout>
