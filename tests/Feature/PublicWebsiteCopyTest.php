<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicWebsiteCopyTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_does_not_advertise_job_placement(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertDontSee('job placement', false);
    }

    public function test_training_classes_page_keeps_job_placement(): void
    {
        $this->get(route('training-classes'))
            ->assertOk()
            ->assertSee('Job Placement', false);
    }

    public function test_security_training_prequal_asks_about_oc_spray_not_mace(): void
    {
        $html = $this->get(route('security-training'))
            ->assertOk()
            ->assertSee('OC Spray', false)
            ->assertDontSee('Mace', false)
            ->getContent();

        $this->assertStringContainsString(
            'Do you plan to carry OC Spray, Baton, Taser, or Handcuffs?',
            $html
        );
    }
}
