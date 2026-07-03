<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ClassSchedule;
use App\Models\WaitlistEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WaitlistController extends Controller
{
    public function store(Request $request, ClassSchedule $classSchedule)
    {
        $student = Auth::guard('student')->user();

        $validated = $request->validate([
            'number_of_students' => 'required|integer|min:1|max:100',
        ]);

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
            'number_of_students' => $validated['number_of_students'],
            'status' => 'waiting',
        ]);

        return back()->with('success', 'You have been added to the waitlist. We will notify you if a spot opens.');
    }
}
