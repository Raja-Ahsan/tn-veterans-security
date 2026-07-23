@extends('student.layouts.master')

@section('title', 'My Certificates')

@section('content')
<h1 class="text-2xl font-bold text-gray-800 mb-6">My Certificates</h1>

@if($certificates->isEmpty())
    <div class="bg-white rounded-lg shadow p-8 text-center text-gray-600">
        <i class="fas fa-certificate text-4xl text-gray-300 mb-4"></i>
        <p>No certificates yet. Certificates are issued for completed non-blended courses when available.</p>
        <p class="text-sm text-gray-500 mt-2">Certificates are issued automatically when you pass every required module quiz.</p>
    </div>
@else
    <div class="grid gap-4">
        @foreach($certificates as $certificate)
            <div class="bg-white rounded-lg shadow p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="font-bold text-gray-900">{{ $certificate->service->title }}</h2>
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
@endsection
