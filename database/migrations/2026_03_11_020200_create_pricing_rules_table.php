<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pricing_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
            $table->date('start_date');
            $table->date('end_date');
            $table->unsignedTinyInteger('discount_percentage');
            $table->enum('type', ['peak_season', 'low_season', 'early_bird', 'last_minute', 'custom']);
            $table->string('description')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->index(['vehicle_id', 'active', 'start_date', 'end_date'], 'pricing_rule_vehicle_active_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pricing_rules');
    }
};