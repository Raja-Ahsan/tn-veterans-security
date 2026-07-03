@extends('admin.layouts.master')

@section('title', 'Instructors')
@section('page-title', 'Instructors')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <p class="text-gray-600">Manage instructors assigned to class schedules.</p>
    <a href="{{ route('admin.instructors.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg"><i class="fas fa-plus mr-2"></i>Add Instructor</a>
</div>
<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Phone</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Active</th>
                <th class="px-6 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse($instructors as $instructor)
                <tr>
                    <td class="px-6 py-4 font-medium">{{ $instructor->name }}</td>
                    <td class="px-6 py-4 text-gray-600">{{ $instructor->email ?? '—' }}</td>
                    <td class="px-6 py-4 text-gray-600">{{ $instructor->phone ?? '—' }}</td>
                    <td class="px-6 py-4">{{ $instructor->is_active ? 'Yes' : 'No' }}</td>
                    <td class="px-6 py-4 text-right space-x-3">
                        <a href="{{ route('admin.instructors.edit', $instructor) }}" class="text-blue-600 hover:underline">Edit</a>
                        <form action="{{ route('admin.instructors.destroy', $instructor) }}" method="POST" class="inline" onsubmit="return confirm('Delete this instructor?')">@csrf @method('DELETE')<button type="submit" class="text-red-600 hover:underline">Delete</button></form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-6 py-8 text-center text-gray-500">No instructors yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
