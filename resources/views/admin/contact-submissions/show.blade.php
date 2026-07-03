@extends('admin.layouts.master')

@section('title', 'Contact Submission')
@section('page-title', 'Contact Submission')

@section('content')
<div class="max-w-3xl bg-white rounded-lg shadow p-6 space-y-4">
    <div class="grid grid-cols-2 gap-4">
        <div><span class="text-sm text-gray-500">Date</span><p class="font-medium">{{ $contactSubmission->created_at->format('M d, Y g:i A') }}</p></div>
        <div><span class="text-sm text-gray-500">Status</span><p class="font-medium">{{ ucfirst($contactSubmission->status) }}</p></div>
        <div><span class="text-sm text-gray-500">Name</span><p class="font-medium">{{ $contactSubmission->full_name }}</p></div>
        <div><span class="text-sm text-gray-500">Email</span><p class="font-medium"><a href="mailto:{{ $contactSubmission->email }}" class="text-blue-600">{{ $contactSubmission->email }}</a></p></div>
        <div class="col-span-2"><span class="text-sm text-gray-500">Subject</span><p class="font-medium">{{ $contactSubmission->subject ?? 'General Inquiry' }}</p></div>
    </div>
    <div>
        <span class="text-sm text-gray-500">Message</span>
        <p class="mt-1 whitespace-pre-wrap bg-gray-50 p-4 rounded">{{ $contactSubmission->message }}</p>
    </div>
    <form method="POST" action="{{ route('admin.contact-submissions.update-status', $contactSubmission) }}" class="flex gap-2 items-end">
        @csrf @method('PUT')
        <div>
            <label class="block text-sm text-gray-500 mb-1">Update Status</label>
            <select name="status" class="border rounded px-3 py-2">
                @foreach(['new','read','responded','archived'] as $status)
                    <option value="{{ $status }}" @selected($contactSubmission->status === $status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Update</button>
    </form>
    <a href="{{ route('admin.contact-submissions.index') }}" class="inline-block text-blue-600 hover:underline">← Back</a>
</div>
@endsection
