<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use App\Models\BreakTime;

class AttendanceController extends Controller
{
    public function login (LoginRequest $request) {
       $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {

            $user = Auth::user();

            // メール未認証ならリダイレクト
            if (is_null($user->email_verified_at)) {
                $request->user()->sendEmailVerificationNotification();

                return redirect()->route('verification.notice');
            }

            // 認証済みなら通常画面へ
            $user_id = Auth()->user()->id;
            $now = Carbon::now();
            $attendance = Attendance::where('user_id', $user_id)
                ->where('date', $now->toDateString())
                ->latest()
                ->first();
            return view('attendance', compact('user_id', 'now', 'attendance'));
        }

        return back()->withErrors([
            'email' => 'ログイン情報が登録されていません',
        ]);
    }

    public function show_attendance () {
        $user_id = Auth()->user()->id;
        $now = Carbon::now();
        $attendance = Attendance::where('user_id', $user_id)
            ->where('date', $now->toDateString())
            ->latest()
            ->first();
        return view('attendance', compact('user_id', 'now', 'attendance'));
    }

    public function clock_in (Request $request) {
        $user_id = Auth()->user()->id;
        $date = Carbon::now()->toDateString();
        $clock_in = Carbon::now()->toTimeString();

        // 既に出勤中・休憩中の記録があるか確認
        $attendance = Attendance::where('user_id', $user_id)
            ->where('date', $date)
            ->latest()
            ->first();

        if ($attendance && $attendance->status !== '退勤済') {
            return back();
        }


        // 出勤記録を作成
        Attendance::create([
            'user_id' => $user_id,
            'date' => $date,
            'clock_in' => $clock_in,
            'status' => '出勤中',
        ]);

        return back();
    }

    public function clock_out (Request $request) {
        $user_id = Auth()->user()->id;
        $date = Carbon::now()->toDateString();
        $clock_out = Carbon::now()->toTimeString();

        // 出勤記録を取得
        $attendance = Attendance::where('user_id', $user_id)
            ->where('date', $date)
            ->latest()
            ->first();

        if (!$attendance || $attendance->status != '出勤中') {
            return back();
        }

        // 退勤記録を更新
        $attendance->update([
            'clock_out' => $clock_out,
            'status' => '退勤済',
        ]);

        return back();
    }

    public function break_start (Request $request) {
        $user_id = Auth()->user()->id;
        $date = Carbon::now()->toDateString();

        // 出勤記録を取得
        $attendance = Attendance::where('user_id', $user_id)
            ->where('date', $date)
            ->latest()
            ->first();

        if (!$attendance || $attendance->status != '出勤中') {
            return back();
        }

        // 休憩開始
        BreakTime::create([
            'attendance_id' => $attendance->id,
            'start_time' => Carbon::now(),
        ]);

        $attendance->update(['status' => '休憩中']);

        return back();
    }

    public function break_end (Request $request) {
        $user_id = Auth()->user()->id;
        $date = Carbon::now()->toDateString();

        // 出勤記録を取得
        $attendance = Attendance::where('user_id', $user_id)
            ->where('date', $date)
            ->latest()
            ->first();

        if (!$attendance || $attendance->status != '休憩中') {
            return back();
        }

        // 最新の休憩を取得
        $break = BreakTime::where('attendance_id', $attendance->id)
            ->whereNull('end_time')
            ->latest()
            ->first();

        if ($break) {
            $break->update(['end_time' => Carbon::now()]);
        }

        $attendance->update(['status' => '出勤中']);

        return back();
    }
}
