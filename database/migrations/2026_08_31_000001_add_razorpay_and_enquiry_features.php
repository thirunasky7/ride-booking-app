<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->string('razorpay_key_id')->nullable()->after('custom_route_price');
            $table->string('razorpay_key_secret')->nullable()->after('razorpay_key_id');
            $table->boolean('razorpay_enabled')->default(false)->after('razorpay_key_secret');
            $table->string('site_name')->default('Apartment Shuttle')->after('razorpay_enabled');
            $table->string('support_phone')->nullable()->after('site_name');
            $table->string('support_email')->nullable()->after('support_phone');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->string('razorpay_order_id')->nullable()->after('paid_at');
            $table->string('razorpay_payment_id')->nullable()->after('razorpay_order_id');
        });

        Schema::create('subscription_enquiries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('subscription_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('mobile', 15);
            $table->string('email')->nullable();
            $table->text('message')->nullable();
            $table->date('preferred_start_date')->nullable();
            $table->enum('status', ['pending', 'contacted', 'closed'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_enquiries');

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['razorpay_order_id', 'razorpay_payment_id']);
        });

        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'razorpay_key_id',
                'razorpay_key_secret',
                'razorpay_enabled',
                'site_name',
                'support_phone',
                'support_email',
            ]);
        });
    }
};
