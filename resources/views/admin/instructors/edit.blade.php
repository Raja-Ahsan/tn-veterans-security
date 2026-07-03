@extends('admin.layouts.master')

@section('title', 'Edit Instructor')
@section('page-title', 'Edit Instructor')

@section('content')
<div class="max-w-2xl bg-white rounded-lg shadow p-6">
    <form method="POST" action="{{ route('admin.instructors.update', $instructor) }}" class="space-y-4">
        @csrf @method('PUT')
        @include('admin.instructors._form')
        <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded-lg">Update Instructor</button>
    </form>
</div>
@endsection
