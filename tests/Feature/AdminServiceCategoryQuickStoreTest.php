<?php

namespace Tests\Feature;

use App\Models\ServiceCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminServiceCategoryQuickStoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_quick_store_category_from_class_form(): void
    {
        $admin = User::factory()->create();

        $response = $this->actingAs($admin)->postJson(route('admin.categories.quick-store'), [
            'name' => 'First Aid Basics',
            'menu_group' => 'training',
        ]);

        $response->assertOk()
            ->assertJsonPath('name', 'First Aid Basics')
            ->assertJsonPath('slug', 'first_aid_basics')
            ->assertJsonPath('menu_group', 'training')
            ->assertJsonPath('group_label', 'Training & Classes');

        $this->assertDatabaseHas('service_categories', [
            'slug' => 'first_aid_basics',
            'name' => 'First Aid Basics',
            'menu_group' => 'training',
            'show_in_nav' => true,
            'assignable' => true,
            'is_active' => true,
        ]);
    }

    public function test_quick_store_generates_unique_slug_when_name_collides(): void
    {
        $admin = User::factory()->create();

        ServiceCategory::query()->create([
            'name' => 'Collision Category',
            'slug' => 'collision_category',
            'menu_group' => 'training',
            'link_type' => 'category',
            'link_value' => 'collision_category',
            'sort_order' => 99,
            'show_in_nav' => true,
            'assignable' => true,
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->postJson(route('admin.categories.quick-store'), [
            'name' => 'Collision Category',
            'menu_group' => 'training',
        ]);

        $response->assertOk()
            ->assertJsonPath('slug', 'collision_category_2');
    }
}
