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
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #10b981;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            color: #10b981;
            font-size: 28px;
        }
        .success-badge {
            display: inline-block;
            background-color: #10b981;
            color: white;
            padding: 8px 16px;
            border-radius: 24px;
            font-weight: bold;
            margin-top: 10px;
        }
        .details {
            background-color: #f9fafb;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
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
            color: #111;
        }
        .total-price {
            background: linear-gradient(135deg, rgba(31, 41, 55, 0.06) 0%, rgba(6, 182, 212, 0.12) 100%);
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            margin: 20px 0;
        }
        .total-price-label {
            color: #6b7280;
            font-size: 14px;
        }
        .total-price-value {
            font-size: 32px;
            color: #1F2937;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            padding: 20px 0;
            border-top: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 12px;
        }
        .button {
            display: inline-block;
            background-color: #1F2937;
            color: white;
            padding: 12px 24px;
            border-radius: 6px;
            text-decoration: none;
            margin: 20px 0;
            font-weight: 600;
        }
        .warning {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 12px;
            margin: 20px 0;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✅ Pembayaran Berhasil</h1>
            <div class="success-badge">{{ $booking->isWaitingList() ? 'ANTREAN' : ($booking->isAwaitingVehicleReturn() ? 'MENUNGGU UNIT' : ($booking->hasNotStartedYet() ? 'TERJADWAL' : 'CONFIRMED')) }}</div>
        </div>

        <p>Halo {{ $booking->user->name }},</p>
        @if($booking->isWaitingList())
            <p>Pembayaran Anda sudah berhasil diverifikasi. Karena kendaraan masih dipakai pada jadwal yang bertabrakan, booking Anda saat ini masuk antrean dan akan diaktifkan otomatis saat kendaraan tersedia.</p>
        @else
            <p>
                Terima kasih! Pembayaran Anda telah berhasil kami terima dan diproses.
                @if($booking->isAwaitingVehicleReturn())
                    Jadwal sewa Anda sudah masuk, tetapi unit masih menunggu pengembalian dari customer sebelumnya.
                @elseif($booking->hasStarted())
                    Booking rental kendaraan Anda sekarang sedang berjalan.
                @else
                    Booking rental kendaraan Anda sekarang sudah dikonfirmasi dan terjadwal mulai pada {{ $booking->start_date->format('d M Y') }}.
                @endif
            </p>
        @endif

        <div class="details">
            <h3 style="margin-top: 0;">📋 Detail Booking Anda</h3>
            
            <div class="detail-row">
                <span class="detail-label">Nomor Booking:</span>
                <span class="detail-value">{{ $booking->id }}</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Kendaraan:</span>
                <span class="detail-value">{{ $booking->vehicle->name }} ({{ $booking->vehicle->plat_number }})</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Check-in:</span>
                <span class="detail-value">{{ $booking->start_date->format('d M Y') }} {{ $booking->pickup_time_label }}</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Check-out:</span>
                <span class="detail-value">{{ $booking->end_date->format('d M Y') }} {{ $booking->return_time_label }}</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Durasi Rental:</span>
                <span class="detail-value">{{ $booking->duration_days }} hari</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Status Booking:</span>
                <span class="detail-value"><strong style="color: #10b981;">{{ $booking->isWaitingList() ? '✓ DIBAYAR · ANTREAN' : ($booking->isAwaitingVehicleReturn() ? '✓ DIBAYAR · MENUNGGU UNIT KEMBALI' : ($booking->hasStarted() ? '✓ DIBAYAR · SEDANG BERJALAN' : '✓ DIBAYAR · TERJADWAL')) }}</strong></span>
            </div>
        </div>

        <div class="total-price">
            <div class="total-price-label">Total Pembayaran</div>
            <div class="total-price-value">Rp{{ number_format($booking->total_price, 0, ',', '.') }}</div>
        </div>

        <div class="warning">
            @if($booking->isWaitingList())
                <strong>⏳ Status Antrean:</strong>
                <ul style="margin: 8px 0 0 0; padding-left: 20px;">
                    <li>Booking Anda sudah aman karena pembayaran telah diverifikasi</li>
                    <li>Sistem akan mengaktifkan booking otomatis saat kendaraan tersedia</li>
                    <li>Anda akan menerima notifikasi lagi ketika antrean diaktifkan menjadi booking aktif</li>
                </ul>
            @elseif($booking->isAwaitingVehicleReturn())
                <strong>⏳ Unit Masih Menunggu Kembali:</strong>
                <ul style="margin: 8px 0 0 0; padding-left: 20px;">
                    <li>Booking Anda tetap aman karena pembayaran sudah diverifikasi</li>
                    <li>Jadwal sewa sudah mulai, tetapi kendaraan masih dipakai customer sebelumnya</li>
                    <li>Silakan pantau halaman detail booking untuk status terbaru sebelum penyerahan unit</li>
                </ul>
            @else
                <strong>⚠️ Catatan Penting:</strong>
                <ul style="margin: 8px 0 0 0; padding-left: 20px;">
                    <li>Harap tiba 15 menit sebelum waktu check-in yang dijadwalkan</li>
                    <li>Bawa dokumen identitas asli dan SIM untuk proses check-in</li>
                    <li>Periksa kondisi kendaraan sebelum meninggalkan area rental</li>
                </ul>
            @endif
        </div>

        @if($booking->notes)
            <p><strong>Catatan Admin:</strong> {{ $booking->notes }}</p>
        @endif

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ url('/bookings/' . $booking->id) }}" class="button">Lihat Detail Booking</a>
        </div>

        <p>Jika Anda memiliki pertanyaan atau memerlukan bantuan, silakan hubungi customer support kami yang siap membantu 24/7.</p>

        <div class="footer">
            <p>© {{ now()->year }} RentalHub. Semua hak dilindungi.</p>
            <p>Email ini dikirim secara otomatis, harap jangan membalas email ini.</p>
        </div>
    </div>
</body>
</html>
