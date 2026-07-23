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
        <label for="location_name" class="mb-1.5 block text-sm font-bold text-gray-700">
            Name <span class="text-red-500">*</span>
        </label>
        <input type="text"
               id="location_name"
               name="name"
               value="{{ old('name', $location->name ?? '') }}"
               required
               placeholder="e.g. Shooter's Guns, Ammo, and Range"
               class="w-full rounded-lg border px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-1 {{ $errors->has('name') ? 'border-red-400 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-green-500 focus:ring-green-500' }}">
        @error('name')
            <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="location_address" class="mb-1.5 block text-sm font-bold text-gray-700">Address</label>
        <div class="relative">
            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                <i class="fas fa-map-marker-alt text-sm"></i>
            </span>
            <input type="text"
                   id="location_address"
                   name="address"
                   value="{{ old('address', $location->address ?? '') }}"
                   placeholder="Street address"
                   class="w-full rounded-lg border border-gray-300 py-2.5 pl-9 pr-3 text-sm shadow-sm focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500">
        </div>
        @error('address')
            <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-6">
        <div class="sm:col-span-3">
            <label for="location_city" class="mb-1.5 block text-sm font-bold text-gray-700">City</label>
            <input type="text"
                   id="location_city"
                   name="city"
                   value="{{ old('city', $location->city ?? '') }}"
                   placeholder="Nashville"
                   class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm shadow-sm focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500">
            @error('city')
                <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div class="sm:col-span-1">
            <label for="location_state" class="mb-1.5 block text-sm font-bold text-gray-700">State</label>
            <input type="text"
                   id="location_state"
                   name="state"
                   value="{{ old('state', $location->state ?? '') }}"
                   placeholder="TN"
                   maxlength="20"
                   class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm shadow-sm focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500">
            @error('state')
                <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div class="sm:col-span-2">
            <label for="location_zip" class="mb-1.5 block text-sm font-bold text-gray-700">ZIP</label>
            <input type="text"
                   id="location_zip"
                   name="zip"
                   value="{{ old('zip', $location->zip ?? '') }}"
                   placeholder="37210"
                   class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm shadow-sm focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500">
            @error('zip')
                <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div>
        <label for="location_notes" class="mb-1.5 block text-sm font-bold text-gray-700">Notes</label>
        <textarea id="location_notes"
                  name="notes"
                  rows="3"
                  placeholder="Parking, suite info, special instructions…"
                  class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm shadow-sm focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500">{{ old('notes', $location->notes ?? '') }}</textarea>
        @error('notes')
            <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 sm:items-end">
        <div class="sm:max-w-[10rem]">
            <label for="location_order" class="mb-1.5 block text-sm font-bold text-gray-700">Order</label>
            <input type="number"
                   id="location_order"
                   name="order"
                   value="{{ old('order', $location->order ?? 0) }}"
                   min="0"
                   class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm shadow-sm focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500">
            @error('order')
                <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div class="rounded-lg border border-gray-200 bg-gray-50 px-4 py-3">
            <label class="flex cursor-pointer items-start gap-3">
                <input type="checkbox"
                       name="is_active"
                       value="1"
                       class="mt-0.5 h-4 w-4 rounded border-gray-300 text-green-600 focus:ring-green-500"
                       {{ old('is_active', $location->is_active ?? true) ? 'checked' : '' }}>
                <span>
                    <span class="block text-sm font-bold text-gray-800">Active</span>
                    <span class="mt-0.5 block text-xs text-gray-500">Inactive locations stay hidden from new schedule forms.</span>
                </span>
            </label>
        </div>
    </div>
</div>
