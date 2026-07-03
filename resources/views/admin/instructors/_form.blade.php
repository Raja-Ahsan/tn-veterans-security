<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
    <input type="text" name="name" value="{{ old('name', $instructor->name ?? '') }}" required class="w-full border rounded px-3 py-2">
</div>
<div class="grid grid-cols-2 gap-4">
    <div><label class="block text-sm font-medium text-gray-700 mb-1">Email</label><input type="email" name="email" value="{{ old('email', $instructor->email ?? '') }}" class="w-full border rounded px-3 py-2"></div>
    <div><label class="block text-sm font-medium text-gray-700 mb-1">Phone</label><input type="text" name="phone" value="{{ old('phone', $instructor->phone ?? '') }}" class="w-full border rounded px-3 py-2"></div>
</div>
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Bio</label>
    <textarea name="bio" rows="4" class="w-full border rounded px-3 py-2">{{ old('bio', $instructor->bio ?? '') }}</textarea>
</div>
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Order</label>
    <input type="number" name="order" value="{{ old('order', $instructor->order ?? 0) }}" min="0" class="w-full border rounded px-3 py-2">
</div>
<div>
    <label class="flex items-center gap-2"><input type="checkbox" name="is_active" value="1" {{ old('is_active', $instructor->is_active ?? true) ? 'checked' : '' }}> Active</label>
</div>
