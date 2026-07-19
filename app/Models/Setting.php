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
    ];
}