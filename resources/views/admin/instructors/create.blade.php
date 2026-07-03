@extends('admin.layouts.master')

@section('title', 'Add Instructor')
@section('page-title', 'Add Instructor')

@section('content')
<div class="max-w-2xl bg-white rounded-lg shadow p-6">
    <form method="POST" action="{{ route('admin.instructors.store') }}" class="space-y-4">
        @csrf
        @include('admin.instructors._form')
        <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded-lg">Save Instructor</button>
    </form>
</div>
@endsection
