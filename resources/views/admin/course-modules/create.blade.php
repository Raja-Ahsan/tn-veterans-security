@extends('admin.layouts.master')

@section('title', 'Add Module')
@section('page-title', 'Add Module')

@section('content')
<div class="max-w-3xl bg-white rounded-lg shadow p-6">
    <form method="POST" action="{{ route('admin.services.course-modules.store', $service) }}" class="space-y-4">
        @csrf
        @include('admin.course-modules._form', ['courseModule' => null])
        <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded-lg">Save Module</button>
    </form>
</div>
@endsection
