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
            $table->string('return_condition_status')->nullable();
            $table->string('return_fuel_level')->nullable();
            $table->unsignedInteger('return_odometer')->nullable();
            $table->json('return_checklist')->nullable();
            $table->decimal('return_damage_fee', 12, 2)->default(0);
            $table->text('return_notes')->nullable();
            $table->string('return_photo')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'return_condition_status',
                'return_fuel_level',
                'return_odometer',
                'return_checklist',
                'return_damage_fee',
                'return_notes',
                'return_photo',
            ]);
        });
    }
};