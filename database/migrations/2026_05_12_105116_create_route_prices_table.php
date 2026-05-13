<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('route_prices', function (Blueprint $table) {

            $table->id();

            $table->foreignId('apartment_id');

            $table->foreignId('bus_stand_id');

            $table->decimal('base_price', 10, 2);

            $table->decimal('peak_price', 10, 2)
                ->nullable();

            $table->time('peak_from')
                ->nullable();

            $table->time('peak_to')
                ->nullable();

            $table->decimal('holiday_price', 10, 2)
                ->nullable();

            $table->boolean('status')
                ->default(1);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('route_prices');
    }
};