<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #fff;
            padding: 24px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #0ea5e9;
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
            background-color: #0ea5e9;
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
            color: #6b7280;
        }
        .detail-value {
            color: #111827;
            text-align: right;
        }
        .notice {
            background-color: #ecfeff;
            border-left: 4px solid #06b6d4;
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
            color: #6b7280;
            font-size: 12px;
            margin-top: 24px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Booking Anda Sudah Aktif</h1>
            <div class="badge">ANTREAN DIAKTIFKAN</div>
        </div>

        <p>Halo {{ $booking->user->name }},</p>
        <p>Booking Anda yang sebelumnya berada di antrean sekarang sudah aktif. Kendaraan sudah tersedia dan jadwal booking Anda telah kami sesuaikan otomatis.</p>

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
                <span class="detail-label">Mulai Sewa</span>
                <span class="detail-value">{{ $booking->start_date->format('d M Y') }} {{ $booking->pickup_time_label }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Selesai Sewa</span>
                <span class="detail-value">{{ $booking->end_date->format('d M Y') }} {{ $booking->return_time_label }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Durasi</span>
                <span class="detail-value">{{ $booking->duration_days }} hari</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Status</span>
                <span class="detail-value"><strong style="color: #10b981;">AKTIF</strong></span>
            </div>
        </div>

        <div class="notice">
            <strong>Catatan:</strong>
            <div>Silakan cek detail booking Anda untuk memastikan jadwal terbaru dan koordinasi pengambilan kendaraan dengan admin.</div>
        </div>

        <a href="{{ url('/bookings/' . $booking->id) }}" class="button">Lihat Detail Booking</a>

        <div class="footer">
            <p>© {{ now()->year }} RentalHub. Email ini dikirim otomatis.</p>
        </div>
    </div>
</body>
</html>