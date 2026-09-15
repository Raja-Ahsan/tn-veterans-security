<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardDeliveryCountsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_shows_blended_and_in_person_counts(): void
    {
        $admin = User::factory()->create();

        Service::query()->create([
            'title' => 'Former Online Now Blended',
            'is_active' => true,
            'has_online_parts' => true,
            'testing_in_person' => false,
            'order' => 0,
        ]);
        Service::query()->create([
            'title' => 'Blended Handgun Carry',
            'is_active' => true,
            'has_online_parts' => true,
            'testing_in_person' => true,
            'order' => 1,
        ]);
        Service::query()->create([
            'title' => 'Second Blended Class',
            'is_active' => true,
            'has_online_parts' => true,
            'testing_in_person' => true,
            'order' => 2,
        ]);
        Service::query()->create([
            'title' => 'In Person First Aid',
            'is_active' => true,
            'has_online_parts' => false,
            'testing_in_person' => true,
            'order' => 3,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Classes by delivery')
            ->assertSee('Blended')
            ->assertSee('In Person')
            ->assertDontSee(route('admin.quiz-modules.index', ['delivery' => 'online']), false)
            ->assertSee(route('admin.quiz-modules.index', ['delivery' => 'blended']), false)
            ->assertSee(route('admin.quiz-modules.index', ['delivery' => 'in-person']), false);

        $counts = Service::deliveryCountMap();

        $this->assertSame(3, $counts['blended']);
        $this->assertSame(1, $counts['in-person']);
        $this->assertArrayNotHasKey('online', $counts);
    }
}
