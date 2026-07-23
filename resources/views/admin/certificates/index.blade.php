@extends('admin.layouts.master')

@section('title', 'Certificates')
@section('page-title', 'Certificates')

@section('content')
<div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
    <div>
        <h3 class="text-xl font-semibold text-gray-900">Course certificates</h3>
        <p class="mt-1 text-sm text-gray-500">Issued when students complete online modules (or non-blended bookings).</p>
    </div>
    <form method="GET" class="flex gap-2">
        <input type="search" name="q" value="{{ request('q') }}" placeholder="Search student, class, number…"
               class="rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500">
        <button type="submit" class="rounded-lg bg-slate-800 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-900">Search</button>
    </form>
</div>

<div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Certificate</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Student</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Course</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Issued</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($certificates as $certificate)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-mono text-xs font-semibold text-gray-800">{{ $certificate->certificate_number }}</td>
                        <td class="px-4 py-3">
                            <p class="font-semibold text-gray-900">{{ $certificate->student?->name }}</p>
                            <p class="text-xs text-gray-500">{{ $certificate->student?->email }}</p>
                        </td>
                        <td class="px-4 py-3 text-gray-700">{{ $certificate->service?->title }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ optional($certificate->issued_at)->format('M j, Y') }}</td>
                        <td class="px-4 py-3 text-right">
                            <div class="inline-flex gap-2">
                                <a href="{{ route('admin.certificates.show', $certificate) }}"
                                   class="rounded-lg border border-blue-200 bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700 hover:bg-blue-100">View</a>
                                <form method="POST" action="{{ route('admin.certificates.destroy', $certificate) }}"
                                      onsubmit="return confirm('Revoke this certificate?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-lg border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-700 hover:bg-red-100">Revoke</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">No certificates issued yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($certificates->hasPages())
        <div class="border-t border-gray-100 px-4 py-3">{{ $certificates->links() }}</div>
    @endif
</div>
@endsection
