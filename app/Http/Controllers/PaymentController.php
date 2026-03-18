<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function showWhatsAppConfirmation(Booking $booking): View|RedirectResponse
    {
        $this->authorize('pay', $booking);

        if (!Auth::user()->isKtpVerified()) {
            return redirect()->route('profile.ktp')
                ->with('warning', 'Silakan verifikasi KTP terlebih dahulu sebelum melakukan pembayaran.');
        }

        $user = Auth::user();

        $paymentContact = [
            'whatsapp_number' => config('services.whatsapp.admin_number', '6281234567890'),
            'bank_name' => config('services.whatsapp.bank_name', 'Bank BCA'),
            'account_name' => config('services.whatsapp.account_name', 'PT. RentalHub Indonesia'),
            'account_number' => config('services.whatsapp.account_number', '1234567890'),
        ];

        $message = implode("\n", [
            'Halo admin, saya ingin konfirmasi transfer untuk booking kendaraan.',
            'Nama: ' . $user->name,
            'Booking ID: ' . $booking->id,
            'Kendaraan: ' . $booking->vehicle->name,
            'Tanggal: ' . $booking->start_date->format('d M Y') . ' - ' . $booking->end_date->format('d M Y'),
            'Total: Rp' . number_format((float) $booking->total_price, 0, ',', '.'),
            'Saya akan mengirim bukti transfer di chat ini.',
        ]);

        $whatsAppUrl = 'https://wa.me/' . $paymentContact['whatsapp_number'] . '?text=' . rawurlencode($message);

        return view('payments.select-method', compact('booking', 'paymentContact', 'whatsAppUrl'));
    }
}
