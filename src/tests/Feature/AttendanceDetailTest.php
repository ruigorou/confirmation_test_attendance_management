<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;

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

    //勤怠詳細画面の「日付」が選択した日付になっている
    public function test_attendance_detail_shows_selected_date() 
    {
        /** @var User $user */
        $user = User::factory()->create();
        $this->actingAs($user);

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => '2024-06-01',
        ]);
        
        // 勤怠詳細画面にアクセス
        $response = $this->get('/attendance/' . $attendance->id);

        // レスポンスの内容を検証
        $response->assertStatus(200);
        $response->assertSee('2024年');
        $response->assertSee('6月1日');
    }

    //「出勤・退勤」にて記されている時間がログインユーザーの打刻と一致している
    public function test_attendance_detail_shows_clock_in_time() 
    {
        /** @var User $user */
        $user = User::factory()->create();
        $this->actingAs($user);

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => now()->toDateString(),
            'clock_in' => '09:00:00',
            'status' => '出勤中',
        ]);
        
        // 勤怠詳細画面にアクセス
        $response = $this->get('/attendance/' . $attendance->id);

        // レスポンスの内容を検証
        $response->assertStatus(200);
        $response->assertSee('09:00');
    }

    //「休憩」にて記されている時間がログインユーザーの打刻と一致している
    public function test_attendance_detail_shows_break_time() 
    {
        /** @var User $user */
        $user = User::factory()->create();
        $this->actingAs($user);

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => now()->toDateString(),
            'break_time' => '12:00:00',
            'status' => '出勤中',
        ]);

        BreakTime::create([
            'attendance_id' => $attendance->id,
            'start_time' => '12:00:00',
            'end_time' => '13:00:00',
        ]);
        
        // 勤怠詳細画面にアクセス
        $response = $this->get('/attendance/' . $attendance->id);

        // レスポンスの内容を検証
        $response->assertStatus(200);
        $response->assertSee('12:00');
    }
}
