<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentVideoProgress extends Model
{
    protected $table = 'student_video_progress';

    protected $fillable = [
        'student_id',
        'service_id',
        'course_module_id',
        'course_module_video_id',
        'video_watched',
        'watched_at',
        'duration_seconds',
        'last_position_seconds',
        'is_completed',
        'best_score',
        'attempts',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'video_watched' => 'boolean',
            'watched_at' => 'datetime',
            'is_completed' => 'boolean',
            'completed_at' => 'datetime',
            'duration_seconds' => 'integer',
            'last_position_seconds' => 'integer',
            'attempts' => 'integer',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function courseModule(): BelongsTo
    {
        return $this->belongsTo(CourseModule::class);
    }

    public function courseModuleVideo(): BelongsTo
    {
        return $this->belongsTo(CourseModuleVideo::class);
    }
}
