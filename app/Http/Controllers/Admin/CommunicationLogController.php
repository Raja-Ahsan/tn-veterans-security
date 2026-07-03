<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassNotification;
use Illuminate\Http\Request;

class CommunicationLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ClassNotification::query()->with(['classSchedule.service', 'sender']);

        if ($scheduleId = $request->query('schedule')) {
            $query->where('class_schedule_id', $scheduleId);
        }

        $logs = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        return view('admin.communication-logs.index', compact('logs'));
    }

    public function show(ClassNotification $communicationLog)
    {
        $communicationLog->load(['classSchedule.service', 'sender']);

        $students = \App\Models\Student::whereIn('id', $communicationLog->student_ids ?? [])->get();

        return view('admin.communication-logs.show', compact('communicationLog', 'students'));
    }
}
