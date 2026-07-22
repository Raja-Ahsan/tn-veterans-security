@extends('admin.layouts.master')

@section('title', 'Students')
@section('page-title', 'Students')

@section('content')
<div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
    <div>
        <h3 class="text-xl font-semibold text-gray-900">Students</h3>
        <p class="mt-1 text-sm text-gray-500">Search and manage student accounts, bookings, and registration details.</p>
    </div>
    <div class="rounded-lg border border-gray-200 bg-white px-4 py-2 shadow-sm">
        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Total</p>
        <p class="text-lg font-bold text-gray-900">{{ $students->total() }}</p>
    </div>
</div>

<form method="GET" action="{{ route('admin.students.index') }}" class="mb-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
        <div class="relative flex-1 max-w-xl">
            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                <i class="fas fa-search text-sm"></i>
            </span>
            <input type="search"
                   name="search"
                   value="{{ request('search') }}"
                   placeholder="Search name, email, phone, registration #…"
                   class="w-full rounded-lg border border-gray-300 py-2.5 pl-9 pr-3 text-sm shadow-sm focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500">
        </div>
        <div class="flex items-center gap-2">
            <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-green-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-green-700">
                <i class="fas fa-search"></i> Search
            </button>
            @if(request()->filled('search'))
                <a href="{{ route('admin.students.index') }}"
                   class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Clear
                </a>
            @endif
        </div>
    </div>
</form>

<div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Student</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Contact</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                        Bookings
                        <span class="block font-normal normal-case tracking-normal text-gray-400">count</span>
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                        Reg. #
                        <span class="block font-normal normal-case tracking-normal text-gray-400">security</span>
                    </th>
                    <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse($students as $student)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-slate-100 text-sm font-bold text-slate-600">
                                    {{ strtoupper(substr($student->name, 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <div class="truncate text-sm font-semibold text-gray-900">{{ $student->name }}</div>
                                    <div class="truncate text-xs text-gray-500">ID #{{ $student->id }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="text-sm text-gray-800">{{ $student->email }}</div>
                            <div class="mt-0.5 text-xs text-gray-500">
                                <i class="fas fa-phone mr-1 text-gray-400"></i>{{ $student->phone ?: 'No phone' }}
                            </div>
                        </td>
                        <td class="whitespace-nowrap px-4 py-3">
                            <span class="inline-flex items-center rounded-md bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700 ring-1 ring-inset ring-blue-200">
                                {{ $student->bookings_count }} {{ Str::plural('booking', $student->bookings_count) }}
                            </span>
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-700">
                            @if($student->security_registration_number)
                                <span class="font-medium">{{ $student->security_registration_number }}</span>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-right">
                            <div class="inline-flex items-center justify-end gap-2">
                                <a href="{{ route('admin.students.show', $student) }}"
                                   class="inline-flex items-center gap-1 rounded-md border border-blue-200 bg-blue-50 px-2.5 py-1.5 text-xs font-semibold text-blue-700 hover:bg-blue-100">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                <a href="{{ route('admin.students.edit', $student) }}"
                                   class="inline-flex items-center gap-1 rounded-md border border-gray-200 bg-white px-2.5 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-50">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 text-gray-400">
                                <i class="fas fa-users text-xl"></i>
                            </div>
                            <p class="font-medium text-gray-700">
                                @if(request()->filled('search'))
                                    No students match your search
                                @else
                                    No students yet
                                @endif
                            </p>
                            <p class="mt-1 text-sm text-gray-500">
                                @if(request()->filled('search'))
                                    Try a different name, email, phone, or registration number.
                                @else
                                    Students appear here after they register or book a class.
                                @endif
                            </p>
                            @if(request()->filled('search'))
                                <a href="{{ route('admin.students.index') }}" class="mt-3 inline-block text-sm font-medium text-green-600 hover:underline">Clear search</a>
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($students->hasPages())
    <div class="mt-4">{{ $students->links() }}</div>
@endif
@endsection
