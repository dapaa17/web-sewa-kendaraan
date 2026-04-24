<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $title }} - {{ $monthLabel }}</title>
    <style>
        @page {
            margin: 18px 22px;
        }

        body {
            margin: 0;
            color: #0f172a;
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }

        .heading {
            margin-bottom: 12px;
        }

        .title {
            margin: 0;
            font-size: 18px;
            font-weight: 700;
        }

        .subtitle {
            margin: 4px 0 0;
            color: #475569;
            font-size: 11px;
        }

        .period {
            margin: 8px 0 0;
            font-size: 11px;
            color: #334155;
        }

        .summary {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0 12px;
        }

        .summary td {
            width: 33.33%;
            border: 1px solid #dbe3ef;
            background: #f8fafc;
            padding: 8px;
            vertical-align: top;
        }

        .summary .label {
            display: block;
            color: #64748b;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            margin-bottom: 4px;
        }

        .summary .value {
            font-size: 14px;
            font-weight: 700;
            color: #0f172a;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th,
        .table td {
            border: 1px solid #dbe3ef;
            padding: 6px 7px;
            vertical-align: top;
        }

        .table th {
            background: #f1f5f9;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            font-size: 10px;
            text-align: left;
        }

        .text-right {
            text-align: right;
            white-space: nowrap;
        }

        .muted {
            color: #64748b;
            font-size: 10px;
        }
    </style>
</head>
<body>
    @php
        $totalTransactions = $bookings->count();
        $totalRevenue = (float) $bookings->sum('total_price');
        $totalFees = (float) $bookings->sum(function ($booking) {
            return abs((float) ($booking->late_fee ?? 0)) + abs((float) ($booking->return_damage_fee ?? 0));
        });
    @endphp

    <div class="heading">
        <h1 class="title">{{ $title }}</h1>
        <p class="subtitle">{{ $subtitle }}</p>
        <p class="period">Periode: {{ $monthLabel }}</p>
    </div>

    <table class="summary">
        <tr>
            <td>
                <span class="label">Total Transaksi</span>
                <span class="value">{{ $totalTransactions }}</span>
            </td>
            <td>
                <span class="label">Total Pendapatan</span>
                <span class="value">Rp{{ number_format($totalRevenue, 0, ',', '.') }}</span>
            </td>
            <td>
                <span class="label">Total Denda</span>
                <span class="value">Rp{{ number_format($totalFees, 0, ',', '.') }}</span>
            </td>
        </tr>
    </table>

    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tanggal Selesai</th>
                @if($showCustomerIdentity)
                    <th>Customer</th>
                @endif
                <th>Kendaraan</th>
                <th>Durasi</th>
                <th class="text-right">Total Harga</th>
                <th class="text-right">Denda</th>
            </tr>
        </thead>
        <tbody>
            @forelse($bookings as $booking)
                @php
                    $totalFee = abs((float) ($booking->late_fee ?? 0)) + abs((float) ($booking->return_damage_fee ?? 0));
                @endphp
                <tr>
                    <td>#{{ $booking->id }}</td>
                    <td>{{ optional($booking->updated_at)->format('d M Y H:i') }}</td>
                    @if($showCustomerIdentity)
                        <td>
                            <strong>{{ $booking->user->name ?? '-' }}</strong><br>
                            <span class="muted">{{ $booking->user->email ?? '-' }}</span>
                        </td>
                    @endif
                    <td>
                        <strong>{{ $booking->vehicle->name ?? '-' }}</strong><br>
                        <span class="muted">{{ $booking->vehicle->plat_number ?? '-' }}</span>
                    </td>
                    <td>{{ (int) $booking->duration_days }} hari</td>
                    <td class="text-right">Rp{{ number_format((float) $booking->total_price, 0, ',', '.') }}</td>
                    <td class="text-right">Rp{{ number_format($totalFee, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ $showCustomerIdentity ? 7 : 6 }}" style="text-align:center;color:#64748b;">
                        Tidak ada transaksi untuk periode ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
