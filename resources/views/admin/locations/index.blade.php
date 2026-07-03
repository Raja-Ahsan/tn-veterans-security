@extends('admin.layouts.master')

@section('title', 'Locations')
@section('page-title', 'Training Locations')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <p class="text-gray-600">Manage class locations used in scheduling.</p>
    <a href="{{ route('admin.locations.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg"><i class="fas fa-plus mr-2"></i>Add Location</a>
</div>
<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Address</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">City</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Active</th>
                <th class="px-6 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse($locations as $location)
                <tr>
                    <td class="px-6 py-4 font-medium">{{ $location->name }}</td>
                    <td class="px-6 py-4 text-gray-600">{{ $location->address ?? '—' }}</td>
                    <td class="px-6 py-4 text-gray-600">{{ $location->city ?? '—' }}</td>
                    <td class="px-6 py-4">{{ $location->is_active ? 'Yes' : 'No' }}</td>
                    <td class="px-6 py-4 text-right space-x-3">
                        <a href="{{ route('admin.locations.edit', $location) }}" class="text-blue-600 hover:underline">Edit</a>
                        <form action="{{ route('admin.locations.destroy', $location) }}" method="POST" class="inline" onsubmit="return confirm('Delete this location?')">@csrf @method('DELETE')<button type="submit" class="text-red-600 hover:underline">Delete</button></form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-6 py-8 text-center text-gray-500">No locations yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
