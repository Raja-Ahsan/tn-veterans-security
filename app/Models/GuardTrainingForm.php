<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GuardTrainingForm extends Model
{
    public const STATUS_DRAFT = 'draft';

    public const STATUS_PUBLISHED = 'published';

    public const REGISTRATION_TYPES = [
        'unarmed_initial' => 'Unarmed Initial',
        'armed_initial' => 'Armed Initial',
        'unarmed_renewal' => 'Unarmed Renewal',
        'armed_renewal' => 'Armed Renewal',
        'add_change_weapon' => 'Add/Change Weapon',
        'add_classification' => 'Add Classification',
    ];

    protected $fillable = [
        'student_id',
        'service_id',
        'service_booking_id',
        'created_by',
        'form_number',
        'status',
        'registration_type',
        'last_name',
        'first_name',
        'middle_initial',
        'ssn',
        'registration_number',
        'initial_general',
        'initial_general_date',
        'initial_general_score',
        'initial_firearms',
        'initial_firearms_date',
        'initial_firearms_score',
        'initial_marksmanship',
        'initial_marksmanship_date',
        'initial_marksmanship_score',
        'weapons',
        'classroom_renewal',
        'classroom_renewal_date',
        'classroom_renewal_score',
        'range_renewal',
        'range_renewal_date',
        'range_renewal_score',
        'classification_cpr',
        'classification_cpr_date',
        'classification_first_aid',
        'classification_first_aid_date',
        'classification_active_shooter',
        'classification_active_shooter_date',
        'classification_de_escalation',
        'classification_de_escalation_date',
        'classification_safe_restraint',
        'classification_safe_restraint_date',
        'trainer_name',
        'trainer_certification_number',
        'trainer_email',
        'trainer_phone',
        'assistant_trainer_name',
        'assistant_trainer_certification_number',
        'comments',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'ssn' => 'encrypted',
            'weapons' => 'array',
            'initial_general' => 'boolean',
            'initial_firearms' => 'boolean',
            'initial_marksmanship' => 'boolean',
            'classroom_renewal' => 'boolean',
            'range_renewal' => 'boolean',
            'classification_cpr' => 'boolean',
            'classification_first_aid' => 'boolean',
            'classification_active_shooter' => 'boolean',
            'classification_de_escalation' => 'boolean',
            'classification_safe_restraint' => 'boolean',
            'initial_general_date' => 'date',
            'initial_firearms_date' => 'date',
            'initial_marksmanship_date' => 'date',
            'classroom_renewal_date' => 'date',
            'range_renewal_date' => 'date',
            'classification_cpr_date' => 'date',
            'classification_first_aid_date' => 'date',
            'classification_active_shooter_date' => 'date',
            'classification_de_escalation_date' => 'date',
            'classification_safe_restraint_date' => 'date',
            'published_at' => 'datetime',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(ServiceBooking::class, 'service_booking_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED;
    }

    public function registrationTypeLabel(): string
    {
        return self::REGISTRATION_TYPES[$this->registration_type] ?? '—';
    }

    public function formattedSsn(): string
    {
        $digits = Student::digitsOnly((string) ($this->ssn ?? ''));
        if (strlen($digits) !== 9) {
            return (string) ($this->ssn ?? '');
        }

        return substr($digits, 0, 3).' / '.substr($digits, 3, 2).' / '.substr($digits, 5, 4);
    }

    /**
     * @return list<array{make_model: string, caliber: string, date: ?string, score: ?int}>
     */
    public function weaponsList(): array
    {
        $weapons = $this->weapons ?? [];
        if (! is_array($weapons) || $weapons === []) {
            return [
                ['make_model' => '', 'caliber' => '', 'date' => null, 'score' => null],
                ['make_model' => '', 'caliber' => '', 'date' => null, 'score' => null],
            ];
        }

        return array_values(array_map(function ($weapon) {
            return [
                'make_model' => (string) ($weapon['make_model'] ?? ''),
                'caliber' => (string) ($weapon['caliber'] ?? ''),
                'date' => $weapon['date'] ?? null,
                'score' => isset($weapon['score']) && $weapon['score'] !== '' ? (int) $weapon['score'] : null,
            ];
        }, $weapons));
    }

    public function studentDisplayName(): string
    {
        return collect([$this->first_name, $this->middle_initial, $this->last_name])
            ->map(fn ($part) => is_string($part) ? trim($part) : '')
            ->filter(fn ($part) => $part !== '')
            ->implode(' ');
    }
}
