<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassSchedule;
use App\Services\TravelClassService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TravelClassController extends Controller
{
    public function __construct(private TravelClassService $travelClassService) {}

    public function notify(Request $request, ClassSchedule $classSchedule): RedirectResponse
    {
        $validated = $request->validate([
            'message' => 'required|string|max:5000',
        ]);

        $sent = $this->travelClassService->notifyEnrolledStudents($classSchedule, $validated['message']);

        return back()->with('success', "Notification sent to {$sent} enrolled student(s).");
    }

    public function cancel(Request $request, ClassSchedule $classSchedule): RedirectResponse
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:2000',
        ]);

        $this->travelClassService->cancelSchedule($classSchedule, $validated['reason']);
        $this->travelClassService->notifyEnrolledStudents(
            $classSchedule,
            'This travel class has been cancelled. Reason: '.$validated['reason']
        );

        return redirect()->route('admin.class-schedules.show', $classSchedule)
            ->with('success', 'Travel class cancelled and students notified.');
    }
}
