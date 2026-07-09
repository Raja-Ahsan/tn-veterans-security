@php
    use Illuminate\Support\Str;
    $search = $search ?? '';
@endphp
@forelse($services as $service)
    <tr>
        <td class="px-6 py-4 whitespace-nowrap">
            @if ($service->image)
                <img src="{{ $service->image_url }}" alt="{{ $service->title }}" class="h-16 w-16 object-cover rounded">
            @else
                <div class="h-16 w-16 bg-gray-200 rounded flex items-center justify-center">
                    <i class="fas fa-image text-gray-400"></i>
                </div>
            @endif
        </td>
        <td class="px-6 py-4">
            <div class="text-sm font-medium text-gray-900">{{ $service->title }}</div>
            @if ($service->short_description)
                <div class="text-sm text-gray-500">{{ Str::limit($service->short_description, 50) }}</div>
            @elseif ($service->description)
                <div class="text-sm text-gray-500">{{ Str::limit(strip_tags($service->description), 50) }}</div>
            @endif
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
            <div class="text-sm">
                @if ($service->price)
                    <div class="font-semibold text-gray-900">${{ number_format($service->price, 2) }}</div>
                @else
                    <div class="text-gray-400">Not set</div>
                @endif
                @if ($service->deposit_amount)
                    <div class="text-xs text-gray-500">Deposit: ${{ number_format($service->deposit_amount, 2) }}</div>
                @endif
            </div>
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
            <div class="text-sm">
                @if ($service->class_type)
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                        {{ $service->class_type === 'one-on-one' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                        {{ $service->class_type === 'one-on-one' ? 'One-on-One' : 'Group' }}
                    </span>
                @else
                    <span class="text-gray-400 text-xs">Not set</span>
                @endif
                @if ($service->has_online_parts)
                    <div class="text-xs text-gray-500 mt-1">
                        <i class="fas fa-globe mr-1"></i> Online parts
                    </div>
                @endif
            </div>
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
            <span class="font-semibold">{{ $service->class_schedules_count ?? 0 }}</span>
            <span class="text-gray-400"> / </span>
            @if (($service->class_schedules_count ?? 0) > 0)
                <a href="{{ route('admin.class-schedules.index', ['expand_service' => $service->id]) }}"
                    class="text-blue-600 hover:underline text-xs"
                    title="View schedules for this class">calendar</a>
            @else
                <a href="{{ route('admin.class-schedules.create', ['service_id' => $service->id]) }}"
                    class="text-blue-600 hover:underline text-xs"
                    title="Create a schedule for this class">calendar</a>
            @endif
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $service->order }}</td>
        <td class="px-6 py-4 whitespace-nowrap">
            @if ($service->is_active)
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Active</span>
            @else
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Inactive</span>
            @endif
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
            <div class="flex gap-2">
                <a href="{{ route('admin.classes.edit', $service) }}" class="text-blue-600 hover:text-blue-900" title="Edit">
                    <i class="fas fa-edit"></i>
                </a>
                <form method="POST" action="{{ route('admin.classes.destroy', $service) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this service?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-600 hover:text-red-900" title="Delete">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="8" class="px-6 py-4 text-center text-gray-500">
            @if ($search !== '')
                No classes match your search.
            @else
                No services found.
            @endif
            <a href="{{ route('admin.classes.create') }}" class="text-green-600 hover:underline">Create one now</a>
        </td>
    </tr>
@endforelse
