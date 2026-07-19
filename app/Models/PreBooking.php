<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PreBooking extends Model
{
    protected $fillable = [
        'user_id',
        'apartment_id',
        'bus_stand_id',
        'time_slot_id',
        'booking_date',
        'slot_time',
        'trip_type',
        'status',
    ];

    protected $casts = [
        'booking_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function apartment()
    {
        return $this->belongsTo(Apartment::class);
    }

    public function busStand()
    {
        return $this->belongsTo(BusStand::class);
    }

    public function timeSlot()
    {
        return $this->belongsTo(TimeSlot::class);
    }
}
