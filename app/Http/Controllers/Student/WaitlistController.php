<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ClassSchedule;
use App\Models\WaitlistEntry;
use App\Services\AdminNotifier;
use Illuminate\Support\Facades\Auth;

class WaitlistController extends Controller
{
    public function store(ClassSchedule $classSchedule)
    {
        $student = Auth::guard('student')->user();

        if ($classSchedule->hasAvailableSpots()) {
            return back()->with('error', 'This class still has open seats. Please enroll directly.');
        }

        $existing = WaitlistEntry::where('class_schedule_id', $classSchedule->id)
            ->where('student_id', $student->id)
            ->where('status', 'waiting')
            ->exists();

        if ($existing) {
            return back()->with('info', 'You are already on the waitlist for this class.');
        }

        WaitlistEntry::create([
            'class_schedule_id' => $classSchedule->id,
            'student_id' => $student->id,
            'number_of_students' => 1,
            'status' => 'waiting',
        ]);

        $classSchedule->loadMissing('service');
        AdminNotifier::broadcast(
            'Student joined waitlist',
            "{$student->name} joined the waitlist for ".($classSchedule->service?->title ?? 'a class').'.',
            'info',
            route('admin.class-schedules.show', $classSchedule),
            'booking'
        );

        return back()->with('success', 'You have been added to the waitlist. We will notify you if a spot opens.');
    }
}
