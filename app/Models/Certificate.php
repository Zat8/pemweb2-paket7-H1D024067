<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    protected $fillable = ['registration_id','certificate_number','file_path','issued_at'];
    public function registration() { return $this->belongsTo(Registration::class); }
}
