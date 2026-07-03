<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ModuleQuizQuestion extends Model
{
    protected $fillable = [
        'course_module_id',
        'question',
        'options',
        'correct_answer',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'options' => 'array',
            'order' => 'integer',
        ];
    }

    public function courseModule(): BelongsTo
    {
        return $this->belongsTo(CourseModule::class);
    }
}
