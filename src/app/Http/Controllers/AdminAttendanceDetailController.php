<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\BreakTime;
use App\Http\Requests\AttendanceDetailRequest;
use Carbon\Carbon;

class AdminAttendanceDetailController extends Controller
{
    public function show($id)
    {
        $attendance = Attendance::with('break_times', 'user')->findOrFail($id);
        return view('admin_attendance_detail', compact('attendance'));
    }

    public function update(AttendanceDetailRequest $request, $id)
    {
        $attendance = Attendance::findOrFail($id);

        $attendance->update([
            'clock_in'        => $request->input('clock_in'),
            'clock_out'       => $request->input('clock_out'),
            'remarks'         => $request->input('remarks'),
            'approval_status' => '承認済み',
        ]);

        $attendance->refresh();

        foreach ($attendance->break_times as $break_time) {
            $start = $request->input("start_time{$break_time->id}");
            $end   = $request->input("end_time{$break_time->id}");

            if (!empty($start) && !empty($end)) {
                $break_time->update([
                    'start_time' => Carbon::parse($attendance->date . ' ' . $start),
                    'end_time'   => Carbon::parse($attendance->date . ' ' . $end),
                ]);
            }
        }

        $newStart = $request->input('new_start_time');
        $newEnd   = $request->input('new_end_time');

        if (!empty($newStart) && !empty($newEnd)) {
            BreakTime::create([
                'attendance_id' => $id,
                'start_time'    => Carbon::parse($attendance->date . ' ' . $newStart),
                'end_time'      => Carbon::parse($attendance->date . ' ' . $newEnd),
            ]);
        }

        return back();
    }
}
