<?php

namespace App\Http\Requests\Admin;

use App\Models\GuardTrainingForm;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GuardTrainingFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'student_id' => ['required', 'exists:students,id'],
            'service_id' => ['nullable', 'exists:services,id'],
            'service_booking_id' => ['nullable', 'exists:service_bookings,id'],
            'registration_type' => ['nullable', Rule::in(array_keys(GuardTrainingForm::REGISTRATION_TYPES))],
            'last_name' => ['required', 'string', 'max:120'],
            'first_name' => ['required', 'string', 'max:120'],
            'middle_initial' => ['nullable', 'string', 'max:10'],
            'ssn' => ['nullable', 'string', 'max:20'],
            'registration_number' => ['nullable', 'string', 'max:50'],

            'initial_general' => ['sometimes', 'boolean'],
            'initial_general_date' => ['nullable', 'date'],
            'initial_general_score' => ['nullable', 'integer', 'min:0', 'max:100'],
            'initial_firearms' => ['sometimes', 'boolean'],
            'initial_firearms_date' => ['nullable', 'date'],
            'initial_firearms_score' => ['nullable', 'integer', 'min:0', 'max:100'],
            'initial_marksmanship' => ['sometimes', 'boolean'],
            'initial_marksmanship_date' => ['nullable', 'date'],
            'initial_marksmanship_score' => ['nullable', 'integer', 'min:0', 'max:100'],

            'weapons' => ['nullable', 'array', 'max:6'],
            'weapons.*.make_model' => ['nullable', 'string', 'max:120'],
            'weapons.*.caliber' => ['nullable', 'string', 'max:40'],
            'weapons.*.date' => ['nullable', 'date'],
            'weapons.*.score' => ['nullable', 'integer', 'min:0', 'max:100'],

            'classroom_renewal' => ['sometimes', 'boolean'],
            'classroom_renewal_date' => ['nullable', 'date'],
            'classroom_renewal_score' => ['nullable', 'integer', 'min:0', 'max:100'],
            'range_renewal' => ['sometimes', 'boolean'],
            'range_renewal_date' => ['nullable', 'date'],
            'range_renewal_score' => ['nullable', 'integer', 'min:0', 'max:100'],

            'classification_cpr' => ['sometimes', 'boolean'],
            'classification_cpr_date' => ['nullable', 'date'],
            'classification_first_aid' => ['sometimes', 'boolean'],
            'classification_first_aid_date' => ['nullable', 'date'],
            'classification_active_shooter' => ['sometimes', 'boolean'],
            'classification_active_shooter_date' => ['nullable', 'date'],
            'classification_de_escalation' => ['sometimes', 'boolean'],
            'classification_de_escalation_date' => ['nullable', 'date'],
            'classification_safe_restraint' => ['sometimes', 'boolean'],
            'classification_safe_restraint_date' => ['nullable', 'date'],

            'trainer_name' => ['nullable', 'string', 'max:120'],
            'trainer_certification_number' => ['nullable', 'string', 'max:50'],
            'trainer_email' => ['nullable', 'email', 'max:120'],
            'trainer_phone' => ['nullable', 'string', 'max:40'],
            'assistant_trainer_name' => ['nullable', 'string', 'max:120'],
            'assistant_trainer_certification_number' => ['nullable', 'string', 'max:50'],
            'comments' => ['nullable', 'string', 'max:2000'],
            'publish' => ['sometimes', 'boolean'],
            'notify_student' => ['sometimes', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'student_id.required' => 'Select a student for this form.',
            'last_name.required' => 'Last name is required.',
            'first_name.required' => 'First name is required.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $boolFields = [
            'initial_general',
            'initial_firearms',
            'initial_marksmanship',
            'classroom_renewal',
            'range_renewal',
            'classification_cpr',
            'classification_first_aid',
            'classification_active_shooter',
            'classification_de_escalation',
            'classification_safe_restraint',
            'publish',
            'notify_student',
        ];

        $merged = [];
        foreach ($boolFields as $field) {
            $merged[$field] = $this->boolean($field);
        }

        $this->merge($merged);
    }
}
