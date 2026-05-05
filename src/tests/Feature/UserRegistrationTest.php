<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserRegistrationTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_user_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => '山田太郎',
            'email' => 'taro@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirectContains('email/verify');

        $this->assertDatabaseHas('users', [
            'email' => 'taro@example.com'
        ]);

        $this->assertAuthenticated();
    }

    public function test_register_requires_valid_email()
    {
        // 無効なメールアドレスを送信
        $data = [
            'name' => 'Test User',
            'email' => 'invalid-email',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $response = $this->post('/register', $data);

        // emailフィールドにエラーがあることを検証
        $response->assertSessionHasErrors(['email']);
    }
}
