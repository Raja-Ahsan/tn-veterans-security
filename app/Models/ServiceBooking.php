<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceBooking extends Model
{
    protected $fillable = [
        'student_id',
        'service_id',
        'class_schedule_id',
        'location',
        'booking_date',
        'booking_time',
        'status',
        'notes',
        // Payment fields
        'total_amount',
        'deposit_amount',
        'remaining_amount',
        'payment_status',
        // Booking type
        'booking_type',
        'number_of_students',
        'group_name',
    ];

    protected $casts = [
        'booking_date' => 'date',
        'booking_time' => 'datetime',
        'total_amount' => 'decimal:2',
        'deposit_amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
        'number_of_students' => 'integer',
    ];

    /**
     * Get the student that owns the booking.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the service that belongs to the booking.
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Get the class schedule for this booking.
     */
    public function classSchedule(): BelongsTo
    {
        return $this->belongsTo(ClassSchedule::class, 'class_schedule_id');
    }

    /**
     * Get all payments for this booking.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'booking_id');
    }

    /**
     * Check if deposit has been paid.
     */
    public function hasPaidDeposit(): bool
    {
        return $this->payment_status === 'deposit_paid' || $this->payment_status === 'fully_paid';
    }

    /**
     * Check if booking is fully paid.
     */
    public function isFullyPaid(): bool
    {
        return $this->payment_status === 'fully_paid';
    }

    /**
     * Get total paid amount.
     */
    public function getTotalPaid(): float
    {
        return $this->payments()
            ->where('status', 'completed')
            ->sum('amount');
    }

    /**
     * @return list<string>
     */
    public static function openEnrollmentStatuses(): array
    {
        return ['pending', 'confirmed'];
    }

    /**
     * A student may hold only one open enrollment per class (one schedule, one student profile).
     */
    public static function findOpenEnrollment(int $studentId, int $serviceId, bool $forUpdate = false): ?self
    {
        $query = static::query()
            ->where('student_id', $studentId)
            ->where('service_id', $serviceId)
            ->whereIn('status', self::openEnrollmentStatuses())
            ->latest('id');

        if ($forUpdate) {
            $query->lockForUpdate();
        }

        return $query->first();
    }

    public static function alreadyEnrolledMessage(): string
    {
        return 'You already have a booking for this class. Each student can enroll in one session, because each person has their own student profile.';
    }
}
