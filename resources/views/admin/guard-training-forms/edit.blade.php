@extends('admin.layouts.master')

@section('title', 'Edit State Form')
@section('page-title', 'Edit Guard Training Form')

@section('content')
<div class="mb-6 overflow-hidden rounded-2xl border border-green-100 bg-gradient-to-br from-green-50 via-white to-slate-50 p-5 shadow-sm sm:p-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div class="flex items-start gap-4">
            <span class="inline-flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-green-600 text-white shadow-sm">
                <i class="fas fa-file-signature text-lg"></i>
            </span>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-green-700">Tennessee Form IN-1144</p>
                <h3 class="mt-1 text-xl font-bold text-gray-900">{{ $form->form_number }}</h3>
                <p class="mt-1 text-sm text-gray-600">
                    Status:
                    @if($form->isPublished())
                        <span class="inline-flex items-center gap-1 rounded-full bg-green-100 px-2 py-0.5 text-xs font-semibold text-green-800">
                            <i class="fas fa-check text-[10px]"></i> Published
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-2 py-0.5 text-xs font-semibold text-amber-800">
                            Draft
                        </span>
                    @endif
                </p>
            </div>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.guard-training-forms.print', $form) }}" target="_blank"
               class="inline-flex items-center gap-2 rounded-lg border border-green-200 bg-white px-3.5 py-2 text-sm font-semibold text-green-800 hover:bg-green-50">
                <i class="fas fa-download text-xs"></i> Download
            </a>
            <a href="{{ route('admin.guard-training-forms.index') }}"
               class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-3.5 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                <i class="fas fa-arrow-left text-xs"></i> All forms
            </a>
        </div>
    </div>
</div>

<form method="POST" action="{{ route('admin.guard-training-forms.update', $form) }}" class="space-y-6 pb-10">
    @csrf
    @method('PUT')
    @include('admin.guard-training-forms._form', ['form' => $form, 'students' => $students, 'services' => $services, 'bookings' => $bookings])
</form>
@endsection
