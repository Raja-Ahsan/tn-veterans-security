@extends('admin.layouts.master')

@section('title', 'Communication Log')
@section('page-title', 'Communication Log Details')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.communication-logs.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-emerald-700">
        <i class="fas fa-arrow-left text-xs"></i>
        Back to logs
    </a>
</div>

<div class="max-w-3xl space-y-5">
    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <span class="text-xs font-semibold uppercase tracking-wide text-gray-400">Timestamp</span>
                <p class="mt-1 font-medium text-gray-900">{{ $communicationLog->created_at->format('M d, Y g:i A') }}</p>
            </div>
            <div>
                <span class="text-xs font-semibold uppercase tracking-wide text-gray-400">Sent By</span>
                <p class="mt-1 font-medium text-gray-900">{{ $communicationLog->sender?->name ?? 'System' }}</p>
            </div>
            <div>
                <span class="text-xs font-semibold uppercase tracking-wide text-gray-400">Class</span>
                <p class="mt-1 font-medium text-gray-900">{{ $communicationLog->classSchedule?->service?->title ?? '—' }}</p>
            </div>
            <div>
                <span class="text-xs font-semibold uppercase tracking-wide text-gray-400">Delivery</span>
                <p class="mt-1 font-medium uppercase text-gray-900">{{ $communicationLog->delivery_method }}</p>
            </div>
            <div>
                <span class="text-xs font-semibold uppercase tracking-wide text-gray-400">Type</span>
                <p class="mt-1">
                    <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">
                        {{ ucfirst(str_replace('_', ' ', $communicationLog->notification_type)) }}
                    </span>
                </p>
            </div>
            <div>
                <span class="text-xs font-semibold uppercase tracking-wide text-gray-400">Results</span>
                <p class="mt-1 font-medium text-gray-900">
                    <span class="text-emerald-700">{{ $communicationLog->sent_count }} sent</span>,
                    <span class="{{ $communicationLog->failed_count > 0 ? 'text-red-600' : 'text-gray-500' }}">{{ $communicationLog->failed_count }} failed</span>
                </p>
            </div>
        </div>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
        <span class="text-xs font-semibold uppercase tracking-wide text-gray-400">Message</span>
        <p class="mt-2 whitespace-pre-wrap rounded-lg bg-gray-50 p-4 text-sm leading-relaxed text-gray-800">{{ $communicationLog->message }}</p>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
        <span class="text-xs font-semibold uppercase tracking-wide text-gray-400">Students Notified ({{ $students->count() }})</span>
        @if($students->count() > 0)
            <ul class="mt-3 divide-y divide-gray-100">
                @foreach($students as $student)
                    <li class="flex items-center justify-between gap-3 py-2.5 text-sm">
                        <span class="font-medium text-gray-900">{{ $student->name }}</span>
                        <span class="truncate text-gray-500">{{ $student->email }}</span>
                    </li>
                @endforeach
            </ul>
        @else
            <p class="mt-2 text-sm text-gray-500">No student records linked to this log.</p>
        @endif
    </div>
</div>
@endsection
