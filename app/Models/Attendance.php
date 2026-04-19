<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = ['registration_id','checked_in_at','checked_in_by'];
    public function registration() { return $this->belongsTo(Registration::class); }
    public function checkedBy() { return $this->belongsTo(User::class, 'checked_in_by'); }
}
