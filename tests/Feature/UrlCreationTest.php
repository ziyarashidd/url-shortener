<?php

// tests/Feature/UrlCreationTest.php
namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use App\Models\Company;
use App\Models\ShortUrl;
use Tests\TestCase;

class UrlCreationTest extends TestCase
{
    public function test_sales_can_create_short_url()
    {
        $company = Company::first();
        $sales = User::where('email', 'sales@example.com')->first();

        $response = $this->actingAs($sales)->post('/short-urls', [
            'original_url' => 'https://example.com',
        ]);

        $this->assertEquals(302, $response->status());
        $this->assertDatabaseHas('short_urls', [
            'user_id' => $sales->id,
            'original_url' => 'https://example.com',
        ]);
    }

    public function test_manager_can_create_short_url()
    {
        $manager = User::where('email', 'manager@example.com')->first();

        $response = $this->actingAs($manager)->post('/short-urls', [
            'original_url' => 'https://laravel.com',
        ]);

        $this->assertEquals(302, $response->status());
        $this->assertDatabaseHas('short_urls', [
            'user_id' => $manager->id,
        ]);
    }

    public function test_admin_cannot_create_short_url()
    {
        $admin = User::where('email', 'admin@example.com')->first();

        $response = $this->actingAs($admin)->post('/short-urls', [
            'original_url' => 'https://example.com',
        ]);

        $this->assertEquals(403, $response->status());
    }

    public function test_member_cannot_create_short_url()
    {
        $member = User::where('email', 'member@example.com')->first();

        $response = $this->actingAs($member)->post('/short-urls', [
            'original_url' => 'https://example.com',
        ]);

        $this->assertEquals(403, $response->status());
    }

    public function test_superadmin_cannot_create_short_url()
    {
        $superAdmin = User::where('email', 'superadmin@example.com')->first();

        $response = $this->actingAs($superAdmin)->post('/short-urls', [
            'original_url' => 'https://example.com',
        ]);

        $this->assertEquals(403, $response->status());
    }

    public function test_url_creation_validates_original_url()
    {
        $sales = User::where('email', 'sales@example.com')->first();

        $response = $this->actingAs($sales)->post('/short-urls', [
            'original_url' => 'not-a-valid-url',
        ]);

        $this->assertSessionHasErrors('original_url');
    }
}
