<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CourseModule;
use App\Models\InPersonTestResult;
use App\Models\ModuleQuizAttempt;
use App\Models\ModuleQuizSession;
use App\Models\Service;
use App\Models\ServiceBooking;
use App\Models\Student;
use App\Models\StudentModuleProgress;
use App\Services\BlendedCourseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BlendedCourseAdminController extends Controller
{
    public function __construct(
        private BlendedCourseService $blendedCourse,
    ) {}

    public function studentProgress(Service $service): View
    {
        abort_unless($service->has_online_parts, 404);

        $modules = $this->blendedCourse->getModulesForService($service);
        $bookings = ServiceBooking::query()
            ->where('service_id', $service->id)
            ->whereIn('status', ['pending', 'confirmed', 'completed'])
            ->whereIn('payment_status', ['deposit_paid', 'fully_paid'])
            ->with('student')
            ->orderBy('created_at')
            ->get();

        $progressByStudent = [];
        $testResults = [];

        foreach ($bookings as $booking) {
            $student = $booking->student;
            if (! $student) {
                continue;
            }

            $progressByStudent[$student->id] = $this->blendedCourse->getProgress($student, $service);
            $testResults[$student->id] = InPersonTestResult::query()
                ->where('student_id', $student->id)
                ->where('service_id', $service->id)
                ->latest()
                ->first();
        }

        return view('admin.blended-course.progress', compact(
            'service',
            'modules',
            'bookings',
            'progressByStudent',
            'testResults'
        ));
    }

    public function overrideModule(Request $request, Service $service, Student $student, CourseModule $courseModule): RedirectResponse
    {
        abort_unless($courseModule->service_id === $service->id, 404);

        StudentModuleProgress::updateOrCreate(
            [
                'student_id' => $student->id,
                'course_module_id' => $courseModule->id,
            ],
            [
                'service_id' => $service->id,
                'admin_override' => true,
                'is_completed' => true,
                'best_score' => 100,
                'completed_at' => now(),
            ]
        );

        return back()->with('success', 'Module marked complete (admin override).');
    }

    public function resetModule(Request $request, Service $service, Student $student, CourseModule $courseModule): RedirectResponse
    {
        abort_unless($courseModule->service_id === $service->id, 404);

        StudentModuleProgress::query()
            ->where('student_id', $student->id)
            ->where('course_module_id', $courseModule->id)
            ->delete();

        ModuleQuizAttempt::query()
            ->where('student_id', $student->id)
            ->where('course_module_id', $courseModule->id)
            ->delete();

        ModuleQuizSession::query()
            ->where('student_id', $student->id)
            ->where('course_module_id', $courseModule->id)
            ->delete();

        return back()->with('success', 'Module progress reset. Student can attempt the quiz again (update questions first if needed).');
    }

    public function storeInPersonTest(Request $request, Service $service, Student $student): RedirectResponse
    {
        $validated = $request->validate([
            'result' => 'required|in:passed,failed,needs_remediation',
            'notes' => 'nullable|string|max:2000',
            'class_schedule_id' => 'nullable|exists:class_schedules,id',
        ]);

        InPersonTestResult::create([
            'student_id' => $student->id,
            'service_id' => $service->id,
            'class_schedule_id' => $validated['class_schedule_id'] ?? null,
            'result' => $validated['result'],
            'notes' => $validated['notes'] ?? null,
            'marked_by' => Auth::id(),
            'tested_at' => now(),
        ]);

        return back()->with('success', 'In-person test result saved.');
    }

    public function reorderModules(Request $request, Service $service): RedirectResponse
    {
        $validated = $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer|exists:course_modules,id',
        ]);

        foreach ($validated['order'] as $index => $moduleId) {
            CourseModule::query()
                ->where('service_id', $service->id)
                ->where('id', $moduleId)
                ->update(['order' => $index + 1]);
        }

        return back()->with('success', 'Module order updated.');
    }
}
