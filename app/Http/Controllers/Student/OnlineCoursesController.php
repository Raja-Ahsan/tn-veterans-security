<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceBooking;
use App\Services\BlendedCourseService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class OnlineCoursesController extends Controller
{
    public function __construct(private BlendedCourseService $blendedCourse) {}

    public function index(): View
    {
        $student = Auth::guard('student')->user();

        $serviceIds = ServiceBooking::query()
            ->where('student_id', $student->id)
            ->whereIn('status', ['pending', 'confirmed', 'completed'])
            ->whereIn('payment_status', ['deposit_paid', 'fully_paid'])
            ->pluck('service_id')
            ->unique();

        $courses = Service::query()
            ->whereIn('id', $serviceIds)
            ->where('has_online_parts', true)
            ->where('is_active', true)
            ->with('courseModules')
            ->orderBy('title')
            ->get()
            ->map(function (Service $service) use ($student) {
                $modules = $this->blendedCourse->getModulesForService($service);
                $progress = $this->blendedCourse->getProgress($student, $service);
                $completed = $progress->where('is_completed', true)->count();
                $service->online_progress = [
                    'completed' => $completed,
                    'total' => $modules->count(),
                    'eligible_in_person' => $this->blendedCourse->isEligibleForInPersonTesting($student, $service),
                ];

                return $service;
            });

        return view('student.online-courses.index', compact('courses'));
    }
}
