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
    ];

    protected $hidden = [
        'password',
    ];

    public function vehicle()
    {
        return $this->hasOne(Vehicle::class);
    }
}