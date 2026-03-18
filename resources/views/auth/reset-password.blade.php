<x-guest-layout>
    <h2>Reset Password</h2>
    <p class="auth-subtitle">Masukkan password baru Anda di bawah ini.</p>

    @if ($errors->any())
        <div class="auth-alert danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('password.store') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div style="margin-bottom: 1.1rem;">
            <label for="email" class="form-label">Email</label>
            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email', $request->email) }}" required autofocus>
        </div>

        <div style="margin-bottom: 1.1rem;">
            <label for="password" class="form-label">Password Baru</label>
            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required placeholder="Minimal 8 karakter">
        </div>

        <div style="margin-bottom: 1.5rem;">
            <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
            <input id="password_confirmation" type="password" class="form-control" name="password_confirmation" required placeholder="Ulangi password baru">
        </div>

        <button type="submit" class="btn-auth">
            <i class="bi bi-key" style="margin-right:0.4rem;"></i> Reset Password
        </button>
    </form>
</x-guest-layout>
