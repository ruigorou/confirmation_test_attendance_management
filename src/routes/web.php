<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AttendanceListController;
use App\Http\Controllers\AttendanceDetailController;
use App\Http\Controllers\AttendanceApplicationListController;
use App\Http\Controllers\AdminLoginController;
use App\Http\Controllers\RegisterController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;


Route::get('/register', [RegisterController::class, 'member_registration'])->name('member.register');
Route::post('/register', [RegisterController::class, 'store'])->name('member.store');

//-------- 管理者ログイン--------
Route::get('/admin/login', [AdminLoginController::class, 'showLoginForm'])->name('admin.login.form');
Route::post('/admin/login', [AdminLoginController::class, 'login'])->name('admin.login');

//-------- ログイン--------
Route::get('/', function () {
    return redirect('/login');
});

 Route::post('/attendance', [AttendanceController::class, 'login'])->name('attendance.login');

 //------------管理者画面 ログイン後------------
Route::middleware('auth.admin_login')->group(function () {
    // --------管理者ダッシュボード（プレースホルダー）-----------

});

//-----------------ログイン後-------------------
Route::middleware('auth.regular_member_login')->group(function () {

    // --------勤怠管理画面-----------
    route::get('/attendance', [AttendanceController::class, 'show_attendance'])->name('attendance.show');

    Route::post('attendance/clock_in', [AttendanceController::class, 'clock_in'])->name('attendance.clock_in');

    Route::post('attendance/clock_out', [AttendanceController::class, 'clock_out'])->name('attendance.clock_out');

    Route::post('attendance/break_start', [AttendanceController::class, 'break_start'])->name('attendance.break_start');

    Route::post('attendance/break_end', [AttendanceController::class, 'break_end'])->name('attendance.break_end');

    // --------勤怠一覧画面-----------
    Route::get('/attendance/list', [AttendanceListController::class, 'attendance_list'])->name('attendance.list');

    // --------申請一覧画面-----------
    Route::get('/stamp_correction_request/list', [AttendanceApplicationListController::class, 'index'])->name('attendance.application.list');

    // --------勤怠詳細画面-----------
    Route::get('/attendance/{id}', [AttendanceDetailController::class, 'attendance_detail'])->name('attendance.detail');
    Route::put('/attendance/{id}', [AttendanceDetailController::class, 'update_attendance'])->name('attendance.update');

});


//---------メール認証----------
Route::get('/email/verify', function () {
    return view('email _verification');
})->middleware('auth')->name('verification.notice');

//------メール検証ハンドラ-------
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    return redirect('/attendance');
})->middleware(['auth', 'signed'])->name('verification.verify');

//-------メール再送信-----------
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return redirect()->route('verification.notice')->with('message', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');
