<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassSchedule;
use App\Models\Payment;
use App\Models\Service;
use App\Models\ServiceBooking;
use App\Models\Student;
use App\Services\TravelClassService;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct(private TravelClassService $travelClassService) {}

    public function index()
    {
        // Services Statistics
        $servicesCount = Service::count();
        $activeServicesCount = Service::where('is_active', true)->count();

        // Bookings Statistics
        $totalBookings = ServiceBooking::count();
        $pendingBookings = ServiceBooking::where('status', 'pending')->count();
        $confirmedBookings = ServiceBooking::where('status', 'confirmed')->count();
        $completedBookings = ServiceBooking::where('status', 'completed')->count();
        $cancelledBookings = ServiceBooking::where('status', 'cancelled')->count();
        $todayBookings = ServiceBooking::whereDate('created_at', today())->count();
        $thisMonthBookings = ServiceBooking::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // Payments Statistics
        $totalRevenue = Payment::where('status', 'completed')->sum('amount');
        $pendingPayments = Payment::where('status', 'pending')->sum('amount');
        $todayRevenue = Payment::whereDate('payment_date', today())
            ->where('status', 'completed')
            ->sum('amount');
        $thisMonthRevenue = Payment::whereMonth('payment_date', now()->month)
            ->whereYear('payment_date', now()->year)
            ->where('status', 'completed')
            ->sum('amount');
        $lastMonth = now()->subMonth();
        $lastMonthRevenue = Payment::whereMonth('payment_date', $lastMonth->month)
            ->whereYear('payment_date', $lastMonth->year)
            ->where('status', 'completed')
            ->sum('amount');

        // Class Schedules Statistics
        $totalSchedules = ClassSchedule::count();
        $upcomingSchedules = ClassSchedule::where('class_date', '>=', today())
            ->where('status', 'scheduled')
            ->count();
        $fullSchedules = ClassSchedule::where('status', 'full')->count();
        $availableSchedules = ClassSchedule::where('class_date', '>=', today())
            ->where('status', 'scheduled')
            ->whereRaw('current_students < max_students')
            ->count();

        // Students Statistics
        $totalStudents = Student::count();
        $newStudentsThisMonth = Student::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // Revenue Trends (Last 30 days)
        $revenueTrends = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $revenueTrends[] = [
                'date' => $date->format('M d'),
                'revenue' => Payment::whereDate('payment_date', $date)
                    ->where('status', 'completed')
                    ->sum('amount'),
            ];
        }

        // Booking Trends (Last 30 days)
        $bookingTrends = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $bookingTrends[] = [
                'date' => $date->format('M d'),
                'count' => ServiceBooking::whereDate('created_at', $date)->count(),
            ];
        }

        // Revenue by Payment Method
        $paymentMethods = Payment::where('status', 'completed')
            ->select('payment_method', DB::raw('SUM(amount) as total'))
            ->groupBy('payment_method')
            ->get()
            ->map(function ($item) {
                return [
                    'method' => ucfirst(str_replace('_', ' ', $item->payment_method)),
                    'total' => (float) $item->total,
                ];
            })
            ->values()
            ->toArray();

        // Revenue by Service (Top 5)
        $revenueByService = ServiceBooking::where('payment_status', '!=', 'pending')
            ->select('service_id', DB::raw('SUM(total_amount) as total'))
            ->groupBy('service_id')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->with('service')
            ->get()
            ->map(function ($item) {
                return [
                    'service' => $item->service->title ?? 'Unknown',
                    'total' => (float) $item->total,
                ];
            })
            ->values()
            ->toArray();

        // Bookings by Status
        $bookingsByStatus = ServiceBooking::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get()
            ->map(function ($item) {
                return [
                    'status' => ucfirst($item->status),
                    'count' => (int) $item->count,
                ];
            })
            ->values()
            ->toArray();

        // QuickBooks & Bank Sync Status
        $quickbooksSynced = Payment::where('synced_to_quickbooks', true)->count();
        $quickbooksPending = Payment::where('status', 'completed')
            ->where('synced_to_quickbooks', false)
            ->count();
        $bankSynced = Payment::where('synced_to_bank', true)->count();
        $bankPending = Payment::where('status', 'completed')
            ->where('synced_to_bank', false)
            ->count();

        // Recent Bookings (Last 5)
        $recentBookings = ServiceBooking::with(['service', 'student', 'classSchedule'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Recent Payments (Last 5)
        $recentPayments = Payment::with(['booking.service', 'student'])
            ->orderBy('payment_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $travelBelowMinimum = $this->travelClassService->getSchedulesBelowTravelMinimum();

        return view('admin.dashboard', compact(
            'servicesCount',
            'activeServicesCount',
            'totalBookings',
            'pendingBookings',
            'confirmedBookings',
            'completedBookings',
            'cancelledBookings',
            'todayBookings',
            'thisMonthBookings',
            'totalRevenue',
            'pendingPayments',
            'todayRevenue',
            'thisMonthRevenue',
            'lastMonthRevenue',
            'totalSchedules',
            'upcomingSchedules',
            'fullSchedules',
            'availableSchedules',
            'totalStudents',
            'newStudentsThisMonth',
            'revenueTrends',
            'bookingTrends',
            'paymentMethods',
            'revenueByService',
            'bookingsByStatus',
            'quickbooksSynced',
            'quickbooksPending',
            'bankSynced',
            'bankPending',
            'recentBookings',
            'recentPayments',
            'travelBelowMinimum'
        ));
    }
}
