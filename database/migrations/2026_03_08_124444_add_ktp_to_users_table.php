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
        Schema::table('users', function (Blueprint $table) {
            $table->string('ktp_number')->nullable()->after('email');
            $table->string('ktp_image')->nullable()->after('ktp_number');
            $table->enum('ktp_status', ['pending', 'verified', 'rejected'])->default('pending')->after('ktp_image');
            $table->timestamp('ktp_verified_at')->nullable()->after('ktp_status');
            $table->text('ktp_rejection_reason')->nullable()->after('ktp_verified_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['ktp_number', 'ktp_image', 'ktp_status', 'ktp_verified_at', 'ktp_rejection_reason']);
        });
    }
};
