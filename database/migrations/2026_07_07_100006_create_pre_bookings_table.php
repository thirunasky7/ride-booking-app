<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pre_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('apartment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('bus_stand_id')->constrained()->cascadeOnDelete();
            $table->foreignId('time_slot_id')->nullable()->constrained('time_slots')->nullOnDelete();
            $table->date('booking_date');
            $table->time('slot_time');
            $table->string('trip_type');
            $table->string('status')->default('pending');
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index('booking_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pre_bookings');
    }
};
