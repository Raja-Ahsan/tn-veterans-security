@extends('student.layouts.master')

@section('title', 'My Certificates')

@section('content')
<h1 class="text-2xl font-bold text-gray-800 mb-6">My Certificates</h1>

@if($certificates->isEmpty() && $guardForms->isEmpty())
    <div class="bg-white rounded-lg shadow p-8 text-center text-gray-600">
        <i class="fas fa-certificate text-4xl text-gray-300 mb-4"></i>
        <p>No certificates or state forms yet.</p>
        <p class="text-sm text-gray-500 mt-2">When your training is complete, your completion form will appear here.</p>
    </div>
@else
    @if($guardForms->isNotEmpty())
        <h2 class="mb-3 text-lg font-bold text-gray-900">State completion forms (IN-1144)</h2>
        <div class="mb-8 grid gap-4">
            @foreach($guardForms as $form)
                <div class="bg-white rounded-lg shadow p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h3 class="font-bold text-gray-900">Certificate of Successful Completion</h3>
                        <p class="text-sm text-gray-600">Form #{{ $form->form_number }}</p>
                        @if($form->service)
                            <p class="text-sm text-gray-500">{{ $form->service->title }}</p>
                        @endif
                        <p class="text-sm text-gray-500">Issued {{ optional($form->published_at)->format('M d, Y') }}</p>
                    </div>
                    <a href="{{ route('student.guard-training-forms.show', $form) }}" target="_blank"
                       class="inline-flex items-center justify-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
                        <i class="fas fa-download"></i> View / Download
                    </a>
                </div>
            @endforeach
        </div>
    @endif

    @if($certificates->isNotEmpty())
        <h2 class="mb-3 text-lg font-bold text-gray-900">Course certificates</h2>
        <div class="grid gap-4">
            @foreach($certificates as $certificate)
                <div class="bg-white rounded-lg shadow p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h3 class="font-bold text-gray-900">{{ $certificate->service->title }}</h3>
                        <p class="text-sm text-gray-600">Certificate #{{ $certificate->certificate_number }}</p>
                        <p class="text-sm text-gray-500">Issued {{ $certificate->issued_at->format('M d, Y') }}</p>
                    </div>
                    <a href="{{ route('student.certificates.show', $certificate) }}" target="_blank" class="inline-flex items-center justify-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
                        <i class="fas fa-download"></i> View / Print
                    </a>
                </div>
            @endforeach
        </div>
    @endif
@endif
@endsection
