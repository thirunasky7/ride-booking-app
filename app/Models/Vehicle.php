<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    protected $fillable = [
        'driver_id',
        'vehicle_name',
        'vehicle_number',
        'capacity',
        'status',
    ];

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}