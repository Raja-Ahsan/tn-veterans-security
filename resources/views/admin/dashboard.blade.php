@extends('admin.layouts.master')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<!-- Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <!-- Total Revenue -->
    <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-green-100 text-sm font-medium">Total Revenue</p>
                <p class="text-3xl font-bold mt-2">${{ number_format($totalRevenue, 2) }}</p>
                <p class="text-green-100 text-xs mt-1">
                    <i class="fas fa-arrow-up"></i> This Month: ${{ number_format($thisMonthRevenue, 2) }}
                </p>
            </div>
            <div class="bg-white/20 p-4 rounded-full">
                <i class="fas fa-dollar-sign text-3xl"></i>
            </div>
        </div>
    </div>

    <!-- Total Bookings -->
    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-blue-100 text-sm font-medium">Total Bookings</p>
                <p class="text-3xl font-bold mt-2">{{ number_format($totalBookings) }}</p>
                <p class="text-blue-100 text-xs mt-1">
                    <i class="fas fa-calendar"></i> This Month: {{ $thisMonthBookings }}
                </p>
            </div>
            <div class="bg-white/20 p-4 rounded-full">
                <i class="fas fa-calendar-check text-3xl"></i>
            </div>
        </div>
    </div>

    <!-- Today's Revenue -->
    <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-purple-100 text-sm font-medium">Today's Revenue</p>
                <p class="text-3xl font-bold mt-2">${{ number_format($todayRevenue, 2) }}</p>
                <p class="text-purple-100 text-xs mt-1">
                    <i class="fas fa-clock"></i> Today's Bookings: {{ $todayBookings }}
                </p>
            </div>
            <div class="bg-white/20 p-4 rounded-full">
                <i class="fas fa-chart-line text-3xl"></i>
            </div>
        </div>
    </div>

    <!-- Total Customers -->
    <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-orange-100 text-sm font-medium">Total Customers</p>
                <p class="text-3xl font-bold mt-2">{{ number_format($totalCustomers) }}</p>
                <p class="text-orange-100 text-xs mt-1">
                    <i class="fas fa-user-plus"></i> New This Month: {{ $newCustomersThisMonth }}
                </p>
            </div>
            <div class="bg-white/20 p-4 rounded-full">
                <i class="fas fa-users text-3xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Secondary Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <!-- Services -->
    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm">Total Classes</p>
                <p class="text-2xl font-bold text-gray-800 mt-1">{{ $servicesCount }}</p>
                <p class="text-green-600 text-xs mt-1">{{ $activeServicesCount }} Active</p>
            </div>
            <i class="fas fa-briefcase text-green-500 text-2xl"></i>
        </div>
    </div>

    <!-- Class Schedules -->
    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm">Upcoming Classes</p>
                <p class="text-2xl font-bold text-gray-800 mt-1">{{ $upcomingSchedules }}</p>
                <p class="text-blue-600 text-xs mt-1">{{ $availableSchedules }} Available</p>
            </div>
            <i class="fas fa-calendar-alt text-blue-500 text-2xl"></i>
        </div>
    </div>

    <!-- Pending Payments -->
    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm">Pending Payments</p>
                <p class="text-2xl font-bold text-gray-800 mt-1">${{ number_format($pendingPayments, 2) }}</p>
                <p class="text-yellow-600 text-xs mt-1">Awaiting Payment</p>
            </div>
            <i class="fas fa-hourglass-half text-yellow-500 text-2xl"></i>
        </div>
    </div>

    <!-- Booking Status -->
    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm">Confirmed</p>
                <p class="text-2xl font-bold text-gray-800 mt-1">{{ $confirmedBookings }}</p>
                <p class="text-purple-600 text-xs mt-1">{{ $pendingBookings }} Pending</p>
            </div>
            <i class="fas fa-check-circle text-purple-500 text-2xl"></i>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- Revenue Trends Chart -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-bold text-gray-800">Revenue Trends (Last 30 Days)</h3>
            <div class="flex gap-2">
                <span class="text-sm text-gray-600">This Month: ${{ number_format($thisMonthRevenue, 2) }}</span>
                @if($lastMonthRevenue > 0)
                    @php
                        $growth = (($thisMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100;
                    @endphp
                    <span class="text-sm {{ $growth >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        <i class="fas fa-arrow-{{ $growth >= 0 ? 'up' : 'down' }}"></i> {{ number_format(abs($growth), 1) }}%
                    </span>
                @endif
            </div>
        </div>
        <canvas id="revenueChart" height="100"></canvas>
    </div>

    <!-- Booking Trends Chart -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-bold text-gray-800">Booking Trends (Last 30 Days)</h3>
            <span class="text-sm text-gray-600">Total: {{ $thisMonthBookings }} this month</span>
        </div>
        <canvas id="bookingChart" height="100"></canvas>
    </div>
</div>

<!-- Second Charts Row -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- Payment Methods Breakdown -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h3 class="text-xl font-bold text-gray-800 mb-4">Payment Methods Breakdown</h3>
        <canvas id="paymentMethodsChart" height="200"></canvas>
    </div>

    <!-- Bookings by Status -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h3 class="text-xl font-bold text-gray-800 mb-4">Bookings by Status</h3>
        <canvas id="bookingsStatusChart" height="200"></canvas>
    </div>
</div>

<!-- Revenue by Service Chart -->
<div class="bg-white rounded-lg shadow-lg p-6 mb-6">
    <h3 class="text-xl font-bold text-gray-800 mb-4">Top 5 Services by Revenue</h3>
    <canvas id="revenueByServiceChart" height="80"></canvas>
</div>

<!-- QuickBooks & Bank Sync Status -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <!-- QuickBooks Sync -->
    <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-blue-500">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-800">
                <i class="fab fa-quickbooks text-blue-600 mr-2"></i> QuickBooks Sync
            </h3>
            <a href="{{ route('admin.payments.index') }}" class="text-blue-600 hover:underline text-sm">
                View All <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-gray-600">Synced Payments</span>
                <span class="font-bold text-green-600">{{ $quickbooksSynced }}</span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-gray-600">Pending Sync</span>
                <span class="font-bold text-yellow-600">{{ $quickbooksPending }}</span>
            </div>
            @if($quickbooksPending > 0)
                <form method="POST" action="{{ route('admin.payments.sync-all-quickbooks') }}" class="mt-4">
                    @csrf
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm">
                        <i class="fab fa-quickbooks mr-1"></i> Sync All Pending
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Bank Sync -->
    <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-green-500">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-800">
                <i class="fas fa-university text-green-600 mr-2"></i> Bank Sync
            </h3>
            <a href="{{ route('admin.payments.index') }}" class="text-green-600 hover:underline text-sm">
                View All <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-gray-600">Synced Payments</span>
                <span class="font-bold text-green-600">{{ $bankSynced }}</span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-gray-600">Pending Sync</span>
                <span class="font-bold text-yellow-600">{{ $bankPending }}</span>
            </div>
            @if($bankPending > 0)
                <form method="POST" action="{{ route('admin.payments.sync-all-bank') }}" class="mt-4">
                    @csrf
                    <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded text-sm">
                        <i class="fas fa-university mr-1"></i> Sync All Pending
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>

<!-- Tables Row -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- Recent Bookings -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-bold text-gray-800">Recent Bookings</h3>
            <a href="{{ route('admin.bookings.index') }}" class="text-blue-600 hover:underline text-sm">
                View All <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Service</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($recentBookings as $booking)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm">
                                <div class="font-medium text-gray-900">{{ $booking->customer->name }}</div>
                                <div class="text-gray-500 text-xs">{{ $booking->customer->email }}</div>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ \Illuminate\Support\Str::limit($booking->service->title, 20) }}</td>
                            <td class="px-4 py-3 text-sm">
                                @if($booking->status === 'pending')
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                                @elseif($booking->status === 'confirmed')
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Confirmed</span>
                                @elseif($booking->status === 'completed')
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Completed</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">{{ ucfirst($booking->status) }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ $booking->created_at->format('M d, Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-gray-500">No recent bookings</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Payments -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-bold text-gray-800">Recent Payments</h3>
            <a href="{{ route('admin.payments.index') }}" class="text-blue-600 hover:underline text-sm">
                View All <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($recentPayments as $payment)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm">
                                <div class="font-medium text-gray-900">{{ $payment->customer->name }}</div>
                                <div class="text-gray-500 text-xs">{{ \Illuminate\Support\Str::limit($payment->booking->service->title ?? 'N/A', 20) }}</div>
                            </td>
                            <td class="px-4 py-3 text-sm font-bold text-gray-900">${{ number_format($payment->amount, 2) }}</td>
                            <td class="px-4 py-3 text-sm">
                                @if($payment->status === 'completed')
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Completed</span>
                                @elseif($payment->status === 'pending')
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">{{ ucfirst($payment->status) }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ $payment->payment_date->format('M d, Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-gray-500">No recent payments</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Upcoming Classes -->
<!-- Quick Actions -->
<div class="bg-white rounded-lg shadow-lg p-6">
    <h3 class="text-xl font-bold text-gray-800 mb-4">Quick Actions</h3>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <a href="{{ route('admin.services.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-3 rounded-lg text-center transition-colors">
            <i class="fas fa-plus text-xl mb-2"></i>
            <div class="text-sm font-semibold">Add Service</div>
        </a>
        <a href="{{ route('admin.class-schedules.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-3 rounded-lg text-center transition-colors">
            <i class="fas fa-calendar-plus text-xl mb-2"></i>
            <div class="text-sm font-semibold">Add Schedule</div>
        </a>
        <a href="{{ route('admin.bookings.index') }}" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-3 rounded-lg text-center transition-colors">
            <i class="fas fa-book text-xl mb-2"></i>
            <div class="text-sm font-semibold">View Bookings</div>
        </a>
        <a href="{{ route('admin.payments.index') }}" class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-3 rounded-lg text-center transition-colors">
            <i class="fas fa-money-bill-wave text-xl mb-2"></i>
            <div class="text-sm font-semibold">View Payments</div>
        </a>
    </div>
