<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = ['event_category_id','created_by','title','slug','description','speaker','event_date','start_time','end_time','location','quota','poster','certificate_template','status'];
    public function category() { return $this->belongsTo(EventCategory::class, 'event_category_id'); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function registrations() { return $this->hasMany(Registration::class); }
    public function attendances() { return $this->hasManyThrough(Attendance::class, Registration::class); }

    public function isFull(): bool
    {
        $count = $this->registrations_count ?? $this->registrations()->count();

        return $count >= $this->quota;
    }
}
