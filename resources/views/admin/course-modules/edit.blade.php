@extends('admin.layouts.master')

@section('title', 'Edit Module')
@section('page-title', 'Edit Module')

@section('content')
<div class="max-w-3xl bg-white rounded-lg shadow p-6">
    <form method="POST" action="{{ route('admin.classes.course-modules.update', [$service, $courseModule]) }}" class="space-y-4">
        @csrf @method('PUT')
        @include('admin.course-modules._form', ['courseModule' => $courseModule])
        <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded-lg">Update Module</button>
    </form>
</div>
@endsection
