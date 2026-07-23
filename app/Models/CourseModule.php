<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

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
        'passing_score',
        'max_attempts',
        'materials',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'order' => 'integer',
            'quiz_time_limit_minutes' => 'integer',
            'passing_score' => 'integer',
            'max_attempts' => 'integer',
            'materials' => 'array',
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

    public function passingScore(): int
    {
        $score = (int) ($this->passing_score ?? 0);

        return $score > 0 ? min($score, 100) : 90;
    }

    public function maxAttempts(): int
    {
        $attempts = (int) ($this->max_attempts ?? 0);

        return $attempts > 0 ? min($attempts, 20) : 1;
    }

    /**
     * @return list<array{path: string, original_name: string, url: string}>
     */
    public function materialFiles(): array
    {
        $materials = $this->materials ?? [];
        $files = [];

        foreach ($materials as $material) {
            $path = is_array($material) ? ($material['path'] ?? null) : null;
            if (! filled($path) || ! Storage::disk('public')->exists($path)) {
                continue;
            }

            $files[] = [
                'path' => $path,
                'original_name' => is_array($material)
                    ? (string) ($material['original_name'] ?? basename($path))
                    : basename($path),
                'url' => Storage::disk('public')->url($path),
            ];
        }

        return $files;
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
