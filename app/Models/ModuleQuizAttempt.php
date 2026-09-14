<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ModuleQuizAttempt extends Model
{
    protected $fillable = [
        'student_id',
        'course_module_id',
        'course_module_video_id',
        'score',
        'passed',
        'answers',
    ];

    protected function casts(): array
    {
        return [
            'passed' => 'boolean',
            'answers' => 'array',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
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
