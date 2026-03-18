<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AdminSettingsController extends Controller
{
    public function index()
    {
        $bookingScheduleDefaults = AppSetting::getBookingScheduleDefaults();
        $scheduleWindowLabel = $this->buildScheduleWindowLabel(
            $bookingScheduleDefaults['pickup_time'],
            $bookingScheduleDefaults['return_time']
        );

        return view('admin.settings', compact('bookingScheduleDefaults', 'scheduleWindowLabel'));
    }

    public function updateBookingScheduleDefaults(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'pickup_time' => 'required|date_format:H:i',
            'return_time' => 'required|date_format:H:i',
        ]);

        if (strcmp($validated['return_time'], $validated['pickup_time']) <= 0) {
            return redirect()->route('admin.settings.index')
                ->withErrors([
                    'return_time' => 'Jam kembali default harus setelah jam ambil default.',
                ])
                ->withInput();
        }

        AppSetting::setValue('booking_default_pickup_time', $validated['pickup_time']);
        AppSetting::setValue('booking_default_return_time', $validated['return_time']);

        return redirect()->route('admin.settings.index')->with('success', 'Jam default booking berhasil diperbarui.');
    }

    private function buildScheduleWindowLabel(string $pickupTime, string $returnTime): string
    {
        $pickup = Carbon::createFromFormat('H:i', $pickupTime);
        $return = Carbon::createFromFormat('H:i', $returnTime);
        $minutes = $pickup->diffInMinutes($return, false);

        if ($minutes <= 0) {
            return '-';
        }

        $hours = intdiv($minutes, 60);
        $remainingMinutes = $minutes % 60;

        if ($hours > 0 && $remainingMinutes > 0) {
            return sprintf('%d jam %d menit', $hours, $remainingMinutes);
        }

        if ($hours > 0) {
            return sprintf('%d jam', $hours);
        }

        return sprintf('%d menit', $remainingMinutes);
    }
}