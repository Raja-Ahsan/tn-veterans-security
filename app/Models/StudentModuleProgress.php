<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentModuleProgress extends Model
{
    protected $table = 'student_module_progress';

    public const PASSING_SCORE = 90;

    protected $fillable = [
        'student_id',
        'service_id',
        'course_module_id',
        'is_completed',
        'best_score',
        'attempts',
        'completed_at',
        'admin_override',
    ];

    protected function casts(): array
    {
        return [
            'is_completed' => 'boolean',
            'admin_override' => 'boolean',
            'completed_at' => 'datetime',
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
}
