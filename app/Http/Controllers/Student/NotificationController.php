<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(): View
    {
        $student = Auth::guard('student')->user();

        $notifications = $student->unreadNotifications()->latest()->paginate(20);

        return view('student.notifications.index', compact('notifications'));
    }

    public function markAsRead(Request $request, string $id): RedirectResponse
    {
        $student = Auth::guard('student')->user();
        $notification = $student->notifications()->where('id', $id)->firstOrFail();

        $data = $notification->data ?? [];
        $url = $data['url'] ?? null;
        $context = [
            'title' => $data['title'] ?? 'Notification',
            'body' => $data['body'] ?? '',
            'category' => $data['category'] ?? 'general',
            'icon' => $data['icon'] ?? 'bell',
        ];

        $notification->delete();

        if ($request->boolean('stay') || $request->boolean('dismiss')) {
            return back()->with('success', 'Notification removed.');
        }

        if (is_string($url) && $url !== '') {
            return redirect()->to($url)->with('notification_context', $context);
        }

        return back()->with('notification_context', $context);
    }

    public function markAllAsRead(): RedirectResponse
    {
        $student = Auth::guard('student')->user();
        $student->notifications()->delete();

        return back()->with('success', 'All notifications cleared.');
    }
}
