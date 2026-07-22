@php
    use Illuminate\Support\Str;
    $search = $search ?? '';
@endphp
@forelse($services as $service)
    @php
        $sessionCount = $service->class_schedules_count ?? 0;
    @endphp
    <tr class="hover:bg-gray-50 transition-colors">
        <td class="px-6 py-4">
            <div class="flex items-start gap-3">
                @if ($service->image)
                    <img src="{{ $service->image_url }}" alt="{{ $service->title }}" class="h-12 w-12 shrink-0 object-cover rounded-md ring-1 ring-gray-200">
                @else
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-md bg-gray-100 ring-1 ring-gray-200">
                        <i class="fas fa-graduation-cap text-gray-400"></i>
                    </div>
                @endif
                <div class="min-w-0">
                    <div class="text-sm font-semibold text-gray-900">{{ $service->title }}</div>
                    @if ($service->short_description)
                        <div class="mt-0.5 text-xs text-gray-500 line-clamp-2">{{ Str::limit($service->short_description, 80) }}</div>
                    @elseif ($service->description)
                        <div class="mt-0.5 text-xs text-gray-500 line-clamp-2">{{ Str::limit(strip_tags($service->description), 80) }}</div>
                    @endif
                </div>
            </div>
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
            @if ($service->price)
                <div class="text-sm font-semibold text-gray-900">${{ number_format($service->price, 2) }}</div>
                @if ($service->deposit_amount)
                    <div class="text-xs text-gray-500">Deposit ${{ number_format($service->deposit_amount, 2) }}</div>
                @endif
            @else
                <span class="inline-flex items-center rounded-md bg-amber-50 px-2 py-0.5 text-xs font-medium text-amber-700 ring-1 ring-inset ring-amber-200">Price not set</span>
            @endif
        </td>
        <td class="px-6 py-4">
            <div class="flex max-w-[11rem] flex-wrap gap-1.5">
                @if ($service->class_type)
                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold
                        {{ $service->class_type === 'one-on-one' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                        {{ $service->class_type === 'one-on-one' ? 'One-on-One' : 'Group' }}
                    </span>
                @else
                    <span class="text-xs text-gray-400">Not set</span>
                @endif
                @if ($service->has_online_parts)
                    <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-semibold text-slate-700">
                        <i class="fas fa-globe text-[10px]"></i> Blended
                    </span>
                @endif
            </div>
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
            @if ($sessionCount > 0)
                <a href="{{ route('admin.class-schedules.index', ['expand_service' => $service->id]) }}"
                    class="group inline-flex flex-col gap-0.5"
                    title="View sessions on Class Schedules">
                    <span class="inline-flex items-center gap-1.5 rounded-md bg-blue-50 px-2.5 py-1 text-sm font-semibold text-blue-700 ring-1 ring-inset ring-blue-200 group-hover:bg-blue-100">
                        <i class="fas fa-calendar-day text-blue-500"></i>
                        {{ $sessionCount }} {{ Str::plural('session', $sessionCount) }}
                    </span>
                    <span class="text-xs text-blue-600 group-hover:underline">View calendar →</span>
                </a>
            @else
                <a href="{{ route('admin.class-schedules.create', ['service_id' => $service->id]) }}"
                    class="group inline-flex flex-col gap-0.5"
                    title="Add the first session for this class">
                    <span class="inline-flex items-center gap-1.5 rounded-md bg-gray-50 px-2.5 py-1 text-sm font-medium text-gray-600 ring-1 ring-inset ring-gray-200 group-hover:bg-green-50 group-hover:text-green-700 group-hover:ring-green-200">
                        <i class="fas fa-calendar-plus"></i>
                        No sessions yet
                    </span>
                    <span class="text-xs text-green-600 group-hover:underline">Add a session →</span>
                </a>
            @endif
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $service->order }}</td>
        <td class="px-6 py-4 whitespace-nowrap">
            @if ($service->is_active)
                <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-semibold text-green-800">Active</span>
            @else
                <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-semibold text-red-800">Inactive</span>
            @endif
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.classes.edit', $service) }}" class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-800" title="Edit class">
                    <i class="fas fa-edit"></i>
                    <span class="hidden lg:inline">Edit</span>
                </a>
                <form method="POST" action="{{ route('admin.classes.destroy', $service) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this class?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center gap-1 text-red-600 hover:text-red-800" title="Delete">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="7" class="px-6 py-10 text-center">
            <i class="fas fa-graduation-cap text-3xl text-gray-300 mb-3"></i>
            <p class="text-gray-500">
                @if ($search !== '')
                    No classes match your search.
                @else
                    No classes yet.
                @endif
            </p>
            <a href="{{ route('admin.classes.create') }}" class="mt-2 inline-block text-green-600 hover:underline font-medium">Create your first class</a>
        </td>
    </tr>
@endforelse
