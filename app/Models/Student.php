<?php

namespace App\Models;

use App\Mail\StudentPasswordResetMail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Mail;

class Student extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'students';

    protected $guard = 'student';

    protected $fillable = [
        'name',
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'password',
        'phone',
        'address',
        'ssn',
        'ssn_last_four',
        'profile_picture',
        'has_security_registration',
        'security_registration_number',
        'security_registration_expiration',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'ssn',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'ssn' => 'encrypted',
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

    public function sendPasswordResetNotification(#[\SensitiveParameter] $token): void
    {
        Mail::to($this->email)->send(new StudentPasswordResetMail($this, $token));
    }

    /**
     * @return array{0: string, 1: ?string, 2: string}
     */
    public static function splitDisplayName(string $name): array
    {
        $parts = preg_split('/\s+/', trim($name), -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $first = $parts[0] ?? 'Student';

        if (count($parts) === 1) {
            return [$first, null, $first];
        }

        $last = (string) array_pop($parts);
        $middle = count($parts) > 1 ? implode(' ', array_slice($parts, 1)) : null;

        return [$first, filled($middle) ? $middle : null, $last];
    }

    public static function composeDisplayName(string $first, ?string $middle, string $last): string
    {
        return collect([$first, $middle, $last])
            ->map(fn ($part) => is_string($part) ? trim($part) : '')
            ->filter(fn ($part) => $part !== '')
            ->implode(' ');
    }

    public static function digitsOnly(string $value): string
    {
        return preg_replace('/\D+/', '', $value) ?? '';
    }

    public static function isValidSsn(string $ssn): bool
    {
        $digits = self::digitsOnly($ssn);

        if (strlen($digits) !== 9) {
            return false;
        }

        $area = (int) substr($digits, 0, 3);
        $group = substr($digits, 3, 2);
        $serial = substr($digits, 5, 4);

        return $area !== 0
            && $area !== 666
            && $area < 900
            && $group !== '00'
            && $serial !== '0000';
    }

    public function applyLegalName(string $first, ?string $middle, string $last): void
    {
        $this->first_name = trim($first);
        $this->middle_name = filled($middle) ? trim($middle) : null;
        $this->last_name = trim($last);
        $this->name = self::composeDisplayName($this->first_name, $this->middle_name, $this->last_name);
    }

    public function applySsn(string $ssn): void
    {
        $digits = self::digitsOnly($ssn);
        $this->ssn = $digits;
        $this->ssn_last_four = substr($digits, -4);
    }

    public function maskedSsn(): string
    {
        return filled($this->ssn_last_four) ? '***-**-'.$this->ssn_last_four : 'Not on file';
    }
}
