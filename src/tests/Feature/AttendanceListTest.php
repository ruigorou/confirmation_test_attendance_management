<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;

class AttendanceListTest extends TestCase
{
    use RefreshDatabase;

    // 勤怠一覧画面に遷移したとき現在の年月が表示されるか
    public function test_attendance_list_shows_current_month()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/attendance/list');

        $response->assertStatus(200);
        $response->assertSee(Carbon::now()->format('Y') . '年');
        $response->assertSee(Carbon::now()->format('n') . '月');
    }

    // 勤怠一覧に登録した全勤怠情報が表示されるか
    public function test_attendance_list_shows_all_registered_records()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $this->actingAs($user);

        $now = Carbon::now();

        $records = [
            [
                'date' => $now->copy()->startOfMonth()->toDateString(),
                'clock_in'  => '09:15:00',
                'clock_out' => '18:15:00',
            ],
            [
                'date' => $now->copy()->startOfMonth()->addDays(4)->toDateString(),
                'clock_in'  => '10:30:00',
                'clock_out' => '19:30:00',
            ],
            [
                'date' => $now->copy()->startOfMonth()->addDays(9)->toDateString(),
                'clock_in'  => '08:45:00',
                'clock_out' => '17:45:00',
            ],
        ];

        foreach ($records as $record) {
            Attendance::create([
                'user_id'   => $user->id,
                'date'      => $record['date'],
                'clock_in'  => $record['clock_in'],
                'clock_out' => $record['clock_out'],
                'status'    => '退勤済',
            ]);
        }

        $response = $this->get('/attendance/list');

        $response->assertStatus(200);

        foreach ($records as $record) {
            $response->assertSee(Carbon::parse($record['date'])->format('m/d'));
            $response->assertSee(Carbon::parse($record['clock_in'])->format('H:i'));
            $response->assertSee(Carbon::parse($record['clock_out'])->format('H:i'));
        }
    }

    //-------- 前月を押したときに表示月の前月が表示されるか -----------
    public function test_attendance_list_shows_previous_month_when_prev_button_clicked()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $this->actingAs($user);

        $prev_month = Carbon::now()->subMonth()->format('Y-m');

        $response = $this->get('/attendance/list?month=' . $prev_month);

        $response->assertStatus(200);
        $response->assertSee(Carbon::parse($prev_month)->format('Y') . '年');
        $response->assertSee(Carbon::parse($prev_month)->format('n') . '月');
    }

    //-------- 翌月を押したときに表示月の翌月が表示されるか -----------
    public function test_attendance_list_shows_next_month_when_next_button_clicked()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $this->actingAs($user);

        $next_month = Carbon::now()->addMonth()->format('Y-m');

        $response = $this->get('/attendance/list?month=' . $next_month);

        $response->assertStatus(200);
        $response->assertSee(Carbon::parse($next_month)->format('Y') . '年');
        $response->assertSee(Carbon::parse($next_month)->format('n') . '月');
    }

    //-------- 「詳細」を押下すると、その日の勤怠詳細画面に遷移する -----------
    public function test_attendance_list_navigates_to_detail_page_when_detail_button_clicked()
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

        $response = $this->get('/attendance/list');

        $response->assertStatus(200);
        $response->assertSee(route('attendance.detail', ['id' => $attendance->id]));
    }
}
