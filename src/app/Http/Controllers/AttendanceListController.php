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
        
        $month = Carbon::parse($request->query('month', Carbon::now()->format('Y-m')));
        $start = $month->copy()->startOfMonth();
        $end   = $month->copy()->endOfMonth();

        $dates = [];
        $current = $start->copy();
        while ($current->lte($end)) {
            $dates[] = $current->copy();
            $current->addDay();
        }
        
       $attendances = Attendance::where('user_id', $user_id)
        ->whereBetween('date', [$start, $end])
        ->orderBy('date')
        ->get()
        ->map(function ($attendance) {

            // --- 休憩時間（秒） ---
            $break_seconds = BreakTime::where('attendance_id', $attendance->id)
                ->whereNotNull('end_time')
                ->get()
                ->sum(function ($break) {
                    return Carbon::parse($break->start_time)
                        ->diffInSeconds(Carbon::parse($break->end_time));
                });

            // --- 勤務時間（秒） ---
            $total_seconds = null;
            if ($attendance->clock_in && $attendance->clock_out) {
                $total_seconds =
                    Carbon::parse($attendance->clock_in)
                        ->diffInSeconds(Carbon::parse($attendance->clock_out))
                    - $break_seconds;
            }

            // --- 表示用フォーマット ---
            // 休憩
            $attendance->break_time = $break_seconds > 0
                ? gmdate('H:i', (int)round($break_seconds / 60)*60)
                : null;

            // 合計
            
            $attendance->total_time = $total_seconds !== null
                ? gmdate('H:i', (int)round($total_seconds / 60)*60)
                : null;

            return $attendance;
        })->keyBy('date');

        $prev_month = $month->copy()->subMonth()->format('Y-m');
        $next_month = $month->copy()->addMonth()->format('Y-m');

        return view('attendance_list', compact('attendances', 'month', 'prev_month', 'next_month', 'dates'));
    }
}
