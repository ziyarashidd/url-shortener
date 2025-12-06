<?php

// tests/Feature/UrlVisibilityTest.php
namespace Tests\Feature;

use App\Models\User;
use App\Models\ShortUrl;
use Tests\TestCase;

class UrlVisibilityTest extends TestCase
{
    public function test_admin_sees_only_company_urls_not_own()
    {
        $admin = User::where('email', 'admin@example.com')->first();
        $sales = User::where('email', 'sales@example.com')->first();

        $adminUrl = ShortUrl::factory()
            ->create(['user_id' => $admin->id, 'company_id' => $admin->company_id]);

        $salesUrl = ShortUrl::factory()
            ->create(['user_id' => $sales->id, 'company_id' => $admin->company_id]);

        $response = $this->actingAs($admin)->get('/short-urls');

        $this->assertStringContainsString($salesUrl->short_code, $response->getContent());
        $this->assertStringNotContainsString($adminUrl->short_code, $response->getContent());
    }

    public function test_member_sees_only_company_urls_not_own()
    {
        $member = User::where('email', 'member@example.com')->first();
        $sales = User::where('email', 'sales@example.com')->first();

        $memberUrl = ShortUrl::factory()
            ->create(['user_id' => $member->id, 'company_id' => $member->company_id]);

        $salesUrl = ShortUrl::factory()
            ->create(['user_id' => $sales->id, 'company_id' => $member->company_id]);

        $response = $this->actingAs($member)->get('/short-urls');

        $this->assertStringContainsString($salesUrl->short_code, $response->getContent());
        $this->assertStringNotContainsString($memberUrl->short_code, $response->getContent());
    }

    public function test_superadmin_cannot_see_urls_list()
    {
        $superAdmin = User::where('email', 'superadmin@example.com')->first();

        $response = $this->actingAs($superAdmin)->get('/short-urls');

        $this->assertEquals(403, $response->status());
    }

    public function test_sales_sees_own_urls()
    {
        $sales = User::where('email', 'sales@example.com')->first();

        $salesUrl = ShortUrl::factory()
            ->create(['user_id' => $sales->id, 'company_id' => $sales->company_id]);

        $response = $this->actingAs($sales)->get('/short-urls');

        $this->assertStringContainsString($salesUrl->short_code, $response->getContent());
    }
}
