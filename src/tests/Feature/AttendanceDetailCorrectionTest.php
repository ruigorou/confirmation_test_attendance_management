<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;

class AttendanceDetailCorrectionTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    //出勤時間が退勤時間より後になっている場合、エラーメッセージが表示される
    public function test_attendance_detail_shows_error_message_when_clock_in_after_clock_out() 
    {
        /** @var User $user */
        $user = User::factory()->create();
        $this->actingAs($user);

         $attendance = Attendance::create([
        'user_id' => $user->id,
        'date' => now()->toDateString(),
        'clock_in' => '09:00:00',
        'clock_out' => '18:00:00',
        'status' => '退勤済',
        ]);

        $response = $this->put('/attendance/' . $attendance->id, [
            'clock_in' => '18:00',
            'clock_out' => '17:00',
            'remarks' => 'テスト',
        ]);

        $response->assertSessionHasErrors([
            'clock_in' => '出勤時間もしくは退勤時間が不適切な値です'
        ]);
    }

    //休憩開始時間が退勤時間より後になっている場合、エラーメッセージが表示される
    public function test_attendance_detail_shows_error_message_when_break_start_time_after_clock_out() {
        /** @var User $user */
        $user = User::factory()->create();
        $this->actingAs($user);

         $attendance = Attendance::create([
        'user_id' => $user->id,
        'date' => now()->toDateString(),
        'clock_in' => '09:00:00',
        'clock_out' => '18:00:00',
        'status' => '退勤済',
        ]);

        $response = $this->put('/attendance/' . $attendance->id, [
            'clock_in' => '09:00',
            'clock_out' => '18:00',
            'remarks' => 'テスト',
            'start_time1' => '19:00', // 退勤時間より後
            'end_time1' => '20:00',
        ]);

        $response->assertSessionHasErrors([
            'start_time1' => '休憩時間が不適切な値です'
        ]);
    }

    //休憩終了時間が退勤時間より後になっている場合、エラーメッセージが表示される
    public function test_attendance_detail_shows_error_message_when_break_end_time_after_clock_out()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $this->actingAs($user);

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => now()->toDateString(),
            'clock_in' => '09:00:00',
            'clock_out' => '18:00:00',
            'status' => '退勤済',
        ]);

        $response = $this->put('/attendance/' . $attendance->id, [
            'clock_in' => '09:00',
            'clock_out' => '18:00',
            'remarks' => 'テスト',
            'start_time1' => '17:00',
            'end_time1' => '19:00', // 退勤時間より後
        ]);

        $response->assertSessionHasErrors([
            'end_time1' => '休憩時間もしくは退勤時間が不適切な値です'
        ]);
    }

    //備考欄が未入力の場合のエラーメッセージが表示される
    public function test_attendance_detail_shows_error_message_when_remarks_empty()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $this->actingAs($user);

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => now()->toDateString(),
            'clock_in' => '09:00:00',
            'clock_out' => '18:00:00',
            'status' => '退勤済',
        ]);

        $response = $this->put('/attendance/' . $attendance->id, [
            'clock_in' => '09:00',
            'clock_out' => '18:00',
            'remarks' => '', // 備考欄が空
        ]);

        $response->assertSessionHasErrors([
            'remarks' => '備考を記入してください'
        ]);
    }

    //修正申請が実行され、管理者の承認画面と申請一覧画面に表示される
    public function test_attendance_detail_shows_attendance_date()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $this->actingAs($user);

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => '2024-06-01',
            'clock_in' => '09:00:00',
            'clock_out' => '18:00:00',
            'status' => '退勤済',
            'approval_status' => '承認待ち',
            'remarks' => 'テスト申請'
        ]);

         $response = $this->get("stamp_correction_request/list");

        $response->assertStatus(200);
        $response->assertSee('2024/06/01');
    }
}
