<?php

// tests/Feature/UrlRedirectTest.php
namespace Tests\Feature;

use App\Models\User;
use App\Models\ShortUrl;
use Tests\TestCase;

class UrlRedirectTest extends TestCase
{
    public function test_short_url_redirect_requires_authentication()
    {
        $shortUrl = ShortUrl::factory()->create();

        $response = $this->get('/s/' . $shortUrl->short_code);

        $this->assertEquals(302, $response->status());
        $this->assertTrue(str_contains($response->getTargetUrl(), 'login'));
    }

    public function test_authenticated_user_can_access_short_url()
    {
        $user = User::where('email', 'sales@example.com')->first();
        $shortUrl = ShortUrl::factory()->create();

        $response = $this->actingAs($user)->get('/s/' . $shortUrl->short_code);

        $this->assertEquals(302, $response->status());
    }

    public function test_short_url_redirects_to_original_url()
    {
        $user = User::where('email', 'sales@example.com')->first();
        $shortUrl = ShortUrl::factory()->create([
            'original_url' => 'https://example.com',
        ]);

        $response = $this->actingAs($user)->get('/s/' . $shortUrl->short_code);

        $this->assertTrue(str_contains($response->getTargetUrl(), 'https://example.com'));
    }

    public function test_short_url_increments_clicks()
    {
        $user = User::where('email', 'sales@example.com')->first();
        $shortUrl = ShortUrl::factory()->create(['clicks' => 0]);

        $this->actingAs($user)->get('/s/' . $shortUrl->short_code);

        $this->assertEquals(1, $shortUrl->fresh()->clicks);
    }
}