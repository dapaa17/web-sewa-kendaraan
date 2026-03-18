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
            border-bottom: 2px solid #ef4444;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            color: #ef4444;
            font-size: 28px;
        }
        .error-badge {
            display: inline-block;
            background-color: #ef4444;
            color: white;
            padding: 8px 16px;
            border-radius: 24px;
            font-weight: bold;
            margin-top: 10px;
        }
        .details {
            background-color: #fef2f2;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #ef4444;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #fecaca;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            font-weight: 600;
            color: #7f1d1d;
        }
        .detail-value {
            color: #4b0000;
        }
        .error-message {
            background-color: #fee2e2;
            border-left: 4px solid #ef4444;
            padding: 12px;
            border-radius: 4px;
            margin: 20px 0;
        }
        .error-message strong {
            color: #7f1d1d;
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
            background-color: #ef4444;
            color: white;
            padding: 12px 24px;
            border-radius: 6px;
            text-decoration: none;
            margin: 20px 0;
            font-weight: 600;
        }
        .tips {
            background-color: #fef9c3;
            border-left: 4px solid #eab308;
            padding: 12px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .tips strong {
            color: #713f12;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>❌ Pembayaran Gagal</h1>
            <div class="error-badge">FAILED</div>
        </div>

        <p>Halo {{ $booking->user->name }},</p>
        <p>Maaf, kami tidak berhasil memproses pembayaran untuk booking rental kendaraan Anda. Silakan coba lagi dengan metode pembayaran yang berbeda.</p>

        <div class="error-message">
            <strong>Alasan:</strong> {{ $errorMessage ?: 'Pembayaran belum dapat kami verifikasi.' }}
        </div>

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
                <span class="detail-value">{{ $booking->start_date->format('d M Y') }}</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Check-out:</span>
                <span class="detail-value">{{ $booking->end_date->format('d M Y') }}</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Total Pembayaran:</span>
                <span class="detail-value">Rp{{ number_format($booking->total_price, 0, ',', '.') }}</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Status Pembayaran:</span>
                <span class="detail-value"><strong style="color: #ef4444;">✗ GAGAL</strong></span>
            </div>
        </div>

        <div class="tips">
            <strong>💡 Tips Troubleshooting:</strong>
            <ul style="margin: 8px 0 0 0; padding-left: 20px;">
                <li>Pastikan nominal transfer sudah sesuai total pembayaran booking</li>
                <li>Pastikan bukti transfer yang dikirim jelas dan dapat dibaca</li>
                <li>Jika memilih konfirmasi via WhatsApp, kirim bukti transfer ke admin melalui chat</li>
                <li>Jika pembayaran ditolak, ulangi pembayaran atau upload bukti transfer yang benar</li>
            </ul>
        </div>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ url('/bookings/' . $booking->id) }}" class="button">Coba Bayar Lagi</a>
        </div>

        <p><strong>Catatan:</strong> Booking Anda masih berlaku. Silakan selesaikan pembayaran sebelum tanggal check-in untuk mengkonfirmasi reservasi.</p>

        <p>Jika Anda terus mengalami masalah, silakan hubungi admin atau customer support untuk bantuan verifikasi pembayaran.</p>

        <div class="footer">
            <p>© {{ now()->year }} RentalHub. Semua hak dilindungi.</p>
            <p>Email ini dikirim secara otomatis, harap jangan membalas email ini.</p>
        </div>
    </div>
</body>
</html>
