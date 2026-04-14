<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AttendanceListController;
use App\Http\Controllers\RegisterController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;


Route::get('/register', [RegisterController::class, 'member_registration'])->name('member.register');
Route::post('/register', [RegisterController::class, 'store'])->name('member.store');

//-------- ログイン--------
Route::get('/', function () {
    return redirect('/login');
});

 Route::post('/attendance', [AttendanceController::class, 'login'])->name('attendance.login');

//-----------------ログイン後-------------------
Route::middleware('auth')->group(function () {

    // --------勤怠管理画面-----------
    route::get('/attendance', [AttendanceController::class, 'show_attendance'])->name('attendance.show');

    Route::post('attendance/clock_in', [AttendanceController::class, 'clock_in'])->name('attendance.clock_in');

    Route::post('attendance/clock_out', [AttendanceController::class, 'clock_out'])->name('attendance.clock_out');

    Route::post('attendance/break_start', [AttendanceController::class, 'break_start'])->name('attendance.break_start');

    Route::post('attendance/break_end', [AttendanceController::class, 'break_end'])->name('attendance.break_end');

    // --------勤怠一覧画面-----------
    Route::get('/attendance/list', [AttendanceListController::class, 'attendance_list'])->name('attendance.list');

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
