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

<div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
    <div class="space-y-5 xl:col-span-2">
        <div>
            <label for="affiliate_category_id" class="mb-1.5 block text-sm font-semibold text-gray-700">
                Category / section <span class="text-red-500">*</span>
            </label>
            <select id="affiliate_category_id" name="affiliate_category_id" required
                    class="w-full rounded-lg border px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-0 {{ $errors->has('affiliate_category_id') ? 'border-red-400 focus:border-red-500 focus:ring-red-200' : 'border-gray-300 focus:border-green-500 focus:ring-green-100' }}">
                <option value="">Select category…</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" @selected((string) old('affiliate_category_id', $affiliate->affiliate_category_id ?? ($selectedCategoryId ?? '')) === (string) $category->id)>
                        {{ $category->name }} ({{ $category->page === 'nra' ? 'NRA page' : 'Partners page' }})
                    </option>
                @endforeach
            </select>
            @if($categories->isEmpty())
                <p class="mt-1 text-xs text-amber-700">No categories yet. <a href="{{ route('admin.affiliate-categories.create') }}" class="font-semibold underline">Create one first</a>.</p>
            @endif
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">
            <div class="sm:col-span-3">
                <label for="affiliate_name" class="mb-1.5 block text-sm font-semibold text-gray-700">Name <span class="text-red-500">*</span></label>
                <input type="text" id="affiliate_name" name="name" value="{{ old('name', $affiliate->name ?? '') }}" required
                       placeholder="e.g. Elite Security"
                       class="w-full rounded-lg border px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-0 {{ $errors->has('name') ? 'border-red-400 focus:border-red-500 focus:ring-red-200' : 'border-gray-300 focus:border-green-500 focus:ring-green-100' }}">
            </div>
            <div>
                <label for="affiliate_initials" class="mb-1.5 block text-sm font-semibold text-gray-700">Initials <span class="text-red-500">*</span></label>
                <input type="text" id="affiliate_initials" name="initials" value="{{ old('initials', $affiliate->initials ?? '') }}" required maxlength="10"
                       placeholder="ES"
                       class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm shadow-sm focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-100 focus:ring-offset-0">
                <p class="mt-1 text-[11px] text-gray-500">Shown on partner cards</p>
            </div>
        </div>

        <div>
            <label for="affiliate_url" class="mb-1.5 block text-sm font-semibold text-gray-700">Website URL <span class="text-red-500">*</span></label>
            <input type="url" id="affiliate_url" name="url" value="{{ old('url', $affiliate->url ?? '') }}" required
                   placeholder="https://example.com"
                   class="w-full rounded-lg border px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-0 {{ $errors->has('url') ? 'border-red-400 focus:border-red-500 focus:ring-red-200' : 'border-gray-300 focus:border-green-500 focus:ring-green-100' }}">
        </div>

        <div>
            <label for="affiliate_blurb" class="mb-1.5 block text-sm font-semibold text-gray-700">
                Short description <span class="font-normal text-gray-400">(optional — NRA page cards)</span>
            </label>
            <textarea id="affiliate_blurb" name="blurb" rows="3" maxlength="500"
                      placeholder="Optional blurb shown on the NRA Services cards…"
                      class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm shadow-sm focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-100 focus:ring-offset-0">{{ old('blurb', $affiliate->blurb ?? '') }}</textarea>
        </div>
    </div>

    <div class="space-y-5">
        <div class="rounded-xl border border-gray-200 bg-slate-50 p-4 space-y-4">
            <div>
                <p class="text-sm font-bold text-gray-900">Where to show</p>
                <p class="mt-0.5 text-xs text-gray-500">Category page always shows active affiliates in that section.</p>
            </div>
            <label class="flex cursor-pointer items-start gap-3 rounded-lg border border-gray-200 bg-white px-3 py-3 hover:border-green-300">
                <input type="checkbox" name="show_in_nav" value="1" class="mt-0.5 h-4 w-4 rounded border-gray-300 text-green-600 focus:ring-green-500"
                       {{ old('show_in_nav', $affiliate->show_in_nav ?? false) ? 'checked' : '' }}>
                <span>
                    <span class="block text-sm font-semibold text-gray-800">Main Affiliated menu</span>
                    <span class="block text-xs text-gray-500">Website header dropdown</span>
                </span>
            </label>
            <label class="flex cursor-pointer items-start gap-3 rounded-lg border border-gray-200 bg-white px-3 py-3 hover:border-green-300">
                <input type="checkbox" name="show_in_nra_nav" value="1" class="mt-0.5 h-4 w-4 rounded border-gray-300 text-green-600 focus:ring-green-500"
                       {{ old('show_in_nra_nav', $affiliate->show_in_nra_nav ?? false) ? 'checked' : '' }}>
                <span>
                    <span class="block text-sm font-semibold text-gray-800">NRA submenu</span>
                    <span class="block text-xs text-gray-500">Under NRA in Affiliated menu</span>
                </span>
            </label>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-4 space-y-4">
            <div>
                <label for="affiliate_order" class="mb-1.5 block text-sm font-semibold text-gray-700">Sort order</label>
                <input type="number" id="affiliate_order" name="order" min="0"
                       value="{{ old('order', $affiliate->order ?? 0) }}"
                       class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm shadow-sm focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-100 focus:ring-offset-0">
                <p class="mt-1 text-xs text-gray-500">Smaller numbers appear first.</p>
            </div>
            <label class="flex cursor-pointer items-start gap-3 rounded-lg border border-gray-200 bg-slate-50 px-3 py-3">
                <input type="checkbox" name="is_active" value="1" class="mt-0.5 h-4 w-4 rounded border-gray-300 text-green-600 focus:ring-green-500"
                       {{ old('is_active', $affiliate->is_active ?? true) ? 'checked' : '' }}>
                <span>
                    <span class="block text-sm font-bold text-gray-800">Active</span>
                    <span class="mt-0.5 block text-xs text-gray-500">Inactive affiliates stay hidden on the website.</span>
                </span>
            </label>
        </div>
    </div>
</div>
