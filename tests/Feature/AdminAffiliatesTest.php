<?php

namespace Tests\Feature;

use App\Models\Affiliate;
use App\Models\AffiliateCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAffiliatesTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_manage_affiliate_categories_and_affiliates(): void
    {
        $admin = User::factory()->create();

        $this->actingAs($admin)
            ->post(route('admin.affiliate-categories.store'), [
                'name' => 'Veteran Owned Security Companies',
                'page' => AffiliateCategory::PAGE_PARTNERS,
                'order' => 1,
                'is_active' => 1,
            ])
            ->assertRedirect(route('admin.affiliate-categories.index'));

        $category = AffiliateCategory::query()->firstOrFail();
        $this->assertSame('veteran-owned-security-companies', $category->slug);

        $this->actingAs($admin)
            ->post(route('admin.affiliates.store'), [
                'affiliate_category_id' => $category->id,
                'name' => 'Elite Security',
                'initials' => 'ES',
                'url' => 'https://www.elitesecuritytn.org',
                'order' => 1,
                'is_active' => 1,
                'show_in_nav' => 1,
            ])
            ->assertRedirect(route('admin.affiliates.index'));

        $affiliate = Affiliate::query()->firstOrFail();

        $this->actingAs($admin)
            ->put(route('admin.affiliates.update', $affiliate), [
                'affiliate_category_id' => $category->id,
                'name' => 'Elite Security Updated',
                'initials' => 'ES',
                'url' => 'https://www.elitesecuritytn.org',
                'order' => 2,
                'is_active' => 1,
                'show_in_nav' => 1,
            ])
            ->assertRedirect(route('admin.affiliates.index'));

        $this->assertSame('Elite Security Updated', $affiliate->fresh()->name);

        $this->actingAs($admin)
            ->get(route('admin.affiliates.index'))
            ->assertOk()
            ->assertSee('Veteran Owned Security Companies')
            ->assertSee('Elite Security Updated')
            ->assertSee('Sort order', false);

        $this->actingAs($admin)
            ->delete(route('admin.affiliates.destroy', $affiliate))
            ->assertRedirect(route('admin.affiliates.index'));

        $this->assertDatabaseMissing('affiliates', ['id' => $affiliate->id]);
    }

    public function test_public_affiliated_pages_and_header_use_database_affiliates(): void
    {
        $partners = AffiliateCategory::query()->create([
            'name' => 'Veteran Owned Security Companies',
            'slug' => 'veteran-owned-security-companies',
            'page' => AffiliateCategory::PAGE_PARTNERS,
            'order' => 1,
            'is_active' => true,
        ]);

        $nra = AffiliateCategory::query()->create([
            'name' => 'NRA Resources',
            'slug' => 'nra-resources',
            'page' => AffiliateCategory::PAGE_NRA,
            'order' => 1,
            'is_active' => true,
        ]);

        Affiliate::query()->create([
            'affiliate_category_id' => $partners->id,
            'name' => 'Elite Security',
            'initials' => 'ES',
            'url' => 'https://www.elitesecuritytn.org',
            'order' => 1,
            'is_active' => true,
            'show_in_nav' => true,
            'show_in_nra_nav' => false,
        ]);

        Affiliate::query()->create([
            'affiliate_category_id' => $nra->id,
            'name' => 'Join NRA',
            'initials' => 'JNRA',
            'url' => 'https://membership.nra.org/recruiters/Join/XI048340',
            'blurb' => 'Join the NRA.',
            'order' => 1,
            'is_active' => true,
            'show_in_nav' => false,
            'show_in_nra_nav' => true,
        ]);

        $this->get(route('affiliated-services'))
            ->assertOk()
            ->assertSee('Veteran Owned Security Companies')
            ->assertSee('Elite Security')
            ->assertSee('https://www.elitesecuritytn.org', false);

        $this->get(route('nra-services'))
            ->assertOk()
            ->assertSee('Join NRA')
            ->assertSee('Join the NRA.');

        $this->get('/')
            ->assertOk()
            ->assertSee('Elite Security')
            ->assertSee('Join NRA');
    }

    public function test_inactive_affiliates_are_hidden_from_public_pages(): void
    {
        $category = AffiliateCategory::query()->create([
            'name' => 'Hidden Section',
            'slug' => 'hidden-section',
            'page' => AffiliateCategory::PAGE_PARTNERS,
            'order' => 1,
            'is_active' => true,
        ]);

        Affiliate::query()->create([
            'affiliate_category_id' => $category->id,
            'name' => 'Hidden Partner',
            'initials' => 'HP',
            'url' => 'https://example.com/hidden',
            'order' => 1,
            'is_active' => false,
            'show_in_nav' => true,
        ]);

        $this->get(route('affiliated-services'))
            ->assertOk()
            ->assertDontSee('Hidden Partner');

        $this->get('/')
            ->assertOk()
            ->assertDontSee('Hidden Partner');
    }
}
