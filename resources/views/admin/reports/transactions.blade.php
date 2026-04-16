@extends('layouts.admin')

@section('title', 'Laporan Transaksi')
@section('page-title', 'Laporan')

@section('content')
<style>
    .rpt-header{background:var(--gradient-brand);color:#fff;padding:3rem 0 5.5rem;position:relative;overflow:hidden}
    .rpt-header::before{content:'';position:absolute;inset:0;background:radial-gradient(circle at 80% 25%,rgba(6,182,212,.22),transparent 55%);pointer-events:none}
    .rpt-header h1{font-weight:800;font-size:1.85rem;letter-spacing:-.06em;margin:0}
    .rpt-header .subtitle{opacity:.7;font-size:.92rem;margin-top:.35rem}
    .rpt-body{margin-top:-3.5rem;position:relative;z-index:2;padding-bottom:3rem}

    .rpt-filter{margin-bottom:1.25rem;padding:1rem 1.25rem;border-radius:1.15rem;background:linear-gradient(135deg,rgba(255,255,255,.98) 0%,rgba(248,250,252,.98) 100%);border:1px solid rgba(203,213,225,.75);box-shadow:var(--shadow-soft)}
    .rpt-filter form{display:flex;align-items:end;gap:.85rem;flex-wrap:wrap}
    .rpt-field label{display:block;font-size:.82rem;font-weight:700;color:#0f172a;margin-bottom:.4rem}
    .rpt-field .form-select{min-height:44px;border-radius:.9rem;border:1px solid rgba(203,213,225,.92)}

    .rpt-summary{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:.85rem;margin-bottom:1.25rem}
    .rpt-stat{background:#fff;border-radius:1.15rem;padding:1.1rem 1.2rem;border:1px solid rgba(203,213,225,.65);box-shadow:var(--shadow-soft)}
    .rpt-stat .icon{width:2.4rem;height:2.4rem;border-radius:.75rem;display:inline-flex;align-items:center;justify-content:center;margin-bottom:.75rem;font-size:1.1rem}
    .rpt-stat .icon.revenue{background:rgba(16,185,129,.12);color:#059669}
    .rpt-stat .icon.count{background:rgba(6,182,212,.12);color:#0e7490}
    .rpt-stat .icon.avg{background:rgba(245,158,11,.12);color:#d97706}
    .rpt-stat .icon.fee{background:rgba(239,68,68,.1);color:#dc2626}
    .rpt-stat .number{font-size:1.55rem;font-weight:700;color:#0f172a;letter-spacing:-.04em;line-height:1}
    .rpt-stat .label{color:#64748b;font-size:.84rem;margin-top:.3rem}

    .rpt-tbl-wrap{background:transparent;border-radius:1.15rem;overflow-x:auto}
    .rpt-tbl{width:100%;border-collapse:separate;border-spacing:0 .65rem;min-width:820px}
    .rpt-tbl thead th{font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.12em;color:#64748b;padding:.5rem 1rem;border:none;white-space:nowrap}
    .rpt-tbl tbody tr{background:#fff;border-radius:1rem;box-shadow:0 2px 12px rgba(15,23,42,.06);transition:all .2s}
    .rpt-tbl tbody tr:hover{box-shadow:0 6px 24px rgba(15,23,42,.1);transform:translateY(-1px)}
    .rpt-tbl tbody td{padding:.85rem 1rem;border:none;vertical-align:middle;font-size:.88rem;color:#334155}
    .rpt-tbl tbody td:first-child{border-radius:1rem 0 0 1rem}
    .rpt-tbl tbody td:last-child{border-radius:0 1rem 1rem 0}

    .rpt-empty{text-align:center;padding:4rem 1rem;background:#fff;border-radius:1.15rem;box-shadow:0 2px 12px rgba(15,23,42,.05)}
    .rpt-empty-icon{font-size:3rem;margin-bottom:1rem;opacity:.4}
    .rpt-empty p{color:#64748b;margin-bottom:0}

    .rpt-pagination{margin-top:.5rem}
    .rpt-pagination .pagination{justify-content:center;gap:.3rem}
    .rpt-pagination .page-link{border-radius:.6rem;border:1px solid rgba(203,213,225,.6);font-size:.84rem;padding:.4rem .8rem;color:#334155;font-weight:500}
    .rpt-pagination .page-item.active .page-link{background:var(--color-secondary);border-color:var(--color-secondary);color:#fff}

    .rpt-badge{display:inline-flex;align-items:center;gap:.3rem;padding:.28rem .65rem;border-radius:2rem;font-size:.75rem;font-weight:600}
    .rpt-badge.completed{background:rgba(16,185,129,.1);color:#059669}

    @media(max-width:767px){
        .rpt-header{padding:2rem 0 4.5rem}
        .rpt-header h1{font-size:1.4rem}
        .rpt-body{margin-top:-2.5rem}
        .rpt-filter form{flex-direction:column;align-items:stretch}
        .rpt-summary{grid-template-columns:1fr 1fr}
    }
</style>

{{-- Header --}}
<div class="rpt-header">
    <div class="container">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div>
                <h1><i class="bi bi-file-earmark-bar-graph me-2" style="font-size:1.5rem"></i>Laporan Transaksi</h1>
                <p class="subtitle mb-0">Rekap transaksi booking yang telah selesai per bulan</p>
            </div>
            <div>
                <span style="background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.16);border-radius:2rem;padding:.4rem 1rem;font-size:.82rem;backdrop-filter:blur(6px)">
                    <i class="bi bi-calendar3 me-1"></i>{{ $monthLabel }}
                </span>
            </div>
        </div>
    </div>
</div>

{{-- Body --}}
<div class="rpt-body">
    <div class="container">
        {{-- Filter --}}
        <div class="rpt-filter">
            <form method="GET" action="{{ route('admin.reports.transactions') }}">
                <div class="rpt-field">
                    <label for="rpt-month">Bulan</label>
                    <select id="rpt-month" name="month" class="form-select">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" @selected($m === $month)>
                                {{ \Carbon\Carbon::create(null, $m, 1)->translatedFormat('F') }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="rpt-field">
                    <label for="rpt-year">Tahun</label>
                    <select id="rpt-year" name="year" class="form-select">
                        @foreach($availableYears as $y)
                            <option value="{{ $y }}" @selected($y === $year)>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary" style="min-height:44px">
                    <i class="bi bi-funnel me-2"></i>Tampilkan
                </button>
            </form>
        </div>

        {{-- Summary --}}
        <div class="rpt-summary">
            <div class="rpt-stat">
                <div class="icon count"><i class="bi bi-receipt"></i></div>
                <div class="number">{{ $totalTransactions }}</div>
                <div class="label">Total Transaksi</div>
            </div>
            <div class="rpt-stat">
                <div class="icon revenue"><i class="bi bi-cash-stack"></i></div>
                <div class="number">Rp{{ number_format($totalRevenue, 0, ',', '.') }}</div>
                <div class="label">Total Pendapatan</div>
            </div>
            <div class="rpt-stat">
                <div class="icon avg"><i class="bi bi-graph-up"></i></div>
                <div class="number">Rp{{ number_format($avgPerTransaction, 0, ',', '.') }}</div>
                <div class="label">Rata-rata / Transaksi</div>
            </div>
            <div class="rpt-stat">
                <div class="icon fee"><i class="bi bi-exclamation-triangle"></i></div>
                <div class="number">Rp{{ number_format($totalLateFees + $totalDamageFees, 0, ',', '.') }}</div>
                <div class="label">Total Denda & Biaya</div>
            </div>
        </div>

        {{-- Table --}}
        @if($bookings->isEmpty())
            <div class="rpt-empty">
                <div class="rpt-empty-icon">📊</div>
                <h5 style="font-weight:700;color:#0f172a">Belum Ada Transaksi</h5>
                <p>Tidak ada transaksi yang selesai pada {{ $monthLabel }}.</p>
            </div>
        @else
            <div class="rpt-tbl-wrap">
                <table class="rpt-tbl">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Tanggal Selesai</th>
                            <th>Customer</th>
                            <th>Kendaraan</th>
                            <th>Durasi</th>
                            <th>Total Harga</th>
                            <th>Denda</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bookings as $booking)
                            @php
                                $normalizedLateFee = abs((float) ($booking->late_fee ?? 0));
                                $normalizedDamageFee = abs((float) ($booking->return_damage_fee ?? 0));
                                $totalFee = $normalizedLateFee + $normalizedDamageFee;
                            @endphp
                            <tr>
                                <td style="font-weight:600;color:#94a3b8">#{{ $booking->id }}</td>
                                <td>{{ $booking->updated_at->format('d M Y') }}</td>
                                <td>
                                    <div style="font-weight:600;color:#0f172a">{{ $booking->user->name ?? '-' }}</div>
                                    <div style="font-size:.78rem;color:#94a3b8">{{ $booking->user->email ?? '-' }}</div>
                                </td>
                                <td>
                                    <div style="font-weight:600">{{ $booking->vehicle->name ?? '-' }}</div>
                                    <div style="font-size:.78rem;color:#94a3b8">{{ $booking->vehicle->plat_number ?? '-' }}</div>
                                </td>
                                <td>{{ $booking->duration_days }} hari</td>
                                <td style="font-weight:700;color:#0f172a">Rp{{ number_format($booking->total_price, 0, ',', '.') }}</td>
                                <td>
                                    @if($totalFee > 0)
                                        <span style="color:#dc2626;font-weight:600">Rp{{ number_format($totalFee, 0, ',', '.') }}</span>
                                    @else
                                        <span style="color:#94a3b8">-</span>
                                    @endif
                                </td>
                                <td><span class="rpt-badge completed"><i class="bi bi-check-circle-fill" style="font-size:.7rem"></i> Selesai</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="rpt-pagination">
                {{ $bookings->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
