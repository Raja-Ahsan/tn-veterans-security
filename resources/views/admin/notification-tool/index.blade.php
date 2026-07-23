@extends('admin.layouts.master')

@section('title', 'Notification Tool')
@section('page-title', 'Notification Tool')

@section('content')
<div class="mb-5 rounded-xl border border-green-100 bg-green-50/80 px-4 py-3.5 sm:px-5">
    <div class="flex gap-3">
        <span class="mt-0.5 inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-green-100 text-green-700">
            <i class="fas fa-bullhorn text-sm"></i>
        </span>
        <div class="min-w-0 text-sm leading-relaxed text-gray-700">
            <p class="font-semibold text-gray-900">Notify Enrolled Students</p>
            <p class="mt-0.5">
                Send Email, Text (SMS), or Both for class canceled, rescheduled, moved, time changed, or instructor changed.
                Every send is logged with timestamp, message, delivery method, class ID, and student IDs.
            </p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 gap-6 xl:grid-cols-5">
    {{-- Notify form --}}
    <div class="xl:col-span-2">
        <div id="notify-enrolled" class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-100 bg-gray-50 px-5 py-4">
                <h3 class="text-lg font-bold text-gray-900">
                    <i class="fas fa-paper-plane mr-2 text-green-600"></i>
                    Notify Enrolled Students
                </h3>
                <p class="mt-1 text-sm text-gray-500">Select a class session, then send the update.</p>
            </div>

            <form
                method="POST"
                action="{{ $selectedSchedule ? route('admin.class-schedules.notify', $selectedSchedule) : '#' }}"
                class="space-y-4 p-5"
                id="notification-tool-form"
            >
                @csrf
                <input type="hidden" name="redirect_to" value="notification-tool">

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Class Schedule</label>
                    <select
                        id="notification-schedule-select"
                        name="schedule_picker"
                        required
                        class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm"
                    >
                        <option value="">Select a class session…</option>
                        @foreach($schedules as $schedule)
                            <option
                                value="{{ $schedule->id }}"
                                data-action="{{ route('admin.class-schedules.notify', $schedule) }}"
                                @selected((int) $selectedScheduleId === (int) $schedule->id)
                            >
                                {{ $schedule->service?->title ?? 'Class' }}
                                — {{ $schedule->class_date->format('M d, Y') }}
                                {{ \Carbon\Carbon::parse($schedule->start_time)->format('g:i A') }}
                                ({{ $schedule->current_students }} enrolled)
                            </option>
                        @endforeach
                    </select>
                    @if($schedules->isEmpty())
                        <p class="mt-2 text-xs text-amber-700">No upcoming scheduled sessions found. Create one under Class Schedules.</p>
                    @endif
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Notification Type</label>
                    <select name="notification_type" required class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm">
                        <option value="class_canceled">Class Canceled</option>
                        <option value="class_rescheduled">Class Rescheduled</option>
                        <option value="class_moved">Class Moved</option>
                        <option value="time_changed">Time Changed</option>
                        <option value="instructor_changed">Instructor Changed</option>
                    </select>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Delivery Method</label>
                    <div class="grid grid-cols-3 gap-2">
                        <label class="flex cursor-pointer items-center justify-center gap-2 rounded-lg border border-gray-200 px-3 py-2.5 text-sm font-medium has-[:checked]:border-green-500 has-[:checked]:bg-green-50 has-[:checked]:text-green-800">
                            <input type="radio" name="delivery_method" value="email" class="text-green-600" checked required>
                            Email
                        </label>
                        <label class="flex cursor-pointer items-center justify-center gap-2 rounded-lg border border-gray-200 px-3 py-2.5 text-sm font-medium has-[:checked]:border-green-500 has-[:checked]:bg-green-50 has-[:checked]:text-green-800">
                            <input type="radio" name="delivery_method" value="sms" class="text-green-600">
                            Text
                        </label>
                        <label class="flex cursor-pointer items-center justify-center gap-2 rounded-lg border border-gray-200 px-3 py-2.5 text-sm font-medium has-[:checked]:border-green-500 has-[:checked]:bg-green-50 has-[:checked]:text-green-800">
                            <input type="radio" name="delivery_method" value="both" class="text-green-600">
                            Both
                        </label>
                    </div>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Message</label>
                    <textarea
                        name="message"
                        rows="5"
                        required
                        maxlength="2000"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm"
                        placeholder="Enter the message students will receive…"
                    >{{ old('message') }}</textarea>
                </div>

                <button
                    type="submit"
                    id="notification-tool-submit"
                    class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-green-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-green-700 disabled:cursor-not-allowed disabled:opacity-50"
                    @disabled($schedules->isEmpty())
                >
                    <i class="fas fa-paper-plane"></i>
                    Notify Enrolled Students
                </button>
            </form>
        </div>
    </div>

    {{-- Logs --}}
    <div class="xl:col-span-3">
        <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
            <h3 class="text-lg font-bold text-gray-900">Notification Logs</h3>
            <p class="text-xs text-gray-500">Timestamp · Message · Method · Class ID · Student IDs</p>
        </div>

        @if($logs->count() > 0)
            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Timestamp</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Class ID</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Type / Method</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Message</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Students</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Sent / Failed</th>
                                <th class="px-4 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($logs as $log)
                                <tr class="hover:bg-gray-50/80">
                                    <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-600">
                                        {{ $log->created_at->format('M d, Y g:i A') }}
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 text-sm font-semibold text-gray-900">
                                        #{{ $log->class_schedule_id }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="text-xs font-semibold text-slate-700">{{ ucfirst(str_replace('_', ' ', $log->notification_type)) }}</div>
                                        <div class="mt-1 text-[11px] font-semibold uppercase text-gray-500">{{ $log->delivery_method }}</div>
                                    </td>
                                    <td class="max-w-[14rem] px-4 py-3 text-sm text-gray-600" title="{{ $log->message }}">
                                        <span class="line-clamp-2">{{ \Illuminate\Support\Str::limit($log->message, 80) }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-700">
                                        {{ is_array($log->student_ids) ? count($log->student_ids) : 0 }}
                                        <span class="text-xs text-gray-400">IDs</span>
                                    </td>
                                    <td class="px-4 py-3 text-sm">
                                        <span class="font-semibold text-green-700">{{ $log->sent_count }}</span>
                                        <span class="text-gray-400">/</span>
                                        <span class="font-semibold {{ $log->failed_count > 0 ? 'text-red-600' : 'text-gray-500' }}">{{ $log->failed_count }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <a href="{{ route('admin.notification-tool.show', $log) }}"
                                           class="inline-flex items-center gap-1 text-sm font-semibold text-green-700 hover:underline">
                                            View <i class="fas fa-chevron-right text-[10px]"></i>
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
            <div class="rounded-xl border border-dashed border-gray-300 bg-white px-6 py-12 text-center shadow-sm">
                <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-gray-100 text-gray-400">
                    <i class="fas fa-inbox text-2xl"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900">No notification logs yet</h3>
                <p class="mx-auto mt-2 max-w-md text-sm text-gray-500">
                    Logs appear here after you notify enrolled students from this tool or a class schedule.
                </p>
            </div>
        @endif
    </div>
</div>

<script>
    (function () {
        const select = document.getElementById('notification-schedule-select');
        const form = document.getElementById('notification-tool-form');
        if (!select || !form) {
            return;
        }

        select.addEventListener('change', function () {
            const option = select.options[select.selectedIndex];
            const action = option?.getAttribute('data-action');
            if (action) {
                form.setAttribute('action', action);
                const url = new URL(window.location.href);
                url.searchParams.set('schedule', option.value);
                window.history.replaceState({}, '', url.toString());
            }
        });

        form.addEventListener('submit', function (e) {
            if (!select.value) {
                e.preventDefault();
                alert('Please select a class schedule first.');
            }
        });
    })();
</script>
@endsection
