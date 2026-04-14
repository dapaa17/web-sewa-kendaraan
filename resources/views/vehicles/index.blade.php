@extends('layouts.admin')

@section('title', 'Kelola Kendaraan')
@section('page-title', 'Kendaraan')

@section('content')
<style>
    .vh-header{background:var(--gradient-brand);color:#fff;padding:3rem 0 5.5rem;position:relative;overflow:hidden}
    .vh-header::before{content:'';position:absolute;inset:0;background:radial-gradient(circle at 80% 25%,rgba(6,182,212,.22),transparent 55%),radial-gradient(circle at 10% 75%,rgba(255,255,255,.05),transparent 45%);pointer-events:none}
    .vh-header h1{font-weight:800;font-size:1.85rem;letter-spacing:-.06em;margin:0}
    .vh-header .subtitle{opacity:.7;font-size:.92rem;margin-top:.35rem}
    .vh-cnt-chip{background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.16);border-radius:2rem;padding:.4rem 1rem;font-size:.82rem;backdrop-filter:blur(6px);white-space:nowrap}
    .vh-body{margin-top:-3.5rem;position:relative;z-index:2;padding-bottom:3rem}
    .vh-add-btn{display:inline-flex;align-items:center;gap:.5rem;background:rgba(255,255,255,.14);border:1px solid rgba(255,255,255,.22);color:#fff;padding:.6rem 1.2rem;border-radius:.85rem;font-weight:600;font-size:.88rem;text-decoration:none;transition:all .25s}
    .vh-add-btn:hover{background:rgba(255,255,255,.22);color:#fff;transform:translateY(-1px)}
    .vh-filter-panel{margin-bottom:1.25rem;padding:1rem;border-radius:1.15rem;background:linear-gradient(135deg,rgba(255,255,255,.98) 0%,rgba(248,250,252,.98) 100%);border:1px solid rgba(203,213,225,.75);box-shadow:var(--shadow-soft)}
    .vh-filter-header{display:flex;justify-content:space-between;align-items:flex-start;gap:1rem;flex-wrap:wrap;margin-bottom:1rem}
    .vh-filter-kicker{display:inline-flex;align-items:center;gap:.4rem;font-size:.74rem;font-weight:700;text-transform:uppercase;letter-spacing:.12em;color:#0f766e;margin-bottom:.35rem}
    .vh-filter-header h2{margin:0;font-size:1.05rem;font-weight:700;color:#0f172a}
    .vh-filter-header p{margin:.28rem 0 0;color:#64748b;line-height:1.6}
    .vh-filter-meta{display:flex;align-items:center;gap:.55rem;flex-wrap:wrap}
    .vh-filter-chip{display:inline-flex;align-items:center;gap:.4rem;padding:.55rem .85rem;border-radius:999px;background:#fff;border:1px solid rgba(203,213,225,.78);color:#334155;font-size:.82rem;font-weight:700;box-shadow:0 10px 20px rgba(15,23,42,.05)}
    .vh-filter-chip.muted{background:rgba(6,182,212,.08);border-color:rgba(6,182,212,.16);color:#0e7490}
    .vh-filter-grid{display:grid;grid-template-columns:minmax(0,1.6fr) repeat(3,minmax(0,1fr));gap:.85rem;align-items:end}
    .vh-field label{display:block;font-size:.82rem;font-weight:700;color:#0f172a;margin-bottom:.4rem}
    .vh-field .form-control,.vh-field .form-select{min-height:48px;border-radius:.9rem;border:1px solid rgba(203,213,225,.92)}
    .vh-field .form-control::placeholder{color:#94a3b8}
    .vh-filter-actions{display:flex;justify-content:flex-end;gap:.65rem;grid-column:1 / -1;flex-wrap:wrap}
    .vh-filter-actions .btn,.vh-filter-reset{min-height:48px;border-radius:999px;padding-inline:1rem;font-weight:600}
    .vh-filter-reset{display:inline-flex;align-items:center;gap:.45rem;border:1px solid rgba(203,213,225,.95);background:#fff;color:#334155;text-decoration:none}
    .vh-filter-reset:hover{color:var(--color-primary);border-color:rgba(var(--color-secondary-rgb),.35)}

    /* Table wrapper */
    .vh-tbl-wrap{background:transparent;border-radius:1.15rem;overflow-x:auto}
    .vh-tbl{width:100%;border-collapse:separate;border-spacing:0 .65rem;min-width:860px}
    .vh-tbl thead th{font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.12em;color:#64748b;padding:.5rem 1rem;border:none;white-space:nowrap}
    .vh-tbl tbody tr{background:#fff;border-radius:1rem;box-shadow:0 2px 12px rgba(15,23,42,.06);transition:all .2s ease}
    .vh-tbl tbody tr:hover{box-shadow:0 6px 24px rgba(15,23,42,.1);transform:translateY(-1px)}
    .vh-tbl tbody td{padding:.9rem 1rem;border:none;vertical-align:middle;font-size:.88rem;color:#334155}
    .vh-tbl tbody td:first-child{border-radius:1rem 0 0 1rem}
    .vh-tbl tbody td:last-child{border-radius:0 1rem 1rem 0}

    /* Vehicle info cell */
    .vh-info{display:flex;align-items:center;gap:.75rem}
    .vh-thumb{width:52px;height:52px;border-radius:.65rem;object-fit:cover;background:#f1f5f9;flex-shrink:0}
    .vh-thumb-placeholder{width:52px;height:52px;border-radius:.65rem;background:linear-gradient(135deg,#f1f5f9,#e2e8f0);display:flex;align-items:center;justify-content:center;font-size:1.3rem;flex-shrink:0}
    .vh-name{font-weight:700;color:#0f172a;font-size:.9rem;line-height:1.3}
    .vh-plat{font-size:.78rem;color:#94a3b8;font-weight:500}

    /* Badges */
    .vh-badge{display:inline-flex;align-items:center;gap:.3rem;padding:.3rem .7rem;border-radius:2rem;font-size:.75rem;font-weight:600;white-space:nowrap}
    .vh-badge.available{background:rgba(16,185,129,.1);color:#059669}
    .vh-badge.rented{background:rgba(245,158,11,.1);color:#d97706}
    .vh-badge.maintenance{background:rgba(100,116,139,.12);color:#475569}
    .vh-type-chip{display:inline-flex;align-items:center;gap:.3rem;padding:.25rem .65rem;border-radius:2rem;font-size:.78rem;font-weight:600;background:rgba(6,182,212,.08);color:#0e7490}

    /* Action buttons */
    .vh-actions{display:flex;align-items:center;gap:.4rem;flex-wrap:nowrap}
    .vh-act{display:inline-flex;align-items:center;gap:.3rem;padding:.38rem .7rem;border-radius:.6rem;font-size:.78rem;font-weight:600;border:none;cursor:pointer;transition:all .2s;text-decoration:none;white-space:nowrap}
    .vh-act.edit{background:rgba(6,182,212,.1);color:#0e7490}
    .vh-act.edit:hover{background:rgba(6,182,212,.2);color:#0e7490}
    .vh-act.activate{background:rgba(16,185,129,.1);color:#059669}
    .vh-act.activate:hover{background:rgba(16,185,129,.2)}
    .vh-act.maint{background:rgba(100,116,139,.1);color:#475569}
    .vh-act.maint:hover{background:rgba(100,116,139,.18)}
    .vh-act.delete{background:rgba(239,68,68,.08);color:#dc2626}
    .vh-act.delete:hover{background:rgba(239,68,68,.16)}

    /* Mobile cards */
    .vh-mobile-list{display:none}
    .vh-mobile-card{background:#fff;border:1px solid rgba(203,213,225,.72);border-radius:1rem;padding:.9rem;box-shadow:var(--shadow-soft)}
    .vh-mobile-head{display:flex;justify-content:space-between;align-items:flex-start;gap:.65rem;margin-bottom:.7rem}
    .vh-mobile-meta{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:.55rem;margin-bottom:.75rem}
    .vh-mobile-meta-item{padding:.55rem .62rem;border-radius:.72rem;background:#f8fafc;border:1px solid rgba(226,232,240,.9)}
    .vh-mobile-meta-item .label{display:block;color:#64748b;font-size:.68rem;text-transform:uppercase;letter-spacing:.06em;margin-bottom:.15rem}
    .vh-mobile-meta-item .value{display:block;color:#0f172a;font-size:.82rem;font-weight:600;line-height:1.35}

    /* Empty state */
    .vh-empty{text-align:center;padding:4rem 1rem;background:#fff;border-radius:1.15rem;box-shadow:0 2px 12px rgba(15,23,42,.05)}
    .vh-empty-icon{font-size:3rem;margin-bottom:1rem;opacity:.4}
    .vh-empty p{color:#64748b;margin-bottom:1.25rem}
    .vh-empty a{display:inline-flex;align-items:center;gap:.4rem;background:var(--gradient-brand);color:#fff;padding:.6rem 1.4rem;border-radius:.85rem;font-weight:600;text-decoration:none;font-size:.9rem;transition:all .25s}
    .vh-empty a:hover{opacity:.9;transform:translateY(-1px);color:#fff}

    /* Pagination */
    .vh-pagination{margin-top:.5rem}
    .vh-pagination .pagination{justify-content:center;gap:.3rem}
    .vh-pagination .page-link{border-radius:.6rem;border:1px solid rgba(203,213,225,.6);font-size:.84rem;padding:.4rem .8rem;color:#334155;font-weight:500}
    .vh-pagination .page-item.active .page-link{background:var(--color-secondary);border-color:var(--color-secondary);color:#fff}

    @media(max-width:1199.98px){
        .vh-filter-grid{grid-template-columns:repeat(2,minmax(0,1fr))}
        .vh-filter-actions{justify-content:flex-start}
    }

    @media(max-width:767px){
        .vh-header{padding:2rem 0 4.5rem}
        .vh-header h1{font-size:1.4rem}
        .vh-header .subtitle{font-size:.84rem;line-height:1.55}
        .vh-body{margin-top:-2.5rem}
        .vh-body .container{padding-inline:1rem}
        .vh-add-btn{width:100%;justify-content:center}
        .vh-filter-panel{padding:.9rem;border-radius:1rem}
        .vh-filter-header{margin-bottom:.8rem}
        .vh-filter-header h2{font-size:.98rem}
        .vh-filter-header p{font-size:.84rem;line-height:1.55}
        .vh-filter-chip{font-size:.76rem;padding:.48rem .7rem}
        .vh-filter-grid{grid-template-columns:1fr}
        .vh-filter-actions{flex-direction:column}
        .vh-filter-actions .btn,.vh-filter-reset{width:100%;justify-content:center}
        .vh-actions{flex-wrap:wrap}
        .vh-mobile-list{display:grid;gap:.7rem}
        .vh-mobile-head .vh-badge{font-size:.7rem;padding:.28rem .58rem}
        .vh-mobile-list .vh-actions{display:grid;grid-template-columns:1fr;gap:.45rem}
        .vh-mobile-list .vh-actions form{width:100%}
        .vh-mobile-list .vh-act{width:100%;justify-content:center;min-height:42px}
    }

    @media(max-width:420px){
        .vh-mobile-meta{grid-template-columns:1fr}
        .vh-header h1{font-size:1.28rem}
        .vh-cnt-chip{width:100%;justify-content:center}
    }
</style>

@php($activeVehicleFilterCount = collect($filters ?? [])->filter(fn ($value) => filled($value))->count())

{{-- ── Header ── --}}
<div class="vh-header">
    <div class="container">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div>
                <h1><i class="bi bi-truck me-2" style="font-size:1.5rem"></i>Kelola Kendaraan</h1>
                <p class="subtitle mb-0">Kelola semua armada kendaraan rental Anda</p>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="vh-cnt-chip"><i class="bi bi-box-seam me-1"></i>{{ $vehicles->total() }} {{ $activeVehicleFilterCount ? 'hasil' : 'kendaraan' }}</span>
                <a href="{{ route('admin.vehicles.create') }}" class="vh-add-btn">
                    <i class="bi bi-plus-lg"></i> Tambah Baru
                </a>
            </div>
        </div>
    </div>
</div>

{{-- ── Body ── --}}
<div class="vh-body">
    <div class="container">
        <div class="vh-filter-panel">
            <div class="vh-filter-header">
                <div>
                    <span class="vh-filter-kicker"><i class="bi bi-funnel"></i>Filter Admin</span>
                    <h2>Cari dan rapikan armada</h2>
                    <p class="mb-0">Gunakan search, tipe, transmisi, dan status operasional supaya daftar kendaraan lebih cepat dibaca.</p>
                </div>
                <div class="vh-filter-meta">
                    <span class="vh-filter-chip"><i class="bi bi-list-ul"></i>{{ $vehicles->total() }} hasil</span>
                    @if($activeVehicleFilterCount)
                        <span class="vh-filter-chip muted"><i class="bi bi-sliders"></i>{{ $activeVehicleFilterCount }} filter aktif</span>
                    @endif
                </div>
            </div>

            <form method="GET" action="{{ route('admin.vehicles.index') }}" class="vh-filter-grid">
                <div class="vh-field">
                    <label for="vehicle-search">Cari kendaraan atau plat</label>
                    <input type="text" id="vehicle-search" name="search" class="form-control" value="{{ $filters['search'] ?? '' }}" placeholder="Contoh: Avanza atau B 1234 XYZ">
                </div>
                <div class="vh-field">
                    <label for="vehicle-type">Tipe kendaraan</label>
                    <select id="vehicle-type" name="vehicle_type" class="form-select">
                        <option value="">Semua tipe</option>
                        <option value="mobil" @selected(($filters['vehicleType'] ?? '') === 'mobil')>Mobil</option>
                        <option value="motor" @selected(($filters['vehicleType'] ?? '') === 'motor')>Motor</option>
                    </select>
                </div>
                <div class="vh-field">
                    <label for="vehicle-transmission">Transmisi</label>
                    <select id="vehicle-transmission" name="transmission" class="form-select">
                        <option value="">Semua transmisi</option>
                        <option value="Manual" @selected(($filters['transmission'] ?? '') === 'Manual')>Manual</option>
                        <option value="Otomatis" @selected(($filters['transmission'] ?? '') === 'Otomatis')>Otomatis</option>
                    </select>
                </div>
                <div class="vh-field">
                    <label for="vehicle-status">Status operasional</label>
                    <select id="vehicle-status" name="status" class="form-select">
                        <option value="">Semua status</option>
                        <option value="available" @selected(($filters['status'] ?? '') === 'available')>Tersedia</option>
                        <option value="rented" @selected(($filters['status'] ?? '') === 'rented')>Sedang disewa</option>
                        <option value="maintenance" @selected(($filters['status'] ?? '') === 'maintenance')>Maintenance</option>
                    </select>
                </div>
                <div class="vh-filter-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-funnel me-2"></i>Terapkan
                    </button>
                    <a href="{{ route('admin.vehicles.index') }}" class="vh-filter-reset">
                        <i class="bi bi-arrow-counterclockwise"></i>Reset
                    </a>
                </div>
            </form>
        </div>

        @if($vehicles->isEmpty())
            <div class="vh-empty">
                <div class="vh-empty-icon">{{ $activeVehicleFilterCount ? '🔎' : '🚗' }}</div>
                @if($activeVehicleFilterCount)
                    <h5 style="font-weight:700;color:#0f172a">Tidak Ada Kendaraan yang Cocok</h5>
                    <p>Coba ubah kata kunci atau filter supaya hasil armada yang tampil lebih luas.</p>
                    <a href="{{ route('admin.vehicles.index') }}"><i class="bi bi-arrow-counterclockwise"></i> Reset Filter</a>
                @else
                    <h5 style="font-weight:700;color:#0f172a">Belum Ada Kendaraan</h5>
                    <p>Mulai tambahkan kendaraan pertama Anda ke dalam armada.</p>
                    <a href="{{ route('admin.vehicles.create') }}"><i class="bi bi-plus-lg"></i> Tambah Kendaraan</a>
                @endif
            </div>
        @else
            <div class="vh-tbl-wrap d-none d-md-block">
                <table class="vh-tbl">
                    <thead>
                        <tr>
                            <th style="padding-left:1.25rem">Kendaraan</th>
                            <th>Tipe</th>
                            <th>Transmisi</th>
                            <th>Tahun</th>
                            <th>Harga/Hari</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($vehicles as $vehicle)
                            @php($displayStatus = $vehicle->current_rental_status)
                            <tr>
                                <td>
                                    <div class="vh-info">
                                        @if($vehicle->display_image_url)
                                            <img src="{{ $vehicle->display_image_url }}" alt="{{ $vehicle->name }}" class="vh-thumb">
                                        @else
                                            <div class="vh-thumb-placeholder">{{ $vehicle->getTypeIcon() }}</div>
                                        @endif
                                        <div>
                                            <div class="vh-name">{{ $vehicle->name }}</div>
                                            <div class="vh-plat">{{ $vehicle->plat_number }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="vh-type-chip">{{ $vehicle->getTypeIcon() }} {{ $vehicle->getTypeLabel() }}</span></td>
                                <td>{{ $vehicle->transmission }}</td>
                                <td>{{ $vehicle->year }}</td>
                                <td style="font-weight:600;color:#0f172a">Rp {{ number_format($vehicle->daily_price, 0, ',', '.') }}</td>
                                <td>
                                    <span class="vh-badge {{ $displayStatus }}">
                                        <i class="bi bi-{{ $displayStatus === 'available' ? 'check-circle-fill' : ($displayStatus === 'rented' ? 'clock-fill' : 'wrench') }}" style="font-size:.7rem"></i>
                                        {{ ucfirst($displayStatus) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="vh-actions">
                                        <a href="{{ route('admin.vehicles.edit', $vehicle) }}" class="vh-act edit" title="Edit">
                                            <i class="bi bi-pencil-square"></i> Edit
                                        </a>

                                        <form method="POST" action="{{ route('admin.vehicles.toggle-maintenance', $vehicle) }}" style="display:inline">
                                            @csrf
                                            @if($displayStatus === 'maintenance')
                                                <button type="submit" class="vh-act activate" title="Aktifkan Kembali">
                                                    <i class="bi bi-check-circle"></i> Aktifkan
                                                </button>
                                            @elseif($displayStatus === 'available')
                                                <button type="submit" class="vh-act maint" title="Set Maintenance" onclick="return confirm('Set kendaraan ke maintenance?')">
                                                    <i class="bi bi-tools"></i> Maintenance
                                                </button>
                                            @endif
                                        </form>

                                        <form method="POST" action="{{ route('admin.vehicles.destroy', $vehicle) }}" style="display:inline" onsubmit="return confirm('Hapus kendaraan ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="vh-act delete" title="Hapus">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="vh-mobile-list d-md-none">
                @foreach ($vehicles as $vehicle)
                    @php($displayStatus = $vehicle->current_rental_status)
                    <div class="vh-mobile-card">
                        <div class="vh-mobile-head">
                            <div class="vh-info">
                                @if($vehicle->display_image_url)
                                    <img src="{{ $vehicle->display_image_url }}" alt="{{ $vehicle->name }}" class="vh-thumb">
                                @else
                                    <div class="vh-thumb-placeholder">{{ $vehicle->getTypeIcon() }}</div>
                                @endif
                                <div>
                                    <div class="vh-name">{{ $vehicle->name }}</div>
                                    <div class="vh-plat">{{ $vehicle->plat_number }}</div>
                                </div>
                            </div>
                            <span class="vh-badge {{ $displayStatus }}">{{ ucfirst($displayStatus) }}</span>
                        </div>

                        <div class="vh-mobile-meta">
                            <div class="vh-mobile-meta-item">
                                <span class="label">Tipe</span>
                                <span class="value">{{ $vehicle->getTypeIcon() }} {{ $vehicle->getTypeLabel() }}</span>
                            </div>
                            <div class="vh-mobile-meta-item">
                                <span class="label">Transmisi</span>
                                <span class="value">{{ $vehicle->transmission }}</span>
                            </div>
                            <div class="vh-mobile-meta-item">
                                <span class="label">Tahun</span>
                                <span class="value">{{ $vehicle->year }}</span>
                            </div>
                            <div class="vh-mobile-meta-item">
                                <span class="label">Harga/Hari</span>
                                <span class="value">Rp {{ number_format($vehicle->daily_price, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <div class="vh-actions">
                            <a href="{{ route('admin.vehicles.edit', $vehicle) }}" class="vh-act edit" title="Edit">
                                <i class="bi bi-pencil-square"></i> Edit
                            </a>

                            <form method="POST" action="{{ route('admin.vehicles.toggle-maintenance', $vehicle) }}" style="display:inline">
                                @csrf
                                @if($displayStatus === 'maintenance')
                                    <button type="submit" class="vh-act activate" title="Aktifkan Kembali">
                                        <i class="bi bi-check-circle"></i> Aktifkan
                                    </button>
                                @elseif($displayStatus === 'available')
                                    <button type="submit" class="vh-act maint" title="Set Maintenance" onclick="return confirm('Set kendaraan ke maintenance?')">
                                        <i class="bi bi-tools"></i> Maintenance
                                    </button>
                                @endif
                            </form>

                            <form method="POST" action="{{ route('admin.vehicles.destroy', $vehicle) }}" style="display:inline" onsubmit="return confirm('Hapus kendaraan ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="vh-act delete" title="Hapus">
                                    <i class="bi bi-trash3"></i> Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="vh-pagination">
                {{ $vehicles->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
