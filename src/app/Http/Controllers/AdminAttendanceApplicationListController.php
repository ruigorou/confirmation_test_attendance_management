<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;

class AdminAttendanceApplicationListController extends Controller
{
    public function attendance_list(Request $request)
    {
        $tab = $request->query('tab', '承認待ち');

        $attendances = Attendance::with('user')
            ->where('approval_status', $tab)
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('admin_attendance_application_list', compact('attendances', 'tab'));
    }
}
