<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Registration extends Model
{
    protected $fillable = ['event_id', 'user_id', 'ticket_token', 'registered_at'];

    protected $casts = [
        'registered_at' => 'datetime',
    ];

    public function setTicketTokenAttribute($value): void
    {
        $this->attributes['ticket_token'] = strtoupper((string) $value);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attendance()
    {
        return $this->hasOne(Attendance::class);
    }

    public function certificate()
    {
        return $this->hasOne(Certificate::class);
    }
}
