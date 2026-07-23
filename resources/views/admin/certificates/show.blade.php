@extends('admin.layouts.master')

@section('title', 'Certificate '.$certificate->certificate_number)
@section('page-title', 'Certificate')

@section('content')
<div class="mb-5 flex flex-wrap items-center justify-between gap-3">
    <a href="{{ route('admin.certificates.index') }}" class="text-sm font-medium text-gray-600 hover:text-green-700">
        ← Back to certificates
    </a>
    <div class="flex gap-2">
        <a href="{{ route('admin.certificates.print', $certificate) }}" target="_blank"
           class="rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-700">Print / PDF</a>
        <form method="POST" action="{{ route('admin.certificates.destroy', $certificate) }}"
              onsubmit="return confirm('Revoke this certificate?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="rounded-lg border border-red-300 bg-white px-4 py-2 text-sm font-semibold text-red-700 hover:bg-red-50">Revoke</button>
        </form>
    </div>
</div>

<div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
    <dl class="grid gap-4 sm:grid-cols-2">
        <div>
            <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">Certificate #</dt>
            <dd class="mt-1 font-mono font-semibold text-gray-900">{{ $certificate->certificate_number }}</dd>
        </div>
        <div>
            <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">Issued</dt>
            <dd class="mt-1 text-gray-900">{{ optional($certificate->issued_at)->format('F j, Y g:i A') }}</dd>
        </div>
        <div>
            <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">Student</dt>
            <dd class="mt-1 text-gray-900">{{ $certificate->student?->name }} ({{ $certificate->student?->email }})</dd>
        </div>
        <div>
            <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">Course</dt>
            <dd class="mt-1 text-gray-900">{{ $certificate->service?->title }}</dd>
        </div>
    </dl>
</div>
@endsection
