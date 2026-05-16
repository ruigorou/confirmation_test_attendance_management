<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;

class AttendanceTest extends TestCase
{
     use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */

    //-------- 出勤処理のテスト -----------
    public function test_user_can_clock_in()
    {
        /** @var User $user */
        // ユーザー作成
        $user = User::factory()->create();

        // ログイン状態にする
        $this->actingAs($user);

        // 出勤処理
        $response = $this->post('/attendance/clock_in');

        // リダイレクト確認
        $response->assertRedirect();

        // DB確認
        $this->assertDatabaseHas('attendances', [
            'user_id' => $user->id,
        ]);
    }


    //-------- 退勤処理のテスト -----------
    public function test_user_can_clock_out()
    {
        /** @var User $user */
        $user = User::factory()->create();

        $this->actingAs($user);

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => now()->toDateString(),
            'clock_in' => now(),
        ]);

        $response = $this->post('/attendance/clock_out');

        $response->assertRedirect();

        $this->assertDatabaseHas('attendances', [
            'id' => $attendance->id,
        ]);
    }

    //-------- 休憩開始のテスト -----------
    public function test_user_can_start_break()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $this->actingAs($user);

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => now()->toDateString(),
            'clock_in' => now()->toTimeString(),
            'status' => '出勤中',
        ]);

        $response = $this->post('/attendance/break_start');

        $response->assertRedirect();

        // breaksテーブルに記録が作成されていること
        $this->assertDatabaseHas('breaks', [
            'attendance_id' => $attendance->id,
        ]);

        // ステータスが「休憩中」に更新されていること
        $this->assertDatabaseHas('attendances', [
            'id' => $attendance->id,
            'status' => '休憩中',
        ]);
    }

    //-------- 休憩終了のテスト -----------
    public function test_user_can_end_break()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $this->actingAs($user);

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => now()->toDateString(),
            'clock_in' => now()->toTimeString(),
            'status' => '休憩中',
        ]);

        $break = BreakTime::create([
            'attendance_id' => $attendance->id,
            'start_time' => now(),
        ]);

        $response = $this->post('/attendance/break_end');

        $response->assertRedirect();

        // end_timeが記録されていること
        $this->assertNotNull(BreakTime::find($break->id)->end_time);

        // ステータスが「出勤中」に戻っていること
        $this->assertDatabaseHas('attendances', [
            'id' => $attendance->id,
            'status' => '出勤中',
        ]);
    }

    //-------- 出勤中でない場合は休憩開始できないテスト -----------
    public function test_user_cannot_start_break_when_not_clocked_in()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $this->actingAs($user);

        // 出勤記録なし
        $response = $this->post('/attendance/break_start');

        $response->assertRedirect();

        // breaksテーブルに記録が作成されていないこと
        $this->assertDatabaseCount('breaks', 0);
    }

    //-------- 休憩中でない場合は休憩終了できないテスト -----------
    public function test_user_cannot_end_break_when_not_on_break()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $this->actingAs($user);

        Attendance::create([
            'user_id' => $user->id,
            'date' => now()->toDateString(),
            'clock_in' => now()->toTimeString(),
            'status' => '出勤中',
        ]);

        $response = $this->post('/attendance/break_end');

        $response->assertRedirect();

        // breaksテーブルに記録が作成されていないこと
        $this->assertDatabaseCount('breaks', 0);
    }

    //-------- 出勤前は休憩ボタンが表示されない -----------
    public function test_break_button_not_shown_before_clock_in()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/attendance');

        $response->assertStatus(200);
        $response->assertSee('出勤');
        $response->assertDontSee('休憩入');
        $response->assertDontSee('休憩戻');
    }

    //-------- 出勤中は「休憩入」ボタンが表示される -----------
    public function test_break_start_button_shown_when_clocked_in()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $this->actingAs($user);

        Attendance::create([
            'user_id' => $user->id,
            'date' => now()->toDateString(),
            'clock_in' => now()->toTimeString(),
            'status' => '出勤中',
        ]);

        $response = $this->get('/attendance');

        $response->assertStatus(200);
        $response->assertSee('休憩入');
        $response->assertDontSee('休憩戻');
    }

    //-------- 休憩中は「休憩戻」ボタンが表示される -----------
    public function test_break_end_button_shown_when_on_break()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $this->actingAs($user);

        Attendance::create([
            'user_id' => $user->id,
            'date' => now()->toDateString(),
            'clock_in' => now()->toTimeString(),
            'status' => '休憩中',
        ]);

        $response = $this->get('/attendance');

        $response->assertStatus(200);
        $response->assertSee('休憩戻');
        $response->assertDontSee('休憩入');
    }

    //-------- 退勤後は休憩ボタンが表示されない -----------
    public function test_break_button_not_shown_after_clock_out()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $this->actingAs($user);

        Attendance::create([
            'user_id' => $user->id,
            'date' => now()->toDateString(),
            'clock_in' => now()->toTimeString(),
            'clock_out' => now()->toTimeString(),
            'status' => '退勤済',
        ]);

        $response = $this->get('/attendance');

        $response->assertStatus(200);
        $response->assertDontSee('休憩入');
        $response->assertDontSee('休憩戻');
    }
}
