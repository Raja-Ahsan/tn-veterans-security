<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Affiliate;
use App\Models\AffiliateCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AffiliateController extends Controller
{
    public function index()
    {
        $categories = AffiliateCategory::query()
            ->with(['affiliates' => fn ($q) => $q->ordered()])
            ->ordered()
            ->get();

        $ungrouped = Affiliate::query()
            ->whereDoesntHave('category')
            ->ordered()
            ->get();

        return view('admin.affiliates.index', compact('categories', 'ungrouped'));
    }

    public function create(Request $request)
    {
        return view('admin.affiliates.create', [
            'categories' => $this->categoryOptions(),
            'selectedCategoryId' => $request->integer('affiliate_category_id') ?: null,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateAffiliate($request);
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['show_in_nav'] = $request->boolean('show_in_nav');
        $validated['show_in_nra_nav'] = $request->boolean('show_in_nra_nav');
        $validated['order'] = $validated['order'] ?? 0;

        Affiliate::query()->create($validated);

        return redirect()->route('admin.affiliates.index')
            ->with('success', 'Affiliate created.');
    }

    public function edit(Affiliate $affiliate)
    {
        return view('admin.affiliates.edit', [
            'affiliate' => $affiliate,
            'categories' => $this->categoryOptions(),
        ]);
    }

    public function update(Request $request, Affiliate $affiliate)
    {
        $validated = $this->validateAffiliate($request);
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['show_in_nav'] = $request->boolean('show_in_nav');
        $validated['show_in_nra_nav'] = $request->boolean('show_in_nra_nav');
        $validated['order'] = $validated['order'] ?? 0;

        $affiliate->update($validated);

        return redirect()->route('admin.affiliates.index')
            ->with('success', 'Affiliate updated.');
    }

    public function destroy(Affiliate $affiliate)
    {
        $affiliate->delete();

        return redirect()->route('admin.affiliates.index')
            ->with('success', 'Affiliate deleted.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validateAffiliate(Request $request): array
    {
        return $request->validate([
            'affiliate_category_id' => ['required', 'integer', Rule::exists('affiliate_categories', 'id')],
            'name' => 'required|string|max:255',
            'initials' => 'required|string|max:10',
            'url' => 'required|url|max:500',
            'blurb' => 'nullable|string|max:500',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'sometimes|boolean',
            'show_in_nav' => 'sometimes|boolean',
            'show_in_nra_nav' => 'sometimes|boolean',
        ], [
            'affiliate_category_id.required' => 'Choose a category/section.',
            'url.url' => 'Enter a valid website URL (include https://).',
        ]);
    }

    /**
     * @return \Illuminate\Support\Collection<int, AffiliateCategory>
     */
    private function categoryOptions()
    {
        return AffiliateCategory::query()->ordered()->get();
    }
}
