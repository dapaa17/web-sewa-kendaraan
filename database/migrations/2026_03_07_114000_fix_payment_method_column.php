<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        Schema::table('bookings', function (Blueprint $table) {
            // Drop the enum constraint and create as string
            DB::statement("ALTER TABLE bookings MODIFY COLUMN payment_method VARCHAR(50) DEFAULT 'transfer_proof'");
        });
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        Schema::table('bookings', function (Blueprint $table) {
            // Revert back to ENUM if needed
            DB::statement("ALTER TABLE bookings MODIFY COLUMN payment_method ENUM('whatsapp', 'transfer_proof') DEFAULT 'transfer_proof'");
        });
    }
};
