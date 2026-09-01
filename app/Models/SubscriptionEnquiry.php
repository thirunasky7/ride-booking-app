<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionEnquiry extends Model
{
    protected $fillable = [
        'user_id',
        'subscription_id',
        'name',
        'mobile',
        'email',
        'message',
        'preferred_start_date',
        'status',
        'admin_notes',
    ];

    protected $casts = [
        'preferred_start_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }
}
