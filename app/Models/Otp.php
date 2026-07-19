<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Otp extends Model
{
    protected $fillable = [
        'mobile',
        'otp',
        'expires_at',
        'attempts',
        'send_count',
        'last_sent_at',
        'send_window_started_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'last_sent_at' => 'datetime',
        'send_window_started_at' => 'datetime',
    ];

    
}