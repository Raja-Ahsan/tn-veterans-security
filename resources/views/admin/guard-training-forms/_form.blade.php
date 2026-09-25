@php
    use App\Models\GuardTrainingForm;
    $weapons = old('weapons', $form->weaponsList());
    while (count($weapons) < 2) {
        $weapons[] = ['make_model' => '', 'caliber' => '', 'date' => null, 'score' => null];
    }
    $inputClass = 'w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm shadow-sm transition focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-100';
    $labelClass = 'mb-1.5 block text-sm font-semibold text-gray-700';
@endphp

@if($errors->any())
    <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
        <p class="font-semibold"><i class="fas fa-exclamation-circle mr-1"></i> Please fix these issues before saving:</p>
        <ul class="mt-2 list-disc space-y-1 pl-5">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- 1. Student & class --}}
<section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
    <div class="mb-5 flex items-start gap-3 border-b border-gray-100 pb-4">
        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-green-600 text-sm font-bold text-white">1</span>
        <div>
            <h3 class="text-lg font-bold text-gray-900">Student & class</h3>
            <p class="text-sm text-gray-500">Pick the student first — personal details auto-fill when available.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <div>
            <label class="{{ $labelClass }}">Student <span class="text-red-500">*</span></label>
            <select name="student_id" required
                    @if(! $form->exists)
                        onchange="window.location='{{ route('admin.guard-training-forms.create') }}?student_id='+this.value"
                    @endif
                    class="{{ $inputClass }}" @disabled($form->exists)>
                <option value="">Select student…</option>
                @foreach($students as $student)
                    <option value="{{ $student->id }}" @selected((int) old('student_id', $form->student_id) === $student->id)>
                        {{ $student->name }} ({{ $student->email }})
                    </option>
                @endforeach
            </select>
            @if($form->exists)
                <input type="hidden" name="student_id" value="{{ $form->student_id }}">
            @endif
        </div>
        <div>
            <label class="{{ $labelClass }}">Linked booking <span class="font-normal text-gray-400">(optional)</span></label>
            <select name="service_booking_id" class="{{ $inputClass }}">
                <option value="">None</option>
                @foreach($bookings as $booking)
                    <option value="{{ $booking->id }}" @selected((int) old('service_booking_id', $form->service_booking_id) === $booking->id)>
                        #{{ $booking->id }} — {{ $booking->service?->title }} ({{ $booking->status }})
                    </option>
                @endforeach
            </select>
        </div>
        <div class="md:col-span-2">
            <label class="{{ $labelClass }}">Class / service <span class="font-normal text-gray-400">(optional)</span></label>
            <select name="service_id" class="{{ $inputClass }}">
                <option value="">None</option>
                @foreach($services as $service)
                    <option value="{{ $service->id }}" @selected((int) old('service_id', $form->service_id) === $service->id)>{{ $service->title }}</option>
                @endforeach
            </select>
        </div>
    </div>
</section>

{{-- 2. Registration & personal --}}
<section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
    <div class="mb-5 flex items-start gap-3 border-b border-gray-100 pb-4">
        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-green-600 text-sm font-bold text-white">2</span>
        <div>
            <h3 class="text-lg font-bold text-gray-900">Registration type & personal info</h3>
            <p class="text-sm text-gray-500">Matches the top of Form IN-1144.</p>
        </div>
    </div>

    <div class="mb-5">
        <p class="{{ $labelClass }}">Registration type</p>
        <div class="grid grid-cols-1 gap-2 sm:grid-cols-2 xl:grid-cols-3">
            @foreach(GuardTrainingForm::REGISTRATION_TYPES as $value => $label)
                <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-gray-200 bg-gray-50/80 px-3.5 py-3 text-sm transition hover:border-green-300 hover:bg-green-50/50 has-[:checked]:border-green-500 has-[:checked]:bg-green-50">
                    <input type="radio" name="registration_type" value="{{ $value }}"
                           @checked(old('registration_type', $form->registration_type) === $value)
                           class="text-green-600 focus:ring-green-500">
                    <span class="font-medium text-gray-800">{{ $label }}</span>
                </label>
            @endforeach
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
        <div>
            <label class="{{ $labelClass }}">Last name <span class="text-red-500">*</span></label>
            <input type="text" name="last_name" value="{{ old('last_name', $form->last_name) }}" required class="{{ $inputClass }}">
        </div>
        <div>
            <label class="{{ $labelClass }}">First name <span class="text-red-500">*</span></label>
            <input type="text" name="first_name" value="{{ old('first_name', $form->first_name) }}" required class="{{ $inputClass }}">
        </div>
        <div>
            <label class="{{ $labelClass }}">Middle initial</label>
            <input type="text" name="middle_initial" value="{{ old('middle_initial', $form->middle_initial) }}" maxlength="10" class="{{ $inputClass }}">
        </div>
        <div>
            <label class="{{ $labelClass }}">Registration #</label>
            <input type="text" name="registration_number" value="{{ old('registration_number', $form->registration_number) }}" class="{{ $inputClass }}">
        </div>
        <div class="md:col-span-2">
            <label class="{{ $labelClass }}">Social Security Number</label>
            <input type="text" name="ssn" value="{{ old('ssn', $form->ssn) }}" placeholder="###-##-####" class="{{ $inputClass }}">
            <p class="mt-1.5 text-xs text-gray-500"><i class="fas fa-lock mr-1 text-gray-400"></i> Stored encrypted. Shown on the printed state form.</p>
        </div>
    </div>
