<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClassSchedule extends Model
{
    protected $fillable = [
        'service_id',
        'class_date',
        'start_time',
        'end_time',
        'duration_hours',
        'max_students',
        'min_students',
        'current_students',
        'room',
        'location',
        'instructor',
        'can_overlap',
        'status',
        'notes',
    ];

    protected $casts = [
        'class_date' => 'date',
        'start_time' => 'string', // Store as time string (H:i:s)
        'end_time' => 'string', // Store as time string (H:i:s)
        'duration_hours' => 'integer',
        'max_students' => 'integer',
        'min_students' => 'integer',
        'current_students' => 'integer',
        'can_overlap' => 'boolean',
    ];

    public static function normalizeLocation(?string $location): ?string
    {
        if ($location === null) {
            return null;
        }

        $location = trim($location);

        return $location === '' ? null : $location;
    }

    public static function normalizeStartTime(string $startTime): string
    {
        return Carbon::parse($startTime)->format('H:i:s');
    }

    public static function normalizeClassDate(string $classDate): string
    {
        return Carbon::parse($classDate)->toDateString();
    }

    /**
     * @return array{service_id: int, class_date: string, start_time: string, location: ?string}
     */
    public static function normalizeSlot(int $serviceId, string $classDate, string $startTime, ?string $location): array
    {
        return [
            'service_id' => $serviceId,
            'class_date' => self::normalizeClassDate($classDate),
            'start_time' => self::normalizeStartTime($startTime),
            'location' => self::normalizeLocation($location),
        ];
    }

    public static function slotFingerprint(int $serviceId, string $classDate, string $startTime, ?string $location): string
    {
        $slot = self::normalizeSlot($serviceId, $classDate, $startTime, $location);

        return implode('|', [
            $slot['service_id'],
            $slot['class_date'],
            $slot['start_time'],
            $slot['location'] ?? '',
        ]);
    }

    public static function duplicateQuery(int $serviceId, string $classDate, string $startTime, ?string $location, ?int $ignoreId = null): Builder
    {
        $slot = self::normalizeSlot($serviceId, $classDate, $startTime, $location);

        return static::query()
            ->where('service_id', $slot['service_id'])
            ->whereDate('class_date', $slot['class_date'])
            ->where('start_time', $slot['start_time'])
            ->when(
                $slot['location'] === null,
                fn (Builder $query) => $query->where(function (Builder $locationQuery) {
                    $locationQuery->whereNull('location')->orWhere('location', '');
                }),
                fn (Builder $query) => $query->where('location', $slot['location'])
            )
            ->when($ignoreId !== null, fn (Builder $query) => $query->where('id', '!=', $ignoreId));
    }

    public static function duplicateExists(int $serviceId, string $classDate, string $startTime, ?string $location, ?int $ignoreId = null): bool
    {
        return self::duplicateQuery($serviceId, $classDate, $startTime, $location, $ignoreId)->exists();
    }

    /**
     * Get the service that owns this schedule.
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Get all bookings for this class schedule.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(ServiceBooking::class, 'class_schedule_id');
    }

    /**
     * Get confirmed bookings for this class schedule.
     */
    public function confirmedBookings()
    {
        return $this->bookings()
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('payment_status', '!=', 'refunded');
    }

    /**
     * Check if class has available spots.
     */
    public function hasAvailableSpots(): bool
    {
        return $this->current_students < $this->max_students && $this->status === 'scheduled';
    }

    /**
     * Get available spots count.
     */
    public function getAvailableSpots(): int
    {
        return max(0, $this->max_students - $this->current_students);
    }

    /**
     * Check if class meets minimum students requirement.
     */
    public function meetsMinimumStudents(): bool
    {
        return $this->current_students >= $this->min_students;
    }

    /**
     * Check if class is full.
     */
    public function isFull(): bool
    {
        return $this->current_students >= $this->max_students;
    }

    /**
     * Increment student count.
     */
    public function incrementStudentCount($count = 1): void
    {
        $this->increment('current_students', $count);

        // Check if class is now full
        if ($this->current_students >= $this->max_students) {
            $this->update(['status' => 'full']);
        }
    }

    /**
     * Decrement student count.
     */
    public function decrementStudentCount($count = 1): void
    {
        $this->decrement('current_students', $count);

        // Update status if not full anymore
        if ($this->status === 'full' && $this->current_students < $this->max_students) {
            $this->update(['status' => 'scheduled']);
        }
    }
}
