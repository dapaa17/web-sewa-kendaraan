<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Add missing columns if they don't exist
            // Removed Midtrans columns
            if (!Schema::hasColumn('bookings', 'payment_proof')) {
                $table->string('payment_proof')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Removed Midtrans columns
            if (Schema::hasColumn('bookings', 'payment_proof')) {
                $table->dropColumn('payment_proof');
            }
        });
    }
};
