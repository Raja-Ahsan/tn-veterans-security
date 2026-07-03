@extends('admin.layouts.master')

@section('title', 'Edit Location')
@section('page-title', 'Edit Location')

@section('content')
<div class="max-w-2xl bg-white rounded-lg shadow p-6">
    <form method="POST" action="{{ route('admin.locations.update', $location) }}" class="space-y-4">
        @csrf @method('PUT')
        @include('admin.locations._form')
        <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded-lg">Update Location</button>
    </form>
</div>
@endsection
