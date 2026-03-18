<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->timestamp('maintenance_hold_at')->nullable();
            $table->text('maintenance_hold_reason')->nullable();
        });

        DB::table('bookings')
            ->join('vehicles', 'vehicles.id', '=', 'bookings.vehicle_id')
            ->where('vehicles.status', 'maintenance')
            ->whereIn('bookings.status', ['confirmed', 'waiting_list'])
            ->where('bookings.payment_status', 'paid')
            ->select('bookings.id', 'bookings.notes', 'bookings.updated_at')
            ->orderBy('bookings.id')
            ->get()
            ->filter(fn (object $booking) => Str::contains(Str::lower((string) $booking->notes), 'maintenance'))
            ->each(function (object $booking): void {
                DB::table('bookings')
                    ->where('id', $booking->id)
                    ->update([
                        'maintenance_hold_at' => $booking->updated_at ?? now(),
                        'maintenance_hold_reason' => $booking->notes,
                    ]);
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'maintenance_hold_at',
                'maintenance_hold_reason',
            ]);
        });
    }
};