</div>

<!-- Chart.js Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
// Revenue Trends Chart
const revenueCtx = document.getElementById('revenueChart').getContext('2d');
new Chart(revenueCtx, {
    type: 'line',
    data: {
        labels: @json(array_column($revenueTrends, 'date')),
        datasets: [{
            label: 'Revenue ($)',
            data: @json(array_column($revenueTrends, 'revenue')),
            borderColor: 'rgb(34, 197, 94)',
            backgroundColor: 'rgba(34, 197, 94, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return '$' + value.toLocaleString();
                    }
                }
            }
        }
    }
});

// Booking Trends Chart
const bookingCtx = document.getElementById('bookingChart').getContext('2d');
new Chart(bookingCtx, {
    type: 'line',
    data: {
        labels: @json(array_column($bookingTrends, 'date')),
        datasets: [{
            label: 'Bookings',
            data: @json(array_column($bookingTrends, 'count')),
            borderColor: 'rgb(59, 130, 246)',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});

// Payment Methods Chart
const paymentMethodsCtx = document.getElementById('paymentMethodsChart').getContext('2d');
new Chart(paymentMethodsCtx, {
    type: 'doughnut',
    data: {
        labels: @json(array_map(function($item) { return $item['method']; }, $paymentMethods)),
        datasets: [{
            data: @json(array_map(function($item) { return $item['total']; }, $paymentMethods)),
            backgroundColor: [
                'rgb(34, 197, 94)',
                'rgb(59, 130, 246)',
                'rgb(168, 85, 247)',
                'rgb(249, 115, 22)',
                'rgb(236, 72, 153)',
                'rgb(14, 165, 233)'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                position: 'bottom'
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return context.label + ': $' + context.parsed.toLocaleString();
                    }
                }
            }
        }
    }
});

// Bookings by Status Chart
const bookingsStatusCtx = document.getElementById('bookingsStatusChart').getContext('2d');
new Chart(bookingsStatusCtx, {
    type: 'pie',
    data: {
        labels: @json(array_map(function($item) { return $item['status']; }, $bookingsByStatus)),
        datasets: [{
            data: @json(array_map(function($item) { return $item['count']; }, $bookingsByStatus)),
            backgroundColor: [
                'rgb(234, 179, 8)',   // Pending - Yellow
                'rgb(34, 197, 94)',   // Confirmed - Green
                'rgb(59, 130, 246)',  // Completed - Blue
                'rgb(239, 68, 68)',   // Cancelled - Red
                'rgb(156, 163, 175)'  // Other - Gray
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// Revenue by Service Chart
const revenueByServiceCtx = document.getElementById('revenueByServiceChart').getContext('2d');
new Chart(revenueByServiceCtx, {
    type: 'bar',
    data: {
        labels: @json(array_map(function($item) { return $item['service']; }, $revenueByService)),
        datasets: [{
            label: 'Revenue ($)',
            data: @json(array_map(function($item) { return $item['total']; }, $revenueByService)),
            backgroundColor: 'rgba(34, 197, 94, 0.8)',
            borderColor: 'rgb(34, 197, 94)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return '$' + value.toLocaleString();
                    }
                }
            }
        }
    }
});
</script>
@endsection
