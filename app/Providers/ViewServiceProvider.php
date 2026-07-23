<?php

namespace App\Providers;

use App\Models\Service;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {
            $settings = SiteSetting::first();
            $view->with('siteSettings', $settings);

            $footerServices = Service::where('is_active', true)
                ->orderBy('order')
                ->orderBy('created_at', 'desc')
                ->limit(6)
                ->get();
            $view->with('footerServices', $footerServices);
        });

        View::composer('student.layouts.master', function ($view) {
            $student = Auth::guard('student')->user();

            if (! $student) {
                $view->with([
                    'headerNotifications' => collect(),
                    'unreadNotificationCount' => 0,
                ]);

                return;
            }

            $headerNotifications = $student->unreadNotifications()->latest()->limit(8)->get();

            $view->with([
                'headerNotifications' => $headerNotifications,
                'unreadNotificationCount' => $headerNotifications->count() > 7
                    ? $student->unreadNotifications()->count()
                    : $headerNotifications->count(),
            ]);
        });

        View::composer('admin.layouts.master', function ($view) {
            $admin = Auth::user();

            if (! $admin) {
                $view->with([
                    'headerNotifications' => collect(),
                    'unreadNotificationCount' => 0,
                ]);

                return;
            }

            $headerNotifications = $admin->unreadNotifications()->latest()->limit(8)->get();

            $view->with([
                'headerNotifications' => $headerNotifications,
                'unreadNotificationCount' => $headerNotifications->count() > 7
                    ? $admin->unreadNotifications()->count()
                    : $headerNotifications->count(),
            ]);
        });
    }
}
