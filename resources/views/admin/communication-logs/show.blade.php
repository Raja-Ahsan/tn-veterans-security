@extends('admin.layouts.master')

@section('title', 'Communication Log')
@section('page-title', 'Communication Log Details')

@section('content')
@php
    $schedule = $communicationLog->classSchedule;
    $classTitle = $schedule?->service?->title ?? '—';
    $typeLabel = ucfirst(str_replace('_', ' ', $communicationLog->notification_type));
    $method = strtoupper((string) $communicationLog->delivery_method);
    $message = trim((string) $communicationLog->message);
@endphp

<div class="mb-5 flex flex-wrap items-center justify-between gap-3">
    <a href="{{ route('admin.communication-logs.index') }}"
       class="inline-flex items-center gap-2 text-sm font-medium text-gray-600 transition hover:text-emerald-700">
        <i class="fas fa-arrow-left text-xs"></i>
        Back to logs
    </a>
    @if($schedule)
        <a href="{{ route('admin.class-schedules.show', $schedule) }}"
           class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-semibold text-gray-700 shadow-sm transition hover:border-emerald-300 hover:text-emerald-700">
            <i class="fas fa-calendar-check text-xs text-emerald-600"></i>
            View class schedule
        </a>
    @endif
</div>

<div class="space-y-5">
    {{-- Summary --}}
    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        <div class="flex flex-wrap items-start justify-between gap-4 border-b border-gray-100 bg-gray-50/80 px-5 py-4 sm:px-6">
            <div class="min-w-0">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Class</p>
                <h3 class="mt-1 truncate text-lg font-bold text-gray-900">{{ $classTitle }}</h3>
                @if($schedule?->class_date)
                    <p class="mt-1 text-sm text-gray-500">
                        {{ $schedule->class_date->format('M d, Y') }}
                        @if($schedule->start_time)
                            · {{ \Carbon\Carbon::parse($schedule->start_time)->format('g:i A') }}
                            @if($schedule->end_time)
                                – {{ \Carbon\Carbon::parse($schedule->end_time)->format('g:i A') }}
                            @endif
                        @endif
                    </p>
                @endif
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">
                    {{ $typeLabel }}
                </span>
                <span class="inline-flex items-center gap-1.5 rounded-full bg-blue-50 px-2.5 py-1 text-xs font-semibold uppercase text-blue-700">
                    <i class="fas {{ $method === 'SMS' ? 'fa-sms' : 'fa-envelope' }} text-[10px]"></i>
                    {{ $method }}
                </span>
                <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold
                    {{ $communicationLog->failed_count > 0 ? 'bg-amber-50 text-amber-800' : 'bg-emerald-50 text-emerald-800' }}">
                    {{ $communicationLog->sent_count }} sent
                    @if($communicationLog->failed_count > 0)
                        · {{ $communicationLog->failed_count }} failed
                    @endif
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-0 sm:grid-cols-3">
            <div class="border-b border-gray-100 px-5 py-4 sm:border-b-0 sm:border-r sm:px-6">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Timestamp</p>
                <p class="mt-1 text-sm font-semibold text-gray-900">{{ $communicationLog->created_at->format('M d, Y g:i A') }}</p>
            </div>
            <div class="border-b border-gray-100 px-5 py-4 sm:border-b-0 sm:border-r sm:px-6">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Sent by</p>
                <p class="mt-1 text-sm font-semibold text-gray-900">{{ $communicationLog->sender?->name ?? 'System' }}</p>
            </div>
            <div class="px-5 py-4 sm:px-6">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Recipients</p>
                <p class="mt-1 text-sm font-semibold text-gray-900">{{ $students->count() }} student{{ $students->count() === 1 ? '' : 's' }}</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-5 lg:grid-cols-5">
        {{-- Message --}}
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm lg:col-span-3">
            <div class="flex items-center gap-2 border-b border-gray-100 px-5 py-3.5 sm:px-6">
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-50 text-emerald-700">
                    <i class="fas fa-comment-alt text-sm"></i>
                </span>
                <h4 class="text-sm font-bold text-gray-900">Message</h4>
            </div>
            <div class="px-5 py-4 sm:px-6 sm:py-5">
                @if($message !== '')
                    <div class="rounded-lg border border-gray-100 bg-gray-50 px-4 py-3 text-sm leading-relaxed text-gray-800 whitespace-pre-wrap">{{ $message }}</div>
                @else
                    <p class="text-sm text-gray-500">No message body was saved for this log.</p>
                @endif
            </div>
        </div>

        {{-- Students --}}
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm lg:col-span-2">
            <div class="flex items-center justify-between gap-2 border-b border-gray-100 px-5 py-3.5 sm:px-6">
                <div class="flex items-center gap-2">
                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-slate-100 text-slate-700">
                        <i class="fas fa-users text-sm"></i>
                    </span>
                    <h4 class="text-sm font-bold text-gray-900">Students notified</h4>
                </div>
                <span class="rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-semibold text-gray-600">{{ $students->count() }}</span>
            </div>

            @if($students->count() > 0)
                <div class="max-h-[28rem] overflow-y-auto">
                    <table class="min-w-full">
                        <thead class="sticky top-0 bg-gray-50">
                            <tr>
                                <th class="px-5 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-gray-400 sm:px-6">Name</th>
                                <th class="px-5 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-gray-400 sm:px-6">Email</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($students as $student)
                                <tr class="hover:bg-gray-50/80">
                                    <td class="px-5 py-3 sm:px-6">
                                        <div class="flex items-center gap-2.5">
                                            <span class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-xs font-bold text-emerald-800">
                                                {{ strtoupper(substr($student->name, 0, 1)) }}
                                            </span>
                                            <span class="text-sm font-medium text-gray-900">{{ $student->name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-3 sm:px-6">
                                        <span class="block truncate text-sm text-gray-500" title="{{ $student->email }}">{{ $student->email }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="px-5 py-8 text-center sm:px-6">
                    <p class="text-sm text-gray-500">No student records linked to this log.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
