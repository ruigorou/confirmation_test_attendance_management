<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Http\Requests\AttendanceDetailRequest;
use App\Models\BreakTime;
use Carbon\Carbon;

class AttendanceDetailController extends Controller
{
    public function attendance_detail($id) {
        $attendance = Attendance::with('break_times')->findOrFail($id);
        return view('attendance_detail', compact('attendance'));
    }

    public function update_attendance(AttendanceDetailRequest $request, $id) {
        $attendance = Attendance::findOrFail($id);

        // --- 勤怠時間の更新 --
        $data = [
            'clock_in' => $request->input('clock_in'),
            'clock_out' => $request->input('clock_out'),
            'remarks' => $request->input('remarks'),
            'approval_status' => '承認待ち', // 更新時に承認待ちに設定
        ];
       
        $attendance->update($data);
        $attendance->refresh();

        // --- 休憩時間の更新 ---
        foreach ($attendance->break_times as $break_time) {
            $start = $request->input("start_time{$break_time->id}");
            $end = $request->input("end_time{$break_time->id}");

            if (!empty($start) && !empty($end)) {
                $break_time->update([
                    'start_time' => Carbon::parse($attendance->date . ' ' . $start),
                    'end_time' => Carbon::parse($attendance->date . ' ' . $end),
                ]);
            }
        }

        // --- 新規休憩時間の登録 ---
        $newStart = $request->input('new_start_time');
        $newEnd = $request->input('new_end_time');

        if (!empty($newStart) && !empty($newEnd)) {
            BreakTime::create([
                'attendance_id' => $id,
                'start_time' => Carbon::parse($attendance->date . ' ' . $newStart),
                'end_time' => Carbon::parse($attendance->date . ' ' . $newEnd),
            ]);
        }

        return back();
    }
}
