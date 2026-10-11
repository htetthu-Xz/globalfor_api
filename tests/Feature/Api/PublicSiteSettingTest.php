<?php

namespace Tests\Feature\Api;

use App\Models\SiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicSiteSettingTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_safe_defaults_before_settings_are_saved(): void
    {
        $this->getJson('/api/v1/site-settings')
            ->assertOk()
            ->assertJsonPath('data.companyName', SiteSetting::DEFAULTS['company_name'])
            ->assertJsonPath('data.phone', SiteSetting::DEFAULTS['phone'])
            ->assertJsonPath('data.email', SiteSetting::DEFAULTS['email'])
            ->assertJsonMissingPath('data.logoUrl');
    }
}
