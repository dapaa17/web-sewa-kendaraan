<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->date('actual_return_date')->nullable()->after('end_date');
            $table->integer('late_days')->default(0)->after('actual_return_date');
            $table->decimal('late_fee', 10, 2)->default(0)->after('late_days');
            $table->decimal('late_fee_per_day', 10, 2)->nullable()->after('late_fee'); // Percentage of daily_price
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['actual_return_date', 'late_days', 'late_fee', 'late_fee_per_day']);
        });
    }
};
