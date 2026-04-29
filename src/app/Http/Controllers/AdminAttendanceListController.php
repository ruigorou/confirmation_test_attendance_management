<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\BreakTime;
use Carbon\Carbon;

class AdminAttendanceListController extends Controller
{
    public function admin_attendance_list(Request $request)
    {
        $date = Carbon::parse($request->query('date', Carbon::now()->format('Y-m-d')));

        $attendances = Attendance::with('user')
            ->where('date', $date->format('Y-m-d'))
            ->orderBy('user_id')
            ->get()
            ->map(function ($attendance) {
                $break_seconds = BreakTime::where('attendance_id', $attendance->id)
                    ->whereNotNull('end_time')
                    ->get()
                    ->sum(function ($break) {
                        return Carbon::parse($break->start_time)
                            ->diffInSeconds(Carbon::parse($break->end_time));
                    });

                $total_seconds = null;
                if ($attendance->clock_in && $attendance->clock_out) {
                    $total_seconds =
                        Carbon::parse($attendance->clock_in)
                            ->diffInSeconds(Carbon::parse($attendance->clock_out))
                        - $break_seconds;
                }

                $attendance->break_time = $break_seconds > 0
                    ? gmdate('H:i', (int)round($break_seconds / 60) * 60)
                    : null;

                $attendance->total_time = $total_seconds !== null
                    ? gmdate('H:i', (int)round($total_seconds / 60) * 60)
                    : null;

                return $attendance;
            });

        $prev_date = $date->copy()->subDay()->format('Y-m-d');
        $next_date = $date->copy()->addDay()->format('Y-m-d');

        return view('admin_attendance_list', compact('attendances', 'date', 'prev_date', 'next_date'));
    }
}
