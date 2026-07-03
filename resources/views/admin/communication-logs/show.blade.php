@extends('admin.layouts.master')

@section('title', 'Communication Log')
@section('page-title', 'Communication Log Details')

@section('content')
<div class="max-w-3xl bg-white rounded-lg shadow p-6 space-y-4">
    <div class="grid grid-cols-2 gap-4">
        <div><span class="text-sm text-gray-500">Timestamp</span><p class="font-medium">{{ $communicationLog->created_at->format('M d, Y g:i A') }}</p></div>
        <div><span class="text-sm text-gray-500">Sent By</span><p class="font-medium">{{ $communicationLog->sender?->name ?? 'System' }}</p></div>
        <div><span class="text-sm text-gray-500">Class</span><p class="font-medium">{{ $communicationLog->classSchedule?->service?->title }}</p></div>
        <div><span class="text-sm text-gray-500">Delivery</span><p class="font-medium uppercase">{{ $communicationLog->delivery_method }}</p></div>
        <div><span class="text-sm text-gray-500">Type</span><p class="font-medium">{{ ucfirst(str_replace('_', ' ', $communicationLog->notification_type)) }}</p></div>
        <div><span class="text-sm text-gray-500">Results</span><p class="font-medium">{{ $communicationLog->sent_count }} sent, {{ $communicationLog->failed_count }} failed</p></div>
    </div>
    <div>
        <span class="text-sm text-gray-500">Message</span>
        <p class="mt-1 whitespace-pre-wrap bg-gray-50 p-4 rounded">{{ $communicationLog->message }}</p>
    </div>
    <div>
        <span class="text-sm text-gray-500">Students Notified ({{ $students->count() }})</span>
        <ul class="mt-2 list-disc list-inside text-gray-700">
            @foreach($students as $student)
                <li>{{ $student->name }} ({{ $student->email }})</li>
            @endforeach
        </ul>
    </div>
    <a href="{{ route('admin.communication-logs.index') }}" class="inline-block text-blue-600 hover:underline">← Back to logs</a>
</div>
@endsection
