<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE bookings MODIFY trip_type ENUM('apartment_to_busstand', 'busstand_to_apartment', 'others') NOT NULL");
        }

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['apartment_id']);
            $table->dropForeign(['bus_stand_id']);
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->unsignedBigInteger('apartment_id')->nullable()->change();
            $table->unsignedBigInteger('bus_stand_id')->nullable()->change();
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->foreign('apartment_id')->references('id')->on('apartments')->cascadeOnDelete();
            $table->foreign('bus_stand_id')->references('id')->on('bus_stands')->cascadeOnDelete();
        });

        Schema::table('settings', function (Blueprint $table) {
            $table->decimal('custom_route_price', 10, 2)->default(150)->after('commission_percent');
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn('custom_route_price');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['apartment_id']);
            $table->dropForeign(['bus_stand_id']);
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->unsignedBigInteger('apartment_id')->nullable(false)->change();
            $table->unsignedBigInteger('bus_stand_id')->nullable(false)->change();
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->foreign('apartment_id')->references('id')->on('apartments')->cascadeOnDelete();
            $table->foreign('bus_stand_id')->references('id')->on('bus_stands')->cascadeOnDelete();
        });

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE bookings MODIFY trip_type ENUM('apartment_to_busstand', 'busstand_to_apartment') NOT NULL");
        }
    }
};
