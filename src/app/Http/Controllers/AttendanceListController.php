<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use App\Models\BreakTime;

class AttendanceListController extends Controller
{
    public function attendance_list (Request $request) {
        $user_id = Auth()->user()->id;

        $month = $request->query('month')
            ? Carbon::parse($request->query('month'))
            : Carbon::now();

        $attendances = Attendance::where('user_id', $user_id)
            ->whereYear('date', $month->year)
            ->whereMonth('date', $month->month)
            ->orderBy('date')
            ->get()
            ->map(function ($attendance) {
                $break_minutes = BreakTime::where('attendance_id', $attendance->id)
                    ->whereNotNull('end_time')
                    ->get()
                    ->sum(function ($break) {
                        return Carbon::parse($break->start_time)->diffInMinutes(Carbon::parse($break->end_time));
                    });

                $total_minutes = null;
                if ($attendance->clock_in && $attendance->clock_out) {
                    $total_minutes = Carbon::parse($attendance->clock_in)->diffInMinutes(Carbon::parse($attendance->clock_out)) - $break_minutes;
                }

                $attendance->break_time  = $break_minutes > 0 ? sprintf('%d:%02d', intdiv($break_minutes, 60), $break_minutes % 60) : null;
                $attendance->total_time  = $total_minutes !== null ? sprintf('%d:%02d', intdiv($total_minutes, 60), $total_minutes % 60) : null;

                return $attendance;
            });

        $prev_month = $month->copy()->subMonth()->format('Y-m');
        $next_month = $month->copy()->addMonth()->format('Y-m');

        return view('attendance_list', compact('attendances', 'month', 'prev_month', 'next_month'));
    }
}
