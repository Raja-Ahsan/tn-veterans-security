@extends('admin.layouts.master')

@section('title', 'Add Location')
@section('page-title', 'Add Location')

@section('content')
<div class="max-w-2xl bg-white rounded-lg shadow p-6">
    <form method="POST" action="{{ route('admin.locations.store') }}" class="space-y-4">
        @csrf
        @include('admin.locations._form')
        <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded-lg">Save Location</button>
    </form>
</div>
@endsection
