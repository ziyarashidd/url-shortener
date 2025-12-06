<?php

// tests/Feature/AuthTest.php
namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class AuthTest extends TestCase
{
    public function test_user_can_login()
    {
        $response = $this->post('/login', [
            'email' => 'sales@example.com',
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $this->assertEquals(302, $response->status());
    }

    public function test_user_cannot_login_with_wrong_password()
    {
        $response = $this->post('/login', [
            'email' => 'sales@example.com',
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
        $this->assertSessionHasErrors('email');
    }

    public function test_user_can_logout()
    {
        $user = User::where('email', 'sales@example.com')->first();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $this->assertEquals(302, $response->status());
    }

    public function test_user_can_register()
    {
        $response = $this->post('/register', [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $this->assertDatabaseHas('users', ['email' => 'newuser@example.com']);
        $this->assertAuthenticated();
    }
}