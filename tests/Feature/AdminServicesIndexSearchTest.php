<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminServicesIndexSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_json_search_filters_services_by_title(): void
    {
        $user = User::factory()->create();

        Service::query()->create([
            'title' => 'Unique NRA Class Alpha',
            'is_active' => true,
            'order' => 0,
        ]);
        Service::query()->create([
            'title' => 'Different Topic Beta',
            'is_active' => true,
            'order' => 1,
        ]);

        $response = $this->actingAs($user)->getJson(route('admin.classes.index', ['q' => 'NRA']));

        $response->assertOk();
        $response->assertJsonStructure(['html']);
        $html = $response->json('html');
        $this->assertStringContainsString('Unique NRA Class Alpha', $html);
        $this->assertStringNotContainsString('Different Topic Beta', $html);
    }
}
