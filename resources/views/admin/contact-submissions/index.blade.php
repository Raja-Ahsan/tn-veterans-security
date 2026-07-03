@extends('admin.layouts.master')

@section('title', 'Contact Submissions')
@section('page-title', 'Contact Form Submissions')

@section('content')
<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subject</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse($submissions as $submission)
                <tr>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $submission->created_at->format('M d, Y g:i A') }}</td>
                    <td class="px-6 py-4 font-medium">{{ $submission->full_name }}</td>
                    <td class="px-6 py-4 text-gray-600">{{ $submission->email }}</td>
                    <td class="px-6 py-4 text-gray-600">{{ $submission->subject ?? '—' }}</td>
                    <td class="px-6 py-4"><span class="px-2 py-1 text-xs rounded-full {{ $submission->status === 'new' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">{{ ucfirst($submission->status) }}</span></td>
                    <td class="px-6 py-4 text-right"><a href="{{ route('admin.contact-submissions.show', $submission) }}" class="text-blue-600 hover:underline">View</a></td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-6 py-8 text-center text-gray-500">No submissions yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $submissions->links() }}</div>
@endsection
