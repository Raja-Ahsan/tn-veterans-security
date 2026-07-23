<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\NotificationFeed;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(): View
    {
        $admin = Auth::user();
        $notifications = $admin->unreadNotifications()->latest()->paginate(20);

        return view('admin.notifications.index', compact('notifications'));
    }

    public function poll(): JsonResponse
    {
        $admin = Auth::user();
        $notifications = $admin->unreadNotifications()->latest()->limit(8)->get();

        return response()->json(NotificationFeed::payload(
            $notifications,
            $admin->unreadNotifications()->count(),
            'admin.notifications.read'
        ));
    }

    public function markAsRead(Request $request, string $id): RedirectResponse
    {
        $admin = Auth::user();
        $notification = $admin->notifications()->where('id', $id)->firstOrFail();

        $data = $notification->data ?? [];
        $url = $data['url'] ?? null;
        $context = [
            'title' => $data['title'] ?? 'Notification',
            'body' => $data['body'] ?? '',
            'category' => $data['category'] ?? 'general',
            'icon' => $data['icon'] ?? 'bell',
        ];

        $notification->delete();

        if ($request->boolean('dismiss') || $request->boolean('stay')) {
            return back()->with('success', 'Notification removed.');
        }

        if (is_string($url) && $url !== '') {
            return redirect()->to($url)->with('notification_context', $context);
        }

        return back()->with('notification_context', $context);
    }

    public function markAllAsRead(): RedirectResponse
    {
        Auth::user()->notifications()->delete();

        return back()->with('success', 'All notifications cleared.');
    }
}
