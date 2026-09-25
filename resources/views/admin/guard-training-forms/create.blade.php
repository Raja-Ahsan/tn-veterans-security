@extends('admin.layouts.master')

@section('title', 'New State Form')
@section('page-title', 'New Guard Training Form')

@section('content')
<div class="mb-6 overflow-hidden rounded-2xl border border-green-100 bg-gradient-to-br from-green-50 via-white to-slate-50 p-5 shadow-sm sm:p-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div class="flex items-start gap-4">
            <span class="inline-flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-green-600 text-white shadow-sm">
                <i class="fas fa-file-signature text-lg"></i>
            </span>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-green-700">Tennessee Form IN-1144</p>
                <h3 class="mt-1 text-xl font-bold text-gray-900">Certificate of Successful Completion</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-600">
                    Select a student to auto-fill name, SSN, and registration. Complete weapons, scores, and trainer details, then send to the student dashboard.
                </p>
            </div>
        </div>
        <a href="{{ route('admin.guard-training-forms.index') }}"
           class="inline-flex items-center gap-2 self-start rounded-lg border border-gray-300 bg-white px-3.5 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
            <i class="fas fa-arrow-left text-xs"></i> All forms
        </a>
    </div>
</div>

<form method="POST" action="{{ route('admin.guard-training-forms.store') }}" class="space-y-6 pb-10">
    @csrf
    @include('admin.guard-training-forms._form', ['form' => $form, 'students' => $students, 'services' => $services, 'bookings' => $bookings])
</form>
@endsection
