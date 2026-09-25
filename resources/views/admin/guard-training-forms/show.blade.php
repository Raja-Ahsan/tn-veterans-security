@extends('admin.layouts.master')

@section('title', 'State Form')
@section('page-title', 'Guard Training Form')

@section('content')
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
    <div>
        <h3 class="text-xl font-semibold text-gray-900">{{ $form->form_number }}</h3>
        <p class="mt-1 text-sm text-gray-500">
            {{ $form->studentDisplayName() }}
            ·
            @if($form->isPublished())
                <span class="font-semibold text-green-700">Published {{ optional($form->published_at)->format('M j, Y') }}</span>
            @else
                <span class="font-semibold text-amber-700">Draft</span>
            @endif
        </p>
    </div>
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('admin.guard-training-forms.edit', $form) }}" class="rounded-md border border-slate-300 bg-white px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Edit</a>
        <a href="{{ route('admin.guard-training-forms.print', $form) }}" target="_blank" class="rounded-md border border-green-200 bg-green-50 px-3 py-2 text-sm font-medium text-green-800 hover:bg-green-100">Download / Print</a>
        @unless($form->isPublished())
            <form method="POST" action="{{ route('admin.guard-training-forms.publish', $form) }}">
                @csrf
                <input type="hidden" name="notify_student" value="1">
                <button type="submit" class="rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white hover:bg-green-700">Publish & Email Student</button>
            </form>
        @endunless
        <a href="{{ route('admin.guard-training-forms.index') }}" class="rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">← Back</a>
    </div>
</div>

<div class="grid gap-4 lg:grid-cols-2">
    <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm space-y-2 text-sm">
        <h4 class="font-bold text-gray-900">Student</h4>
        <p><span class="text-gray-500">Name:</span> {{ $form->studentDisplayName() }}</p>
        <p><span class="text-gray-500">Email:</span> {{ $form->student?->email }}</p>
        <p><span class="text-gray-500">Registration #:</span> {{ $form->registration_number ?: '—' }}</p>
        <p><span class="text-gray-500">SSN:</span> {{ $form->formattedSsn() ?: '—' }}</p>
        <p><span class="text-gray-500">Type:</span> {{ $form->registrationTypeLabel() }}</p>
        <p><span class="text-gray-500">Class:</span> {{ $form->service?->title ?: '—' }}</p>
    </div>
    <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm space-y-2 text-sm">
        <h4 class="font-bold text-gray-900">Trainer</h4>
        <p><span class="text-gray-500">Name:</span> {{ $form->trainer_name ?: '—' }}</p>
        <p><span class="text-gray-500">Cert #:</span> {{ $form->trainer_certification_number ?: '—' }}</p>
        <p><span class="text-gray-500">Email:</span> {{ $form->trainer_email ?: '—' }}</p>
        <p><span class="text-gray-500">Phone:</span> {{ $form->trainer_phone ?: '—' }}</p>
        <p><span class="text-gray-500">Created by:</span> {{ $form->creator?->name ?: '—' }}</p>
    </div>
</div>

<form method="POST" action="{{ route('admin.guard-training-forms.destroy', $form) }}" class="mt-6" onsubmit="return confirm('Delete this form?');">
    @csrf
    @method('DELETE')
    <button type="submit" class="rounded-md border border-red-200 bg-red-50 px-3 py-2 text-sm font-semibold text-red-700 hover:bg-red-100">Delete form</button>
</form>
@endsection
