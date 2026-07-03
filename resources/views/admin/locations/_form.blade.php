<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
    <input type="text" name="name" value="{{ old('name', $location->name ?? '') }}" required class="w-full border rounded px-3 py-2">
</div>
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
    <input type="text" name="address" value="{{ old('address', $location->address ?? '') }}" class="w-full border rounded px-3 py-2">
</div>
<div class="grid grid-cols-3 gap-4">
    <div><label class="block text-sm font-medium text-gray-700 mb-1">City</label><input type="text" name="city" value="{{ old('city', $location->city ?? '') }}" class="w-full border rounded px-3 py-2"></div>
    <div><label class="block text-sm font-medium text-gray-700 mb-1">State</label><input type="text" name="state" value="{{ old('state', $location->state ?? '') }}" class="w-full border rounded px-3 py-2"></div>
    <div><label class="block text-sm font-medium text-gray-700 mb-1">ZIP</label><input type="text" name="zip" value="{{ old('zip', $location->zip ?? '') }}" class="w-full border rounded px-3 py-2"></div>
</div>
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
    <textarea name="notes" rows="3" class="w-full border rounded px-3 py-2">{{ old('notes', $location->notes ?? '') }}</textarea>
</div>
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Order</label>
    <input type="number" name="order" value="{{ old('order', $location->order ?? 0) }}" min="0" class="w-full border rounded px-3 py-2">
</div>
<div>
    <label class="flex items-center gap-2"><input type="checkbox" name="is_active" value="1" {{ old('is_active', $location->is_active ?? true) ? 'checked' : '' }}> Active</label>
</div>
