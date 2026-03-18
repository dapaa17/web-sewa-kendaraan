<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #334155;
            background-color: #f8fafc;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 4px 18px rgba(15, 23, 42, 0.08);
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #f59e0b;
            padding-bottom: 18px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            color: #0f172a;
            font-size: 28px;
        }
        .badge {
            display: inline-block;
            background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%);
            color: white;
            padding: 8px 16px;
            border-radius: 999px;
            font-weight: 700;
            margin-top: 10px;
        }
        .details {
            background-color: #f8fafc;
            padding: 18px;
            border-radius: 8px;
            margin: 22px 0;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            padding: 10px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            font-weight: 600;
            color: #64748b;
        }
        .detail-value {
            color: #0f172a;
            text-align: right;
        }
        .notice {
            background-color: #fff7ed;
            border-left: 4px solid #f59e0b;
            padding: 14px;
            border-radius: 6px;
            margin: 22px 0;
        }
        .button {
            display: inline-block;
            background-color: #0f172a;
            color: white;
            padding: 12px 22px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            margin-top: 16px;
        }
        .footer {
            text-align: center;
            padding-top: 18px;
            border-top: 1px solid #e5e7eb;
            color: #64748b;
            font-size: 12px;
            margin-top: 24px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Jadwal Booking Anda Diperbarui</h1>
            <div class="badge">JADWAL BARU</div>
        </div>

        <p>Halo {{ $booking->user->name }},</p>
        <p>Admin telah menyesuaikan ulang jadwal booking Anda setelah kendaraan selesai ditangani dari maintenance. Pembayaran Anda tetap aman dan tidak perlu diulang.</p>

        <div class="details">
            <div class="detail-row">
                <span class="detail-label">Nomor Booking</span>
                <span class="detail-value">#{{ $booking->id }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Kendaraan</span>
                <span class="detail-value">{{ $booking->vehicle->name }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Mulai Sewa Baru</span>
                <span class="detail-value">{{ $booking->start_date->format('d M Y') }} {{ $booking->pickup_time_label }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Selesai Sewa Baru</span>
                <span class="detail-value">{{ $booking->end_date->format('d M Y') }} {{ $booking->return_time_label }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Durasi</span>
                <span class="detail-value">{{ $booking->duration_days }} hari</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Status</span>
                <span class="detail-value"><strong style="color: #0ea5e9;">TERJADWAL ULANG</strong></span>
            </div>
        </div>

        <div class="notice">
            <strong>Langkah selanjutnya:</strong>
            <div>Silakan cek detail booking Anda untuk memastikan jadwal terbaru dan koordinasikan serah terima kendaraan dengan admin.</div>
            @if($booking->getMaintenanceRescheduleAdminNote())
                <div style="margin-top: 10px;"><strong>Catatan admin:</strong> {{ $booking->getMaintenanceRescheduleAdminNote() }}</div>
            @endif
        </div>

        <a href="{{ url('/bookings/' . $booking->id) }}" class="button">Lihat Detail Booking</a>

        <div class="footer">
            <p>© {{ now()->year }} RentalHub. Email ini dikirim otomatis.</p>
        </div>
    </div>
</body>
</html>