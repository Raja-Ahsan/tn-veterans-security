@extends('admin.layouts.master')

@section('title', 'Certificates')
@section('page-title', 'Certificates')

@section('content')
<div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
    <div>
        <h3 class="text-xl font-semibold text-gray-900">Course certificates</h3>
        <p class="mt-1 text-sm text-gray-500">Internal branded certificates (TNV-…). Official TN Form IN-1144 lives under State Forms.</p>
    </div>
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
        <a href="{{ route('admin.guard-training-forms.index') }}"
           class="inline-flex items-center justify-center gap-2 rounded-lg border border-green-200 bg-green-50 px-4 py-2.5 text-sm font-semibold text-green-800 hover:bg-green-100">
            <i class="fas fa-file-signature"></i> State Forms
        </a>
        <form method="GET" class="flex gap-2">
            <input type="search" name="q" value="{{ request('q') }}" placeholder="Search student, class, number…"
                   class="min-w-[220px] rounded-lg border border-gray-300 px-3 py-2.5 text-sm shadow-sm focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500">
            <button type="submit" class="rounded-lg bg-slate-800 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-900">Search</button>
        </form>
    </div>
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
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center gap-2 font-mono text-xs font-semibold text-gray-800">
                                <span class="inline-flex h-7 w-7 items-center justify-center rounded-lg bg-green-50 text-green-700">
                                    <i class="fas fa-check text-[10px]"></i>
                                </span>
                                {{ $certificate->certificate_number }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <p class="font-semibold text-gray-900">{{ $certificate->student?->name }}</p>
                            <p class="text-xs text-gray-500">{{ $certificate->student?->email }}</p>
                        </td>
                        <td class="px-4 py-3 text-gray-700">{{ $certificate->service?->title }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ optional($certificate->issued_at)->format('M j, Y') }}</td>
                        <td class="px-4 py-3 text-right">
                            <div class="inline-flex flex-wrap justify-end gap-2">
                                <a href="{{ route('admin.certificates.show', $certificate) }}"
                                   class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50">View</a>
                                <a href="{{ route('admin.certificates.print', $certificate) }}" target="_blank"
                                   class="rounded-lg border border-green-200 bg-green-50 px-3 py-1.5 text-xs font-semibold text-green-700 hover:bg-green-100">Print</a>
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
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-certificate mb-3 text-3xl text-gray-300"></i>
                            <p>No certificates issued yet.</p>
                        </td>
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
