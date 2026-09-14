<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class CourseModuleVideo extends Model
{
    protected $fillable = [
        'course_module_id',
        'title',
        'video_url',
        'video_path',
        'original_name',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'order' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::deleting(function (CourseModuleVideo $video): void {
            $video->deleteStoredFile();
        });
    }

    public function courseModule(): BelongsTo
    {
        return $this->belongsTo(CourseModule::class);
    }

    public function quizQuestions(): HasMany
    {
        return $this->hasMany(ModuleQuizQuestion::class)->orderBy('order');
    }

    public function progressRecords(): HasMany
    {
        return $this->hasMany(StudentVideoProgress::class);
    }

    public function displayTitle(): string
    {
        $title = trim((string) $this->title);

        return $title !== '' ? $title : 'Video '.$this->order;
    }

    public function uploadedVideoUrl(): ?string
    {
        $path = trim((string) $this->video_path);
        if ($path === '' || ! Storage::disk('public')->exists($path)) {
            return null;
        }

        return Storage::disk('public')->url($path);
    }

    public function embedVideoUrl(): ?string
    {
        $url = trim((string) $this->video_url);
        if ($url === '') {
            return null;
        }

        if (preg_match('#(?:youtube\.com/embed/|youtube-nocookie\.com/embed/)([A-Za-z0-9_-]{6,})#', $url, $matches)) {
            return 'https://www.youtube.com/embed/'.$matches[1].'?enablejsapi=1&rel=0';
        }

        if (preg_match('#(?:youtube\.com/watch\?v=|youtu\.be/|youtube\.com/shorts/)([A-Za-z0-9_-]{6,})#', $url, $matches)) {
            return 'https://www.youtube.com/embed/'.$matches[1].'?enablejsapi=1&rel=0';
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

    public function hasPlayableVideo(): bool
    {
        return $this->uploadedVideoUrl() !== null || $this->hasEmbeddableVideo();
    }

    public function requiresWatchCompletion(): bool
    {
        return $this->hasPlayableVideo();
    }

    public function deleteStoredFile(): void
    {
        $path = trim((string) $this->video_path);
        if ($path !== '') {
            Storage::disk('public')->delete($path);
        }
    }
}
