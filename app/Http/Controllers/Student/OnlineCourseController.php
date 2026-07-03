<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\CourseModule;
use App\Models\Service;
use App\Services\BlendedCourseCompletionService;
use App\Services\BlendedCourseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OnlineCourseController extends Controller
{
    public function __construct(
        private BlendedCourseService $blendedCourse,
        private BlendedCourseCompletionService $completionService
    ) {}

    public function index(Service $service)
    {
        if (! $service->has_online_parts) {
            abort(404);
        }

        $student = Auth::guard('student')->user();
        $modules = $this->blendedCourse->getModulesForService($service);
        $progress = $this->blendedCourse->getProgress($student, $service);
        $eligible = $this->blendedCourse->isEligibleForInPersonTesting($student, $service);

        return view('student.online-course.index', compact('service', 'modules', 'progress', 'eligible'));
    }

    public function show(Service $service, CourseModule $courseModule)
    {
        if ($courseModule->service_id !== $service->id || ! $service->has_online_parts) {
            abort(404);
        }

        $student = Auth::guard('student')->user();
        $modules = $this->blendedCourse->getModulesForService($service);
        $progress = $this->blendedCourse->getProgress($student, $service);

        if (! $this->blendedCourse->canAccessModule($student, $courseModule, $progress, $modules)) {
            return redirect()->route('student.online-course.index', $service)
                ->with('error', 'Complete the previous module with 90% or higher before continuing.');
        }

        $courseModule->load('quizQuestions');
        $moduleProgress = $progress->get($courseModule->id);

        return view('student.online-course.module', compact('service', 'courseModule', 'moduleProgress', 'modules'));
    }

    public function submitQuiz(Request $request, Service $service, CourseModule $courseModule)
    {
        if ($courseModule->service_id !== $service->id) {
            abort(404);
        }

        $student = Auth::guard('student')->user();
        $modules = $this->blendedCourse->getModulesForService($service);
        $progress = $this->blendedCourse->getProgress($student, $service);

        if (! $this->blendedCourse->canAccessModule($student, $courseModule, $progress, $modules)) {
            return back()->with('error', 'Module is locked.');
        }

        $result = $this->blendedCourse->submitQuiz($student, $courseModule, $request->input('answers', []));

        if ($result['passed']) {
            $this->completionService->notifyIfCourseCompleted($student, $service, $this->blendedCourse, $courseModule);

            return redirect()->route('student.online-course.index', $service)
                ->with('success', "Quiz passed with {$result['score']}%! You may continue to the next module.");
        }

        return back()->with('error', "Score: {$result['score']}%. You need 90% or higher to pass. Please try again.");
    }
}