</section>

{{-- 3. Initial training --}}
<section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
    <div class="mb-5 flex items-start gap-3 border-b border-gray-100 pb-4">
        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-green-600 text-sm font-bold text-white">3</span>
        <div>
            <h3 class="text-lg font-bold text-gray-900">Initial training</h3>
            <p class="text-sm text-gray-500">Check each completed training and enter date + score.</p>
        </div>
    </div>

    <div class="space-y-3">
        @foreach([
            ['initial_general', 'Initial – Four (4) Hours General Guard Training'],
            ['initial_firearms', 'Initial – Eight (8) Hours Classroom Firearms Training'],
            ['initial_marksmanship', 'Initial – Four (4) Hours Marksmanship Training'],
        ] as [$key, $label])
            <div class="grid grid-cols-1 gap-3 rounded-xl border border-gray-100 bg-slate-50/80 p-4 md:grid-cols-12 md:items-center">
                <label class="flex items-start gap-3 text-sm font-medium text-gray-800 md:col-span-6">
                    <input type="checkbox" name="{{ $key }}" value="1" @checked(old($key, $form->{$key})) class="mt-0.5 rounded border-gray-400 text-green-600 focus:ring-green-500">
                    <span>{{ $label }}</span>
                </label>
                <div class="md:col-span-3">
                    <label class="mb-1 block text-xs font-semibold text-gray-500">Date</label>
                    <input type="date" name="{{ $key }}_date" value="{{ old($key.'_date', optional($form->{$key.'_date'})->format('Y-m-d')) }}" class="{{ $inputClass }}">
                </div>
                <div class="md:col-span-3">
                    <label class="mb-1 block text-xs font-semibold text-gray-500">Score %</label>
                    <input type="number" min="0" max="100" name="{{ $key }}_score" value="{{ old($key.'_score', $form->{$key.'_score'}) }}" class="{{ $inputClass }}">
                </div>
            </div>
        @endforeach
    </div>
</section>

{{-- 4. Weapons --}}
<section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
    <div class="mb-5 flex items-start gap-3 border-b border-gray-100 pb-4">
        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-green-600 text-sm font-bold text-white">4</span>
        <div>
            <h3 class="text-lg font-bold text-gray-900">Weapon information</h3>
            <p class="text-sm text-gray-500">Make/model, caliber, qualification date, and score.</p>
        </div>
    </div>

    <div class="space-y-3">
        @foreach($weapons as $index => $weapon)
            <div class="rounded-xl border border-gray-100 bg-slate-50/80 p-4">
                <p class="mb-3 text-xs font-semibold uppercase tracking-wide text-gray-500">Weapon {{ $index + 1 }}</p>
                <div class="grid grid-cols-1 gap-3 md:grid-cols-4">
                    <div>
                        <label class="mb-1 block text-xs font-semibold text-gray-500">Make / Model</label>
                        <input type="text" name="weapons[{{ $index }}][make_model]" value="{{ $weapon['make_model'] ?? '' }}" class="{{ $inputClass }}">
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold text-gray-500">Caliber</label>
                        <input type="text" name="weapons[{{ $index }}][caliber]" value="{{ $weapon['caliber'] ?? '' }}" class="{{ $inputClass }}">
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold text-gray-500">Date</label>
                        <input type="date" name="weapons[{{ $index }}][date]" value="{{ $weapon['date'] ?? '' }}" class="{{ $inputClass }}">
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold text-gray-500">Score %</label>
                        <input type="number" min="0" max="100" name="weapons[{{ $index }}][score]" value="{{ $weapon['score'] ?? '' }}" class="{{ $inputClass }}">
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</section>

