<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    /**
     * Class images on disk live under public/{this path}. The `image` column stores the filename only for new rows.
     */
    public const CLASS_IMAGE_PUBLIC_PATH = 'assets/images/classes';

    protected $guarded = ['id'];

    protected $casts = [
        'categories' => 'array',
        'sub_titles' => 'array',
        'is_active' => 'boolean',
        'order' => 'integer',
        'price' => 'decimal:2',
        'deposit_amount' => 'decimal:2',
        'duration_hours' => 'integer',
        'max_students' => 'integer',
        'min_students' => 'integer',
        'has_online_parts' => 'boolean',
        'testing_in_person' => 'boolean',
        'requires_dallas_law' => 'boolean',
        'requires_active_shooter' => 'boolean',
        'requirements' => 'string',
        'is_travel_based' => 'boolean',
        'travel_distance_fee' => 'decimal:2',
        'travel_lodging_fee' => 'decimal:2',
        'travel_time_fee' => 'decimal:2',
        'travel_minimum_students' => 'integer',
    ];

    /**
     * Services linked from this one (e.g. Unarmed → Less Lethal, Dallas Law).
     * Pivot: service_relationships (parent_service_id, linked_service_id, order).
     */
    public function linkedServices(): BelongsToMany
    {
        return $this->belongsToMany(
            Service::class,
            'service_relationships',
            'parent_service_id',
            'linked_service_id'
        )
            ->withPivot('order')
            ->withTimestamps()
            ->orderByPivot('order');
    }

    /**
     * Services that link to this one (reverse of linkedServices).
     */
    public function linkedFromServices(): BelongsToMany
    {
        return $this->belongsToMany(
            Service::class,
            'service_relationships',
            'linked_service_id',
            'parent_service_id'
        )
            ->withPivot('order')
            ->withTimestamps()
            ->orderByPivot('order');
    }

    /**
     * Public URL for the service image (filename-only DB values, full assets/ path, or legacy storage paths).
     */
    public function getImageUrlAttribute(): ?string
    {
        if (! $this->image) {
            return null;
        }

        $path = $this->image;
        if (str_starts_with($path, 'assets/')) {
            return asset($path);
        }

        if (str_contains($path, '/')) {
            return asset('storage/'.$path);
        }

        return asset(self::CLASS_IMAGE_PUBLIC_PATH.'/'.basename($path));
    }

    /**
     * Deterministic placeholder when no uploaded image (same on list + detail pages).
     */
    public function getFallbackImageUrlAttribute(): string
    {
        return asset('images/training-img-'.(($this->id % 6) + 1).'.png');
    }

    /**
     * Uploaded image URL, or placeholder fallback.
     */
    public function getDisplayImageUrlAttribute(): string
    {
        return $this->image_url ?? $this->fallback_image_url;
    }

    /**
     * Primary category (first of multiple) for backward compatibility.
     */
    public function getCategoryAttribute(): ?string
    {
        $cats = $this->categories ?? [];

        return is_array($cats) && count($cats) > 0 ? $cats[0] : null;
    }

    /**
     * Get all class schedules for this service.
     */
    public function classSchedules(): HasMany
    {
        return $this->hasMany(ClassSchedule::class);
    }

    /**
     * Get all bookings for this service.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(ServiceBooking::class);
    }

    public function courseModules(): HasMany
    {
        return $this->hasMany(CourseModule::class)->orderBy('order');
    }

    /**
     * Resolve deposit amount for this class (defaults to $20 if not configured).
     */
    public function getResolvedDepositAmount(): float
    {
        return (float) ($this->deposit_amount ?? 20);
    }

    /**
     * Get upcoming available class schedules.
     */
    public function availableSchedules()
    {
        return $this->classSchedules()
            ->where('status', 'scheduled')
            ->where('class_date', '>=', now()->toDateString())
            ->whereRaw('current_students < max_students')
            ->orderBy('class_date')
            ->orderBy('start_time');
    }

    /**
     * Check if service has available spots in upcoming classes.
     */
    public function hasAvailableSpots(): bool
    {
        return $this->availableSchedules()->exists();
    }

    /**
     * Get next available class date.
     */
    public function getNextAvailableDate()
    {
        $schedule = $this->availableSchedules()->first();

        return $schedule ? $schedule->class_date : null;
    }
}
