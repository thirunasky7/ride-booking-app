<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'user_id',
        'vehicle_id',
        'apartment_id',
        'bus_stand_id',
        'booking_date',
        'slot_time',
        'trip_type',
        'status',
        'price'
    ];

    protected $casts = [
        'booking_date' => 'date',
    ];

    public function customer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function apartment()
    {
        return $this->belongsTo(Apartment::class);
    }

    public function busStand()
    {
        return $this->belongsTo(BusStand::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}