{{-- 5. Renewals & classifications --}}
<section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
    <div class="mb-5 flex items-start gap-3 border-b border-gray-100 pb-4">
        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-green-600 text-sm font-bold text-white">5</span>
        <div>
            <h3 class="text-lg font-bold text-gray-900">Renewals & classifications</h3>
            <p class="text-sm text-gray-500">Classroom / range renewals and additional certifications.</p>
        </div>
    </div>

    <div class="mb-4 space-y-3">
        @foreach([
            ['classroom_renewal', 'Classroom Renewal'],
            ['range_renewal', 'Firing Range Renewal'],
        ] as [$key, $label])
            <div class="grid grid-cols-1 gap-3 rounded-xl border border-gray-100 bg-slate-50/80 p-4 md:grid-cols-12 md:items-center">
                <label class="flex items-center gap-3 text-sm font-medium text-gray-800 md:col-span-6">
                    <input type="checkbox" name="{{ $key }}" value="1" @checked(old($key, $form->{$key})) class="rounded border-gray-400 text-green-600 focus:ring-green-500">
                    {{ $label }}
                </label>
                <div class="md:col-span-3">
                    <label class="mb-1 block text-xs font-semibold text-gray-500">Date</label>
                    <input type="date" name="{{ $key }}_date" value="{{ old($key.'_date', optional($form->{$key.'_date'})->format('Y-m-d')) }}" class="{{ $inputClass }}">
                </div>
                <div class="md:col-span-3">
                    <label class="mb-1 block text-xs font-semibold text-gray-500">Score %</label>
                    <input type="number" min="0" max="100" name="{{ $key }}_score" value="{{ old($key.'_score', $form->{$key.'_score'}) }}" class="{{ $inputClass }}">
                </div>
            </div>
        @endforeach
    </div>

    <p class="mb-3 text-xs font-semibold uppercase tracking-wide text-gray-500">Additional classifications</p>
    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
        @foreach([
            ['classification_cpr', 'CPR'],
            ['classification_first_aid', 'First Aid'],
            ['classification_active_shooter', 'Active Shooter'],
            ['classification_de_escalation', 'De-escalation'],
            ['classification_safe_restraint', 'Safe Restraint'],
        ] as [$key, $label])
            <div class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-gray-100 bg-slate-50/80 px-4 py-3">
                <label class="inline-flex items-center gap-2.5 text-sm font-medium text-gray-800">
                    <input type="checkbox" name="{{ $key }}" value="1" @checked(old($key, $form->{$key})) class="rounded border-gray-400 text-green-600 focus:ring-green-500">
                    {{ $label }}
                </label>
                <input type="date" name="{{ $key }}_date" value="{{ old($key.'_date', optional($form->{$key.'_date'})->format('Y-m-d')) }}"
                       class="rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-100">
            </div>
        @endforeach
    </div>
</section>

{{-- 6. Trainer --}}
<section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
    <div class="mb-5 flex items-start gap-3 border-b border-gray-100 pb-4">
        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-green-600 text-sm font-bold text-white">6</span>
        <div>
            <h3 class="text-lg font-bold text-gray-900">Trainer information</h3>
            <p class="text-sm text-gray-500">Trainer and assistant details for the printed form.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <div>
            <label class="{{ $labelClass }}">Trainer name</label>
            <input type="text" name="trainer_name" value="{{ old('trainer_name', $form->trainer_name) }}" class="{{ $inputClass }}">
        </div>
        <div>
            <label class="{{ $labelClass }}">Certification #</label>
            <input type="text" name="trainer_certification_number" value="{{ old('trainer_certification_number', $form->trainer_certification_number) }}" class="{{ $inputClass }}">
        </div>
        <div>
            <label class="{{ $labelClass }}">Email</label>
            <input type="email" name="trainer_email" value="{{ old('trainer_email', $form->trainer_email) }}" class="{{ $inputClass }}">
        </div>
        <div>
            <label class="{{ $labelClass }}">Telephone</label>
            <input type="text" name="trainer_phone" value="{{ old('trainer_phone', $form->trainer_phone) }}" class="{{ $inputClass }}">
        </div>
        <div>
            <label class="{{ $labelClass }}">Assistant trainer</label>
            <input type="text" name="assistant_trainer_name" value="{{ old('assistant_trainer_name', $form->assistant_trainer_name) }}" class="{{ $inputClass }}">
        </div>
        <div>
            <label class="{{ $labelClass }}">Assistant certification #</label>
            <input type="text" name="assistant_trainer_certification_number" value="{{ old('assistant_trainer_certification_number', $form->assistant_trainer_certification_number) }}" class="{{ $inputClass }}">
        </div>
        <div class="md:col-span-2">
            <label class="{{ $labelClass }}">Comments</label>
            <textarea name="comments" rows="3" class="{{ $inputClass }}">{{ old('comments', $form->comments) }}</textarea>
        </div>
    </div>
</section>

{{-- Actions --}}
<section class="rounded-xl border border-green-200 bg-gradient-to-br from-green-50 to-white p-5 shadow-sm sm:p-6">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div class="space-y-2.5 text-sm text-gray-700">
            <p class="text-xs font-semibold uppercase tracking-wide text-green-700">When saving</p>
            <label class="flex items-center gap-2.5 font-semibold">
                <input type="checkbox" name="publish" value="1" class="rounded border-gray-400 text-green-600 focus:ring-green-500">
                Publish to student dashboard
            </label>
            <label class="flex items-center gap-2.5">
                <input type="checkbox" name="notify_student" value="1" checked class="rounded border-gray-400 text-green-600 focus:ring-green-500">
                Email student when published
            </label>
        </div>
        <div class="flex flex-wrap gap-2">
            <button type="submit" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-sm font-semibold text-gray-800 hover:bg-gray-50">
                <i class="fas fa-save text-xs"></i> Save Draft
            </button>
            <button type="submit" name="publish" value="1" class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-5 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-green-700">
                <i class="fas fa-paper-plane text-xs"></i> Save & Send to Student
            </button>
        </div>
    </div>
</section>
