<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ServiceCategoryController extends Controller
{
    public function index(): View
    {
        $categories = ServiceCategory::query()
            ->ordered()
            ->get()
            ->groupBy('menu_group');

        return view('admin.categories.index', compact('categories'));
    }

    public function create(): View
    {
        return view('admin.categories.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validated($request);
        ServiceCategory::query()->create($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category created successfully.');
    }

    public function quickStore(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'menu_group' => 'required|in:training,security',
        ]);

        $slug = Str::snake($validated['name']);
        $base = $slug;
        $i = 2;
        while (ServiceCategory::query()->where('slug', $slug)->exists()) {
            $slug = $base.'_'.$i;
            $i++;
        }

        $category = ServiceCategory::query()->create([
            'name' => $validated['name'],
            'slug' => $slug,
            'menu_group' => $validated['menu_group'],
            'link_type' => 'category',
            'link_value' => $slug,
            'sort_order' => (int) (ServiceCategory::query()->where('menu_group', $validated['menu_group'])->max('sort_order') ?? 0) + 1,
            'show_in_nav' => true,
            'assignable' => true,
            'is_active' => true,
        ]);

        return response()->json([
            'id' => $category->id,
            'name' => $category->name,
            'slug' => $category->slug,
            'menu_group' => $category->menu_group,
            'group_label' => $category->menu_group === 'security' ? 'Security Training' : 'Training & Classes',
        ]);
    }

    public function edit(ServiceCategory $serviceCategory): View
    {
        return view('admin.categories.edit', [
            'category' => $serviceCategory,
        ]);
    }

    public function update(Request $request, ServiceCategory $serviceCategory): RedirectResponse
    {
        $validated = $this->validated($request, $serviceCategory);
        $serviceCategory->update($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category updated successfully.');
    }

    public function destroy(ServiceCategory $serviceCategory): RedirectResponse
    {
        $serviceCategory->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category deleted successfully.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?ServiceCategory $category = null): array
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => [
                'nullable',
                'string',
                'max:100',
                'regex:/^[a-z0-9_]+$/',
                Rule::unique('service_categories', 'slug')->ignore($category?->id),
            ],
            'menu_group' => 'required|in:training,security',
            'link_type' => 'required|in:category,slug,route',
            'link_value' => 'required|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
            'show_in_nav' => 'sometimes|boolean',
            'assignable' => 'sometimes|boolean',
            'is_active' => 'sometimes|boolean',
        ], [
            'slug.regex' => 'Slug may only contain lowercase letters, numbers, and underscores.',
        ]);

        $validated['slug'] = filled($validated['slug'] ?? null)
            ? Str::lower($validated['slug'])
            : Str::snake($validated['name']);

        // Category list links always use the category slug.
        if (($validated['link_type'] ?? '') === 'category') {
            $validated['link_value'] = $validated['slug'];
        }

        $validated['sort_order'] = (int) ($validated['sort_order'] ?? 0);
        $validated['show_in_nav'] = $request->boolean('show_in_nav');
        $validated['assignable'] = $request->boolean('assignable');
        $validated['is_active'] = $request->boolean('is_active');

        return $validated;
    }
}
