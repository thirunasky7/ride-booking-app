<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Driver extends Authenticatable
{
    use HasApiTokens;

    protected $fillable = [
        'name',
        'mobile',
        'license_number',
        'password',
        'status',
        'device_token',
        'is_online',
    ];

    protected $casts = [
        'is_online' => 'boolean',
    ];

    protected $hidden = [
        'password',
    ];

    public function vehicle()
    {
        return $this->hasOne(Vehicle::class);
    }

    public function earnings()
    {
        return $this->hasMany(DriverEarning::class);
    }
}