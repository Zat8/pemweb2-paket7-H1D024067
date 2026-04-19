<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    protected $fillable = ['name', 'email', 'password', 'role', 'institution'];

    public function registrations() { return $this->hasMany(Registration::class); }
    public function createdEvents() { return $this->hasMany(Event::class, 'created_by'); }

    public function isAdmin() { return $this->role === 'admin'; }
    public function isPanitia() { return $this->role === 'panitia'; }
    public function isPeserta() { return $this->role === 'peserta'; }
}
