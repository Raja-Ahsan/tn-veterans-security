<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClassNotification extends Model
{
    protected $fillable = [
        'class_schedule_id',
        'sent_by',
        'notification_type',
        'delivery_method',
        'message',
        'student_ids',
        'sent_count',
        'failed_count',
    ];

    protected function casts(): array
    {
        return [
            'student_ids' => 'array',
        ];
    }

    public function classSchedule(): BelongsTo
    {
        return $this->belongsTo(ClassSchedule::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sent_by');
    }
}
