@extends('admin.layouts.master')

@section('title', 'Students')
@section('page-title', 'Students')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <form method="GET" class="flex gap-2 flex-1 max-w-lg">
        <input type="search" name="search" value="{{ request('search') }}" placeholder="Search name, email, phone, registration #..." class="flex-1 border rounded-lg px-4 py-2">
        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg">Search</button>
    </form>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Phone</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Bookings</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reg. #</th>
                <th class="px-6 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse($students as $student)
                <tr>
                    <td class="px-6 py-4 font-medium text-gray-900">{{ $student->name }}</td>
                    <td class="px-6 py-4 text-gray-600">{{ $student->email }}</td>
                    <td class="px-6 py-4 text-gray-600">{{ $student->phone ?? '—' }}</td>
                    <td class="px-6 py-4 text-gray-600">{{ $student->bookings_count }}</td>
                    <td class="px-6 py-4 text-gray-600">{{ $student->security_registration_number ?? '—' }}</td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('admin.students.show', $student) }}" class="text-blue-600 hover:underline">View</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-6 py-8 text-center text-gray-500">No students found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $students->links() }}</div>
@endsection
