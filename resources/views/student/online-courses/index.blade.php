@extends('student.layouts.master')

@section('title', 'Online Courses')

@section('content')
<h1 class="text-2xl font-bold text-gray-800 mb-6">My Online Courses</h1>

@if($courses->isEmpty())
    <div class="bg-white rounded-lg shadow p-8 text-center text-gray-600">
        <p>Enroll in a blended course to access online modules and quizzes here.</p>
    </div>
@else
    <div class="grid gap-4">
        @foreach($courses as $course)
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">{{ $course->title }}</h2>
                        <p class="text-sm text-gray-600 mt-1">Progress: {{ $course->online_progress['completed'] }} / {{ $course->online_progress['total'] }} modules</p>
                        @if($course->online_progress['eligible_in_person'])
                            <p class="text-sm text-green-700 font-medium mt-1"><i class="fas fa-check-circle"></i> Eligible for in-person testing</p>
                        @endif
                    </div>
                    <a href="{{ route('student.online-course.index', $course) }}" class="inline-flex items-center justify-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
                        <i class="fas fa-play"></i> Continue Course
                    </a>
                </div>
            </div>
        @endforeach
    </div>
@endif
@endsection
