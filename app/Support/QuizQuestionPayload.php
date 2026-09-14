<?php

namespace App\Support;

class QuizQuestionPayload
{
    /**
     * Keep only complete quiz questions. Incomplete rows (empty prompt, fewer than
     * two options, or no correct answer) are dropped so optional quizzes do not block save.
     *
     * @return list<array{question: string, options: list<string>, allow_multiple: bool, correct_answer: list<string>}>
     */
    public static function normalize(mixed $raw): array
    {
        return collect(is_array($raw) ? $raw : [])
            ->filter(fn ($question): bool => is_array($question))
            ->map(function (array $question): array {
                $options = collect($question['options'] ?? [])
                    ->map(fn ($option) => is_string($option) ? trim($option) : $option)
                    ->filter(fn ($option) => filled($option))
                    ->values()
                    ->all();

                $allowMultiple = filter_var($question['allow_multiple'] ?? false, FILTER_VALIDATE_BOOLEAN);
                $correctRaw = $question['correct_answer'] ?? [];
                if (! is_array($correctRaw)) {
                    $correctRaw = filled($correctRaw) ? [$correctRaw] : [];
                }

                $correct = collect($correctRaw)
                    ->map(fn ($answer) => is_string($answer) ? trim($answer) : $answer)
                    ->filter(fn ($answer) => filled($answer) && in_array($answer, $options, true))
                    ->unique()
                    ->values()
                    ->all();

                if ($correct === [] && $options !== []) {
                    $correct = [$options[0]];
                }

                if (! $allowMultiple && count($correct) > 1) {
                    $correct = [reset($correct)];
                }

                return [
                    'question' => trim((string) ($question['question'] ?? '')),
                    'options' => $options,
                    'allow_multiple' => $allowMultiple,
                    'correct_answer' => $correct,
                ];
            })
            ->filter(function (array $question): bool {
                return $question['question'] !== ''
                    && count($question['options']) >= 2
                    && $question['correct_answer'] !== [];
            })
            ->values()
            ->all();
    }
}
