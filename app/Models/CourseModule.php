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
        'quiz_time_limit_minutes',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'order' => 'integer',
            'quiz_time_limit_minutes' => 'integer',
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

    /**
     * Convert stored video URL into an iframe-safe embed URL when possible.
     */
    public function embedVideoUrl(): ?string
    {
        $url = trim((string) $this->video_url);
        if ($url === '') {
            return null;
        }

        if (preg_match('#(?:youtube\.com/embed/|youtube-nocookie\.com/embed/)([A-Za-z0-9_-]{6,})#', $url, $matches)) {
            return 'https://www.youtube.com/embed/'.$matches[1];
        }

        if (preg_match('#(?:youtube\.com/watch\?v=|youtu\.be/|youtube\.com/shorts/)([A-Za-z0-9_-]{6,})#', $url, $matches)) {
            return 'https://www.youtube.com/embed/'.$matches[1];
        }

        if (preg_match('#vimeo\.com/(?:video/)?(\d+)#', $url, $matches)) {
            return 'https://player.vimeo.com/video/'.$matches[1];
        }

        if (preg_match('#player\.vimeo\.com/video/(\d+)#', $url, $matches)) {
            return 'https://player.vimeo.com/video/'.$matches[1];
        }

        return null;
    }

    public function hasEmbeddableVideo(): bool
    {
        return $this->embedVideoUrl() !== null;
    }

    public function hasExternalVideoLink(): bool
    {
        $url = trim((string) $this->video_url);

        return $url !== '' && filter_var($url, FILTER_VALIDATE_URL) && ! $this->hasEmbeddableVideo();
    }
}
