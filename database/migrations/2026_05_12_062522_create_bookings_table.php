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
        Schema::create('bookings', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')
            ->constrained()
            ->onDelete('cascade');
        $table->foreignId('vehicle_id')
            ->constrained()
            ->onDelete('cascade');
        $table->foreignId('apartment_id')
            ->constrained()
            ->onDelete('cascade');
        $table->foreignId('bus_stand_id')
            ->constrained()
            ->onDelete('cascade');
        $table->date('booking_date');
        $table->time('slot_time');
        $table->enum('trip_type', [
            'apartment_to_busstand',
            'busstand_to_apartment'
        ]);
        $table->enum('status', [
            'pending',
            'confirmed',
            'started',
            'completed',
            'cancelled'
        ])->default('pending');
        $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
