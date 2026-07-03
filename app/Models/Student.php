<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Student extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'students';

    protected $guard = 'student';

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
        'profile_picture',
        'has_security_registration',
        'security_registration_number',
        'security_registration_expiration',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'has_security_registration' => 'boolean',
            'security_registration_expiration' => 'date',
        ];
    }

    public function bookings()
    {
        return $this->hasMany(ServiceBooking::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function waitlistEntries()
    {
        return $this->hasMany(WaitlistEntry::class);
    }

    public function moduleProgress()
    {
        return $this->hasMany(StudentModuleProgress::class);
    }

    public function certificates()
    {
        return $this->hasMany(CourseCertificate::class);
    }
}
