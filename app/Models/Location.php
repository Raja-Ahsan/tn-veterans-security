<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    protected $fillable = [
        'name',
        'address',
        'city',
        'state',
        'zip',
        'notes',
        'is_active',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'order' => 'integer',
        ];
    }

    public function classSchedules(): HasMany
    {
        return $this->hasMany(ClassSchedule::class);
    }

    public function getDisplayNameAttribute(): string
    {
        $parts = array_filter([$this->name, $this->city, $this->state]);

        return implode(', ', $parts);
    }
}
