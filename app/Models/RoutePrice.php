<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoutePrice extends Model
{
    protected $fillable = [

        'apartment_id',
        'bus_stand_id',
        'base_price',
        'peak_price',
        'peak_from',
        'peak_to',
        'holiday_price',
        'status'

    ];

    public function apartment()
    {
        return $this->belongsTo(Apartment::class);
    }

    public function busStand()
    {
        return $this->belongsTo(BusStand::class);
    }
}