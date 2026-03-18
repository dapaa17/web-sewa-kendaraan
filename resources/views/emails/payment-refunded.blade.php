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
            border-bottom: 2px solid #06b6d4;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            color: #06b6d4;
            font-size: 28px;
        }
        .refund-badge {
            display: inline-block;
            background-color: #06b6d4;
            color: white;
            padding: 8px 16px;
            border-radius: 24px;
            font-weight: bold;
            margin-top: 10px;
        }
        .details {
            background-color: #ecfdf5;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #06b6d4;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #d1fae5;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            font-weight: 600;
            color: #0d6e65;
        }
        .detail-value {
            color: #065f46;
        }
        .refund-amount {
            background-color: #cffafe;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            margin: 20px 0;
        }
        .refund-amount-label {
            color: #0d6e65;
            font-size: 14px;
        }
        .refund-amount-value {
            font-size: 32px;
            color: #06b6d4;
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
            background-color: #06b6d4;
            color: white;
            padding: 12px 24px;
            border-radius: 6px;
            text-decoration: none;
            margin: 20px 0;
            font-weight: 600;
        }
        .info {
            background-color: #dbeafe;
            border-left: 4px solid #0284c7;
            padding: 12px;
            margin: 20px 0;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>💰 Pembayaran Dikembalikan</h1>
            <div class="refund-badge">REFUNDED</div>
        </div>

        <p>Halo {{ $user->name }},</p>
        <p>Kami telah memproses pengembalian dana untuk booking rental kendaraan Anda. Silakan tunggu beberapa hari kerja untuk dana masuk ke rekening Anda.</p>

        <div class="details">
            <h3 style="margin-top: 0;">📋 Detail Booking</h3>
            
            <div class="detail-row">
                <span class="detail-label">Nomor Booking:</span>
                <span class="detail-value">{{ $booking->id }}</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Kendaraan:</span>
                <span class="detail-value">{{ $vehicle->name }} ({{ $vehicle->plat_number }})</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Check-in (Rencana):</span>
                <span class="detail-value">{{ $startDate }}</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Check-out (Rencana):</span>
                <span class="detail-value">{{ $endDate }}</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Status Pembayaran:</span>
                <span class="detail-value"><strong style="color: #06b6d4;">↩️ DIKEMBALIKAN</strong></span>
            </div>
        </div>

        <div class="refund-amount">
            <div class="refund-amount-label">Jumlah Pengembalian</div>
            <div class="refund-amount-value">Rp{{ number_format($refundAmount, 0, ',', '.') }}</div>
        </div>

        <div class="info">
            <strong>ℹ️ Informasi Pengembalian Dana:</strong>
            <ul style="margin: 8px 0 0 0; padding-left: 20px;">
                <li>Waktu pemrosesan: 3-5 hari kerja</li>
                <li>Dana akan dikembalikan melalui transfer manual ke rekening yang Anda konfirmasi ke admin</li>
                <li>Pastikan data rekening penerima yang diberikan ke admin sudah benar</li>
                <li>Admin dapat menghubungi Anda jika diperlukan konfirmasi tambahan</li>
            </ul>
        </div>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ url('/bookings') }}" class="button">Lihat Booking Saya</a>
        </div>

        <p>Jika Anda memiliki pertanyaan mengenai pengembalian dana ini atau ingin membuat booking baru, silakan hubungi <strong>customer support kami</strong> yang siap membantu 24/7.</p>

        <div class="footer">
            <p>© {{ now()->year }} RentalHub. Semua hak dilindungi.</p>
            <p>Email ini dikirim secara otomatis, harap jangan membalas email ini.</p>
        </div>
    </div>
</body>
</html>
