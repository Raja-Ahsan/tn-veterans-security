<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ModuleQuizSession extends Model
{
    public const STATUS_IN_PROGRESS = 'in_progress';

    public const STATUS_SUBMITTED = 'submitted';

    public const STATUS_EXPIRED = 'expired';

    protected $fillable = [
        'student_id',
        'service_id',
        'course_module_id',
        'current_index',
        'answers',
        'started_at',
        'expires_at',
        'status',
        'module_quiz_attempt_id',
        'submitted_at',
    ];

    protected function casts(): array
    {
        return [
            'answers' => 'array',
            'current_index' => 'integer',
            'started_at' => 'datetime',
            'expires_at' => 'datetime',
            'submitted_at' => 'datetime',
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

    public function attempt(): BelongsTo
    {
        return $this->belongsTo(ModuleQuizAttempt::class, 'module_quiz_attempt_id');
    }

    public function isOpen(): bool
    {
        return $this->status === self::STATUS_IN_PROGRESS && $this->expires_at->isFuture();
    }

    public function isExpired(): bool
    {
        return $this->status === self::STATUS_IN_PROGRESS && $this->expires_at->isPast();
    }

    public function remainingSeconds(): int
    {
        return max(0, $this->expires_at->getTimestamp() - now()->getTimestamp());
    }
}
