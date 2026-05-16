<?php

use App\Http\Controllers\AdminAttendanceApplicationListController;
use App\Http\Controllers\AdminApprovalController;
use App\Http\Controllers\AdminAttendanceListController;
use App\Http\Controllers\AdminAttendanceDetailController;
use App\Http\Controllers\AdminAttendanceStaffController;
use App\Http\Controllers\AdminStaffListController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AttendanceListController;
use App\Http\Controllers\AttendanceDetailController;
use App\Http\Controllers\AttendanceApplicationListController;
use App\Http\Controllers\AdminLoginController;
use App\Http\Controllers\AdminLogoutController;
use App\Http\Controllers\RegisterController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;


Route::get('/register', [RegisterController::class, 'member_registration'])->name('member.register');
Route::post('/register', [RegisterController::class, 'store'])->name('member.store');

//-------- 管理者ログイン--------
Route::get('/admin/login', [AdminLoginController::class, 'show_login_form'])->name('admin.login.form');
Route::post('/admin/login', [AdminLoginController::class, 'login'])->name('admin.login');

//-------- ログイン--------
Route::get('/', function () {
    return redirect('/login');
});

Route::post('/attendance', [AttendanceController::class, 'login'])->name('attendance.login');

 //------------管理者画面 ログイン後------------
Route::middleware('auth.admin_login')->group(function () {
    // --------  勤怠一覧画面(管理者)  -----------
    Route::get('/admin/attendance/list', [AdminAttendanceListController::class, 'admin_attendance_list'])->name('admin.attendance.list');

    // --------  勤怠詳細画面（管理者）  -----------
    Route::get('/admin/attendance/{id}', [AdminAttendanceDetailController::class, 'show'])->name('admin.attendance.detail');
    Route::put('/admin/attendance/{id}', [AdminAttendanceDetailController::class, 'update'])->name('admin.attendance.detail.update');

    // --------  スタッフ一覧画面（管理者）  -----------
    Route::get('/admin/staff/list', [AdminStaffListController::class, 'index'])->name('admin.staff.list');

    Route::get('/admin/attendance/staff/{id}', [AdminAttendanceStaffController::class, 'attendance_staff'])->name('admin.attendance.staff');

    Route::get('/admin/attendance/staff/{id}/csv-download', [AdminAttendanceStaffController::class, 'export_csv'])->name('admin.export.csv');

    //----------- 申請一覧 -----------------
    Route::get('/satmp_correction_request/list', [AdminAttendanceApplicationListController::class, 'attendance_list'])->name('admin.attendance.application.list');

    //----------- 申請承認 -----------------
    Route::get('/stamp_correction_request/approval/{attendance_correct_request_id}', [AdminApprovalController::class, 'show'])->name('admin.approval.show');
    Route::patch('/stamp_correction_request/approval/{attendance_correct_request_id}', [AdminApprovalController::class, 'approve'])->name('admin.approval.approve');
});
Route::post('/admin/logout', [AdminLogoutController::class, 'admin_logout'])->name('admin.logout');

//-----------------ログイン後（一般ユーザー）-------------------
Route::middleware('auth.regular_member_login')->group(function () {

    // --------勤怠管理画面（一般ユーザー）-----------
    Route::get('/attendance', [AttendanceController::class, 'show_attendance'])->name('attendance.show');

    Route::post('attendance/clock_in', [AttendanceController::class, 'clock_in'])->name('attendance.clock_in');

    Route::post('attendance/clock_out', [AttendanceController::class, 'clock_out'])->name('attendance.clock_out');

    Route::post('attendance/break_start', [AttendanceController::class, 'break_start'])->name('attendance.break_start');

    Route::post('attendance/break_end', [AttendanceController::class, 'break_end'])->name('attendance.break_end');

    // --------勤怠一覧画面（一般ユーザー）-----------
    Route::get('/attendance/list', [AttendanceListController::class, 'attendance_list'])->name('attendance.list');

    // --------申請一覧画面（一般ユーザー）-----------
    Route::get('/stamp_correction_request/list', [AttendanceApplicationListController::class, 'index'])->name('attendance.application.list');

    // --------勤怠詳細画面（一般ユーザー）-----------
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
