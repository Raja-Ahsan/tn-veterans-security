@extends('admin.layouts.master')

@section('title', 'Certificate '.$certificate->certificate_number)
@section('page-title', 'Course Certificate')

@section('content')
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
    <div>
        <a href="{{ route('admin.certificates.index') }}" class="mb-2 inline-flex items-center gap-1.5 text-sm font-medium text-gray-500 hover:text-green-700">
            <i class="fas fa-arrow-left text-xs"></i> Back to certificates
        </a>
        <h3 class="text-xl font-semibold text-gray-900">{{ $certificate->certificate_number }}</h3>
        <p class="mt-1 text-sm text-gray-500">Internal course certificate for {{ $certificate->student?->name }}</p>
    </div>
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('admin.certificates.print', $certificate) }}" target="_blank"
           class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-green-700">
            <i class="fas fa-print text-xs"></i> Print / PDF
        </a>
        <form method="POST" action="{{ route('admin.certificates.destroy', $certificate) }}"
              onsubmit="return confirm('Revoke this certificate?')">
            @csrf
            @method('DELETE')
            <button type="submit"
                    class="inline-flex items-center gap-2 rounded-lg border border-red-200 bg-white px-4 py-2.5 text-sm font-semibold text-red-700 hover:bg-red-50">
                <i class="fas fa-ban text-xs"></i> Revoke
            </button>
        </form>
    </div>
</div>

<div class="mb-6 grid gap-4 lg:grid-cols-3">
    <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm lg:col-span-2">
        <div class="mb-4 flex items-center gap-3 border-b border-gray-100 pb-4">
            <span class="inline-flex h-11 w-11 items-center justify-center rounded-xl bg-green-50 text-green-700">
                <i class="fas fa-certificate text-lg"></i>
            </span>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Course certificate</p>
                <p class="font-mono text-base font-bold text-gray-900">{{ $certificate->certificate_number }}</p>
            </div>
        </div>

        <dl class="grid gap-5 sm:grid-cols-2">
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">Student</dt>
                <dd class="mt-1.5">
                    <p class="font-semibold text-gray-900">{{ $certificate->student?->name }}</p>
                    <p class="text-sm text-gray-500">{{ $certificate->student?->email }}</p>
                </dd>
            </div>
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">Course</dt>
                <dd class="mt-1.5 font-semibold text-gray-900">{{ $certificate->service?->title }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">Issued</dt>
                <dd class="mt-1.5 text-gray-900">{{ optional($certificate->issued_at)->format('F j, Y') }}</dd>
                <dd class="text-sm text-gray-500">{{ optional($certificate->issued_at)->format('g:i A') }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">Status</dt>
                <dd class="mt-1.5">
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-green-100 px-2.5 py-1 text-xs font-semibold text-green-800">
                        <i class="fas fa-check text-[10px]"></i> Issued
                    </span>
                </dd>
            </div>
        </dl>
    </div>

    <div class="rounded-xl border border-green-200 bg-gradient-to-br from-green-50 to-white p-5 shadow-sm">
        <div class="mb-3 inline-flex h-10 w-10 items-center justify-center rounded-lg bg-green-600 text-white">
            <i class="fas fa-file-signature"></i>
        </div>
        <h4 class="text-base font-bold text-gray-900">Need the state form?</h4>
        <p class="mt-2 text-sm leading-relaxed text-gray-600">
            Official Tennessee <span class="font-semibold text-gray-800">Form IN-1144</span> includes scores, weapons, trainer, SSN, and more.
        </p>
        <a href="{{ route('admin.guard-training-forms.create', ['student_id' => $certificate->student_id, 'booking_id' => $certificate->service_booking_id]) }}"
           class="mt-4 inline-flex w-full items-center justify-center gap-2 rounded-lg bg-green-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-green-700">
            Create State Form
            <i class="fas fa-arrow-right text-xs"></i>
        </a>
        <a href="{{ route('admin.guard-training-forms.index') }}"
           class="mt-2 inline-flex w-full items-center justify-center rounded-lg px-4 py-2 text-sm font-medium text-green-800 hover:bg-green-100/60">
            View all State Forms
        </a>
    </div>
</div>
@endsection
