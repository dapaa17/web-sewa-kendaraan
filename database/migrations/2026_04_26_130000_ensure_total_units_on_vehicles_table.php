<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('vehicles', 'total_units')) {
            Schema::table('vehicles', function (Blueprint $table) {
                $table->unsignedInteger('total_units')->default(1)->after('plat_number');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('vehicles', 'total_units')) {
            Schema::table('vehicles', function (Blueprint $table) {
                $table->dropColumn('total_units');
            });
        }
    }
};
