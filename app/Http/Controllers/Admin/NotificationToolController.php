<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassNotification;
use App\Models\ClassSchedule;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationToolController extends Controller
{
    public function index(Request $request): View
    {
        $schedules = ClassSchedule::query()
            ->with(['service'])
            ->whereDate('class_date', '>=', now()->toDateString())
            ->whereIn('status', ['scheduled', 'full'])
            ->orderBy('class_date')
            ->orderBy('start_time')
            ->get();

        $selectedScheduleId = $request->integer('schedule') ?: null;
        $selectedSchedule = $selectedScheduleId
            ? ClassSchedule::query()->with(['service'])->find($selectedScheduleId)
            : null;

        $logs = ClassNotification::query()
            ->with(['classSchedule.service', 'sender'])
            ->when($selectedScheduleId, fn ($query) => $query->where('class_schedule_id', $selectedScheduleId))
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.notification-tool.index', compact(
            'schedules',
            'selectedSchedule',
            'selectedScheduleId',
            'logs'
        ));
    }

    public function show(ClassNotification $communicationLog): View
    {
        $communicationLog->load(['classSchedule.service', 'sender']);

        $students = Student::query()
            ->whereIn('id', $communicationLog->student_ids ?? [])
            ->get();

        return view('admin.notification-tool.show', compact('communicationLog', 'students'));
    }
}
