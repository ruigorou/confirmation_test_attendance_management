<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;

class AttendanceDetailTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    //勤怠詳細画面の「名前」がログインユーザーの氏名になっているか
    public function test_attendance_detail_shows_user_name() 
    {
        /** @var User $user */
        $user = User::factory()->create([
            'name' => 'テストユーザー',
        ]);
        $this->actingAs($user);

        // 2. ログイン状態にする
        $this->actingAs($user);

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => now()->toDateString(),
            'clock_in' => now()->toTimeString(),
            'status' => '出勤中',
        ]);
        
        // 3. 勤怠詳細画面にアクセス
        $response = $this->get('/attendance/' . $attendance->id);

        // 4. レスポンスの内容を検証
        $response->assertStatus(200);
        $response->assertSee('テストユーザー');
    }
}
