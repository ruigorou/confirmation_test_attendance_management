<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminLoginTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    //------------ admin test codes -----------

    //----------- 認証確認 -----------
     public function test_admin_can_login()
    {
        // 1. テスト用ユーザーを作成
        $admin = \App\Models\User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin'
        ]);

        // 2. ログインデータを用意
        $data = [
            'email' => 'test@example.com',
            'password' => 'password',
        ];

        // 3. POSTリクエストを送信
        $response = $this->post('/admin/login', $data);

        // 4. リダイレクト先を検証
        $response->assertRedirect('/admin/attendance/list');

        // 5. 指定したユーザーとして認証されていることを検証
        $this->assertAuthenticatedAs($admin);
    }

    //----------- メールアドレスが未入力の場合 -----------
    public function test_admin_cannot_login_with_empty_email()
    {
        \App\Models\User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $response = $this->post('/admin/login', [
            'email' => '',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors(['email' => 'メールアドレスを入力してください']);
        $this->assertGuest();
    }

    //------- パスワードが未入力の場合 -----------
    public function test_admin_cannot_login_with_empty_password()
    {
        \App\Models\User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $response = $this->post('/admin/login', [
            'email' => 'test@example.com',
            'password' => '',
        ]);

        $response->assertSessionHasErrors(['password' => 'パスワードを入力してください']);
        $this->assertGuest();
    }

    //------- 登録内容の一致しない場合のバリデーション確認 -----------
     public function test_admin_cannot_login_with_invalid_credentials()
    {
        // 1. テスト用ユーザーを作成
    
        \App\Models\User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->post('/admin/login', [
            'email' => 'error@example.com',
            'password' => 'password',
        ]);
        $response->assertSessionHasErrors([
            'email' => 'ログイン情報が登録されていません'
        ]);
        $this->assertGuest();
    }
}
