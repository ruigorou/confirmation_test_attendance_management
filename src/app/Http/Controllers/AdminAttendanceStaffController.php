<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\BreakTime;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdminAttendanceStaffController extends Controller
{
    public function attendance_staff(Request $request, $id)
    {
        $user  = User::findOrFail($id);
        $month = Carbon::parse($request->query('month', Carbon::now()->format('Y-m')));
        $start = $month->copy()->startOfMonth();
        $end   = $month->copy()->endOfMonth();

        $dates = [];
        $current = $start->copy();
        while ($current->lte($end)) {
            $dates[] = $current->copy();
            $current->addDay();
        }

        $attendances = Attendance::where('user_id', $id)
            ->whereBetween('date', [$start, $end])
            ->orderBy('date')
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
            })->keyBy('date');

        $prev_month = $month->copy()->subMonth()->format('Y-m');
        $next_month = $month->copy()->addMonth()->format('Y-m');

        return view('admin_attendance_staff', compact('user', 'attendances', 'month', 'prev_month', 'next_month', 'dates'));
    }

    public function export_csv(Request $request, $id){

        // 指定月（なければ今月）
        $month = Carbon::parse($request->query('month', now()->format('Y-m')));

        $start = $month->copy()->startOfMonth();
        $end   = $month->copy()->endOfMonth();

        $attendances = Attendance::with('break_times')
            ->where('user_id', $id)
            ->whereBetween('date', [$start, $end])
            ->orderBy('date')
            ->get();

        $headers = [
            'date', 
            'start_time', 
            'end_time',
            'break_time',
            'total_time',
        ];

        $callback = function () use ($attendances, $headers) {
            $handle = fopen('php://output', 'w');
            // ヘッダーを書き込み
            fputcsv($handle, $headers);

            foreach ($attendances as $attendance) {
                // 休憩時間合計（秒）
                $break_seconds = $attendance->break_times
                    ->whereNotNull('end_time')
                    ->sum(function ($break) {
                        return Carbon::parse($break->start_time)
                            ->diffInSeconds(Carbon::parse($break->end_time));
                    });

                // 勤務時間
                $total_seconds = null;
                if ($attendance->clock_in && $attendance->clock_out) {
                    $total_seconds =
                        Carbon::parse($attendance->clock_in)
                        ->diffInSeconds(Carbon::parse($attendance->clock_out))
                        - $break_seconds;
                }

                fputcsv($handle, [
                    $attendance->date ? Carbon::parse($attendance->date)->isoFormat('MM/DD(ddd)') : '',
                    $attendance->clock_in ? Carbon::parse($attendance->clock_in)->format('H:i') : '', $attendance->clock_out ? Carbon::parse($attendance->clock_out)->format('H:i') : '',
                    $break_seconds ? gmdate('H:i', (int)round($break_seconds / 60) * 60) : '',
                    $total_seconds ? gmdate('H:i', (int)round($total_seconds / 60) * 60) : '',
                ]);
            }
                fclose($handle);
            };

        $filename = 'attendance_' . date('Ymd_His') . '.csv';

        return response()->streamDownload($callback, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }
}
