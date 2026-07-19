<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriverEarning extends Model
{
    protected $fillable = [
        'driver_id',
        'booking_id',
        'commission_amount',
        'driver_amount',
    ];

    protected $casts = [
        'commission_amount' => 'decimal:2',
        'driver_amount' => 'decimal:2',
    ];

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
