<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserLoginTest extends TestCase
{
     use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */

    //----------- ログイン確認 -----------
    public function test_user_can_login()
    {
        // 1. テスト用ユーザーを作成
        $user = \App\Models\User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);

        // 2. ログインデータを用意
        $data = [
            'email' => 'test@example.com',
            'password' => 'password',
        ];

        // 3. POSTリクエストを送信
        $response = $this->post('/login', $data);

        // 4. リダイレクト先を検証
        $response->assertRedirect('/attendance');

        // 5. 指定したユーザーとして認証されていることを検証
        $this->assertAuthenticatedAs($user);
    }

    public function test_user_cannot_login_with_invalid_credentials()
    {
        // 1. テスト用ユーザーを作成
        $user = \App\Models\User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        // 2. 間違ったパスワードでログインを試みる
        $data = [
            'email' => 'test@example.com',
            'password' => 'testpassword',
        ];

        // 3. POSTリクエストを送信
        $response = $this->post('/attendance', $data);

        // 4. セッションにエラーがあることを検証
        $response->assertSessionHasErrors();

        // 5. ユーザーが未認証であることを検証
        $this->assertGuest();
    }

    //------------ メールアドレスが未入力の場合 -----------
    public function test_user_cannot_login_with_empty_email()
    {
        \App\Models\User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->post('/attendance', [
            'email' => '',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    //--------- パスワードが８文字未満の場合のバリデーション確認 -----------
    public function test_user_cannot_login_with_short_password() {
        \App\Models\User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('12345678'),
        ]);

        $response = $this->post('/attendance', [
            'email' => 'test@example.com',
            'password' => '1234567',
        ]);
        
        $response->assertSessionHasErrors([
            'password' => 'パスワードは8文字以上で入力してください',
        ]);

        $this->assertGuest();
    }

    //------- パスワードが一致しない場合のバリデーション確認 -----------
    public function test_user_cannot_login_with_unmatched_password()
    {
        \App\Models\User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('12345678'),
        ]);

        $response = $this->post('/attendance', [
            'email' => 'test@example.com',
            'password' => '23456789',
        ]);

        $response->assertSessionHasErrors([
            'password' => 'パスワードと一致しません',
        ]);

        $this->assertGuest();
    }

    //------------- パスワードが未入力の場合のバリデーション確認 -------------
    public function test_user_cannot_login_with_empty_password()
    {
        \App\Models\User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->post('/attendance', [
            'email' => 'test@example.com',
            'password' => '',
        ]);

        $response->assertSessionHasErrors(['password']);
        $this->assertGuest();
    }

    //------- 登録内容の一致しない場合のバリデーション確認 -----------
     public function test_cannot_login_with_invalid_credentials()
    {
        // 1. テスト用ユーザーを作成
        \App\Models\User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->post('/attendance', [
            'email' => 'error@example.com',
            'password' => 'password',
        ]);


        $response->assertSessionHasErrors([
            'email' => 'ログイン情報が登録されていません'
        ]);
        $this->assertGuest();
    }
}
