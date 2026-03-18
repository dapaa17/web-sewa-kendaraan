<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->decimal('base_price', 10, 2)->nullable()->after('daily_price');
            $table->decimal('weekend_multiplier', 5, 2)->default(1.2)->after('base_price');
            $table->decimal('peak_season_multiplier', 5, 2)->default(1.4)->after('weekend_multiplier');
            $table->decimal('low_season_multiplier', 5, 2)->default(0.8)->after('peak_season_multiplier');
        });

        DB::table('vehicles')
            ->select(['id', 'daily_price'])
            ->orderBy('id')
            ->chunkById(100, function ($vehicles) {
                foreach ($vehicles as $vehicle) {
                    DB::table('vehicles')
                        ->where('id', $vehicle->id)
                        ->update(['base_price' => $vehicle->daily_price]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn([
                'base_price',
                'weekend_multiplier',
                'peak_season_multiplier',
                'low_season_multiplier',
            ]);
        });
    }
};