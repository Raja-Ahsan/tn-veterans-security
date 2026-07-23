@php
    $selectedCategories = $selectedCategories ?? old('categories', []);
    $selectedCategories = is_array($selectedCategories) ? $selectedCategories : [];
    $selectedCategory = old('category', $selectedCategories[0] ?? '');

    $groupedOptions = \App\Models\ServiceCategory::assignableOptionsGrouped();
    $knownSlugs = collect($groupedOptions)->flatMap(fn ($options) => array_keys($options))->all();
    $configNames = config('service_categories', []);

    // Keep currently selected value visible under the correct menu group (never "Other").
    if ($selectedCategory !== '' && ! in_array($selectedCategory, $knownSlugs, true)) {
        $friendly = $configNames[$selectedCategory]
            ?? ucwords(str_replace('_', ' ', $selectedCategory));
        $group = str_contains($selectedCategory, 'renew') || str_contains($selectedCategory, 'security_training') || str_contains($selectedCategory, 'initial')
            ? 'Security Training'
            : 'Training & Classes';
        $groupedOptions[$group][$selectedCategory] = $friendly;
    }
@endphp

<div class="space-y-4">
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <div>
            <div class="mb-1.5 flex items-center justify-between gap-2">
                <label for="category" class="block text-sm font-bold text-gray-700">
                    Category <span class="font-normal text-gray-400">(optional)</span>
                </label>
                <a href="{{ route('admin.categories.index') }}" target="_blank"
                   class="text-xs font-semibold text-green-700 hover:underline">Manage all</a>
            </div>
            <select id="category"
                    name="categories[]"
                    class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500">
                <option value="">No category</option>
                @foreach($groupedOptions as $groupLabel => $options)
                    @if(count($options))
                        <optgroup label="{{ $groupLabel }}" data-group="{{ $groupLabel }}">
                            @foreach($options as $slug => $label)
                                <option value="{{ $slug }}" @selected($selectedCategory === $slug)>{{ $label }}</option>
                            @endforeach
                        </optgroup>
                    @endif
                @endforeach
            </select>
            <p class="mt-1.5 text-xs text-gray-500">Choose where this class appears in the website menu.</p>
            @error('categories')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
            @error('categories.0')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="subcategory" class="mb-1.5 block text-sm font-bold text-gray-700">
                Subcategory <span class="font-normal text-gray-400">(optional)</span>
            </label>
            <input type="text"
                   id="subcategory"
                   name="subcategory"
                   value="{{ old('subcategory', $subcategoryValue ?? '') }}"
                   placeholder="e.g. Armed Security, ASP"
                   class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm shadow-sm focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500">
            @error('subcategory')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="rounded-lg border border-dashed border-gray-300 bg-gray-50 p-3 sm:p-4">
        <p class="mb-2 text-sm font-semibold text-gray-800">Need a new category?</p>
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end">
            <div class="min-w-0 flex-1">
                <label for="quick-category-name" class="mb-1 block text-xs font-medium text-gray-600">Category name</label>
                <input type="text" id="quick-category-name" placeholder="e.g. First Aid"
                       class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500">
            </div>
            <div class="sm:w-48">
                <label for="quick-category-group" class="mb-1 block text-xs font-medium text-gray-600">Menu</label>
                <select id="quick-category-group"
                        class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500">
                    <option value="training">Training &amp; Classes</option>
                    <option value="security">Security Training</option>
                </select>
            </div>
            <button type="button" id="quick-category-add"
                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-slate-800 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-900">
                <i class="fas fa-plus text-xs"></i> Add
            </button>
        </div>
        <p id="quick-category-status" class="mt-2 text-xs text-gray-500" aria-live="polite"></p>
    </div>
</div>

<script>
(function () {
    var nameInput = document.getElementById('quick-category-name');
    var groupInput = document.getElementById('quick-category-group');
    var addBtn = document.getElementById('quick-category-add');
    var statusEl = document.getElementById('quick-category-status');
    var selectEl = document.getElementById('category');
    if (! nameInput || ! addBtn || ! selectEl) return;

    function setStatus(text, isError) {
        if (! statusEl) return;
        statusEl.textContent = text || '';
        statusEl.className = 'mt-2 text-xs ' + (isError ? 'text-red-600' : 'text-emerald-700');
    }

    function ensureOptGroup(label) {
        var groups = selectEl.querySelectorAll('optgroup');
        for (var i = 0; i < groups.length; i++) {
            if (groups[i].label === label) return groups[i];
        }
        var group = document.createElement('optgroup');
        group.label = label;
        group.setAttribute('data-group', label);
        selectEl.appendChild(group);
        return group;
    }

    addBtn.addEventListener('click', function () {
        var name = (nameInput.value || '').trim();
        if (! name) {
            setStatus('Enter a category name.', true);
            nameInput.focus();
            return;
        }

        addBtn.disabled = true;
        setStatus('Adding…');

        fetch(@json(route('admin.categories.quick-store')), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                    || document.querySelector('input[name="_token"]')?.value
                    || ''
            },
            body: JSON.stringify({
                name: name,
                menu_group: groupInput.value
            })
        })
            .then(function (res) {
                return res.json().then(function (data) {
                    if (! res.ok) {
                        var msg = data.message || (data.errors && Object.values(data.errors)[0] && Object.values(data.errors)[0][0]) || 'Could not add category.';
                        throw new Error(msg);
                    }
                    return data;
                });
            })
            .then(function (data) {
                var group = ensureOptGroup(data.group_label);
                var option = document.createElement('option');
                option.value = data.slug;
                option.textContent = data.name;
                option.selected = true;
                group.appendChild(option);
                selectEl.value = data.slug;
                nameInput.value = '';
                setStatus('Added “' + data.name + '” and selected it.');
            })
            .catch(function (err) {
                setStatus(err.message || 'Could not add category.', true);
            })
            .finally(function () {
                addBtn.disabled = false;
            });
    });
})();
</script>
