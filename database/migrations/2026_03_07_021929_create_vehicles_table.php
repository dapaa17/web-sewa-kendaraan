<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nama mobil (e.g., "Honda Civic 2023")
            $table->string('plat_number')->unique(); // Nomor plat
            $table->enum('transmission', ['Manual', 'Otomatis']); // Jenis transmisi
            $table->integer('year'); // Tahun produksi
            $table->decimal('daily_price', 10, 2); // Harga per hari
            $table->enum('status', ['available', 'rented', 'maintenance'])->default('available');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};