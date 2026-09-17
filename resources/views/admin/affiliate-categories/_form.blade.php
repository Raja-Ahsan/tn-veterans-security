@if($errors->any())
    <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
        <p class="font-semibold"><i class="fas fa-exclamation-circle mr-1"></i> Please fix these issues:</p>
        <ul class="mt-2 list-disc space-y-1 pl-5">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
    <div class="space-y-5">
        <div>
            <label for="category_name" class="mb-1.5 block text-sm font-semibold text-gray-700">Name <span class="text-red-500">*</span></label>
            <input type="text" id="category_name" name="name" value="{{ old('name', $category->name ?? '') }}" required
                   placeholder="e.g. Veteran Owned Security Companies"
                   class="w-full rounded-lg border px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-0 {{ $errors->has('name') ? 'border-red-400 focus:border-red-500 focus:ring-red-200' : 'border-gray-300 focus:border-green-500 focus:ring-green-100' }}">
        </div>

        <div>
            <label for="category_page" class="mb-1.5 block text-sm font-semibold text-gray-700">Show on page <span class="text-red-500">*</span></label>
            <select id="category_page" name="page" required
                    class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm shadow-sm focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-100 focus:ring-offset-0">
                @foreach(\App\Models\AffiliateCategory::pageOptions() as $value => $label)
                    <option value="{{ $value }}" @selected(old('page', $category->page ?? 'partners') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="space-y-5 rounded-xl border border-gray-200 bg-slate-50 p-4">
        <div>
            <label for="category_order" class="mb-1.5 block text-sm font-semibold text-gray-700">Sort order</label>
            <input type="number" id="category_order" name="order" min="0"
                   value="{{ old('order', $category->order ?? 0) }}"
                   class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm shadow-sm focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-100 focus:ring-offset-0">
            <p class="mt-1 text-xs text-gray-500">Smaller numbers appear first on the page.</p>
        </div>
        <label class="flex cursor-pointer items-start gap-3 rounded-lg border border-gray-200 bg-white px-3 py-3">
            <input type="checkbox" name="is_active" value="1" class="mt-0.5 h-4 w-4 rounded border-gray-300 text-green-600 focus:ring-green-500"
                   {{ old('is_active', $category->is_active ?? true) ? 'checked' : '' }}>
            <span>
                <span class="block text-sm font-bold text-gray-800">Active</span>
                <span class="mt-0.5 block text-xs text-gray-500">Inactive sections are hidden on the website.</span>
            </span>
        </label>
    </div>
</div>
