<?php

namespace App\Services;

use App\Models\Student;
use App\Notifications\StudentAlert;
use Illuminate\Support\Facades\Log;

class StudentNotifier
{
    public static function push(
        Student $student,
        string $title,
        string $body,
        string $icon = 'bell',
        ?string $url = null,
        string $category = 'general',
    ): void {
        try {
            $student->notify(new StudentAlert($title, $body, $icon, $url, $category));
        } catch (\Throwable $exception) {
            Log::warning('Student in-app notification failed', [
                'student_id' => $student->id,
                'title' => $title,
                'error' => $exception->getMessage(),
            ]);
        }
    }
}
