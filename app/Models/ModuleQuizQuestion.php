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
        'allow_multiple',
        'correct_answer',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'options' => 'array',
            'correct_answer' => 'array',
            'allow_multiple' => 'boolean',
            'order' => 'integer',
        ];
    }

    public function courseModule(): BelongsTo
    {
        return $this->belongsTo(CourseModule::class);
    }

    /**
     * @return list<string>
     */
    public function correctAnswers(): array
    {
        $answers = $this->correct_answer;

        if (! is_array($answers)) {
            return filled($answers) ? [(string) $answers] : [];
        }

        return array_values(array_map('strval', array_filter($answers, fn ($a) => filled($a))));
    }

    /**
     * @param  string|array<int, string>|null  $given
     */
    public function isAnswerCorrect(string|array|null $given): bool
    {
        $correct = $this->correctAnswers();
        $selected = is_array($given)
            ? array_values(array_map('strval', array_filter($given, fn ($a) => filled($a))))
            : (filled($given) ? [(string) $given] : []);

        sort($correct);
        sort($selected);

        return $correct !== [] && $correct === $selected;
    }
}
