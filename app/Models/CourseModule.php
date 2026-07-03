<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseModule extends Model
{
    protected $fillable = [
        'service_id',
        'title',
        'content',
        'video_url',
        'image_path',
        'order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'order' => 'integer',
        ];
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function quizQuestions(): HasMany
    {
        return $this->hasMany(ModuleQuizQuestion::class)->orderBy('order');
    }

    public function progressRecords(): HasMany
    {
        return $this->hasMany(StudentModuleProgress::class);
    }
}
