<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('otps', function (Blueprint $table) {
            $table->unsignedTinyInteger('attempts')->default(0)->after('expires_at');
            $table->unsignedTinyInteger('send_count')->default(0)->after('attempts');
            $table->timestamp('last_sent_at')->nullable()->after('send_count');
            $table->timestamp('send_window_started_at')->nullable()->after('last_sent_at');
            $table->unique('mobile');
        });

        Schema::table('settings', function (Blueprint $table) {
            $table->decimal('commission_percent', 5, 2)->default(10)->after('booking_close_time');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->foreignId('time_slot_id')->nullable()->after('bus_stand_id')->constrained('time_slots')->nullOnDelete();
            $table->string('booking_type')->default('daily')->after('trip_type');
            $table->decimal('commission_amount', 10, 2)->default(0)->after('price');
            $table->decimal('driver_amount', 10, 2)->default(0)->after('commission_amount');

            $table->index('booking_date');
            $table->index('vehicle_id');
            $table->index(['booking_date', 'slot_time']);
        });

        Schema::table('route_prices', function (Blueprint $table) {
            $table->foreign('apartment_id')->references('id')->on('apartments')->cascadeOnDelete();
            $table->foreign('bus_stand_id')->references('id')->on('bus_stands')->cascadeOnDelete();
            $table->unique(['apartment_id', 'bus_stand_id']);
        });
    }

    public function down(): void
    {
        Schema::table('route_prices', function (Blueprint $table) {
            $table->dropForeign(['apartment_id']);
            $table->dropForeign(['bus_stand_id']);
            $table->dropUnique(['apartment_id', 'bus_stand_id']);
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['time_slot_id']);
            $table->dropIndex(['booking_date']);
            $table->dropIndex(['vehicle_id']);
            $table->dropIndex(['booking_date', 'slot_time']);
            $table->dropColumn(['time_slot_id', 'booking_type', 'commission_amount', 'driver_amount']);
        });

        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn('commission_percent');
        });

        Schema::table('otps', function (Blueprint $table) {
            $table->dropUnique(['mobile']);
            $table->dropColumn(['attempts', 'send_count', 'last_sent_at', 'send_window_started_at']);
        });
    }
};
