<?php

namespace App\Http\Controllers;

use App\Models\ClassSchedule;
use App\Models\Service;

class ServicePageController extends Controller
{
    /**
     * Show service detail by ID (e.g. /training-classes/42).
     */
    public function showById($id)
    {
        $service = Service::with('linkedServices')
            ->where('is_active', true)
            ->findOrFail($id);

        return $this->serviceDetailResponse($service);
    }

    /**
     * Show service detail by slug (e.g. /service/asp) for direct/uncategorized services.
     */
    public function showBySlug(string $slug)
    {
        $service = Service::with('linkedServices')
            ->where('is_active', true)
            ->where('slug', $slug)
            ->firstOrFail();

        return $this->serviceDetailResponse($service);
    }

    /**
     * Build booking data and return service-details view.
     */
    protected function serviceDetailResponse(Service $service): \Illuminate\View\View
    {
        $linkedServices = $service->linkedServices->where('is_active', true);
        $relatedServices = $linkedServices->isNotEmpty()
            ? $linkedServices
            : Service::where('is_active', true)
                ->where('id', '!=', $service->id)
                ->orderBy('order')
                ->limit(3)
                ->get();

        $bookingSchedules = ClassSchedule::where('service_id', $service->id)
            ->where('status', 'scheduled')
            ->where('class_date', '>=', now()->toDateString())
            ->whereRaw('current_students < max_students')
            ->orderBy('class_date')
            ->orderBy('start_time')
            ->get();

        /** All upcoming sessions (including full) for accurate seat totals on the service page. */
        $upcomingSchedulesOverview = ClassSchedule::query()
            ->where('service_id', $service->id)
            ->where('class_date', '>=', now()->toDateString())
            ->whereNotIn('status', ['cancelled', 'completed'])
            ->orderBy('class_date')
            ->orderBy('start_time')
            ->get();

        $bookingLocations = $bookingSchedules
            ->pluck('location')
            ->map(fn ($loc) => $loc ?: 'No Specific Location')
            ->unique()
            ->sort()
            ->values();

        return view('class-details', compact(
            'service',
            'relatedServices',
            'linkedServices',
            'bookingLocations',
            'bookingSchedules',
            'upcomingSchedulesOverview'
        ));
    }
}
