@php
    $linkType = old('link_type', $category->link_type ?? 'category');
    $existingSlug = old('slug', $category->slug ?? '');
@endphp

<div class="space-y-5">
    {{-- Keep existing slug on edit; create auto-generates from name in controller --}}
    <input type="hidden" id="category-slug" name="slug" value="{{ $existingSlug }}">

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <div>
            <label class="mb-1.5 block text-sm font-bold text-gray-700">Name <span class="text-red-500">*</span></label>
            <input type="text" id="category-name" name="name" value="{{ old('name', $category->name ?? '') }}" required
                   placeholder="e.g. Red Cross"
                   class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm shadow-sm focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500">
            @error('name')<p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-bold text-gray-700">Menu group <span class="text-red-500">*</span></label>
            <select name="menu_group" required
                    class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500">
                <option value="training" @selected(old('menu_group', $category->menu_group ?? 'training') === 'training')>Training &amp; Classes</option>
                <option value="security" @selected(old('menu_group', $category->menu_group ?? '') === 'security')>Security Training</option>
            </select>
        </div>
    </div>

    <div>
        <label class="mb-1.5 block text-sm font-bold text-gray-700">Sort order</label>
        <input type="number" name="sort_order" min="0"
               value="{{ old('sort_order', $category->sort_order ?? 0) }}"
               class="w-full max-w-xs rounded-lg border border-gray-300 px-3 py-2.5 text-sm shadow-sm focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500">
        <p class="mt-1 text-xs text-gray-500">Smaller number shows first in the menu.</p>
    </div>

    <div>
        <p class="mb-1 text-sm font-bold text-gray-800">Menu click action <span class="text-red-500">*</span></p>
        <p class="mb-3 text-xs text-gray-500">What should open when someone clicks this item in the website menu?</p>
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
            <label data-link-card class="cursor-pointer rounded-lg border-2 p-3 transition {{ $linkType === 'category' ? 'border-green-500 bg-green-50' : 'border-gray-200 bg-white hover:border-gray-300' }}">
                <input type="radio" name="link_type" value="category" class="sr-only" @checked($linkType === 'category') required>
                <span class="block text-sm font-bold text-gray-900">Class list</span>
                <span class="mt-1 block text-xs text-gray-500">Open a page with all classes in this category (most common)</span>
            </label>
            <label data-link-card class="cursor-pointer rounded-lg border-2 p-3 transition {{ $linkType === 'slug' ? 'border-green-500 bg-green-50' : 'border-gray-200 bg-white hover:border-gray-300' }}">
                <input type="radio" name="link_type" value="slug" class="sr-only" @checked($linkType === 'slug')>
                <span class="block text-sm font-bold text-gray-900">Single class</span>
                <span class="mt-1 block text-xs text-gray-500">Open one specific class page directly</span>
            </label>
            <label data-link-card class="cursor-pointer rounded-lg border-2 p-3 transition {{ $linkType === 'route' ? 'border-green-500 bg-green-50' : 'border-gray-200 bg-white hover:border-gray-300' }}">
                <input type="radio" name="link_type" value="route" class="sr-only" @checked($linkType === 'route')>
                <span class="block text-sm font-bold text-gray-900">Special page</span>
                <span class="mt-1 block text-xs text-gray-500">Open a fixed site page (example: Renewals)</span>
            </label>
        </div>
        @error('link_type')<p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
    </div>

    <div id="link-value-listing" class="{{ $linkType === 'category' ? '' : 'hidden' }}">
        <input type="hidden" id="link-value-auto" name="link_value" value="{{ old('link_value', $category->link_value ?? $existingSlug) }}" @disabled($linkType !== 'category')>
        <div class="rounded-lg border border-emerald-100 bg-emerald-50 px-3 py-2.5 text-sm text-emerald-900">
            Recommended: menu opens the list of classes assigned to this category.
        </div>
    </div>

    <div id="link-value-slug" class="{{ $linkType === 'slug' ? '' : 'hidden' }}">
        <label class="mb-1.5 block text-sm font-bold text-gray-700">Class page slug <span class="text-red-500">*</span></label>
        <input type="text" id="link-value-slug-input" name="link_value"
               value="{{ old('link_value', $category->link_value ?? '') }}"
               placeholder="e.g. handle-with-care"
               class="w-full rounded-lg border border-gray-300 px-3 py-2.5 font-mono text-sm shadow-sm focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500"
               @disabled($linkType !== 'slug')>
        <p class="mt-1 text-xs text-gray-500">Must match the class Direct page slug (example: <code>handle-with-care</code>).</p>
    </div>

    <div id="link-value-route" class="{{ $linkType === 'route' ? '' : 'hidden' }}">
        <label class="mb-1.5 block text-sm font-bold text-gray-700">Page route name <span class="text-red-500">*</span></label>
        <input type="text" id="link-value-route-input" name="link_value"
               value="{{ old('link_value', $category->link_value ?? '') }}"
               placeholder="e.g. renewals"
               class="w-full rounded-lg border border-gray-300 px-3 py-2.5 font-mono text-sm shadow-sm focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500"
               @disabled($linkType !== 'route')>
        <p class="mt-1 text-xs text-gray-500">Examples: <code>renewals</code>, <code>intial-security</code></p>
    </div>
    @error('link_value')<p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>@enderror

    <div class="rounded-lg border border-gray-200 bg-gray-50 px-4 py-3">
        <p class="mb-2 text-sm font-bold text-gray-800">Visibility</p>
        <div class="flex flex-col gap-2 sm:flex-row sm:flex-wrap sm:gap-x-6 sm:gap-y-2">
            <label class="inline-flex items-center gap-2 text-sm text-gray-800">
                <input type="hidden" name="show_in_nav" value="0">
                <input type="checkbox" name="show_in_nav" value="1" class="rounded border-gray-400 text-green-600 focus:ring-green-500"
                       @checked(old('show_in_nav', $category->show_in_nav ?? true))>
                Show in public menu
            </label>
            <label class="inline-flex items-center gap-2 text-sm text-gray-800">
                <input type="hidden" name="assignable" value="0">
                <input type="checkbox" name="assignable" value="1" class="rounded border-gray-400 text-green-600 focus:ring-green-500"
                       @checked(old('assignable', $category->assignable ?? true))>
                Show in class form
            </label>
            <label class="inline-flex items-center gap-2 text-sm text-gray-800">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" class="rounded border-gray-400 text-green-600 focus:ring-green-500"
                       @checked(old('is_active', $category->is_active ?? true))>
                Active
            </label>
        </div>
    </div>
