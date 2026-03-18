<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('bookings')
            ->where('payment_method', 'online')
            ->update(['payment_method' => 'whatsapp']);

        DB::table('bookings')
            ->where('payment_method', 'offline')
            ->update(['payment_method' => 'transfer_proof']);

        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE bookings MODIFY COLUMN payment_method VARCHAR(50) DEFAULT 'transfer_proof'");
        }
    }

    public function down(): void
    {
        DB::table('bookings')
            ->where('payment_method', 'whatsapp')
            ->update(['payment_method' => 'online']);

        DB::table('bookings')
            ->where('payment_method', 'transfer_proof')
            ->update(['payment_method' => 'offline']);

        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE bookings MODIFY COLUMN payment_method VARCHAR(50) DEFAULT 'offline'");
        }
    }
};