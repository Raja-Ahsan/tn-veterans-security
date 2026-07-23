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

        $paidBookings = ServiceBooking::query()
            ->where('student_id', $student->id)
            ->whereIn('status', ['pending', 'confirmed', 'completed'])
            ->whereIn('payment_status', ['deposit_paid', 'fully_paid'])
            ->with('service')
            ->get();

        $serviceIds = $paidBookings->pluck('service_id')->unique();

        $courses = Service::query()
            ->whereIn('id', $serviceIds)
            ->where('has_online_parts', true)
            ->where('is_active', true)
            ->with('courseModules')
            ->orderBy('title')
            ->get()
            ->map(function (Service $service) use ($student) {
                $summary = $this->blendedCourse->progressSummary($student, $service);
                $continueModule = $this->blendedCourse->firstContinueModule($student, $service);
                $service->online_progress = [
                    'completed' => $summary['completed'],
                    'total' => $summary['total'],
                    'percent' => $summary['percent'],
                    'eligible_in_person' => $this->blendedCourse->isEligibleForInPersonTesting($student, $service),
                    'continue_module_id' => $continueModule?->id,
                ];

                return $service;
            });

        $pendingUnlockBookings = ServiceBooking::query()
            ->where('student_id', $student->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('payment_status', 'pending')
            ->whereHas('service', fn ($q) => $q->where('has_online_parts', true)->where('is_active', true))
            ->with('service')
            ->orderByDesc('created_at')
            ->get();

        return view('student.online-courses.index', compact('courses', 'pendingUnlockBookings'));
    }
}
