@extends('layouts.admin')

@section('title', 'Edit Kendaraan')
@section('page-title', 'Edit Kendaraan')

@section('content')
<style>
    .vf-header{background:var(--gradient-brand);color:#fff;padding:3rem 0 5.5rem;position:relative;overflow:hidden}
    .vf-header::before{content:'';position:absolute;inset:0;background:radial-gradient(circle at 80% 25%,rgba(6,182,212,.22),transparent 55%),radial-gradient(circle at 10% 75%,rgba(255,255,255,.05),transparent 45%);pointer-events:none}
    .vf-header h1{font-weight:800;font-size:1.85rem;letter-spacing:-.06em;margin:0}
    .vf-header .subtitle{opacity:.7;font-size:.92rem;margin-top:.35rem}
    .vf-back{display:inline-flex;align-items:center;gap:.4rem;color:rgba(255,255,255,.7);text-decoration:none;font-size:.85rem;font-weight:500;margin-bottom:.75rem;transition:color .2s}
    .vf-back:hover{color:#fff}
    .vf-body{margin-top:-3.5rem;position:relative;z-index:2;padding-bottom:3rem}

    /* Form card */
    .vf-card{background:#fff;border-radius:1.15rem;box-shadow:0 4px 24px rgba(15,23,42,.07);border:1px solid rgba(203,213,225,.45);overflow:hidden}
    .vf-card-body{padding:2rem}

    /* Form fields */
    .vf-group{margin-bottom:1.35rem}
    .vf-label{display:block;font-size:.82rem;font-weight:700;color:#334155;margin-bottom:.4rem;letter-spacing:.02em}
    .vf-label .required{color:#ef4444;margin-left:.15rem}
    .vf-hint{font-size:.76rem;color:#94a3b8;margin-top:.3rem}
    .vf-control{display:block;width:100%;padding:.65rem .9rem;border:1.5px solid rgba(203,213,225,.8);border-radius:.75rem;font-size:.88rem;color:#0f172a;background:#fff;transition:all .2s}
    .vf-control:focus{outline:none;border-color:var(--color-secondary);box-shadow:0 0 0 3px rgba(6,182,212,.12)}
    .vf-control.is-invalid{border-color:#ef4444}
    .vf-control.is-invalid:focus{box-shadow:0 0 0 3px rgba(239,68,68,.1)}
    select.vf-control{appearance:auto}
    textarea.vf-control{resize:vertical;min-height:100px}

    /* Image upload */
    .vf-current-img{display:inline-block;position:relative;margin-bottom:.75rem}
    .vf-current-img img{max-width:180px;border-radius:.65rem;box-shadow:0 2px 8px rgba(0,0,0,.08)}
    .vf-current-img .label{font-size:.76rem;color:#94a3b8;margin-top:.35rem}
    .vf-upload-zone{border:2px dashed rgba(203,213,225,.7);border-radius:.85rem;padding:1.5rem;text-align:center;transition:all .2s;cursor:pointer;background:rgba(248,250,252,.5)}
    .vf-upload-zone:hover{border-color:var(--color-secondary);background:rgba(6,182,212,.03)}
    .vf-upload-zone i{font-size:1.8rem;color:#94a3b8;display:block;margin-bottom:.5rem}
    .vf-upload-zone span{font-size:.84rem;color:#64748b}
    .vf-preview{margin-top:.75rem;display:none}
    .vf-preview img{max-width:180px;border-radius:.65rem;box-shadow:0 2px 8px rgba(0,0,0,.08)}

    /* Row grid */
    .vf-shell{width:100%;max-width:1120px;margin:0 auto}
    .vf-row{display:grid;grid-template-columns:1fr 1fr;gap:0 1.25rem}
    @media(max-width:1199.98px){.vf-row{grid-template-columns:1fr}}

    /* Status display */
    .vf-status-bar{display:flex;align-items:center;justify-content:space-between;padding:.75rem 1rem;border-radius:.75rem;background:rgba(248,250,252,.8);border:1.5px solid rgba(203,213,225,.5)}
    .vf-status-bar .label{font-size:.88rem;color:#475569}
    .vf-status-badge{display:inline-flex;align-items:center;gap:.3rem;padding:.3rem .75rem;border-radius:2rem;font-size:.78rem;font-weight:600}
    .vf-status-badge.available{background:rgba(16,185,129,.1);color:#059669}
    .vf-status-badge.rented{background:rgba(245,158,11,.1);color:#d97706}
    .vf-status-badge.maintenance{background:rgba(100,116,139,.12);color:#475569}

    /* Buttons */
    .vf-submit{display:flex;align-items:center;justify-content:center;gap:.5rem;width:100%;padding:.75rem;border:none;border-radius:.85rem;background:var(--gradient-brand);color:#fff;font-weight:700;font-size:.92rem;cursor:pointer;transition:all .25s}
    .vf-submit:hover{opacity:.92;transform:translateY(-1px);box-shadow:0 8px 24px rgba(15,23,42,.15)}
    .vf-cancel{display:flex;align-items:center;justify-content:center;gap:.5rem;width:100%;padding:.7rem;border:1.5px solid rgba(203,213,225,.8);border-radius:.85rem;background:#fff;color:#475569;font-weight:600;font-size:.88rem;text-decoration:none;transition:all .2s;margin-top:.65rem}
    .vf-cancel:hover{border-color:#94a3b8;color:#0f172a}

    /* Alert */
    .vf-alert{border-radius:.85rem;border:none;padding:1rem 1.25rem;margin-bottom:1.5rem;font-size:.88rem}
    .vf-alert.danger{background:rgba(239,68,68,.06);color:#dc2626;border-left:3px solid #ef4444}
    .vf-alert ul{margin:0;padding-left:1.2rem}
    .vf-alert li{margin-bottom:.2rem}

    @media(max-width:767px){
        .vf-header{padding:2rem 0 4.5rem}
        .vf-header h1{font-size:1.4rem}
        .vf-body{margin-top:-2.5rem}
        .vf-card-body{padding:1.25rem}
    }
</style>

{{-- ── Header ── --}}
<div class="vf-header">
    <div class="container">
        <div class="vf-shell">
            <a href="{{ route('admin.vehicles.index') }}" class="vf-back"><i class="bi bi-arrow-left"></i> Kembali ke Daftar</a>
            <h1><i class="bi bi-pencil-square me-2" style="font-size:1.5rem"></i>Edit Kendaraan</h1>
            <p class="subtitle mb-0">{{ $vehicle->name }} — {{ $vehicle->plat_number }}</p>
        </div>
    </div>
</div>

{{-- ── Body ── --}}
<div class="vf-body">
    <div class="container">
        <div class="vf-shell">
            <div class="vf-card">
                <div class="vf-card-body">
                    @if($errors->any())
                        <div class="vf-alert danger">
                            <ul>
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.vehicles.update', $vehicle) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        {{-- Image upload --}}
                        <div class="vf-group">
                            <label class="vf-label">Foto Kendaraan</label>
                            @if($vehicle->image)
                                <div class="vf-current-img">
                                    <img src="{{ Storage::url($vehicle->image) }}" alt="{{ $vehicle->name }}">
                                    <div class="label">Foto saat ini</div>
                                </div>
                            @endif
                            <label class="vf-upload-zone" for="image">
                                <i class="bi bi-cloud-arrow-up"></i>
                                <span>{{ $vehicle->image ? 'Upload foto baru untuk mengganti' : 'Klik untuk upload foto' }} — JPG, PNG, WEBP maks 5MB</span>
                            </label>
                            <input type="file" class="d-none @error('image') is-invalid @enderror" id="image" name="image" accept="image/*">
                            @error('image')<div class="text-danger" style="font-size:.8rem;margin-top:.35rem">{{ $message }}</div>@enderror
                            <div class="vf-preview" id="imagePreview">
                                <img src="" alt="Preview">
                            </div>
                        </div>

                        {{-- Name + Type --}}
                        <div class="vf-row">
                            <div class="vf-group">
                                <label for="name" class="vf-label">Nama Kendaraan <span class="required">*</span></label>
                                <input type="text" class="vf-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $vehicle->name) }}" required>
                                @error('name')<div class="text-danger" style="font-size:.8rem;margin-top:.35rem">{{ $message }}</div>@enderror
                            </div>
                            <div class="vf-group">
                                <label for="vehicle_type" class="vf-label">Tipe Kendaraan <span class="required">*</span></label>
                                <select class="vf-control @error('vehicle_type') is-invalid @enderror" id="vehicle_type" name="vehicle_type" required>
                                    <option value="mobil" {{ old('vehicle_type', $vehicle->vehicle_type) === 'mobil' ? 'selected' : '' }}>🚗 Mobil</option>
                                    <option value="motor" {{ old('vehicle_type', $vehicle->vehicle_type) === 'motor' ? 'selected' : '' }}>🏍️ Motor</option>
                                </select>
                                @error('vehicle_type')<div class="text-danger" style="font-size:.8rem;margin-top:.35rem">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        {{-- Plat + Transmisi --}}
                        <div class="vf-row">
                            <div class="vf-group">
                                <label for="plat_number" class="vf-label">Nomor Plat <span class="required">*</span></label>
                                <input type="text" class="vf-control @error('plat_number') is-invalid @enderror" id="plat_number" name="plat_number" value="{{ old('plat_number', $vehicle->plat_number) }}" required>
                                @error('plat_number')<div class="text-danger" style="font-size:.8rem;margin-top:.35rem">{{ $message }}</div>@enderror
                            </div>
                            <div class="vf-group">
                                <label for="transmission" class="vf-label">Transmisi <span class="required">*</span></label>
                                <select class="vf-control @error('transmission') is-invalid @enderror" id="transmission" name="transmission" required>
                                    <option value="Manual" {{ old('transmission', $vehicle->transmission) === 'Manual' ? 'selected' : '' }}>Manual</option>
                                    <option value="Otomatis" {{ old('transmission', $vehicle->transmission) === 'Otomatis' ? 'selected' : '' }}>Otomatis</option>
                                </select>
                                @error('transmission')<div class="text-danger" style="font-size:.8rem;margin-top:.35rem">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        {{-- Tahun + Harga --}}
                        <div class="vf-row">
                            <div class="vf-group">
                                <label for="year" class="vf-label">Tahun <span class="required">*</span></label>
                                <input type="number" class="vf-control @error('year') is-invalid @enderror" id="year" name="year" value="{{ old('year', $vehicle->year) }}" required>
                                @error('year')<div class="text-danger" style="font-size:.8rem;margin-top:.35rem">{{ $message }}</div>@enderror
                            </div>
                            <div class="vf-group">
                                <label for="daily_price" class="vf-label">Harga Dasar per Hari (Rp) <span class="required">*</span></label>
                                <input type="number" class="vf-control @error('daily_price') is-invalid @enderror" id="daily_price" name="daily_price" value="{{ old('daily_price', $vehicle->daily_price) }}" step="0.01" required>
                                @error('daily_price')<div class="text-danger" style="font-size:.8rem;margin-top:.35rem">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="vf-row">
                            <div class="vf-group">
                                <label for="weekend_multiplier" class="vf-label">Multiplier Weekend</label>
                                <input type="number" class="vf-control @error('weekend_multiplier') is-invalid @enderror" id="weekend_multiplier" name="weekend_multiplier" value="{{ old('weekend_multiplier', $vehicle->weekend_multiplier ?? '1.20') }}" step="0.01" min="1">
                                <div class="vf-hint">Digunakan untuk Jumat-Minggu.</div>
                                @error('weekend_multiplier')<div class="text-danger" style="font-size:.8rem;margin-top:.35rem">{{ $message }}</div>@enderror
                            </div>
                            <div class="vf-group">
                                <label for="peak_season_multiplier" class="vf-label">Multiplier Peak Season</label>
                                <input type="number" class="vf-control @error('peak_season_multiplier') is-invalid @enderror" id="peak_season_multiplier" name="peak_season_multiplier" value="{{ old('peak_season_multiplier', $vehicle->peak_season_multiplier ?? '1.40') }}" step="0.01" min="1">
                                <div class="vf-hint">Default untuk Desember dan Januari.</div>
                                @error('peak_season_multiplier')<div class="text-danger" style="font-size:.8rem;margin-top:.35rem">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="vf-row">
                            <div class="vf-group">
                                <label for="low_season_multiplier" class="vf-label">Multiplier Low Season</label>
                                <input type="number" class="vf-control @error('low_season_multiplier') is-invalid @enderror" id="low_season_multiplier" name="low_season_multiplier" value="{{ old('low_season_multiplier', $vehicle->low_season_multiplier ?? '0.80') }}" step="0.01" min="0.10">
                                <div class="vf-hint">Default untuk Juli dan Agustus.</div>
                                @error('low_season_multiplier')<div class="text-danger" style="font-size:.8rem;margin-top:.35rem">{{ $message }}</div>@enderror
                            </div>
                            <div class="vf-group">
                                <label class="vf-label">Ringkasan Pricing</label>
                                <div class="vf-hint">Aturan promo tambahan tetap dikelola dari Kalender Armada agar harga harian dan availability selalu sinkron.</div>
                            </div>
                        </div>

                        {{-- Status --}}
                        <div class="vf-group">
                            <label class="vf-label">Status Operasional</label>
                            @php($displayStatus = $vehicle->current_rental_status)
                            <div class="vf-status-bar">
                                <span class="label">
                                    {{ match ($displayStatus) {
                                        'available' => 'Tersedia',
                                        'rented' => 'Sedang Disewa',
                                        default => 'Maintenance',
                                    } }}
                                </span>
                                <span class="vf-status-badge {{ $displayStatus }}">
                                    <i class="bi bi-{{ $displayStatus === 'available' ? 'check-circle-fill' : ($displayStatus === 'rented' ? 'clock-fill' : 'wrench') }}" style="font-size:.7rem"></i>
                                    {{ ucfirst($displayStatus) }}
                                </span>
                            </div>
                            <div class="vf-hint">Status tersedia/disewa mengikuti booking aktif secara otomatis. Untuk maintenance, gunakan tombol di halaman kelola kendaraan.</div>
                        </div>

                        {{-- Deskripsi --}}
                        <div class="vf-group">
                            <label for="description" class="vf-label">Deskripsi</label>
                            <textarea class="vf-control @error('description') is-invalid @enderror" id="description" name="description" rows="4">{{ old('description', $vehicle->description) }}</textarea>
                            @error('description')<div class="text-danger" style="font-size:.8rem;margin-top:.35rem">{{ $message }}</div>@enderror
                        </div>

                        <button type="submit" class="vf-submit"><i class="bi bi-check-lg"></i> Simpan Perubahan</button>
                        <a href="{{ route('admin.vehicles.index') }}" class="vf-cancel"><i class="bi bi-x-lg"></i> Batal</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@section('js')
<script>
    document.getElementById('image').addEventListener('change', function(e) {
        const preview = document.getElementById('imagePreview');
        const img = preview.querySelector('img');
        if (e.target.files && e.target.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                img.src = e.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(e.target.files[0]);
        }
    });
</script>
@endsection
@endsection
