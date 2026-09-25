<?php

namespace App\Services;

use App\Mail\GuardTrainingFormPublishedMail;
use App\Models\GuardTrainingForm;
use App\Models\Service;
use App\Models\ServiceBooking;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class GuardTrainingFormService
{
    /**
     * Prefill form fields from the student (and optional booking/class).
     *
     * @return array<string, mixed>
     */
    public function prefillFromStudent(Student $student, ?ServiceBooking $booking = null): array
    {
        $student->loadMissing(['bookings.classSchedule.instructorRecord']);

        $first = $student->first_name;
        $middle = $student->middle_name;
        $last = $student->last_name;

        if (blank($first) || blank($last)) {
            [$first, $middle, $last] = Student::splitDisplayName((string) $student->name);
        }

        $middleInitial = filled($middle) ? Str::upper(Str::substr(trim((string) $middle), 0, 1)) : null;

        $payload = [
            'student_id' => $student->id,
            'last_name' => $last,
            'first_name' => $first,
            'middle_initial' => $middleInitial,
            'ssn' => $student->ssn,
            'registration_number' => $student->security_registration_number,
            'weapons' => [
                ['make_model' => '', 'caliber' => '', 'date' => null, 'score' => null],
                ['make_model' => '', 'caliber' => '', 'date' => null, 'score' => null],
            ],
        ];

        if ($booking) {
            $booking->loadMissing(['service', 'classSchedule.instructorRecord']);
            $payload['service_id'] = $booking->service_id;
            $payload['service_booking_id'] = $booking->id;

            $instructor = $booking->classSchedule?->instructorRecord;
            if ($instructor) {
                $payload['trainer_name'] = $instructor->name;
                $payload['trainer_email'] = $instructor->email;
                $payload['trainer_phone'] = $instructor->phone;
            } elseif (filled($booking->classSchedule?->instructor)) {
                $payload['trainer_name'] = $booking->classSchedule->instructor;
            }

            $payload = array_merge($payload, $this->guessRegistrationType($booking->service));
            $payload = array_merge($payload, $this->scoresFromOnlineProgress($student, $booking->service));
        }

        return $payload;
    }

    /**
     * @return array<string, mixed>
     */
    public function guessRegistrationType(?Service $service): array
    {
        if (! $service) {
            return [];
        }

        $title = Str::lower($service->title);

        if (Str::contains($title, ['renewal', 'renew'])) {
            $armed = Str::contains($title, ['armed', 'firearm', 'handgun', 'weapon']);

            return ['registration_type' => $armed ? 'armed_renewal' : 'unarmed_renewal'];
        }

        if (Str::contains($title, ['weapon', 'add/change', 'caliber'])) {
            return ['registration_type' => 'add_change_weapon'];
        }

        if (Str::contains($title, ['armed', 'firearm', 'handgun', 'marksmanship'])) {
            return ['registration_type' => 'armed_initial'];
        }

        if (Str::contains($title, ['unarmed', 'guard'])) {
            return ['registration_type' => 'unarmed_initial'];
        }

        return [];
    }

    /**
     * Pull best online module score into the most relevant written score field.
     *
     * @return array<string, mixed>
     */
    public function scoresFromOnlineProgress(Student $student, ?Service $service): array
    {
        if (! $service?->has_online_parts) {
            return [];
        }

        $progress = $student->moduleProgress()
            ->where('service_id', $service->id)
            ->where('is_completed', true)
            ->get();

        if ($progress->isEmpty()) {
            return [];
        }

        $scores = $progress->pluck('best_score')->filter(fn ($score) => $score !== null)->map(fn ($score) => (int) $score);
        if ($scores->isEmpty()) {
            return [];
        }

        $average = (int) round($scores->avg());
        $completedAt = $progress->max('completed_at');

        return [
            'initial_general' => true,
            'initial_general_date' => $completedAt ? date('Y-m-d', strtotime((string) $completedAt)) : now()->toDateString(),
            'initial_general_score' => min(100, max(0, $average)),
        ];
    }

    public function createDraft(Student $student, ?ServiceBooking $booking, User $admin, array $overrides = []): GuardTrainingForm
    {
        $data = array_merge(
            $this->prefillFromStudent($student, $booking),
            $overrides,
            [
                'created_by' => $admin->id,
                'form_number' => $this->generateFormNumber(),
                'status' => GuardTrainingForm::STATUS_DRAFT,
            ]
        );

        return GuardTrainingForm::create($data);
    }

    public function publish(GuardTrainingForm $form, bool $notifyStudent = true): GuardTrainingForm
    {
        $form->update([
            'status' => GuardTrainingForm::STATUS_PUBLISHED,
            'published_at' => $form->published_at ?? now(),
        ]);

        $form->loadMissing(['student', 'service']);

        if ($notifyStudent && $form->student?->email) {
            Mail::to($form->student->email)->send(new GuardTrainingFormPublishedMail($form));
        }

        return $form->fresh(['student', 'service']);
    }

    /**
     * @param  array<int, array<string, mixed>>  $weapons
     * @return list<array{make_model: string, caliber: string, date: ?string, score: ?int}>
     */
    public function normalizeWeapons(array $weapons): array
    {
        $normalized = [];

        foreach ($weapons as $weapon) {
            $make = trim((string) ($weapon['make_model'] ?? ''));
            $caliber = trim((string) ($weapon['caliber'] ?? ''));
            $date = filled($weapon['date'] ?? null) ? (string) $weapon['date'] : null;
            $score = filled($weapon['score'] ?? null) ? (int) $weapon['score'] : null;

            if ($make === '' && $caliber === '' && $date === null && $score === null) {
                continue;
            }

            $normalized[] = [
                'make_model' => $make,
                'caliber' => $caliber,
                'date' => $date,
                'score' => $score,
            ];
        }

        return $normalized;
    }

    private function generateFormNumber(): string
    {
        do {
            $number = 'IN1144-'.now()->format('Y').'-'.strtoupper(Str::random(6));
        } while (GuardTrainingForm::query()->where('form_number', $number)->exists());

        return $number;
    }
}
