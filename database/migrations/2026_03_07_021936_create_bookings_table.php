<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('vehicle_id')->constrained()->onDelete('cascade');
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('duration_days');
            $table->decimal('daily_price', 10, 2);
            $table->decimal('total_price', 10, 2);
            
            // Booking status
            $table->enum('status', ['pending', 'waiting_list', 'confirmed', 'completed', 'cancelled'])->default('pending');
            
            // Payment fields (UPDATED)
            $table->enum('payment_method', ['whatsapp', 'transfer_proof'])->default('transfer_proof');
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');
            $table->string('payment_proof')->nullable(); // Untuk flow upload bukti transfer

            
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};