</div>

<script>
(function () {
    function toSlug(value) {
        return String(value || '')
            .toLowerCase()
            .trim()
            .replace(/[^a-z0-9]+/g, '_')
            .replace(/^_+|_+$/g, '');
    }

    var nameInput = document.getElementById('category-name');
    var slugInput = document.getElementById('category-slug');
    var autoLink = document.getElementById('link-value-auto');
    var isEdit = {{ isset($category) && $category->exists ? 'true' : 'false' }};

    function syncAutoLink() {
        if (! autoLink) return;
        if (slugInput && slugInput.value) {
            autoLink.value = slugInput.value;
        } else if (nameInput) {
            autoLink.value = toSlug(nameInput.value);
        }
    }

    if (nameInput && slugInput && ! isEdit) {
        nameInput.addEventListener('input', function () {
            slugInput.value = toSlug(nameInput.value);
            syncAutoLink();
        });
    }

    function setLinkType(type) {
        document.querySelectorAll('[data-link-card]').forEach(function (card) {
            var radio = card.querySelector('input[type="radio"]');
            var on = radio && radio.value === type;
            card.classList.toggle('border-green-500', on);
            card.classList.toggle('bg-green-50', on);
            card.classList.toggle('border-gray-200', ! on);
            card.classList.toggle('bg-white', ! on);
        });

        var listing = document.getElementById('link-value-listing');
        var slugBox = document.getElementById('link-value-slug');
        var routeBox = document.getElementById('link-value-route');
        var slugField = document.getElementById('link-value-slug-input');
        var routeField = document.getElementById('link-value-route-input');

        listing.classList.toggle('hidden', type !== 'category');
        slugBox.classList.toggle('hidden', type !== 'slug');
        routeBox.classList.toggle('hidden', type !== 'route');

        if (autoLink) autoLink.disabled = type !== 'category';
        if (slugField) slugField.disabled = type !== 'slug';
        if (routeField) routeField.disabled = type !== 'route';

        if (type === 'category') {
            syncAutoLink();
        }
    }

    document.querySelectorAll('input[name="link_type"]').forEach(function (radio) {
        radio.addEventListener('change', function () {
            setLinkType(radio.value);
        });
    });

    setLinkType(@json($linkType));
    syncAutoLink();
})();
</script>
