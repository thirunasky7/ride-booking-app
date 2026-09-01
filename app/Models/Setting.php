<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'slot_gap_minutes',
        'booking_open_time',
        'booking_close_time',
        'commission_percent',
        'custom_route_price',
        'razorpay_key_id',
        'razorpay_key_secret',
        'razorpay_enabled',
        'site_name',
        'support_phone',
        'support_email',
    ];

    protected $casts = [
        'razorpay_enabled' => 'boolean',
        'commission_percent' => 'decimal:2',
        'custom_route_price' => 'decimal:2',
    ];

    protected $hidden = [
        'razorpay_key_secret',
    ];
}