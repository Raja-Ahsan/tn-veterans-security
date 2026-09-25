<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\GuardTrainingFormRequest;
use App\Models\GuardTrainingForm;
use App\Models\Service;
use App\Models\ServiceBooking;
use App\Models\Student;
use App\Services\GuardTrainingFormService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class GuardTrainingFormController extends Controller
{
    public function __construct(private GuardTrainingFormService $forms) {}

    public function index(Request $request): View
    {
        $forms = GuardTrainingForm::query()
            ->with(['student', 'service'])
            ->when($request->filled('q'), function ($query) use ($request) {
                $q = $request->string('q')->toString();
                $query->where(function ($inner) use ($q) {
                    $inner->where('form_number', 'like', "%{$q}%")
                        ->orWhere('first_name', 'like', "%{$q}%")
                        ->orWhere('last_name', 'like', "%{$q}%")
                        ->orWhere('registration_number', 'like', "%{$q}%")
                        ->orWhereHas('student', fn ($s) => $s->where('name', 'like', "%{$q}%")->orWhere('email', 'like', "%{$q}%"));
                });
            })
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')->toString()))
            ->orderByDesc('updated_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.guard-training-forms.index', compact('forms'));
    }

    public function create(Request $request): View
    {
        $students = Student::query()->orderBy('name')->limit(500)->get(['id', 'name', 'email']);
        $services = Service::query()->where('is_active', true)->orderBy('title')->get(['id', 'title']);
        $selectedStudentId = $request->integer('student_id') ?: null;
        $selectedBookingId = $request->integer('booking_id') ?: null;

        $form = new GuardTrainingForm([
            'weapons' => [
                ['make_model' => '', 'caliber' => '', 'date' => null, 'score' => null],
                ['make_model' => '', 'caliber' => '', 'date' => null, 'score' => null],
            ],
        ]);

        if ($selectedStudentId) {
            $student = Student::query()->find($selectedStudentId);
            $booking = $selectedBookingId
                ? ServiceBooking::query()->where('student_id', $selectedStudentId)->find($selectedBookingId)
                : null;

            if ($student) {
                $form->fill($this->forms->prefillFromStudent($student, $booking));
            }
        }

        $bookings = $selectedStudentId
            ? ServiceBooking::query()
                ->where('student_id', $selectedStudentId)
                ->with('service:id,title')
                ->latest('id')
                ->limit(50)
                ->get()
            : collect();

        return view('admin.guard-training-forms.create', compact('form', 'students', 'services', 'bookings', 'selectedStudentId', 'selectedBookingId'));
    }

    public function store(GuardTrainingFormRequest $request): RedirectResponse
    {
        $student = Student::query()->findOrFail($request->integer('student_id'));
        $booking = $request->filled('service_booking_id')
            ? ServiceBooking::query()->find($request->integer('service_booking_id'))
            : null;

        $payload = $this->payloadFromRequest($request);
        $form = $this->forms->createDraft($student, $booking, Auth::user(), $payload);

        if ($request->boolean('publish')) {
            $this->forms->publish($form, $request->boolean('notify_student', true));

            return redirect()
                ->route('admin.guard-training-forms.show', $form)
                ->with('success', 'Form saved and sent to the student.');
        }

        return redirect()
            ->route('admin.guard-training-forms.edit', $form)
            ->with('success', 'Draft form created. Complete remaining fields, then publish to the student.');
    }

    public function show(GuardTrainingForm $guardTrainingForm): View
    {
        $guardTrainingForm->load(['student', 'service', 'booking', 'creator']);

        return view('admin.guard-training-forms.show', ['form' => $guardTrainingForm]);
    }

    public function edit(GuardTrainingForm $guardTrainingForm): View
    {
        $guardTrainingForm->load(['student', 'service', 'booking']);
        $students = Student::query()->orderBy('name')->limit(500)->get(['id', 'name', 'email']);
        $services = Service::query()->where('is_active', true)->orderBy('title')->get(['id', 'title']);
        $bookings = ServiceBooking::query()
            ->where('student_id', $guardTrainingForm->student_id)
            ->with('service:id,title')
            ->latest('id')
            ->limit(50)
            ->get();

        return view('admin.guard-training-forms.edit', [
            'form' => $guardTrainingForm,
            'students' => $students,
            'services' => $services,
            'bookings' => $bookings,
        ]);
    }

    public function update(GuardTrainingFormRequest $request, GuardTrainingForm $guardTrainingForm): RedirectResponse
    {
        $guardTrainingForm->update($this->payloadFromRequest($request));

        if ($request->boolean('publish')) {
            $this->forms->publish($guardTrainingForm->fresh(), $request->boolean('notify_student', true));

            return redirect()
                ->route('admin.guard-training-forms.show', $guardTrainingForm)
                ->with('success', 'Form updated and sent to the student.');
        }

        return redirect()
            ->route('admin.guard-training-forms.edit', $guardTrainingForm)
            ->with('success', 'Form saved as draft.');
    }

    public function publish(Request $request, GuardTrainingForm $guardTrainingForm): RedirectResponse
    {
        $this->forms->publish($guardTrainingForm, $request->boolean('notify_student', true));

        return redirect()
            ->route('admin.guard-training-forms.show', $guardTrainingForm)
            ->with('success', 'Form published. Student can now view it on their dashboard.');
    }

    public function print(GuardTrainingForm $guardTrainingForm): View
    {
        $guardTrainingForm->load(['student', 'service']);

        return view('guard-training-forms.print', [
            'form' => $guardTrainingForm,
            'forAdmin' => true,
        ]);
    }

    public function destroy(GuardTrainingForm $guardTrainingForm): RedirectResponse
    {
        $guardTrainingForm->delete();

        return redirect()
            ->route('admin.guard-training-forms.index')
            ->with('success', 'Form deleted.');
    }

    /**
     * @return array<string, mixed>
     */
    private function payloadFromRequest(GuardTrainingFormRequest $request): array
    {
        $validated = $request->validated();
        unset($validated['publish'], $validated['notify_student']);

        $validated['weapons'] = $this->forms->normalizeWeapons($validated['weapons'] ?? []);
        $validated['ssn'] = filled($validated['ssn'] ?? null)
            ? Student::digitsOnly((string) $validated['ssn'])
            : null;

        return $validated;
    }
}
