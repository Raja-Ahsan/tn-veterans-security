@extends('admin.layouts.master')

@section('title', 'Communication Logs')
@section('page-title', 'Communication Logs')

@section('content')
<div class="mb-5 rounded-xl border border-emerald-100 bg-emerald-50/70 px-4 py-3.5 sm:px-5">
    <div class="flex gap-3">
        <span class="mt-0.5 inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-emerald-100 text-emerald-700">
            <i class="fas fa-bullhorn text-sm"></i>
        </span>
        <div class="min-w-0 text-sm leading-relaxed text-gray-600">
            <p>
                Logs are created when you notify students from a
                <a href="{{ route('admin.class-schedules.index') }}" class="font-semibold text-emerald-800 hover:underline">Class Schedule</a>
                (cancel, reschedule, move, time/instructor change, or general notice).
            </p>
        </div>
    </div>
</div>

@if($logs->count() > 0)
<div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Timestamp</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Class</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Type</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Method</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Sent / Failed</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($logs as $log)
                    <tr class="hover:bg-gray-50/80">
                        <td class="whitespace-nowrap px-5 py-3.5 text-sm text-gray-600">
                            {{ $log->created_at->format('M d, Y g:i A') }}
                        </td>
                        <td class="max-w-[16rem] truncate px-5 py-3.5 text-sm font-medium text-gray-900" title="{{ $log->classSchedule?->service?->title }}">
                            {{ $log->classSchedule?->service?->title ?? '—' }}
                        </td>
                        <td class="px-5 py-3.5">
                            <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">
                                {{ ucfirst(str_replace('_', ' ', $log->notification_type)) }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5">
                            <span class="inline-flex items-center gap-1.5 text-xs font-semibold uppercase text-gray-600">
                                <i class="fas {{ strtoupper((string) $log->delivery_method) === 'SMS' ? 'fa-sms' : 'fa-envelope' }} text-[10px] text-gray-400"></i>
                                {{ $log->delivery_method }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-sm">
                            <span class="font-semibold text-emerald-700">{{ $log->sent_count }}</span>
                            <span class="text-gray-400">/</span>
                            <span class="font-semibold {{ $log->failed_count > 0 ? 'text-red-600' : 'text-gray-500' }}">{{ $log->failed_count }}</span>
                        </td>
                        <td class="px-5 py-3.5 text-right">
                            <a href="{{ route('admin.communication-logs.show', $log) }}"
                               class="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1.5 text-sm font-semibold text-emerald-700 transition hover:bg-emerald-50 hover:text-emerald-900">
                                View
                                <i class="fas fa-chevron-right text-[10px]"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<div class="mt-4">{{ $logs->links() }}</div>
@else
<div class="rounded-xl border border-dashed border-gray-300 bg-white px-6 py-10 text-center shadow-sm">
    <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-gray-100 text-gray-400">
        <i class="fas fa-inbox text-2xl"></i>
    </div>
    <h3 class="text-lg font-bold text-gray-900">No communication logs yet</h3>
    <p class="mx-auto mt-2 max-w-lg text-sm text-gray-500">
        Logs appear after you notify students from a class schedule. Nothing has been sent yet.
    </p>
    <a
        href="{{ route('admin.class-schedules.index') }}"
        class="mt-6 inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-700"
    >
        <i class="fas fa-calendar-check text-xs"></i>
        Go to Class Schedules
    </a>
</div>
@endif
@endsection
