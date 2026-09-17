<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AffiliateCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AffiliateCategoryController extends Controller
{
    public function index()
    {
        $categories = AffiliateCategory::query()
            ->with(['affiliates' => fn ($q) => $q->ordered()])
            ->withCount('affiliates')
            ->ordered()
            ->get();

        return view('admin.affiliate-categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.affiliate-categories.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validateCategory($request);
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['slug'] = $this->uniqueSlug($validated['name'], $validated['slug'] ?? null);

        AffiliateCategory::query()->create($validated);

        return redirect()->route('admin.affiliate-categories.index')
            ->with('success', 'Category created.');
    }

    public function edit(AffiliateCategory $affiliateCategory)
    {
        return view('admin.affiliate-categories.edit', [
            'category' => $affiliateCategory,
        ]);
    }

    public function update(Request $request, AffiliateCategory $affiliateCategory)
    {
        $validated = $this->validateCategory($request, $affiliateCategory);
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['slug'] = $this->uniqueSlug(
            $validated['name'],
            $validated['slug'] ?? null,
            $affiliateCategory->id
        );

        $affiliateCategory->update($validated);

        return redirect()->route('admin.affiliate-categories.index')
            ->with('success', 'Category updated.');
    }

    public function destroy(AffiliateCategory $affiliateCategory)
    {
        $affiliateCategory->delete();

        return redirect()->route('admin.affiliate-categories.index')
            ->with('success', 'Category deleted.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validateCategory(Request $request, ?AffiliateCategory $category = null): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('affiliate_categories', 'slug')->ignore($category?->id),
            ],
            'page' => ['required', Rule::in(array_keys(AffiliateCategory::pageOptions()))],
            'order' => 'nullable|integer|min:0',
            'is_active' => 'sometimes|boolean',
        ]);
    }

    private function uniqueSlug(string $name, ?string $slug = null, ?int $ignoreId = null): string
    {
        $base = Str::slug($slug ?: $name) ?: 'category';
        $candidate = $base;
        $suffix = 2;

        while (
            AffiliateCategory::query()
                ->where('slug', $candidate)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $candidate = $base.'-'.$suffix;
            $suffix++;
        }

        return $candidate;
    }
}
