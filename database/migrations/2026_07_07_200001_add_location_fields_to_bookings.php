<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('pickup_address')->nullable()->after('bus_stand_id');
            $table->decimal('pickup_lat', 10, 7)->nullable()->after('pickup_address');
            $table->decimal('pickup_lng', 10, 7)->nullable()->after('pickup_lat');
            $table->string('drop_address')->nullable()->after('pickup_lng');
            $table->decimal('drop_lat', 10, 7)->nullable()->after('drop_address');
            $table->decimal('drop_lng', 10, 7)->nullable()->after('drop_lat');
        });

        DB::table('bookings')->where('booking_type', 'daily')->update(['booking_type' => 'instant']);
        DB::table('bookings')->whereIn('booking_type', ['prebook', 'scheduled'])->update(['booking_type' => 'scheduled']);
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'pickup_address', 'pickup_lat', 'pickup_lng',
                'drop_address', 'drop_lat', 'drop_lng',
            ]);
        });
    }
};
