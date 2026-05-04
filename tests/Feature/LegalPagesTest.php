<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LegalPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_privacy_policy_page_is_publicly_accessible(): void
    {
        $this->get('/privacy-policy')
            ->assertOk()
            ->assertSee('PRIVACY', false);
    }

    public function test_terms_and_conditions_page_is_publicly_accessible(): void
    {
        $this->get('/terms-and-conditions')
            ->assertOk()
            ->assertSee('RELEASE OF LIABILITY', false);
    }
}
