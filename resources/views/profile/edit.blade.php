@extends(Auth::user()->isAdmin() ? 'layouts.admin' : 'layouts.app')

@section('title', 'Edit Profile')
@if(Auth::user()->isAdmin())
@section('page-title', 'Profile')
@endif

@section('content')
<style>
    .pf-header{background:var(--gradient-brand);color:#fff;padding:3rem 0 5.5rem;position:relative;overflow:hidden}
    .pf-header::before{content:'';position:absolute;inset:0;background:radial-gradient(circle at 80% 25%,rgba(6,182,212,.22),transparent 55%),radial-gradient(circle at 10% 75%,rgba(255,255,255,.05),transparent 45%);pointer-events:none}
    .pf-header h1{font-weight:800;font-size:1.85rem;letter-spacing:-.06em;margin:0}
    .pf-header .subtitle{opacity:.7;font-size:.92rem;margin-top:.35rem}
    .pf-body{margin-top:-3.5rem;position:relative;z-index:2;padding-bottom:3rem}
    .pf-shell{width:100%;max-width:1120px;margin:0 auto}

    /* Avatar circle */
    .pf-avatar{width:72px;height:72px;border-radius:50%;background:rgba(255,255,255,.15);border:2px solid rgba(255,255,255,.25);display:flex;align-items:center;justify-content:center;font-size:1.65rem;font-weight:800;color:#fff;letter-spacing:-.04em;flex-shrink:0}

    /* Card */
    .pf-card{background:#fff;border-radius:1.15rem;box-shadow:0 4px 24px rgba(15,23,42,.07);border:1px solid rgba(203,213,225,.45);overflow:hidden}
    .pf-card-head{padding:1.25rem 2rem;border-bottom:1px solid rgba(203,213,225,.35);display:flex;align-items:center;gap:.6rem}
    .pf-card-head i{color:var(--color-secondary);font-size:1.1rem}
    .pf-card-head h2{font-size:1.05rem;font-weight:700;margin:0;color:#0f172a;letter-spacing:-.03em}
    .pf-card-body{padding:2rem}

    /* Form */
    .pf-group{margin-bottom:1.35rem}
    .pf-label{display:block;font-size:.82rem;font-weight:700;color:#334155;margin-bottom:.4rem;letter-spacing:.02em}
    .pf-label .req{color:#ef4444;margin-left:.15rem}
    .pf-control{display:block;width:100%;padding:.65rem .9rem;border:1.5px solid rgba(203,213,225,.8);border-radius:.75rem;font-size:.88rem;color:#0f172a;background:#fff;transition:all .2s}
    .pf-control:focus{outline:none;border-color:var(--color-secondary);box-shadow:0 0 0 3px rgba(6,182,212,.12)}
    .pf-control.is-invalid{border-color:#ef4444}

    /* Buttons */
    .pf-submit{display:flex;align-items:center;justify-content:center;gap:.5rem;width:100%;padding:.75rem;border:none;border-radius:.85rem;background:var(--gradient-brand);color:#fff;font-weight:700;font-size:.92rem;cursor:pointer;transition:all .25s}
    .pf-submit:hover{opacity:.92;transform:translateY(-1px);box-shadow:0 8px 24px rgba(15,23,42,.15)}

    /* Alert */
    .pf-alert{border-radius:.85rem;border:none;padding:1rem 1.25rem;margin-bottom:1.5rem;font-size:.88rem}
    .pf-alert.danger{background:rgba(239,68,68,.06);color:#dc2626;border-left:3px solid #ef4444}
    .pf-alert.success{background:rgba(16,185,129,.06);color:#059669;border-left:3px solid #10b981}
    .pf-alert ul{margin:0;padding-left:1.2rem}

    /* Danger zone */
    .pf-danger{background:#fff;border-radius:1.15rem;box-shadow:0 4px 24px rgba(15,23,42,.07);border:1px solid rgba(239,68,68,.2);overflow:hidden;margin-top:1.5rem}
    .pf-danger-head{padding:1.1rem 2rem;background:rgba(239,68,68,.04);border-bottom:1px solid rgba(239,68,68,.12);display:flex;align-items:center;gap:.6rem}
    .pf-danger-head i{color:#dc2626;font-size:1.05rem}
    .pf-danger-head h2{font-size:1.05rem;font-weight:700;margin:0;color:#dc2626;letter-spacing:-.03em}
    .pf-danger-body{padding:1.5rem 2rem}
    .pf-danger-body p{font-size:.88rem;color:#64748b;margin-bottom:1rem}
    .pf-del-btn{display:inline-flex;align-items:center;gap:.4rem;padding:.55rem 1.2rem;border:1.5px solid rgba(239,68,68,.3);border-radius:.75rem;background:rgba(239,68,68,.05);color:#dc2626;font-weight:600;font-size:.85rem;cursor:pointer;transition:all .2s}
    .pf-del-btn:hover{background:rgba(239,68,68,.12);border-color:rgba(239,68,68,.45)}

    @media(max-width:767px){
        .pf-header{padding:2rem 0 4.5rem}
        .pf-header h1{font-size:1.4rem}
        .pf-body{margin-top:-2.5rem}
        .pf-card-body,.pf-danger-body{padding:1.25rem}
        .pf-card-head,.pf-danger-head{padding:1rem 1.25rem}
        .pf-del-btn{width:100%;justify-content:center}
    }
</style>

{{-- ── Header ── --}}
<div class="pf-header">
    <div class="container">
        <div class="pf-shell">
            <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center gap-3">
                <div class="pf-avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                <div class="min-w-0">
                    <h1>Edit Profil</h1>
                    <p class="subtitle mb-0">Perbarui informasi akun Anda</p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── Body ── --}}
<div class="pf-body">
    <div class="container">
        <div class="pf-shell">

            {{-- Success --}}
            @if(session('status') === 'profile-updated')
                <div class="pf-alert success"><i class="bi bi-check-circle me-1"></i> Profil berhasil diperbarui.</div>
            @endif

            {{-- Profile Card --}}
            <div class="pf-card">
                <div class="pf-card-head">
                    <i class="bi bi-person-circle"></i>
                    <h2>Informasi Profil</h2>
                </div>
                <div class="pf-card-body">
                    @if ($errors->any())
                        <div class="pf-alert danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('profile.update') }}">
                        @csrf
                        @method('PATCH')

                        <div class="pf-group">
                            <label for="name" class="pf-label">Nama <span class="req">*</span></label>
                            <input id="name" name="name" type="text" class="pf-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                            @error('name')<div class="text-danger" style="font-size:.8rem;margin-top:.35rem">{{ $message }}</div>@enderror
                        </div>

                        <div class="pf-group">
                            <label for="email" class="pf-label">Email <span class="req">*</span></label>
                            <input id="email" name="email" type="email" class="pf-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                            @error('email')<div class="text-danger" style="font-size:.8rem;margin-top:.35rem">{{ $message }}</div>@enderror
                        </div>

                        <button type="submit" class="pf-submit"><i class="bi bi-check-lg"></i> Simpan Perubahan</button>
                    </form>
                </div>
            </div>

            {{-- Danger Zone --}}
            <div class="pf-danger">
                <div class="pf-danger-head">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <h2>Zona Berbahaya</h2>
                </div>
                <div class="pf-danger-body">
                    <p>Setelah akun dihapus, semua data dan riwayat booking Anda akan hilang secara permanen dan tidak dapat dipulihkan.</p>
                    <form method="POST" action="{{ route('profile.destroy') }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun? Tindakan ini tidak bisa dibatalkan.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="pf-del-btn"><i class="bi bi-trash3"></i> Hapus Akun Saya</button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
