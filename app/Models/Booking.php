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
        'time_slot_id',
        'booking_date',
        'slot_time',
        'trip_type',
        'booking_type',
        'status',
        'price',
        'commission_amount',
        'driver_amount',
        'pickup_address',
        'pickup_lat',
        'pickup_lng',
        'drop_address',
        'drop_lat',
        'drop_lng',
    ];

    protected $casts = [
        'booking_date' => 'date',
        'price' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'driver_amount' => 'decimal:2',
        'pickup_lat' => 'decimal:7',
        'pickup_lng' => 'decimal:7',
        'drop_lat' => 'decimal:7',
        'drop_lng' => 'decimal:7',
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

    public function timeSlot()
    {
        return $this->belongsTo(TimeSlot::class);
    }

    public function driverEarning()
    {
        return $this->hasOne(DriverEarning::class);
    }
}