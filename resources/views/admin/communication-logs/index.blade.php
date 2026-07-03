@extends('admin.layouts.master')

@section('title', 'Communication Logs')
@section('page-title', 'Communication Logs')

@section('content')
<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Timestamp</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Class</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Method</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sent / Failed</th>
                <th class="px-6 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse($logs as $log)
                <tr>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $log->created_at->format('M d, Y g:i A') }}</td>
                    <td class="px-6 py-4">{{ $log->classSchedule?->service?->title ?? '—' }}</td>
                    <td class="px-6 py-4">{{ ucfirst(str_replace('_', ' ', $log->notification_type)) }}</td>
                    <td class="px-6 py-4 uppercase text-sm">{{ $log->delivery_method }}</td>
                    <td class="px-6 py-4">{{ $log->sent_count }} / {{ $log->failed_count }}</td>
                    <td class="px-6 py-4 text-right"><a href="{{ route('admin.communication-logs.show', $log) }}" class="text-blue-600 hover:underline">View</a></td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-6 py-8 text-center text-gray-500">No communication logs yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $logs->links() }}</div>
@endsection
