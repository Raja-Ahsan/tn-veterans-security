@if($errors->any())
    <div class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
        <p class="font-semibold"><i class="fas fa-exclamation-circle mr-1"></i> Please fix these issues:</p>
        <ul class="mt-2 list-disc space-y-1 pl-5">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="space-y-5">
    <div>
        <label for="instructor_name" class="mb-1.5 block text-sm font-bold text-gray-700">Name <span class="text-red-500">*</span></label>
        <input type="text" id="instructor_name" name="name" value="{{ old('name', $instructor->name ?? '') }}" required
               placeholder="e.g. Jayson"
               class="w-full rounded-lg border px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-1 {{ $errors->has('name') ? 'border-red-400 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-green-500 focus:ring-green-500' }}">
        @error('name')
            <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
            <label for="instructor_email" class="mb-1.5 block text-sm font-bold text-gray-700">Email</label>
            <div class="relative">
                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                    <i class="fas fa-envelope text-sm"></i>
                </span>
                <input type="email" id="instructor_email" name="email" value="{{ old('email', $instructor->email ?? '') }}"
                       placeholder="name@example.com"
                       class="w-full rounded-lg border border-gray-300 py-2.5 pl-9 pr-3 text-sm shadow-sm focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500">
            </div>
            @error('email')
                <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label for="instructor_phone" class="mb-1.5 block text-sm font-bold text-gray-700">Phone</label>
            <div class="relative">
                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                    <i class="fas fa-phone text-sm"></i>
                </span>
                <input type="text" id="instructor_phone" name="phone" value="{{ old('phone', $instructor->phone ?? '') }}"
                       placeholder="615-555-0100"
                       class="w-full rounded-lg border border-gray-300 py-2.5 pl-9 pr-3 text-sm shadow-sm focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500">
            </div>
            @error('phone')
                <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div>
        <label for="instructor_bio" class="mb-1.5 block text-sm font-bold text-gray-700">Bio</label>
        <textarea id="instructor_bio" name="bio" rows="4"
                  placeholder="Short background or credentials (optional)…"
                  class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm shadow-sm focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500">{{ old('bio', $instructor->bio ?? '') }}</textarea>
        @error('bio')
            <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
            <label for="instructor_order" class="mb-1.5 block text-sm font-bold text-gray-700">Display order</label>
            <input type="number" id="instructor_order" name="order" value="{{ old('order', $instructor->order ?? 0) }}" min="0"
                   class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm shadow-sm focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500">
            <p class="mt-1 text-xs text-gray-500">Lower numbers appear first in schedule dropdowns.</p>
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-bold text-gray-700">Status</label>
            <input type="hidden" name="is_active" value="0">
            <label for="instructor_is_active"
                   class="flex h-[42px] cursor-pointer items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 transition-colors hover:border-green-300 has-[:checked]:border-green-500 has-[:checked]:bg-green-50">
                <input type="checkbox" id="instructor_is_active" name="is_active" value="1"
                       {{ (string) old('is_active', ($instructor->is_active ?? true) ? '1' : '0') === '1' ? 'checked' : '' }}
                       class="rounded border-gray-400 text-green-600 focus:ring-green-500">
                <span class="text-sm font-semibold text-gray-900">Active</span>
            </label>
            <p class="mt-1 text-xs text-gray-500">Uncheck to hide from new class schedules.</p>
        </div>
    </div>
</div>
